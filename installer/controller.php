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
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador base del instalador.
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
		if (is_object($this->template) && ! Request::is_ajax() && error_get_last() === NULL)
		{
			PRODUCTION OR $this->template->assign('execution', get_readable_file_size(memory_get_peak_usage() - START_MEMORY));
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

		// Cache.
		$steps[] = array('caption' => 'Cache');

		// Imagenes.
		$steps[] = array('caption' => 'Imagenes');

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
}
