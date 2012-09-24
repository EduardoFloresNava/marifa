<?php
/**
 * contenido.php is part of Marifa.
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
 * Controlador de administración de contenido.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Contenido extends Controller {

	/**
	 * Listado de posts existentes.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_posts($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/posts');

		// Noticia Flash.
		if (Session::is_set('posts_correcto'))
		{
			$vista->assign('success', Session::get_flash('posts_correcto'));
		}

		if (Session::is_set('posts_error'))
		{
			$vista->assign('error', Session::get_flash('posts_error'));
		}

		// Modelo de posts.
		$model_posts = new Model_Post;

		// Cargamos el listado de posts.
		$lst = $model_posts->listado($pagina, $cantidad_por_pagina);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/admin/contenido/posts');
		}

		// Paginación.
		$total = $model_posts->cantidad();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de los posts.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de posts.
		$vista->assign('posts', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Eliminamos el post de un usuario.
	 * @param int $id ID del post a borrar.
	 */
	public function action_eliminar_post($id)
	{
		// Cargamos el modelo del post.
		$model_post = new Model_Post( (int) $id);
		if ( ! $model_post->existe())
		{
			Request::redirect('/admin/contenido/posts/');
		}

		// Seteamos como eliminado.
		$model_post->actualizar_estado(Model_Post::ESTADO_BORRADO);

		// Seteamos mensaje flash y volvemos.
		Session::set('post_correcto', 'Post borrado correctamente.');
		Request::redirect('/admin/contenido/posts/');
	}

	/**
	 * Listado de fotos existentes.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_fotos($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/fotos');

		// Noticia Flash.
		if (Session::is_set('fotos_correcto'))
		{
			$vista->assign('success', Session::get_flash('fotos_correcto'));
		}

		if (Session::is_set('fotos_error'))
		{
			$vista->assign('error', Session::get_flash('fotos_error'));
		}

		// Modelo de fotos.
		$model_fotos = new Model_Foto;

		// Verifico busqueda.
		if (Request::method() == 'POST')
		{
			$q = isset($_POST['q']) ? trim($_POST['q']) : NULL;

			$vista->assign('q', $q);
		}
		else
		{
			$vista->assign('q', '');
		}

		// Cargamos el listado de fotos.
		$lst = $model_fotos->listado($pagina, $cantidad_por_pagina);

		// Si no hay elementos y no estamos en la inicial redireccionamos (Puso página incorrecta).
		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/admin/contenido/fotos');
		}

		// Paginación.
		$total = $model_fotos->cantidad();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las fotos.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de fotos.
		$vista->assign('fotos', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_fotos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
