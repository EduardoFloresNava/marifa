<?php
/**
 * home.php is part of Marifa.
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
 * Controlador para la configuración de la base de datos.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Installer
 */
class Installer_Controller_Bd extends Installer_Controller {

	/**
	 * Constructor de la clase.
	 * Seteo paso según acción.
	 */
	public function __construct()
	{
		$request = Request::current();
		if ($request['action'] == 'index')
		{
			$this->step = 2;
		}
		elseif ($request['action'] == 'install')
		{
			$this->step = 3;
		}
		parent::__construct();
	}

	public function action_index()
	{
		// Verifico estado de la base de datos.
		if ($this->check_database())
		{
			// Seteo el paso como terminado.
			if ($_SESSION['step'] < 3)
			{
				$_SESSION['step'] = 3;
			}
			Request::redirect('/installer/bd/install/');
		}

		// Cargo la vista.
		$vista = View::factory('bd/home');

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
		$vista->assign('driver', isset($drivers['mysql']) ? 'mysql' : (isset($drivers['mysqli']) ? 'mysqli' : 'pdo'));
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
				if($this->check_database())
				{
					// Seteo el paso como terminado.
					if ($_SESSION['step'] < 3)
					{
						$_SESSION['step'] = 3;
					}
					Request::redirect('/installer/bd/install/');
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
		$this->template->assign('steps', parent::steps(2));

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
			return "'$value'";
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

	public function action_install()
	{
		// Verifico estado de la base de datos.
		if ( ! $this->check_database())
		{
			// Vuelvo para atrás.
			$_SESSION['step'] = 2;
			Request::redirect('/installer/bd');
		}

		// Cargo la vista.
		$vista = View::factory('bd/installer');

		// Seteo el menu.
		$this->template->assign('steps', parent::steps(3));

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

}
