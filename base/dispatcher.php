<?php
/**
 * dispatcher.php is part of Marifa.
 *
 * Marifa is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Marifa is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Marifa. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase encargada de despachar las peticiones.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Dispatcher {

	/**
	 * Realiza el despachado de la petición web.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public static function dispatch()
	{
		// Obtenemos la URL pedida actualmente.
		if (php_sapi_name() == 'cli-server')
		{
			$url = $_SERVER['REQUEST_URI'];
		}
		else
		{
			$url = Request::current_url();
		}

		$url = trim($url, '/');

		// En caso de ser /, la transformamos en vacía.
		if ($url === '/')
		{
			$url = '';
		}

		// Verificamos assets tema.
		if (preg_match('/^(\/){0,1}(theme)\/([a-z0-9_]+)\/(assets)\/(css|js)\/(.*?)(\.css|\.js)$/D', $url))
		{
			// Genero el path.
			if ($url{0} == '/')
			{
				$p = APP_BASE.$url;
			}
			else
			{
				$p = APP_BASE.DS.$url;
			}

			// Compilo el asset.
			Assets::reverse_compile($p, ! DEBUG);
		}

		// Verificamos assets en plugins.
		if (preg_match('/^(\/){0,1}(plugins)\/([a-z0-9]+)\/(assets)\/((css|js)\/){0,1}([a-z0-9_\.]+)(\.css|\.js)$/D', $url))
		{
			// Transformo en un path válido.
			if ($url{0} == '/')
			{
				$p = APP_BASE.DS.'plugin'.substr($url, 8);
			}
			else
			{
				$p = APP_BASE.DS.'plugin'.DS.substr($url, 8);
			}

			// Compilo el asset.
			Assets::reverse_compile($p, ! DEBUG);
		}

		return self::rewrite_urls($url);
	}

	/**
	 * Realizamos una petición interna.
	 *
	 * Es una simulación que nos permite implementar HMVC que es muy útil en
	 * en algunas situaciones.
	 *
	 * En caso de ser incorrecta la petición, se emite una excepción informando dicho error.
	 * @param string $url URL de la petición.
	 * @param bool $finish Utiliza un buffer limpio y finaliza la ejecución.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 * @throws Exception
	 */
	public static function call($url, $finish = FALSE)
	{
		if ($url === '/')
		{
			$url = '';
		}

		if (isset($url{0}) && $url{0} == '/')
		{
			$url = substr($url, 1);
		}

		if ($finish)
		{
			// Limpiamos el buffer.
			ob_clean();

			// Procesamos la consulta y terminamos.
			die(self::rewrite_urls($url, TRUE));
		}
		else
		{
			// Iniciamos el buffer, esa para no mostrar nada por pantalla de esta petición.
			ob_start();

			// Realizamos la llamada.
			$rst = self::rewrite_urls($url, TRUE);

			// Si no hubo respuesta probamos con lo que tenemos el en buffer.
			if ($rst === NULL)
			{
				$rst = ob_get_contents();
			}

			// Borramos el buffer y devolvemos el resultado.
			ob_end_clean();

			return $rst;
		}
	}

	/**
	 * Realizamos la re-escritura de URL mediante el enrutador.
	 * @param string $url URL actual.
	 * @param bool $throw Si en caso de error debe generar una excepción o mostrar un error.
	 */
	private static function rewrite_urls($url, $throw = FALSE)
	{
		// Verifico empiece con /.
		if ($url === '' || $url{0} !== '/')
		{
			$url = '/'.$url;
		}

		// Cargo enrutador.
		$router = new Router;

		// Cargo rutas del sistema.
		if (file_exists(APP_BASE.DS.'routes.php'))
		{
			$routes = include(APP_BASE.DS.'routes.'.FILE_EXT);
			foreach ($routes as $route)
			{
				call_user_func_array(array($router, 'map'), $route);
			}
		}

		// Obtengo listado de plugins activos.
		$pl = Plugin_Manager::get_instance()->get_actives();

		// Cargo URL's si hay plugins.
		if (count($pl) > 0)
		{
			if (count($pl) == 1)
			{
				// Cargo ruta del único plugin.
				if (file_exists(APP_BASE.DS.PLUGINS_PATH.DS.$pl[0].DS.'routes.'.FILE_EXT))
				{
					$routes = include(APP_BASE.DS.PLUGINS_PATH.DS.$pl[0].DS.'routes.'.FILE_EXT);
					foreach ($routes as $route)
					{
						call_user_func_array(array($router, 'map'), $route);
					}
				}
			}
			else
			{
				// Cargo las rutas que existan.
				foreach (glob(APP_BASE.DS.PLUGINS_PATH.DS.'{'.(implode(',', $pl)).'}'.DS.'routes.'.FILE_EXT, GLOB_BRACE) as $v)
				{
					$routes = include($v);
					foreach ($routes as $route)
					{
						call_user_func_array(array($router, 'map'), $route);
					}
				}
			}
		}

		// Realizo enrutado.
		//TODO: Métodos personalizados.
		$matched = $router->match($url, Request::method());
		if ($matched !== FALSE)
		{
			// Obtengo el target.
			$target = $matched->getTarget();

			if (is_array($target))
			{
				// Genero nombre del controlador.
				if (isset($target['plugin']))
				{
					// Formateo nombre del plugin.
					$p_name = strtolower($target['plugin']);

					// Validamos que tenga el formato requerido.
					if (preg_match('/^[a-z0-9]+$/D', $p_name) < 1)
					{
						if ( ! $throw)
						{
							Error::show_error('Petición inválida', 404);
						}
						else
						{
							throw new Exception('Petición inválida', 404);
						}
						return FALSE;
					}

					// Verifico su existencia y que este activo.
					$p_obj = Plugin_Manager::get_instance()->get($p_name);

					if ($p_obj === NULL || ! $p_obj->estado())
					{
						if ( ! $throw)
						{
							Error::show_error('Plugin inexistente', 404);
						}
						else
						{
							throw new Exception('Plugin inexistente', 404);
						}
						return FALSE;
					}

					// Nombre del controlador.
					$controller = 'Plugin_'.ucfirst($p_name).'_Controller_'.ucfirst($target['controller']);
				}
				else
				{
					if (isset($target['directory']))
					{
						$controller = 'Controller_'.ucfirst($target['directory']).'_'.ucfirst($target['controller']);
					}
					else
					{
						$controller = 'Controller_'.ucfirst($target['controller']);
					}
				}


			}
			else
			{
				// Vuelvo a procesar la URL.
				$target = trim($target, '/');

				// En caso de ser /, la transformamos en vacía.
				if ($target === '/')
				{
					$target = '';
				}

				// Llamo al routeo.
				return self::route($target);
			}

			// Nombre de la acción.
			$accion = $target['action'];

			// Creo instancia del controlador.
			if ( ! class_exists($controller))
			{
				if ( ! $throw)
				{
					Error::show_error("No existe el controlador: '$controller'", 404);
				}
				else
				{
					throw new Exception("No existe el controlador: '$controller'", 404);
				}
			}
			else
			{
				// Verificamos exista método.
				$r_c = new ReflectionClass($controller);
				if ( ! $r_c->hasMethod('action_'.$accion))
				{
					if ( ! $throw)
					{
						Error::show_error("No existe la acción '$accion' para el controlador '$controller'", 404);
					}
					else
					{
						throw new Exception("No existe la acción '$accion' para el controlador '$controller'", 404);
					}
				}
			}

			// Realizo la llamada.
			return self::call_controller($controller, $accion, $matched->getParameters(), isset($p_name) ? $p_name : NULL);
		}

		self::route($url, $throw);
	}

	/**
	 * Función encargada de enrutar una petición a controlador indicado.
	 * @param string $url URL de la petición.
	 * @param bool $throw Si en caso de error debe generar una excepción o mostrar un error.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	private static function route($url, $throw = FALSE)
	{
		// Obtenemos los segmentos de la URL
		$segmentos = explode('/', trim($url, '/'));

		// Verificamos si es un plugin.
		if (strtolower($segmentos[0]) === 'plugins')
		{
			// Verifico esté especificado un plugin.
			if ( ! isset($segmentos[1]))
			{
				if ( ! $throw)
				{
					Error::show_error('Petición inválida', 404);
				}
				else
				{
					throw new Exception('Petición inválida', 404);
				}
				return FALSE;
			}

			// Formateo el plugins.
			$p_name = strtolower($segmentos[1]);

			// Validamos que tenga el formato requerido.
			if (preg_match('/^[a-z0-9]+$/D', $p_name) < 1)
			{
				if ( ! $throw)
				{
					Error::show_error('Petición inválida', 404);
				}
				else
				{
					throw new Exception('Petición inválida', 404);
				}
				return FALSE;
			}

			// Verifico su existencia y que este activo.
			$p_obj = Plugin_Manager::get_instance()->get($p_name);

			if ($p_obj === NULL || ! $p_obj->estado())
			{
				if ( ! $throw)
				{
					Error::show_error('Plugin inexistente', 404);
				}
				else
				{
					throw new Exception('Plugin inexistente', 404);
				}
				return FALSE;
			}

			$p_dir = Plugin_Manager::nombre_as_path($p_name);


			if (file_exists($p_dir) && is_dir($p_dir))
			{
				// Cargamos el plugin.

				// Obtenemos el controlador
				$controller = empty($segmentos[2]) ? 'home' : strtolower($segmentos[2]);

				// Validamos que tenga el formato requerido.
				if (preg_match('/^[a-z0-9_]+$/D', $controller) < 1)
				{
					if ( ! $throw)
					{
						Error::show_error('Petición inválida', 404);
					}
					else
					{
						throw new Exception('Petición inválida', 404);
					}
					return FALSE;
				}

				// Obtenemos la acción.
				$accion = empty($segmentos[3]) ? 'index' : strtolower($segmentos[3]);

				// Validamos que tenga el formato requerido.
				if (preg_match('/^[a-z0-9_]+$/D', $accion) < 1)
				{
					if ( ! $throw)
					{
						Error::show_error('Petición inválida', 404);
					}
					else
					{
						throw new Exception('Petición inválida', 404);
					}
					return FALSE;
				}

				// Obtenemos los argumentos.
				if (is_array($segmentos) && count($segmentos) > 4)
				{
					$args = array_slice($segmentos, 4);
				}
				else
				{
					$args = array();
				}

				// Normalizamos el nombre del controlador para usar en las clases.
				$controller_name = 'Plugin_'.ucfirst($p_name).'_Controller_'.ucfirst($controller);

				// Creo instancia del controlador.
				if ( ! class_exists($controller_name))
				{
					if ( ! $throw)
					{
						Error::show_error("No existe el controlador: '$controller_name'", 404);
					}
					else
					{
						throw new Exception("No existe el controlador: '$controller_name'", 404);
					}
				}
				else
				{
					// Verificamos exista método.
					$r_c = new ReflectionClass($controller_name);
					if ( ! $r_c->hasMethod('action_'.$accion))
					{
						if ( ! $throw)
						{
							Error::show_error("No existe la acción '$accion' para el controlador '$controller_name'", 404);
						}
						else
						{
							throw new Exception("No existe la acción '$accion' para el controlador '$controller_name'", 404);
						}
					}
				}

				// Realizo la llamada.
				return self::call_controller($controller_name, $accion, $args, $p_name);
			}
			else
			{
				// Plugin Inválido.
				if ( ! $throw)
				{
					Error::show_error("El plugin '$p_name' no existe", 404);
				}
				else
				{
					throw new Exception("El plugin '$p_name' no existe", 404);
				}
				return FALSE;
			}
		}

		// Verificamos subdirectorio.
		if ( ! empty($segmentos[0]))
		{
			// Directorio
			$directorio = strtolower($segmentos[0]);

			// Obtenemos el controlador
			$controller = empty($segmentos[1]) ? 'home' : strtolower($segmentos[1]);

			// Obtenemos la acción.
			$accion = empty($segmentos[2]) ? 'index' : strtolower($segmentos[2]);

			if (preg_match('/^[a-z0-9_]+$/D', $controller) && preg_match('/^[a-z0-9_]+$/D', $accion))
			{
				// Obtenemos los argumentos.
				if (is_array($segmentos) && count($segmentos) > 3)
				{
					$args = array_slice($segmentos, 3);
				}
				else
				{
					$args = array();
				}

				// Normalizamos el nombre del controlador para usar en las clases.
				$controller_name = 'Controller_'.ucfirst($directorio).'_'.ucfirst($controller);

				// Creo instancia del controlador.
				if (class_exists($controller_name))
				{
					// Verificamos exista método.
					$r_c = new ReflectionClass($controller_name);
					if ($r_c->hasMethod('action_'.$accion))
					{
						// Realizo la llamada.
						return self::call_controller($controller_name, $accion, $args);
					}
				}
			}
		}

		// Obtenemos el controlador
		$controller = empty($segmentos[0]) ? 'home' : strtolower($segmentos[0]);

		if (preg_match('/^[a-z0-9_]+$/D', $controller) < 1)
		{
			if ( ! $throw)
			{
				Error::show_error('Petición inválida', 404);
			}
			else
			{
				throw new Exception('Petición inválida', 404);
			}
			return FALSE;
		}

		// Obtenemos la acción.
		$accion = empty($segmentos[1]) ? 'index' : strtolower($segmentos[1]);

		if (preg_match('/^[a-z0-9_]+$/D', $accion) < 1)
		{
			if ( ! $throw)
			{
				Error::show_error('Petición inválida', 404);
			}
			else
			{
				throw new Exception('Petición inválida', 404);
			}
			return FALSE;
		}

		// Obtenemos los argumentos.
		if (is_array($segmentos) && count($segmentos) > 2)
		{
			$args = array_slice($segmentos, 2);
		}
		else
		{
			$args = array();
		}

		// Normalizamos el nombre del controlador para usar en las clases.
		$controller_name = 'Controller_'.ucfirst($controller);

		// Creo instancia del controlador.
		if ( ! class_exists($controller_name))
		{
			if ( ! $throw)
			{
				Error::show_error("No existe el controlador: '$controller_name'", 404);
			}
			else
			{
				throw new Exception("No existe el controlador: '$controller_name'", 404);
			}
		}
		else
		{
			// Verificamos exista método.
			$r_c = new ReflectionClass($controller_name);
			if ( ! $r_c->hasMethod('action_'.$accion))
			{
				if ( ! $throw)
				{
					Error::show_error("No existe la acción '$accion' para el controlador '$controller_name'", 404);
				}
				else
				{
					throw new Exception("No existe la acción '$accion' para el controlador '$controller_name'", 404);
				}
			}
		}

		// Realizo la llamada.
		return self::call_controller($controller_name, $accion, $args);
	}

	/**
	 * Llamamos a la acción en el controlador indicado.
	 * @param string $controller Nombre de la clase del controlador.
	 * @param string $accion Nombre de la acción a ejecutar.
	 * @param array $args Listado de argumentos provistos.
	 * @param string $plugin Plugin que se llama si corresponde.
	 * @return mixed Resultado de la llamada al controlador.
	 */
	private static function call_controller($controller, $accion, $args, $plugin = NULL)
	{
		// Verifico modo mantenimiento.
		if (Mantenimiento::is_locked(FALSE))
		{
			// Verifico si esta autenticado.
			if ( ! Usuario::is_login() || Mantenimiento::is_locked_for(Usuario::$usuario_id, FALSE))
			{
				// Verifico el método.
				if ($controller !== 'Controller_Mantenimiento')
				{
					Request::redirect('/mantenimiento/');
				}
			}
		}

		// Creo instancia del objeto.
		$cont = new $controller;

		// Obtenemos la cantidad de parámetros necesaria.
		$r_m = new ReflectionMethod($cont, 'action_'.$accion);
		$p_n = $r_m->getNumberOfRequiredParameters();

		// Expando el arreglo de parámetros con NULL si es necesario.
		while (count($args) < $p_n)
		{
			$args[] = NULL;
		}

		// Agrego a Stack.
		Request::add_stack(NULL, $controller, $accion, $args, $plugin);

		// Evento Pre-Inicialización de todos los controladores.
		Event::trigger('Controller.Pre_Before', $cont);

		// Evento pre-before.
		Event::trigger('Controller.'.$controller.'.Pre_Before', $cont);

		// Llamo pre-llamada.
		if (method_exists($cont, 'before'))
		{
			call_user_func(array($cont, 'before'));
		}

		// Evento Pre-Ejecución de todos los controladores.
		Event::trigger('Controller.Pre_Controller', $cont);

		// Evento Pre-Ejecución del controlador.
		Event::trigger('Controller.'.$controller.'.Pre_Controller', $cont);

		// Llamo la acción.
		$rst = call_user_func_array(array(
				$cont,
				'action_'.$accion
		), $args);

		// Evento Post-Ejecución de todos los controladores.
		Event::trigger('Controller.Post_Controller', $cont);

		// Evento Post-Ejecución del controlador.
		Event::trigger('Controller.'.$controller.'.Post_Controller', $cont);

		// Llamo post-llamada.
		if (method_exists($cont, 'after'))
		{
			call_user_func(array($cont, 'after'));
		}

		// Evento Post-Post-Ejecución de todos los controladores.
		Event::trigger('Controller.Post_After', $cont);

		// Evento Post-Post-Ejecución del controlador.
		Event::trigger('Controller.'.$controller.'.Post_After', $cont);

		// Quito del Stack.
		Request::pop_stack();

		// Retorno el valor.
		return $rst;
	}
}
