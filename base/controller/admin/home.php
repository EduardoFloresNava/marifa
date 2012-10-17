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

	/**
	 * Constructor de la clase.
	 * Verificamos los permisos para acceder a la sección.
	 */
	public function __construct()
	{
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder acceder a esta sección.';
			Request::redirect('/usuario/login');
		}

		// Verifico si tiene alguno de los permisos solicitados.
		if ( ! self::permisos_acceso())
		{
			$_SESSION['flash_error'] = 'No tienes permisos para acceder a esa sección.';
			Request::redirect('/');
		}

		parent::__construct();
	}

	/**
	 * Verifica si los permisos de acceso son los requeridos para acceder a esta sección.
	 * @return bool
	 */
	public static function permisos_acceso()
	{
		// Verifico si tiene algun permiso.
		$permisos = array(
			Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR,
			Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO,
			Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR
		);

		return Usuario::permiso($permisos);
	}

	/**
	 * Listado de menus activos.
	 * @param string $activo Clave del listado de menus activa en la petición.
	 * @return array
	 */
	public static function submenu($activo)
	{
		$listado = array();
		$listado['p_general'] = array('caption' => 'General');
		$listado['index'] = array('link' => '/admin/', 'caption' => 'Inicio', 'active' => FALSE);

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR))
		{
			$listado['p_configuracion'] = array('caption' => 'Configuración');
			$listado['configuracion'] = array('link' => '/admin/configuracion/', 'caption' => 'Configuracion', 'active' => FALSE);
			$listado['configuracion_temas'] = array('link' => '/admin/configuracion/temas/', 'caption' => 'Temas', 'active' => FALSE);
			$listado['configuracion_plugins'] = array('link' => '/admin/configuracion/plugins/', 'caption' => 'Plugins', 'active' => FALSE);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			$listado['p_contenido'] = array('caption' => 'Contenido');
			$listado['contenido'] = array('link' => '/admin/contenido', 'caption' => 'Informe contenido', 'active' => FALSE);
			$listado['contenido_posts'] = array('link' => '/admin/contenido/posts', 'caption' => 'Posts', 'active' => FALSE);
			$listado['contenido_fotos'] = array('link' => '/admin/contenido/fotos', 'caption' => 'Fotos', 'active' => FALSE);
			$listado['contenido_categorias'] = array('link' => '/admin/contenido/categorias', 'caption' => 'Categorias', 'active' => FALSE);
			$listado['contenido_noticias'] = array('link' => '/admin/contenido/noticias/', 'caption' => 'Noticias', 'active' => FALSE);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR))
		{
			$listado['p_usuarios'] = array('caption' => 'Usuarios');
			$listado['usuario'] = array('link' => '/admin/usuario/', 'caption' => 'General', 'active' => FALSE);
			$listado['usuario_sesiones'] = array('link' => '/admin/usuario/sesiones', 'caption' => 'Sesiones', 'active' => FALSE);
			$listado['usuario_rangos'] = array('link' => '/admin/usuario/rangos', 'caption' => 'Rangos', 'active' => FALSE);
		}

		if (isset($listado[$activo]))
		{
			$listado[$activo]['active'] = TRUE;
		}
		return $listado;
	}

	/**
	 * Portada del panel de administración.
	 */
	public function action_index()
	{
		// Cargamos la portada.
		$portada = View::factory('admin/home/index');

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $portada->parse());
		unset($portada);
		$admin_template->assign('top_bar', self::submenu('index'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}
}
