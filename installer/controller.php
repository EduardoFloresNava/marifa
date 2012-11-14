<?php
/**
 * controller.php is part of Marifa.
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
 * @package		Marifa\Installer
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador del instalador.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Installer
 */
class Installer_Controller {

	/**
	 * Paso actual.
	 * @var int
	 */
	protected $step = 0;

	/**
	 * Plantilla Base.
	 * @var RainTPL
	 */
	protected $template;

	/**
	 * Cargamos la plantilla base.
	 */
	public function __construct()
	{
		// Inicio la sessión.
		session_start();

		// Verifico el paso actual.
		if ( ! isset($_SESSION['step']) && $this->step !== 0)
		{
			$_SESSION['step'] = 0;
			Request::redirect('/installer/');
		}

		if ( ! isset($_SESSION['step']))
		{
			$_SESSION['step'] = 0;
		}

		// Cargamos la plantilla base.
		$this->template = View::factory('template');

		// Contenido inicial vacio.
		$this->template->assign('contenido', '');

		// Eventos flash.
		if (isset($_SESSION['flash_success']))
		{
			$this->template->assign('flash_success', get_flash('flash_success'));
		}

		if (isset($_SESSION['flash_error']))
		{
			$this->template->assign('flash_error', get_flash('flash_error'));
		}
	}

	/**
	 * Mostramos el template.
	 */
	public function __destruct()
	{
		if (is_object($this->template) && ! Request::is_ajax() && ! Error::$has_error)
		{
			$this->template->assign('execution', get_readable_file_size(memory_get_peak_usage() - START_MEMORY));
			$this->template->show();
		}
	}

	/**
	 * Listado de pasos.
	 * @param int $actual Número de paso actual. 0 Implica que estamos en el 1er paso.
	 */
	protected function steps($actual = 0)
	{
		$steps = array();

		// Inicial, pantalla de bienvenida.
		$steps[] = array('caption' => 'Inicio');

		// Requerimientos.
		$steps[] = array('caption' => 'Requerimientos');

		// Base de datos.
		$steps[] = array('caption' => 'BD');
		$steps[] = array('caption' => 'BD install');
		$steps[] = array('caption' => 'Configuraciones');

		// Cache.
		// $steps[] = array('caption' => 'Cache');

		// Imagenes.
		// $steps[] = array('caption' => 'Imagenes');

		// Final.
		$steps[] = array('caption' => 'Terminación');

		// Seteo los estados.
		foreach ($steps as $k => $v)
		{
			// Asigno estado a los terminados.
			if ($k < $actual)
			{
				$steps[$k]['estado'] = 1;
				continue;
			}

			// Asigno estado al activo.
			if ($k == $actual)
			{
				$steps[$k]['estado'] = 0;
				continue;
			}

			// Asigno estado a los pendientes.
			if ($k > $actual)
			{
				$steps[$k]['estado'] = -1;
				continue;
			}
		}

		return $steps;
	}

	/**
	 * Menu principal.
	 * @param string $selected Clave seleccionada.
	 * @return array
	 */
	protected function base_menu($selected = NULL)
	{
		$data = array();

		// Listado de elementos ONLINE.
		if (Usuario::is_login())
		{
			$data['inicio'] = array('link' => '/perfil/', 'caption' => 'Inicio', 'icon' => 'home', 'active' => FALSE);
		}

		// Listado de elemento OFFLINE.
		$data['posts'] = array('link' => '/', 'caption' => 'Posts', 'icon' => 'book', 'active' => FALSE);
		$data['fotos'] = array('link' => '/foto/', 'caption' => 'Fotos', 'icon' => 'picture', 'active' => FALSE);
		$data['tops'] = array('link' => '/tops/', 'caption' => 'TOPs', 'icon' => 'signal', 'active' => FALSE);

		// Listado elemento por permisos.
		if (Controller_Moderar_Home::permisos_acceso())
		{
			$data['moderar'] = array('link' => '/moderar/', 'caption' => 'Moderación', 'icon' => 'eye-open', 'active' => FALSE);
		}

		if (Controller_Admin_Home::permisos_acceso())
		{
			$data['admin'] = array('link' => '/admin/', 'caption' => 'Administración', 'icon' => 'certificate', 'active' => FALSE);
		}

		// Seleccionamos elemento.
		if ($selected !== NULL && isset($data[$selected]))
		{
			$data[$selected]['active'] = TRUE;
		}
		else
		{
			$data['posts']['active'] = TRUE;
		}

		return $data;
	}

	/**
	 * Portada del instalador.
	 */
	public function action_index()
	{
		// Cargo la vista.
		$vista = View::factory('index');

		// Seteo el menu.
		$this->template->assign('steps', $this->steps(0));

		// Seteo el paso como terminado.
		if ($_SESSION['step'] < 1)
		{
			$_SESSION['step'] = 1;
		}

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Requerimientos del sistema.
	 */
	public function action_requerimientos()
	{
		// Cargo la vista.
		$vista = View::factory('requerimientos');

		// Intento crear /plugin/plugins.php y /config/database.php para solucionar problema de verificación de permisos.
		@touch(APP_BASE.'/plugin/plugin.php');
		@touch(APP_BASE.'/config/database.php');

		// Listado de requerimientos.
		$reqs = array(
			array('titulo' => 'Versión PHP', 'requerido' => '> 5.2', 'actual' => phpversion(), 'estado' => version_compare(PHP_VERSION, '5.2.0', '>=')),
			array('titulo' => 'MCrypt', 'requerido' => 'ON', 'actual' => extension_loaded('mcrypt') ? 'ON' : 'OFF', 'estado' => extension_loaded('mcrypt')),
			'Base de Datos', // Separador.
			array('titulo' => 'MySQL', 'requerido' => 'ON', 'actual' => function_exists('mysql_connect') ? 'ON' : 'OFF', 'estado' => function_exists('mysql_connect'), 'opcional' => class_exists('pdo')),
			array('titulo' => 'PDO', 'requerido' => 'ON', 'actual' => class_exists('pdo') ? 'ON' : 'OFF', 'estado' => class_exists('pdo'), 'opcional' => function_exists('mysql_connect')),
			'Cache',
			array('titulo' => 'File', 'requerido' => 'ON', 'actual' => 'ON', 'estado' => is_writable(CACHE_PATH.DS.'file'), 'opcional' => TRUE),
			array('titulo' => 'APC', 'requerido' => 'ON', 'actual' => (extension_loaded('apc') && function_exists('apc_store')) ? 'ON' : 'OFF', 'estado' => (extension_loaded('apc') && function_exists('apc_store')), 'opcional' => TRUE),
			array('titulo' => 'Memcached', 'requerido' => 'ON', 'actual' => extension_loaded('memcached') ? 'ON' : 'OFF', 'estado' => extension_loaded('memcached'), 'opcional' => TRUE),
			'Sistema de actualizaciones',
			array('titulo' => 'CUrl', 'requerido' => 'ON', 'actual' => function_exists('curl_init') ? 'ON' : 'OFF', 'estado' => function_exists('curl_init'), 'opcional' => TRUE),
			array('titulo' => 'External open', 'requerido' => 'ON', 'actual' => ini_get('allow_url_fopen') ? 'ON' : 'OFF', 'estado' => ini_get('allow_url_fopen'), 'opcional' => TRUE),
			'Permisos escritura',
			array('titulo' => '/cache/raintpl/', 'requerido' => 'ON', 'actual' => is_writable(CACHE_PATH.DS.'raintpl') ? 'ON' : 'OFF', 'estado' => is_writable(CACHE_PATH.DS.'raintpl')),
			array('titulo' => '/log/', 'requerido' => 'ON', 'actual' => is_writable(APP_BASE.DS.'log') ? 'ON' : 'OFF', 'estado' => is_writable(APP_BASE.DS.'log')),
			array('titulo' => '/theme/theme.php', 'requerido' => 'ON', 'actual' => is_writable(APP_BASE.'/theme/theme.php') ? 'ON' : 'OFF', 'estado' => is_writable(APP_BASE.'/theme/theme.php')),
			array('titulo' => '/plugin/plugin.php', 'requerido' => 'ON', 'actual' => is_writable(APP_BASE.'/plugin/plugin.php') ? 'ON' : 'OFF', 'estado' => is_writable(APP_BASE.'/plugin/plugin.php')),
			array('titulo' => '/config/database.php', 'requerido' => 'ON', 'actual' => is_writable(APP_BASE.'/config/database.php') ? 'ON' : 'OFF', 'estado' => is_writable(APP_BASE.'/config/database.php')),
		);

		// Seteo el listado de requerimientos.
		$vista->assign('requerimientos', $reqs);

		// Verifico si puedo seguir.
		$is_ok = TRUE;
		foreach ($reqs as $v)
		{
			// Separador.
			if ( ! is_array($v))
			{
				continue;
			}

			// Verifico si no es correcto.
			if ( ! $v['estado'])
			{
				// Verifico si es opcional.
				if (isset($v['opcional']) && $v['opcional'])
				{
					continue;
				}

				// No se encuentra disponible.
				$is_ok = FALSE;
				break;
			}
		}

		// Seteo paso.
		if ($is_ok)
		{
			// Seteo el paso como terminado.
			if ($_SESSION['step'] < 2)
			{
				$_SESSION['step'] = 2;
			}
		}
		else
		{
			$_SESSION['step'] = 1;
		}

		// Paso estado a la vista.
		$vista->assign('can_continue', $is_ok);

		// Seteo el menu.
		$this->template->assign('steps', $this->steps(1));

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Configuración de la base de datos.
	 */
	public function action_bd()
	{
		// Verifico estado de la base de datos.
		if ($this->check_database())
		{
			// Seteo el paso como terminado.
			if ($_SESSION['step'] < 3)
			{
				$_SESSION['step'] = 3;
			}
			Request::redirect('/installer/bd_install/');
		}

		// Cargo la vista.
		$vista = View::factory('bd');

		// Listado de drivers.
		$drivers = array();

		if (function_exists('mysql_connect'))
		{
			$drivers['mysql'] = 'MySQL';
		}

		if (class_exists('pdo'))
		{
			$drivers['pdo'] = 'PDO';
		}

		$vista->assign('drivers', $drivers);

		// Información por defecto.
		$vista->assign('driver', isset($drivers['mysql']) ? 'mysql' : 'pdo');
		$vista->assign('error_driver', FALSE);
		$vista->assign('host', '');
		$vista->assign('error_host', FALSE);
		$vista->assign('db_name', '');
		$vista->assign('error_db_name', FALSE);
		$vista->assign('usuario', '');
		$vista->assign('error_usuario', FALSE);
		$vista->assign('password', '');
		$vista->assign('error_password', FALSE);

		if (Request::method() == 'POST')
		{
			// Obtengo los datos.
			foreach (array('driver', 'host', 'db_name', 'usuario', 'password') as $v)
			{
				$$v = isset($_POST[$v]) ? trim($_POST[$v]) : NULL;
				$vista->assign($v, $$v);
			}

			$error = FALSE;

			// Verifico driver.
			if ( ! isset($drivers[$driver]))
			{
				$error = TRUE;
				$vista->assign('error_driver', 'El driver seleccionado es incorrecto.');
			}

			// Verifico lo datos.
			if ($driver == 'mysql')
			{
				if (empty($host))
				{
					$error = TRUE;
					$vista->assign('error_host', 'Debes ingresar un host válido.');
				}

				if (empty($db_name))
				{
					$error = TRUE;
					$vista->assign('error_db_name', 'Debes ingresar una base de datos válida.');
				}
			}

			if ( ! $error)
			{
				if ($driver == 'pdo')
				{
					// Genero arreglo de configuraciones.
					$config = array(
						'type' => $driver,
						'dsn' => $host,
						'username' => $usuario,
						'password' => $password
					);
				}
				else
				{
					// Genero arreglo de configuraciones.
					$config = array(
						'type' => $driver,
						'host' => $host,
						'db_name' => $db_name,
						'username' => $usuario,
						'password' => $password
					);
				}

				//FIXME: Puede generar una falla de inyección de código PHP.
				//FIXME: Verificar presencia de ' y escaparlos.

				// Genero template.
				$tmp = '<?php defined(\'APP_BASE\') || die(\'No direct access allowed.\');'.PHP_EOL.'return '.$this->value_to_php($config).';';

				// Guardo la configuración.
				file_put_contents(CONFIG_PATH.DS.'database.php', $tmp);

				// Intento conectar.
				if ($this->check_database())
				{
					// Seteo el paso como terminado.
					if ($_SESSION['step'] < 3)
					{
						$_SESSION['step'] = 3;
					}
					Request::redirect('/installer/bd_install/');
				}
				else
				{
					// Borro archivo de configuración.
					if (file_exists(CONFIG_PATH.DS.'database.php'))
					{
						unlink(CONFIG_PATH.DS.'database.php');
					}

					// Informo resultado.
					$vista->assign('error', 'No se pudo conectar a la base de datos. Verifique los datos ingresados.');
				}
			}
		}

		// Seteo el menu.
		$this->template->assign('steps', $this->steps(2));

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Verificamos si la conección es correcta.
	 * @return bool
	 */
	private function check_database()
	{
		// Verifico archivo de configuración.
		if ( ! file_exists(CONFIG_PATH.DS.'database.php'))
		{
			return FALSE;
		}

		// Verifico los datos de conección.
		try {
			Database::get_instance(TRUE);
			return TRUE;
		}
		catch (Database_Exception $e)
		{
			return FALSE;
		}
	}

	/**
	 * Obtenemos la representación PHP de una variable.
	 * @param mixed $value Variable a representar.
	 * @return string
	 */
	private function value_to_php($value)
	{
		if ($value === TRUE || $value === FALSE)
		{
			return $value ? 'TRUE' : 'FALSE';
		}
		elseif (is_int($value) || is_float($value))
		{
			return "$value";
		}
		elseif (is_string($value))
		{
			return "'".str_replace("'", "\\'", $value)."'";
		}
		elseif (is_array($value))
		{
			$rst = array();
			foreach ($value as $k => $v)
			{
				if (is_int($k))
				{
					$rst[] = $this->value_to_php($v);
				}
				else
				{
					$rst[] = "'$k' => ".$this->value_to_php($v);
				}
			}

			return 'array('.implode(', ', $rst).')';
		}
		return 'NULL';
	}

	/**
	 * Instalamos la base de datos.
	 */
	public function action_bd_install()
	{
		// Verifico estado de la base de datos.
		if ( ! $this->check_database())
		{
			// Vuelvo para atrás.
			$_SESSION['step'] = 2;
			Request::redirect('/installer/bd');
		}

		// Cargo la vista.
		$vista = View::factory('bd_installer');

		// Cargo las tablas.
		$database_list = require(APP_BASE.DS.'installer'.DS.'database.'.FILE_EXT);

		// Armo listado para las vistas.
		$lst = array();
		foreach ($database_list as $k => $v)
		{
			$lst[$k] = array('titulo' => $v[0]);
		}

		// Ejecuto el listado de consultas.
		if (Request::method() == 'POST')
		{
			// Cargo la base de datos.
			$db = Database::get_instance();

			// Error global. Permite saber si todo fue correcto para continuar.
			$error_global = FALSE;

			// Ejecuto las consultas.
			foreach ($database_list as $k => $v)
			{
				// Ejecuto las consultas.
				$error = FALSE;
				foreach ($v[1] as $query)
				{
					try {
						switch ($query[0])
						{
							case 'INSERT':
								list(, $c) = $db->insert($query[1], isset($query[2]) ? $query[2] : NULL);
								if ($c <= 0)
								{
									throw new Exception("El resultado de la consulta: '{$query[1]}' es incorrecto.");
								}
								break;
							case 'DELETE':
								if ($db->delete($query[1], isset($query[2]) ? $query[2] : NULL) === FALSE)
								{
									throw new Exception("El resultado de la consulta: '{$query[1]}' es incorrecto.");
								}
								break;
							case 'UPDATE':
							case 'ALTER':
								if ($db->update($query[1], isset($query[2]) ? $query[2] : NULL) === FALSE)
								{
									throw new Exception("El resultado de la consulta: '{$query[1]}' es incorrecto.");
								}
								break;
							case 'QUERY':
								if ($db->query($query[1], isset($query[2]) ? $query[2] : NULL) === FALSE)
								{
									throw new Exception("El resultado de la consulta: {$query[1]}' es incorrecto.");
								}
								break;
						}
					}
					catch (Exception $e)
					{
						if (isset($query[3]) && isset($query[3]['error_no']))
						{
							if ($query[3]['error_no'] == $e->getCode())
							{
								continue;
							}
						}

						$error = '['.$e->getCode().'] '.$e->getMessage();
						break;
					}
				}

				// Agrego el resultado.
				if ($error === FALSE)
				{
					$lst[$k]['success'] = TRUE;
				}
				else
				{
					$lst[$k]['error'] = $error;
					$error_global = TRUE;
				}
			}

			if ( ! $error_global)
			{
				// Seteo el paso como terminado.
				if ($_SESSION['step'] < 4)
				{
					$_SESSION['step'] = 4;
				}
				$vista->assign('execute', TRUE);
			}
		}

		// Paso listado resultado.
		$vista->assign('consultas', $lst);

		// Seteo el menu.
		$this->template->assign('steps', $this->steps(3));

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Configuramos parámetros del sistema.
	 */
	public function action_configuracion()
	{
		// Cargo la vista.
		$vista = View::factory('configuracion');

		// Seteo el menu.
		$this->template->assign('steps', $this->steps(4));

		// Cargamos las configuraciones.
		$model_configuracion = new Model_Configuracion;

		// Datos por defecto.
		foreach (array('nombre', 'descripcion', 'usuario', 'email', 'password', 'cpassword', 'bd_password') as $v)
		{
			$vista->assign($v, '');
			$vista->assign('error_'.$v, FALSE);
		}

		if (Request::method() == 'POST')
		{
			// Cargo los valores.
			foreach (array('nombre', 'descripcion', 'usuario', 'email', 'password', 'cpassword', 'bd_password') as $v)
			{
				$$v = isset($_POST[$v]) ? trim($_POST[$v]) : '';
			}

			// Limpio los valores.
			$nombre = preg_replace('/\s+/', ' ', $_POST['nombre']);
			$descripcion = preg_replace('/\s+/', ' ', $_POST['descripcion']);

			// Seteo nuevos valores a las vistas.
			foreach (array('nombre', 'descripcion', 'usuario', 'email', 'password', 'cpassword') as $v)
			{
				$vista->assign($v, $$v);
			}
			
			$error = FALSE;
			
			// Verifico la clave de la base de datos.
			$cfg = configuracion_obtener(CONFIG_PATH.DS.'database.php');
			if ($bd_password !== $cfg['password'])
			{
				$error = TRUE;
				$vista->assign('error_bd_password', 'La contraseña de la base de datos es incorrecta.');
			}

			// Verifico el nombre.
			if ( ! preg_match('/^[a-z0-9áéíóúñ !\-_\.]{2,20}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre debe tener entre 2 y 20 caracteres. Pueden ser letras, números, espacios, !, -, _, . y \\');
			}

			// Verifico el contenido.
			if ( ! preg_match('/^[a-z0-9áéíóúñ !\-_\.]{5,30}$/iD', $descripcion))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', 'La descripción debe tener entre 5 y 30 caracteres. Pueden ser letras, números, espacios, !, -, _, . y \\');
			}

			// Verifico usuario.
			if ( ! preg_match('/^[a-zA-Z0-9]{4,16}$/D', $usuario))
			{
				$error = TRUE;
				$vista->assign('error_usuario', 'El usuario debe tener entren 4 y 16 caracteres alphanumericos.');
			}

			// Verifico email.
			if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $email))
			{
				$error = TRUE;
				$vista->assign('error_email', 'El E-Mail ingresado no es válido.');
			}

			// Verifico contraseña.
			if ( ! isset($password{6}) || isset($password{21}))
			{
				$error = TRUE;
				$vista->assign('error_password', 'La contraseña debe tener entre 6 y 20 caracteres.');
			}

			// Verifico contraseña repetida válida.
			if ($password !== $cpassword)
			{
				$error = TRUE;
				$vista->assign('error_cpassword', 'Las contraseñas ingresadas no coinciden.');
			}

			// Actualizo los valores.
			if ( ! $error)
			{
				// Actualizo las configuraciones.
				$model_configuracion->nombre = $nombre;
				$model_configuracion->descripcion = $descripcion;

				// Cargo modelo de usuarios.
				$model_usuario = new Model_Usuario;

				// Verifico no exista la cuenta.
				if ($model_usuario->exists_nick($usuario))
				{
					// Actualizo los datos.
					$model_usuario->load_by_nick($usuario);
					$model_usuario->actualizar_contrasena($password);
					$model_usuario->actualizar_campo('rango', 1);
					$model_usuario->actualizar_campo('estado', Model_Usuario::ESTADO_ACTIVA);
					$model_usuario->actualizar_campo('email', $email);
				}
				elseif ($model_usuario->exists_email($email))
				{
					// Actualizo los datos.
					$model_usuario->load_by_email($email);
					$model_usuario->actualizar_contrasena($password);
					$model_usuario->actualizar_campo('rango', 1);
					$model_usuario->actualizar_campo('estado', Model_Usuario::ESTADO_ACTIVA);
					$model_usuario->actualizar_campo('nick', $usuario);
				}
				else
				{
					// Creo la cuenta.
					$model_usuario->register($usuario, $email, $password, 1);
					$model_usuario->load_by_nick($usuario);
					$model_usuario->actualizar_campo('estado', Model_Usuario::ESTADO_ACTIVA);
				}

				// Seteo el paso como terminado.
				if ($_SESSION['step'] < 4)
				{
					$_SESSION['step'] = 4;
				}

				// Redirecciono al siguiente.
				Request::redirect('/installer/finalizacion/');
			}
		}

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Configuración de la cache del sitio.
	 */
	public function action_finalizacion()
	{
		// Cargo la vista.
		$vista = View::factory('finalizacion');

		// Seteo el paso como terminado.
		if ($_SESSION['step'] < 6)
		{
			$_SESSION['step'] = 6;
		}

		// Seteo el menu.
		$this->template->assign('steps', $this->steps(6));

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}
}
