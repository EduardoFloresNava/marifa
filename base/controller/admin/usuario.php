<?php
/**
 * usuario.php is part of Marifa.
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
 * Controlador de administración de usuarios.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Usuario extends Controller {

	public function __construct()
	{
		parent::__construct();
		//TODO: verificar permisos para la accion.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/usuario/login');
		}
	}

	/**
	 * Listado de rangos.
	 */
	public function action_rangos()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/rangos');

		// Noticia Flash.
		if (Session::is_set('rango_correcto'))
		{
			$vista->assign('success', Session::get_flash('rango_correcto'));
		}

		if (Session::is_set('rango_error'))
		{
			$vista->assign('error', Session::get_flash('rango_error'));
		}

		// Modelo de rangos.
		$model_rangos = new Model_Usuario_Rango;

		// Cargamos el listado de rangos.
		$lst = $model_rangos->listado();

		// Obtenemos datos de los rangos.
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Seteamos listado de rangos.
		$vista->assign('rangos', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Creamos un nuevo rango.
	 */
	public function action_nuevo_rango()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/nuevo_rango');

		// Valores por defecto y errores.
		$vista->assign('nombre', '');
		$vista->assign('error_nombre', FALSE);
		$vista->assign('color', '');
		$vista->assign('error_color', FALSE);
		$vista->assign('imagen', '');
		$vista->assign('error_imagen', FALSE);

		// Cargamos el listado de imagens para rangos disponibles.
		//TODO: implementar funcion para obtener URL completa.
		$imagenes_rangos = scandir(APP_BASE.DS.VIEW_PATH.'default'.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		unset($imagenes_rangos[1], $imagenes_rangos[0]); // Quitamos . y ..

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
			$color = isset($_POST['color']) ? $_POST['color'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('color', $color);
			$vista->assign('imagen', $imagen);


			// Formateamos el nombre.
			$nombre = preg_replace('/\s+/', ' ', trim($nombre));

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre del rango deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-z]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', 'El color debe ser HEXADECIMAL de 6 digitos. Por ejemplo: 00FF00.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_rangos))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			if ( ! $error)
			{
				// Convertimos el color a entero.
				$color = hexdec($color);

				// Creamos el rango.
				$model_rango = new Model_Usuario_Rango;
				$model_rango->nuevo_rango($nombre, $color, $imagen);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				Session::set('rango_correcto', 'El rango se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/usuario/rangos');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Editamos el rango.
	 * @param int $id ID del rango a editar.
	 */
	public function action_editar_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);
		if ( ! $model_rango->existe())
		{
			Request::redirect('/admin/usuario/rangos');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/editar_rango');

		// Cargamos el listado de imagens para rangos disponibles.
		//TODO: implementar funcion para obtener URL completa.
		$imagenes_rangos = scandir(APP_BASE.DS.VIEW_PATH.'default'.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		unset($imagenes_rangos[1], $imagenes_rangos[0]); // Quitamos . y ..

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_rango->nombre);
		$vista->assign('error_nombre', FALSE);
		$vista->assign('color', strtoupper(dechex($model_rango->color)));
		$vista->assign('error_color', FALSE);
		$vista->assign('imagen', $model_rango->imagen);
		$vista->assign('error_imagen', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
			$color = isset($_POST['color']) ? $_POST['color'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('color', $color);
			$vista->assign('imagen', $imagen);

			// Formateamos el nombre.
			$nombre = preg_replace('/\s+/', ' ', trim($nombre));

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre del rango deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-z]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', 'El color debe ser HEXADECIMAL de 6 digitos. Por ejemplo: 00FF00.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_rangos))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			if ( ! $error)
			{
				// Convertimos el color a entero.
				$color = hexdec($color);

				// Actualizo el color.
				if ($model_rango->color != $color)
				{
					$model_rango->cambiar_color($color);
				}

				// Actualizo el imagen.
				if ($model_rango->imagen != $imagen)
				{
					$model_rango->cambiar_imagen($imagen);
				}

				// Actualizo el nombre.
				if ($model_rango->nombre != $nombre)
				{
					$model_rango->renombrar($nombre);
				}

				// Informamos suceso.
				$vista->assign('success', 'Información actualizada correctamente');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vemos detalles de rango. Como los permisos y usuarios asignados.
	 * @param int $id ID del rango a ver.
	 */
	public function action_ver_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);
		if ( ! $model_rango->existe())
		{
			Request::redirect('/admin/usuario/rangos');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/ver_rango');

		// Listado de permisos.
		$permisos = array();
		for ($i = 1; $i < 20; $i++)
		{
			$permisos[$i] = 'permiso'.$i;
		}

		$vista->assign('permisos', $permisos);

		if (Request::method() == 'POST')
		{
			// Obtenemos permisos usuario.
			$permisos_usuario = $model_rango->permisos();

			// Obtenemos permisos marcados.
			$activos = array();
			foreach (array_keys($permisos) as $k)
			{
				if (isset($_POST[$k]))
				{
					$activos[$k] = $k;
				}
			}

			// Calculamos nuevos y quitados.
			$i = array_intersect($permisos_usuario, $activos);
			$quitar = array_diff($permisos_usuario, $i);
			$nuevos = array_diff($activos, $i);

			// Realizamos modificaciones.
			foreach ($quitar as $q)
			{
				$model_rango->borrar_permiso($q);
			}

			foreach ($nuevos as $q)
			{
				$model_rango->agregar_permiso($q);
			}

			$vista->assign('success', 'Permisos actualizados correctamente.');
		}

		// Seteamos datos del rango.
		$vista->assign('rango', $model_rango->as_array());

		// Permisos del rango.
		$vista->assign('permisos_rango', $model_rango->permisos());

		// Usuarios del rango.
		$lst = $model_rango->usuarios();
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos un rango.
	 * @param int $id ID del rango a borrar.
	 */
	public function action_borrar_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);

		// Verificamos exista.
		if ($model_rango->existe())
		{
			// Verificamos exista otro y además no tenga usuarios.
			if ($model_rango->tiene_usuarios())
			{
				Session::set('rango_error', 'El rango tiene usuarios y no puede ser eliminado.');
				Request::redirect('/admin/usuario/rangos');
			}

			if (count($model_rango->listado()) < 2)
			{
				Session::set('rango_error', 'No se puede eliminar al único rango existente.');
				Request::redirect('/admin/usuario/rangos');
			}

			// Borramos la noticia.
			$model_rango->borrar_rango();
			Session::set('rango_correcto', 'Se borró correctamente el rango.');
		}
		Request::redirect('/admin/usuario/rangos');
	}

}
