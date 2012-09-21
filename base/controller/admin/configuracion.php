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
class Base_Controller_Admin_Configuracion extends Controller {

	public function __construct()
	{
		parent::__construct();
		//TODO: verificar permisos para la accion.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/usuario/login');
		}
	}


	public function action_noticias($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/noticias');

		// Noticia Flash.
		if (Session::is_set('noticia_correcta'))
		{
			$vista->assign('success', Session::get_flash('noticia_correcta'));
		}

		// Modelo de noticias.
		$model_noticias = new Model_Noticia;

		// Cargamos el listado de noticias.
		$lst = $model_noticias->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$total = $model_noticias->total();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['contenido_raw'] = $a['contenido'];
			$a['contenido'] = Decoda::procesar($a['contenido']);
			$a['usuario'] = $v->usuario()->as_array();

			$lst[$k] = $a;
		}

		// Seteamos listado de noticias.
		$vista->assign('noticias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	public function action_nueva_noticia()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/nueva_noticia');

		// Valores por defecto y errores.
		$vista->assign('contenido', '');
		$vista->assign('error_contenido', FALSE);
		$vista->assign('visible', FALSE);
		$vista->assign('error_visible', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos el contenido.
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : NULL;

			// Obtenemos estado por defecto.
			$visible = isset($_POST['visible']) ? $_POST['visible'] == 1 : FALSE;

			// Quitamos BBCode para dimenciones.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);

			if ( ! isset($contenido_clean{10}) || isset($contenido_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'El contenido debe tener entre 10 y 200 caractéres');
			}
			unset($contenido_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Creamos la noticia.
				$model_noticia = new Model_Noticia;
				$id = $model_noticia->nuevo(Session::get('usuario_id'), $contenido, $visible ? Model_Noticia::ESTADO_VISIBLE : Model_Noticia::ESTADO_OCULTO);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				Session::set('noticia_correcta', 'La noticia se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/configuracion/noticias');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Activamos o desactivamos una noticia.
	 * @param int $id
	 * @param int $estado
	 */
	public function action_estado_noticia($id, $estado)
	{
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia( (int) $id);
		if ($model_noticia->existe())
		{
			$estado = (bool) $estado;
			if ($estado)
			{
				$model_noticia->activar();
				Session::set('noticia_correcta', 'Se habilitó correctamente la noticia #'. (int) $id);
			}
			else
			{
				$model_noticia->desactivar();
				Session::set('noticia_correcta', 'Se ocultó correctamente la noticia #'. (int) $id);
			}
		}
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Desctivamos todas las noticias.
	 */
	public function action_ocultar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->desactivar_todas();
		Session::set('noticia_correcta', 'Se han ocultado correctamente todas las noticias.');
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Activamos o desactivamos una noticia.
	 * @param int $id
	 * @param int $estado
	 */
	public function action_borrar_noticia($id)
	{
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia( (int) $id);
		if ($model_noticia->existe())
		{
			// Borramos la noticia.
			$model_noticia->eliminar();
			Session::set('noticia_correcta', 'Se borró correctamente la noticia #'. (int) $id);
		}
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Borramos todas las noticias.
	 */
	public function action_limpiar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->eliminar_todas();
		Session::set('noticia_correcta', 'Se han borrado correctamente todas las noticias.');
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Editamos una noticia.
	 * @param int $id ID de la noticia a editar.
	 */
	public function action_editar_noticia($id)
	{
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia( (int) $id);
		if ( ! $model_noticia->existe())
		{
			Request::redirect('/admin/configuracion/noticias');
		}


		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/editar_noticia');

		// Valores por defecto y errores.
		$vista->assign('contenido', $model_noticia->contenido);
		$vista->assign('error_contenido', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos el contenido.
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : NULL;

			// Quitamos BBCode para dimenciones.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);

			if ( ! isset($contenido_clean{10}) || isset($contenido_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'El contenido debe tener entre 10 y 200 caractéres');
			}
			else
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Actualizamos el contenido.
				$model_noticia->actualizar_contenido($contenido);
				$vista->assign('contenido', $model_noticia->contenido);
				$vista->assign('success', 'Contenido actualizado correctamente');
			}
			unset($contenido_clean);
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
