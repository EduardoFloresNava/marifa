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
	 * Plantilla Base.
	 * @var RainTPL
	 */
	protected $template;

	/**
	 * Listado de pasos.
	 * El formato es 'method' => array('titulo' => 'TITULO EN LA BARRA', 'posicion' => POSICION)
	 * donde titulo será un string con el nombre que se muestra en la barra de progreso
	 * y posicion un entero que especifica el número de paso (empezando en 1).
	 * method debe ser el nombre sin el prefijo action_
	 * @var array
	 */
	protected $steps = array(
		'index'          => array('titulo' => 'Inicio',          'posicion' => 1), // Portada.
		'requerimientos' => array('titulo' => 'Requerimientos',  'posicion' => 2), // Requerimientos del entorno.
		'datos_entorno'  => array('titulo' => 'Datos entorno',   'posicion' => 3), // Configuraciones esenciales del sistema referidas al entorno.
		'bd'             => array('titulo' => 'BD',              'posicion' => 4), // Configuramos la conexión a la base de datos.
		'bd_install'     => array('titulo' => 'BD install',      'posicion' => 5), // Instalamos la Base de datos.
		'importar'       => array('titulo' => 'Importar',        'posicion' => 6), // Importamos datos de otros lados.
		'configuracion'  => array('titulo' => 'Configuraciones', 'posicion' => 7), // Configuramos el sistema.
		'finalizacion'   => array('titulo' => 'Terminación',     'posicion' => 8)  // Terminada la instalación.
	);

	/**
	 * Cargamos la plantilla base.
	 */
	public function before()
	{
		// Inicio la clase para manejar los pasos del instalador.
		Installer_Step::get_instance()->setup($this->steps);

		// Cargamos la plantilla base.
		$this->template = View::factory('template');

		// Contenido inicial vacío.
		$this->template->assign('contenido', '');
	}

	/**
	 * Mostramos la plantilla.
	 */
	public function after()
	{
		// Asigno el menú.
		$this->template->assign('steps', Installer_Step::get_instance()->listado());

		// Eventos flash.
		foreach (array('flash_success', 'flash_info', 'flash_error') as $k)
		{
			if (isset($_SESSION[$k]))
			{
				$this->template->assign($k, get_flash($k));
			}
		}

		// Muestro la plantilla.
		if (is_object($this->template) && ! Request::is_ajax())
		{
			$this->template->assign('execution', get_readable_file_size(memory_get_peak_usage() - START_MEMORY));
			$this->template->show();
		}
	}

	/**
	 * Portada del instalador.
	 * Simplemente da la bienvenida al usuario.
	 */
	public function action_index()
	{
		// Cargo la vista.
		$vista = View::factory('index');

		// Marco el paso como terminado.
		Installer_Step::get_instance()->terminado();

		// Asigno la vista.
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
			array('titulo' => 'MySQL', 'requerido' => 'ON', 'actual' => function_exists('mysql_connect') ? 'ON' : 'OFF', 'estado' => function_exists('mysql_connect'), 'opcional' => class_exists('pdo') || function_exists('mysqli_connect')),
			array('titulo' => 'MySQLi', 'requerido' => 'ON', 'actual' => function_exists('mysqli_connect') ? 'ON' : 'OFF', 'estado' => function_exists('mysqli_connect'), 'opcional' => class_exists('pdo') || function_exists('mysql_connect')),
			array('titulo' => 'PDO', 'requerido' => 'ON', 'actual' => class_exists('pdo') ? 'ON' : 'OFF', 'estado' => class_exists('pdo'), 'opcional' => function_exists('mysql_connect')),
			'Cache',
			array('titulo' => 'File', 'requerido' => 'ON', 'actual' => 'ON', 'estado' => is_writable(CACHE_PATH.DS.'file'), 'opcional' => TRUE),
			array('titulo' => 'APC', 'requerido' => 'ON', 'actual' => (extension_loaded('apc') && function_exists('apc_store')) ? 'ON' : 'OFF', 'estado' => (extension_loaded('apc') && function_exists('apc_store')), 'opcional' => TRUE),
			array('titulo' => 'Memcached', 'requerido' => 'ON', 'actual' => extension_loaded('memcached') ? 'ON' : 'OFF', 'estado' => extension_loaded('memcached'), 'opcional' => TRUE),
			'Procesamiento de imagenes',
			array('titulo' => 'GD', 'requerido' => 'ON', 'actual' => function_exists('gd_info') ? 'ON' : 'OFF', 'estado' => function_exists('gd_info')),
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

		// Asigno el listado de requerimientos.
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

		// Marco el paso como terminado.
		if ($is_ok)
		{
			Installer_Step::get_instance()->terminado();
		}

		// Paso estado a la vista.
		$vista->assign('can_continue', $is_ok);

		// Asigno la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Configuraciones base del sistema relacionadas al entorno.
	 */
	public function action_datos_entorno()
	{
		// Cargo configuración.
		$config = new Configuracion(CONFIG_PATH.DS.'marifa.php');

		// Asigno valores por defecto.
		if ( ! isset($config['cookie_secret']))
		{
			$config['cookie_secret'] = Texto::random_string(20);
		}

		if ( ! isset($config['language']))
		{
			$config['language'] = 'esp';
		}

		if ( ! isset($config['default_timezone']))
		{
			$config['default_timezone'] = 'UTC';
		}

		// Cargo listado de zonas horarias.
		$tz_list = timezone_identifiers_list();

		// Cargo la vista.
		$vista = View::factory('datos_entorno');

		// Asigno valores por defecto.
		$vista->assign('config', $config->as_array());
		$vista->assign('tz_list', $tz_list);

		$vista->assign('error_cookie_secret', FALSE);
		$vista->assign('error_language', FALSE);
		$vista->assign('error_default_timezone', FALSE);

		// Proceso lo enviado.
		if (Request::method() == 'POST')
		{
			// Cargo valores enviados.
			$cookie_secret = arr_get($_POST, 'cookie_secret', NULL);
			$language = arr_get($_POST, 'language', NULL);
			$default_timezone = arr_get($_POST, 'default_timezone', NULL);

			// Asigno actuales.
			$config['cookie_secret'] = $cookie_secret;
			$config['language'] = $language;
			$config['default_timezone'] = arr_get($tz_list, $default_timezone, 'UTC');
			$vista->assign('config', $config->as_array());

			// Marco sin error.
			$error = FALSE;

			// Verifico valores.
			if (empty($cookie_secret) || strlen($cookie_secret) < 5)
			{
				$error = TRUE;
				$vista->assign('error_cookie_secret', 'La clave de las cookies debe tener al menos 5 caracteres. Se recomiendan 20.');
			}

			if ( ! preg_match('/^[a-z]{3}$/', $language))
			{
				$error = TRUE;
				$vista->assign('error_language', 'El lenguaje es incorrecto. Debe usar un código de 3 letras minúsculas.');
			}

			if ( ! in_array($default_timezone, array_keys($tz_list)))
			{
				$erro = TRUE;
				$vista->assign('error_default_timezone', 'La zona horaria no es válida.');
			}

			if ( ! $error)
			{
				// Guardo.
				$config->save();

				// Marco el paso como terminado.
				Installer_Step::get_instance()->terminado();

				// Voy al siguiente.
				Installer_Step::get_instance()->ir_al_paso();
			}
		}

		// Asigno la vista a la plantilla base.
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
			// Marco como terminado.
			Installer_Step::get_instance()->terminado();

			// Voy al siguiente.
			Request::redirect(Installer_Step::url_siguiente('bd'));
		}

		// Cargo la vista.
		$vista = View::factory('bd');

		// Listado de drivers.
		$drivers = array();

		if (function_exists('mysql_connect'))
		{
			$drivers['mysql'] = 'MySQL';
		}

		if (function_exists('mysqli_connect'))
		{
			$drivers['mysqli'] = 'MySQLi';
		}

		if (class_exists('pdo'))
		{
			$drivers['pdo'] = 'PDO';
		}

		$vista->assign('drivers', $drivers);

		// Información por defecto.
		$vista->assign('driver', isset($drivers['mysqli']) ? 'mysqli' : (isset($drivers['pdo']) ? 'pdo' : 'mysql'));
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
			if ($driver == 'mysql' || $driver == 'mysqli')
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
						'type'     => $driver,
						'dsn'      => "mysql:dbname=$db_name;host=$host;charset=utf-8",
						'username' => $usuario,
						'password' => $password,
						'options'  => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
					);
				}
				else
				{
					// Genero arreglo de configuraciones.
					$config = array(
						'type'     => $driver,
						'host'     => $host,
						'db_name'  => $db_name,
						'username' => $usuario,
						'password' => $password,
						'utf8'     => TRUE
					);
				}

				// Verifico conexión.
				if (Database::test($config))
				{
					// Genero archivo de configuración.
					$o_cfg = Configuracion::factory(CONFIG_PATH.DS.'database.php', $config)->save();

					// Marco el paso como terminado.
					Installer_Step::get_instance()->terminado();

					// Voy al siguiente.
					Installer_Step::get_instance()->ir_al_paso();
				}
				else
				{
					// Borro archivo de configuración.
					if (file_exists(CONFIG_PATH.DS.'database.php'))
					{
						unlink(CONFIG_PATH.DS.'database.php');
					}

					// Informo resultado.
					add_flash_message(FLASH_ERROR, 'No se pudo conectar a la base de datos. Verifique los datos ingresados.');
				}
			}
		}

		// Asigno la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Verificamos si la conexión es correcta.
	 * @return bool
	 */
	private function check_database($config = NULL)
	{
		// Verifico archivo de configuración.
		if ($config === NULL && ! file_exists(CONFIG_PATH.DS.'database.php'))
		{
			return FALSE;
		}

		// Cargo datos de donde verificar.
		$datos = $config === NULL ? configuracion_obtener(CONFIG_PATH.DS.'database.php') : $config;

		// Intento la conexión.
		return Database::test($datos);
	}

	/**
	 * Instalamos la base de datos.
	 */
	public function action_bd_install()
	{
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

		// Obtengo versión actual.
		try {
			$version_actual = Database::get_instance()->query('SELECT valor FROM configuracion WHERE clave = \'version_actual\';')->get_var();
		}
		catch (Database_Exception $e)
		{
			$version_actual = VERSION;
		}

		// Obtengo el listado de actualizaciones.
		$aux = scandir(APP_BASE.DS.'installer'.DS.'update'.DS);
		foreach ($aux as $u)
		{
			// Verifico sea un archivo válido.
			if (substr($u, (-1) * strlen(FILE_EXT)) !== FILE_EXT)
			{
				continue;
			}

			// Verifico si debe ser importada.
			if (version_compare($version_actual, substr($u, 0, (-1) * (strlen(FILE_EXT) + 1))) < 0)
			{
				// Agrego el título.
				$lst[] = substr($u, 0, (-1) * (strlen(FILE_EXT) + 1));

				// Listado de títulos.
				$upd_aux_list = require(APP_BASE.DS.'installer'.DS.'update'.DS.$u);

				foreach ($upd_aux_list as $k => $item)
				{
					$lst[$u.$k] = array('titulo' => $item[0]);
					$database_list[$u.$k] = $item;
				}
			}
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
				// Marco el paso como terminado.
				Installer_Step::get_instance()->terminado();

				$vista->assign('execute', TRUE);
			}
		}

		// Paso listado resultado.
		$vista->assign('consultas', $lst);

		// Asigno la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Configuramos parámetros del sistema.
	 */
	public function action_configuracion()
	{
		// Cargo la vista.
		$vista = View::factory('configuracion');

		// Cargamos las configuraciones.
		$model_configuracion = Model_Configuracion::get_instance();

		// Datos por defecto.
		$vista->assign('nombre', $model_configuracion->get('nombre', ''));
		$vista->assign('error_nombre', FALSE);
		$vista->assign('descripcion', $model_configuracion->get('descripcion', ''));
		$vista->assign('error_descripcion', FALSE);
		$vista->assign('usuario', '');
		$vista->assign('error_usuario', FALSE);
		$vista->assign('email', '');
		$vista->assign('error_email', FALSE);
		$vista->assign('password', '');
		$vista->assign('error_password', FALSE);
		$vista->assign('cpassword', '');
		$vista->assign('error_cpassword', FALSE);
		$vista->assign('bd_password', '');
		$vista->assign('error_bd_password', FALSE);

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

			// Asigno nuevos valores a las vistas.
			foreach (array('nombre', 'descripcion', 'usuario', 'email', 'password', 'cpassword') as $v)
			{
				$vista->assign($v, $$v);
			}

			$error = FALSE;

			// Verifico la clave de la base de datos.
			if ($bd_password !== Configuracion::factory(CONFIG_PATH.DS.'database.php')->password)
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

			// Verifico no exista correo.
			if ( ! $error)
			{
				// Cargo el modelo.
				$model_usuario = new Model_Usuario;

				// Verifico tenga ese email.
				$model_usuario->load_by_nick($usuario);
				if ($model_usuario->existe() && $model_usuario->email !== $email)
				{
					if ($model_usuario->existe(array('email' => $email)))
					{
						$error = TRUE;
						$vista->assign('error_email', 'Ya existe un usuario con ese correo, introduce otro.');
					}
				}
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

				// Marco el paso como terminado.
				Installer_Step::get_instance()->terminado();

				// Envío al siguiente paso.
				Installer_Step::get_instance()->ir_al_paso();
			}
		}

		// Asigno la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Configuración de la cache del sitio.
	 */
	public function action_finalizacion()
	{
		// Cargo la vista.
		$vista = View::factory('finalizacion');

		// Marco el paso como terminado.
		Installer_Step::get_instance()->terminado();

		// Asigno la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Acción de importar datos de otros sistemas.
	 */
	public function action_importar()
	{
		// Cargamos la vista.
		$vista = View::factory('importar');

		// Listado de drivers.
		$drivers = array();

		if (function_exists('mysql_connect'))
		{
			$drivers['mysql'] = 'MySQL';
		}

		if (function_exists('mysqli_connect'))
		{
			$drivers['mysqli'] = 'MySQLi';
		}

		if (class_exists('pdo'))
		{
			$drivers['pdo'] = 'PDO';
		}

		$vista->assign('drivers', $drivers);

		// Listado de importadores.
		$importadores = array('phpost');
		$vista->assign('importadores', $importadores);

		// Información por defecto.
		$vista->assign('importador', 'phpost');
		$vista->assign('error_importador', FALSE);
		$vista->assign('driver', isset($drivers['mysqli']) ? 'mysqli' : (isset($drivers['pdo']) ? 'pdo' : 'mysql'));
		$vista->assign('error_driver', FALSE);
		$vista->assign('host', '');
		$vista->assign('error_host', FALSE);
		$vista->assign('db_name', '');
		$vista->assign('error_db_name', FALSE);
		$vista->assign('usuario', '');
		$vista->assign('error_usuario', FALSE);
		$vista->assign('password', '');
		$vista->assign('error_password', FALSE);

		// Acciones de la importación.
		if (Request::method() == 'POST')
		{
			// Verifico acción.
			$method = isset($_POST['method']) ? $_POST['method'] : 'skip';

			if ($method == 'skip')
			{
				// Omitimos la importación.
				Installer_Step::get_instance()->terminado();
				Installer_Step::get_instance()->ir_al_paso();
			}
			else
			{
				// Importamos datos.

				// Obtengo los datos.
				foreach (array('driver', 'host', 'db_name', 'usuario', 'password', 'importador') as $v)
				{
					$$v = isset($_POST[$v]) ? trim($_POST[$v]) : NULL;
					$vista->assign($v, $$v);
				}

				$error = FALSE;

				// Verifico importador.
				if ( ! in_array($importador, $importadores))
				{
					$error = TRUE;
					$vista->assign('error_importador', 'El importador seleccionado es incorrecto.');
				}

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

					$db_instance = Database::test($config, TRUE);

					if ($db_instance === FALSE)
					{
						$error = TRUE;
						add_flash_message(FLASH_ERROR, 'Los datos ingresados para la conexión a la base de datos son incorrectos');
					}
				}

				// Realizo la importación.
				if ( ! $error)
				{
					// Cargo el importador.
					$importador = 'Installer_Importador_'.ucfirst(strtolower($importador));
					$o_importador = new $importador(Database::get_instance(), $db_instance);

					// Realizo la tarea.
					try {
						$o_importador->importar();
						$error = FALSE;
					}
					catch (Exception $e)
					{
						add_flash_message(FLASH_ERROR, 'Error al importar: \''.$e->getMessage().'\'');
						$error = TRUE;
					}
				}

				// Resultado de la importación.
				if ( ! $error)
				{
					add_flash_message(FLASH_SUCCESS, 'Se han importado correctamente los datos.');
					Installer_Step::get_instance()->terminado();

					$vista->assign('terminado', TRUE);
				}
			}
		}

		// Asigno la vista.
		$this->template->assign('contenido', $vista->parse());
	}
}
