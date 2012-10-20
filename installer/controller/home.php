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
 * Controlador de la portada del instalador.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Installer
 */
class Installer_Controller_Home extends Installer_Controller {

	/**
	 * Constructor de la clase.
	 * Seteo paso según acción.
	 */
	public function __construct()
	{
		$request = Request::current();
		if ($request['action'] == 'index')
		{
			$this->step = 0;
		}
		elseif ($request['action'] == 'requerimientos')
		{
			$this->step = 1;
		}
		parent::__construct();
	}

	public function action_index()
	{
		// Cargo la vista.
		$vista = View::factory('home/home');

		// Seteo el menu.
		$this->template->assign('steps', parent::steps(0));

		// Seteo el paso como terminado.
			if ($_SESSION['step'] < 1)
			{
				$_SESSION['step'] = 1;
			}

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	public function action_requerimientos()
	{
		// Cargo la vista.
		$vista = View::factory('home/requerimientos');

		// Listado de requerimientos.
		$reqs = array(
			array('titulo' => 'Versión PHP', 'requerido' => '> 5.2', 'actual' => phpversion(), 'estado' => version_compare(PHP_VERSION, '5.2.0', '>=')),
			array('titulo' => 'MCrypt', 'requerido' => 'ON', 'actual' => extension_loaded('mcrypt') ? 'ON' : 'OFF', 'estado' => extension_loaded('mcrypt')),
			'Base de Datos', // Separador.
			array('titulo' => 'MySQL', 'requerido' => 'ON', 'actual' => function_exists('mysql_connect') ? 'ON' : 'OFF', 'estado' => function_exists('mysql_connect'), 'opcional' => function_exists('mysqli_connect') || class_exists('pdo')),
			array('titulo' => 'MySQLi', 'requerido' => 'ON', 'actual' => function_exists('mysqli_connect') ? 'ON' : 'OFF', 'estado' => function_exists('mysqli_connect'), 'opcional' => function_exists('mysql_connect') || class_exists('pdo')),
			array('titulo' => 'PDO', 'requerido' => 'ON', 'actual' => class_exists('pdo') ? 'ON' : 'OFF', 'estado' => class_exists('pdo'), 'opcional' => function_exists('mysql_connect') || function_exists('mysqli_connect')),
			'Cache',
			array('titulo' => 'File', 'requerido' => 'ON', 'actual' => 'ON', 'estado' => TRUE, 'opcional' => TRUE),
			array('titulo' => 'APC', 'requerido' => 'ON', 'actual' => (extension_loaded('apc') && function_exists('apc_store')) ? 'ON' : 'OFF', 'estado' => (extension_loaded('apc') && function_exists('apc_store')), 'opcional' => TRUE),
			array('titulo' => 'Memcached', 'requerido' => 'ON', 'actual' => extension_loaded('memcached') ? 'ON' : 'OFF', 'estado' => extension_loaded('memcached'), 'opcional' => TRUE),
			'Sistema de actualizaciones',
			array('titulo' => 'CUrl', 'requerido' => 'ON', 'actual' => function_exists('curl_init') ? 'ON' : 'OFF', 'estado' => function_exists('curl_init'), 'opcional' => TRUE),
			array('titulo' => 'External open', 'requerido' => 'ON', 'actual' => ini_get('allow_url_fopen') ? 'ON' : 'OFF', 'estado' => ini_get('allow_url_fopen'), 'opcional' => TRUE),
		);

		//TODO: verificar cache FILE.

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
		$this->template->assign('steps', parent::steps(1));

		// Seteo la vista.
		$this->template->assign('contenido', $vista->parse());
	}

}
