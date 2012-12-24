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
	 * Verificamos los permisos para acceder a la sección.
	 */
	public function before()
	{
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para poder acceder a esta sección.');
			Request::redirect('/usuario/login');
		}

		// Verifico si tiene alguno de los permisos solicitados.
		if ( ! self::permisos_acceso())
		{
			add_flash_message(FLASH_ERROR, 'No tienes permisos para acceder a esa sección.');
			Request::redirect('/');
		}

		parent::before();
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
		$listado['home_logs'] = array('link' => '/admin/home/logs', 'caption' => 'Log\'s', 'active' => FALSE);

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR))
		{
			$listado['p_configuracion'] = array('caption' => 'Configuración');
			$listado['configuracion'] = array('link' => '/admin/configuracion/', 'caption' => 'Configuración', 'active' => FALSE);
			$listado['configuracion_seo'] = array('link' => '/admin/configuracion/seo', 'caption' => 'SEO', 'active' => FALSE);
			$listado['configuracion_mantenimiento'] = array('link' => '/admin/configuracion/mantenimiento/', 'caption' => 'Modo Mantenimiento', 'active' => FALSE);
			$listado['configuracion_temas'] = array('link' => '/admin/configuracion/temas/', 'caption' => 'Temas', 'active' => FALSE);
			$listado['configuracion_plugins'] = array('link' => '/admin/configuracion/plugins/', 'caption' => 'Plugins', 'active' => FALSE);
			$listado['configuracion_correo'] = array('link' => '/admin/configuracion/correo/', 'caption' => 'Correo', 'active' => FALSE);
			$listado['configuracion_optimizar'] = array('link' => '/admin/configuracion/optimizar/', 'caption' => 'Optimizaciones', 'active' => FALSE);
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
			$listado['usuario_medallas'] = array('link' => '/admin/usuario/medallas', 'caption' => 'Medallas', 'active' => FALSE);
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
		$vista = View::factory('admin/home/index');

		// Ultimos usuarios.
		$model_usuario = new Model_Usuario;
		$usuarios = $model_usuario->listado(1, 5);
		foreach ($usuarios as $k => $v)
		{
			$usuarios[$k] = $v->as_array();
		}
		$vista->assign('usuarios', $usuarios);

		// Total de usuarios.
		$vista->assign('usuarios_total', $model_usuario->cantidad());
		unset($usuarios, $model_usuario);

		// Obtenemos versiones de Marifa.
		$rst = Cache::get_instance()->get('last_version');
		if ( ! is_array($rst))
		{
			$rst = @json_decode(Utils::remote_call('https://api.github.com/repos/Marifa/marifa/tags'));
			Cache::get_instance()->save('last_version', $rst, 60);
		}

		// Ordenamos y obtenemos la última y si podemos actualizar.
		if (is_array($rst) && isset($rst[0]))
		{
			// Ordeno las versiones.
			usort($rst, create_function('$a, $b', 'return version_compare(substr($b->name, 1), substr($a->name, 1));'));

			$vista->assign('version', $rst[0]->name);
			$vista->assign('version_new', version_compare(substr($rst[0]->name, 1), VERSION) > 0);
			$vista->assign('download', array('zip' => $rst[0]->zipball_url, 'tar' => $rst[0]->tarball_url));
		}

		// Obtenemos contenido.
		$rst = Database::get_instance()->query('SELECT * FROM ((SELECT "foto" as type, id, creacion AS fecha FROM foto ORDER BY fecha DESC LIMIT 5) UNION (SELECT "post" as type, id, fecha FROM post ORDER BY fecha DESC LIMIT 5)) as A ORDER BY fecha DESC LIMIT 5')->get_records();

		$lst = array();
		foreach ($rst as $v)
		{
			if ($v['type'] == 'post')
			{
				$obj = new Model_Post( (int) $v['id']);
				$aux = $obj->as_array();
				$aux['tipo'] = 'post';
				$aux['usuario'] = $obj->usuario()->as_array();
				$aux['categoria'] = $obj->categoria()->as_array();
				$lst[] = $aux;
			}
			else
			{
				$obj = new Model_Foto( (int) $v['id']);
				$aux = $obj->as_array();
				$aux['tipo'] = 'foto';
				$aux['usuario'] = $obj->usuario()->as_array();
				$aux['categoria'] = $obj->categoria()->as_array();
				$lst[] = $aux;
			}
		}
		$vista->assign('contenido', $lst);
		unset($lst);

		$vista->assign('contenido_total', Model_Post::s_cantidad() + Model_Foto::s_cantidad());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', self::submenu('index'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Mostramos los logs existentes en el sistema de archivos.
	 */
	public function action_logs($file)
	{
		// Cargamos la portada.
		$vista = View::factory('/admin/home/log');

		// Listado de archivos.
		$file_list = glob(APP_BASE.DS.'log'.DS.'*.log');
		$file_list = array_map(create_function('$str', 'return substr($str, strlen(APP_BASE.DS.\'log\'.DS));'), $file_list);
		$vista->assign('file_list', $file_list);

		if ($file !== NULL)
		{
			// Verifico si esta en la lista.
			if ( ! in_array($file, $file_list))
			{
				add_flash_message(FLASH_ERROR, 'El archivo no es correcto.');
				Request::redirect('/admin/home/logs/');
			}

			// Cargo el archivo.
			$data = file(APP_BASE.DS.'log'.DS.$file);

			// Proceso las lineas.
			$pd = array();
			foreach ($data as $v)
			{
				// Obtengo los datos.
				preg_match('/\[(.*)\] \[(.*)\] (.*)/', $v, $aux);

				// Verifico sea correcto.
				if (count($aux) != 4)
				{
					continue;
				}

				// Genero la linea.
				$pd[] = array('fecha' => new Fechahora($aux[1]), 'tipo' => trim($aux[2]), 'str' => $aux[3]);
			}
			unset($data);

			// Envio los datos a la vista.
			$vista->assign('lineas', $pd);
			$vista->assign('actual', $file);
			unset($pd);
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', self::submenu('home_logs'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Eliminamos un log.
	 * @param string $file Nombre del archivo de log's a eliminar.
	 */
	public function action_borrar_log($file)
	{
		// Listado de archivos.
		$file_list = glob(APP_BASE.DS.'log'.DS.'*.log');
		$file_list = array_map(create_function('$str', 'return substr($str, strlen(APP_BASE.DS.\'log\'.DS));'), $file_list);

		// Verifico si esta en la lista.
		if ( ! in_array($file, $file_list))
		{
			add_flash_message(FLASH_ERROR, 'El archivo de log que deseas eliminar no es correcto.');
			Request::redirect('/admin/home/logs/');
		}

		// Elimino el archivo.
		if (@unlink(APP_BASE.DS.'log'.DS.$file))
		{
			add_flash_message(FLASH_SUCCESS, 'El archivo de log se ha eliminado correctamente.');
			Request::redirect('/admin/home/logs/');
		}
		else
		{
			add_flash_message(FLASH_ERROR, 'Se ha producido un falla al borrar el archivo de logs. Verifique los permisos.');
			Request::redirect('/admin/home/logs/');
		}
	}
}
