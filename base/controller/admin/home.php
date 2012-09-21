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
 * @subpackage  Controller\Admin
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador de la portada de la administración.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Home extends Controller {

	public static function submenu($activo)
	{
		$listado = array();
		$listado['p_general'] = array('caption' => 'General');
		$listado['index'] = array('link' => '/admin/', 'caption' => 'Inicio', 'active' => FALSE);

		$listado['p_configuracion'] = array('caption' => 'Configuración');
		$listado['configuracion'] = array('link' => '/admin/configuracion/', 'caption' => 'Configuracion', 'active' => FALSE);
		$listado['configuracion_temas'] = array('link' => '/admin/configuracion/temas/', 'caption' => 'Temas', 'active' => FALSE);
		$listado['configuracion_plugins'] = array('link' => '/admin/configuracion/plugins/', 'caption' => 'Plugins', 'active' => FALSE);
		$listado['configuracion_noticias'] = array('link' => '/admin/configuracion/noticias/', 'caption' => 'Noticias', 'active' => FALSE);
		$listado['configuracion_publicidad'] = array('link' => '/admin/configuracion/publicidad/', 'caption' => 'Publicidad', 'active' => FALSE);

		$listado['p_control'] = array('caption' => 'Control de PHPost');
		$listado['control_medallas'] = array('link' => '/admin/control/medallas', 'caption' => 'Medallas', 'active' => FALSE);
		$listado['control_afiliados'] = array('link' => '/admin/control/afiliados', 'caption' => 'Afiliados', 'active' => FALSE);
		$listado['control_estaditicas'] = array('link' => '/admin/control/estadisticas', 'caption' => 'Estadísticas', 'active' => FALSE);
		$listado['control_bloqueos'] = array('link' => '/admin/control/bloqueos', 'caption' => 'Bloqueos', 'active' => FALSE);
		$listado['control_censuras'] = array('link' => '/admin/control/censuras', 'caption' => 'Censuras', 'active' => FALSE);

		$listado['p_contenido'] = array('caption' => 'Contenido');
		$listado['contenido_comunidades'] = array('link' => '/admin/contenido/comunidades', 'caption' => 'Comunidades', 'active' => FALSE);
		$listado['contenido_posts'] = array('link' => '/admin/contenido/posts', 'caption' => 'Posts', 'active' => FALSE);
		$listado['contenido_fotos'] = array('link' => '/admin/contenido/fotos', 'caption' => 'Fotos', 'active' => FALSE);
		$listado['contenido_categorias'] = array('link' => '/admin/contenido/categorias', 'caption' => 'Categorias', 'active' => FALSE);

		$listado['p_usuarios'] = array('caption' => 'Usuarios');
		$listado['usuario'] = array('link' => '/admin/usuario/', 'caption' => 'General', 'active' => FALSE);
		$listado['usuario_sesiones'] = array('link' => '/admin/usuario/sesiones', 'caption' => 'Sessiones', 'active' => FALSE);
		$listado['usuario_nicks'] = array('link' => '/admin/usuario/nicks', 'caption' => 'Nicks', 'active' => FALSE);
		$listado['usuario_rangos'] = array('link' => '/admin/usuario/rangos', 'caption' => 'Rangos', 'active' => FALSE);
		
		if (isset($listado[$activo]))
		{
			$listado[$activo]['active'] = TRUE;
		}
		return $listado;
	}

	public function action_index()
	{
		// Cargamos la portada.
		$portada = View::factory('admin/home/index');

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $portada->parse());
		unset($portada);
		$admin_template->assign('top_bar', self::submenu('index'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}
}
