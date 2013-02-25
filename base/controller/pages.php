<?php
/**
 * pages.php is part of Marifa.
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
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador con páginas estáticas como Protocolo, DMCA, etc.
 *
 * @since      Versión 0.2
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Pages extends Controller {

	/**
	 * Protocolo.
	 */
	public function action_protocolo()
	{
		// Menu principal.
		$this->template->assign('master_bar', parent::base_menu('inicio'));

		// Asignamos la vista.
		$view = View::factory('/pages/protocolo');

		// Nombre del sitio.
		$model_config = new Model_Configuracion;
		$view->assign('nombre', $model_config->get('nombre', 'Marifa'));

		// Compilamos la vista.
		$this->template->assign('contenido', $view->parse());

		// Seteamos título.
		$this->template->assign('title', __('Protocolo', FALSE));
	}

	/**
	 * Terminos y condiciones.
	 */
	public function action_tyc()
	{
		// Menu principal.
		$this->template->assign('master_bar', parent::base_menu('inicio'));

		// Asignamos la vista.
		$view = View::factory('/pages/tyc');

		// Nombre del sitio.
		$model_config = new Model_Configuracion;
		$view->assign('nombre', $model_config->get('nombre', 'Marifa'));

		$this->template->assign('contenido', $view->parse());

		// Seteamos título.
		$this->template->assign('title', __('Términos y condiciones', FALSE));
	}

	/**
	 * Privacidad de datos.
	 */
	public function action_privacidad()
	{
		// Menu principal.
		$this->template->assign('master_bar', parent::base_menu('inicio'));

		// Asignamos la vista.
		$view = View::factory('/pages/privacidad');

		// Nombre del sitio.
		$model_config = new Model_Configuracion;
		$view->assign('nombre', $model_config->get('nombre', 'Marifa'));

		$this->template->assign('contenido', $view->parse());

		// Seteamos título.
		$this->template->assign('title', __('Privacidad de datos', FALSE));
	}

	/**
	 * DMCA
	 */
	public function action_dmca()
	{
		// Menu principal.
		$this->template->assign('master_bar', parent::base_menu('inicio'));

		// Asignamos la vista.
		$view = View::factory('/pages/dmca');

		// Nombre del sitio.
		$model_config = new Model_Configuracion;
		$view->assign('nombre', $model_config->get('nombre', 'Marifa'));

		$this->template->assign('contenido', $view->parse());

		// Seteamos título.
		$this->template->assign('title', __('Report Abuse - DMCA', FALSE));
	}
}