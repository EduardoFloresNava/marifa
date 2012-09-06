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
	 * Obtiene el URL de la petición actual.
	 * @return string Url actual
	 */
	protected static function geturl()
	{
		if ( ! isset($_SERVER['REQUEST_URI']) || ! isset($_SERVER['SCRIPT_NAME']))
		{
			return '';
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
		{
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
		{
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}

		// This section ensures that even on servers that require the URI to be in the
		// query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strncmp($uri, '?/', 2) === 0)
		{
			$uri = substr($uri, 2);
		}
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1]))
		{
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}
		else
		{
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri == '/' || empty($uri))
		{
			return '/';
		}

		$uri = parse_url($uri, PHP_URL_PATH);

		// Do some final cleaning of the URI and return it
		$uri = str_replace(array(
				'//',
				'../'
		), '/', trim($uri, '/'));

		return $uri;
	}

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
			// $url = dirname();
		}
		else
		{
			$url = self::geturl();
		}

		$url = trim($url, '/');

		// En caso de ser /, la transformamos en vacia.
		if ($url === '/')
		{
			$url = '';
		}

		return self::route($url);
	}

	/**
	 * Realizamos una petición interna.
	 *
	 * Es una simulación que nos permite implementar HMVC que es muy util en
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
		// Reemplazamos // y \ por / para normalizar.
		$str = preg_replace('/\/+/', '/', $url);
		$str = preg_replace('//', '/', $url);

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
			die(self::route($url, TRUE));
		}
		else
		{
			// Iniciamos el buffer, esa para no mostrar nada por pantalla de esta
			// peticion.
			ob_start();

			// Realizamos la llamada.
			$rst = self::route($url, TRUE);

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
	 * Función encargada de enrutar una petición a controlador indicado.
	 * @param string $url URL de la petición.
	 * @param bool $throw Si en caso de error debe generar una excepción o mostrar un error.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	private static function route($url, $throw = FALSE)
	{
		// Obtenemos los segmentos de la URL
		$segmentos = explode('/', $url);

		// Verificamos si es un plugin.
		if (count($segmentos) >= 2)
		{
			if (strtolower($segmentos[0]) === 'plugin')
			{
				// Buscamos el plugin.
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

					//Instanciamos el controllador
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
						else
						{
							$cont = new $controller_name;
						}
					}

					// Obtenemos la cantidad de parámetros necesaria.
					$r_m = new ReflectionMethod($cont, 'action_'.$accion);
					$p_n = $r_m->getNumberOfRequiredParameters();

					// Expandemos el arreglo de parámetros con NULL si es necesario.
					while (count($args) < $p_n)
					{
						$args[] = NULL;
					}

					Request::add_stack(NULL, $controller, $accion, $args, $p_name);
					// No hubo problemas, llamamos.
					$rst = call_user_func_array(array(
							$cont,
							'action_'.$accion
					), $args);
					Request::pop_stack();
					return $rst;
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

		//Instanciamos el controllador
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
			else
			{
				$cont = new $controller_name;
			}
		}

		// Obtenemos la cantidad de parámetros necesaria.
		$r_m = new ReflectionMethod($cont, 'action_'.$accion);
		$p_n = $r_m->getNumberOfRequiredParameters();

		// Expandemos el arreglo de parámetros con NULL si es necesario.
		while (count($args) < $p_n)
		{
			$args[] = NULL;
		}

		Request::add_stack(NULL, $controller, $accion, $args, NULL);
		// No hubo problemas, llamamos.
		$rst = call_user_func_array(array(
				$cont,
				'action_'.$accion
		), $args);
		Request::pop_stack();
		return $rst;
	}
}
