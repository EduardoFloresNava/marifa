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
		// Verifico estar identificado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder acceder a esta sección.', FALSE));
			Request::redirect('/usuario/login', TRUE);
		}

		// Verifico si tiene alguno de los permisos solicitados.
		if ( ! self::permisos_acceso())
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para acceder a esa sección.', FALSE));
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
		// Verifico si tiene algún permiso.
		$permisos = array(
			Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR,
			Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO,
			Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR
		);

		return Usuario::permiso($permisos);
	}

	/**
	 * Listado de menúes activos.
	 * @param string $activo Clave del listado de menúes activa en la petición.
	 * @return array
	 */
	public static function submenu($activo)
	{
		// Objeto para manejo del menú.
		$menu = new Menu('admin_menu');

		// Información general.
		$menu->group_set(__('General', FALSE), 'general');
		$menu->element_set(__('Inicio', FALSE), '/admin/', 'index', 'general');
		$menu->element_set(__('Log\'s', FALSE), '/admin/home/logs/', 'logs', 'general');

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR))
		{
			// Sistema.
			$menu->group_set(__('Sistema', FALSE), 'sistema');
			$menu->element_set(__('Información', FALSE), '/admin/sistema/', 'informacion', 'sistema');
			$menu->element_set(__('Temas', FALSE), '/admin/sistema/temas/', 'temas', 'sistema');
			$menu->element_set(__('Plugins', FALSE), '/admin/sistema/plugins/', 'plugins', 'sistema');
			$menu->element_set(__('Optimizaciones', FALSE), '/admin/sistema/optimizar/', 'optimizar', 'sistema');
			$menu->element_set(__('Traducciones', FALSE), '/admin/sistema/traducciones/', 'traducciones', 'sistema');

			// Configuraciones.
			$menu->group_set(__('Configuración', FALSE), 'configuracion');
			$menu->element_set(__('Configuración', FALSE), '/admin/configuracion/', 'configuracion', 'configuracion');
			$menu->element_set(__('SEO', FALSE), '/admin/configuracion/seo/', 'seo', 'configuracion');
			$menu->element_set(__('Modo Mantenimiento', FALSE), '/admin/configuracion/mantenimiento/', 'mantenimiento', 'configuracion');
			$menu->element_set(__('Correo', FALSE), '/admin/configuracion/correo/', 'correo', 'configuracion');
			$menu->element_set(__('Base de Datos', FALSE), '/admin/configuracion/bd/', 'bd', 'configuracion');
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			$menu->group_set(__('Contenido', FALSE), 'contenido');
			$menu->element_set(__('Informe contenido', FALSE), '/admin/contenido/', 'index', 'contenido');
			$menu->element_set(__('Posts', FALSE), '/admin/contenido/posts/', 'posts', 'contenido');
			$menu->element_set(__('Fotos', FALSE), '/admin/contenido/fotos/', 'fotos', 'contenido');
			$menu->element_set(__('Categorías', FALSE), '/admin/contenido/categorias/', 'categorias', 'contenido');
			$menu->element_set(__('Noticias', FALSE), '/admin/contenido/noticias/', 'noticias', 'contenido');
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR))
		{
			$menu->group_set(__('Usuarios', FALSE), 'usuarios');
			$menu->element_set(__('General', FALSE), '/admin/usuario/', 'usuario', 'usuarios');
			$menu->element_set(__('Sesiones', FALSE), '/admin/usuario/sesiones/', 'sesiones', 'usuarios');
			$menu->element_set(__('Rangos', FALSE), '/admin/usuario/rangos/', 'rangos', 'usuarios');
			$menu->element_set(__('Medallas', FALSE), '/admin/usuario/medallas/', 'medallas', 'usuarios');
		}

		// Envío respuesta.
		$el = explode('.', $activo);
		if (count($el) == 2)
		{
			return $menu->as_array($el[1], $el[0], FALSE);
		}
		else
		{
			return $menu->as_array($activo, FALSE);
		}
	}

	/**
	 * Portada del panel de administración.
	 */
	public function action_index()
	{
		// Cargamos la portada.
		$vista = View::factory('admin/home/index');

		// Verifico driver SQL.
		if (Database::get_instance() instanceof Database_Driver_Mysql)
		{
			add_flash_message(FLASH_INFO, sprintf(__('<strong>¡Importante!</strong> Se recomienda utilizar MySQLi como driver por cuestiones de rendimiento y seguridad. Para editar estas configuraciones haga click <a href="%s/admin/configuracion/bd">aquí</a>.', FALSE), SITE_URL));
		}

		// Últimos usuarios.
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
		// TODO: Hacer asincronico.
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

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', self::submenu('general.index'));

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
		$file_list = glob(APP_BASE.DS.'log'.DS.'*.{log,log.gz}', GLOB_BRACE);
		$file_list = array_map(create_function('$str', 'return substr($str, strlen(APP_BASE.DS.\'log\'.DS));'), $file_list);
		$vista->assign('file_list', $file_list);

		if ($file !== NULL)
		{
			// Verifico si esta en la lista.
			if ( ! in_array($file, $file_list))
			{
				add_flash_message(FLASH_ERROR, __('El archivo no es correcto.', FALSE));
				Request::redirect('/admin/home/logs/');
			}

			if (substr($file, -3) == '.gz')
			{
				// Cargo el archivo.
				$data = explode("\n", gzuncompress(file_get_contents(APP_BASE.DS.'log'.DS.$file)));
			}
			else
			{
				// Cargo el archivo.
				$data = file(APP_BASE.DS.'log'.DS.$file);
			}

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

			// Envía los datos a la vista.
			$vista->assign('lineas', $pd);
			$vista->assign('actual', $file);
			unset($pd);
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', self::submenu('general.logs'));

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
		$file_list = glob(APP_BASE.DS.'log'.DS.'*.{log,log.gz}', GLOB_BRACE);
		$file_list = array_map(create_function('$str', 'return substr($str, strlen(APP_BASE.DS.\'log\'.DS));'), $file_list);

		// Verifico si esta en la lista.
		if ( ! in_array($file, $file_list))
		{
			add_flash_message(FLASH_ERROR, __('El archivo de log que deseas eliminar no es correcto.', FALSE));
			Request::redirect('/admin/home/logs/');
		}

		// Elimino el archivo.
		if (@unlink(APP_BASE.DS.'log'.DS.$file))
		{
			add_flash_message(FLASH_SUCCESS, __('El archivo de log se ha eliminado correctamente.', FALSE));
			Request::redirect('/admin/home/logs/');
		}
		else
		{
			add_flash_message(FLASH_ERROR, __('Se ha producido un falla al borrar el archivo de logs. Verifique los permisos.', FALSE));
			Request::redirect('/admin/home/logs/');
		}
	}
}
