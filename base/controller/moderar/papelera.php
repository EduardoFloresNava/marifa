<?php
/**
 * desaprobado.php is part of Marifa.
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
 * Controlador para controlar el contenido desaprobado.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Moderar
 */
class Base_Controller_Moderar_Papelera extends Controller {

	/**
	 * Listado de posts que se encuentran en la papelera.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_posts($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('/moderar/papelera/posts');

		// Modelo de posts.
		$model_post = new Model_Post;

		// Cargamos el listado de posts.
		$lst = $model_post->listado($pagina, $cantidad_por_pagina, Model_Post::ESTADO_PAPELERA);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/papelera/posts');
		}

		// Paginación.
		$total = Model_Post::s_cantidad(Model_Post::ESTADO_PAPELERA);
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
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('papelera_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Restauramos un post.
	 * @param int $post ID del post a modificar el atributo.
	 */
	public function action_restaurar_post($post)
	{
		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Post incorrecto.';
			Request::redirect('/moderar/papelera/posts');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_POSTS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/moderar/papelera/posts');
		}

		// Actualizo el estado.
		$model_post->actualizar_estado(Model_Post::ESTADO_ACTIVO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Post restaurado correctamente.';
		//TODO: agregar suceso.
		Request::redirect('/moderar/papelera/posts');
	}

	/**
	 * Borramos un post.
	 * @param int $post ID del post a modificar el atributo.
	 */
	public function action_borrar_post($post)
	{
		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Post incorrecto.';
			Request::redirect('/moderar/papelera/posts');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_POSTS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/moderar/papelera/posts');
		}

		// Actualizo el estado.
		$model_post->actualizar_estado(Model_Post::ESTADO_BORRADO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Post borrado correctamente.';
		//TODO: agregar suceso.
		Request::redirect('/moderar/papelera/posts');
	}

	/**
	 * Listado de fotos que se encuentran en la papelera.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_fotos($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('/moderar/papelera/fotos');

		// Modelo de fotos.
		$model_foto = new Model_Foto;

		// Cargamos el listado de posts.
		$lst = $model_foto->listado($pagina, $cantidad_por_pagina, Model_Foto::ESTADO_PAPELERA);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/papelera/fotos');
		}

		// Paginación.
		$total = Model_Foto::s_cantidad(Model_Foto::ESTADO_PAPELERA);
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

		// Seteamos listado de fotos.
		$vista->assign('fotos', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('papelera_fotos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Restauramos una foto.
	 * @param int $foto ID de la foto a restaurar.
	 */
	public function action_restaurar_foto($foto)
	{
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Foto incorrecta.';
			Request::redirect('/moderar/papelera/fotos');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_foto->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_FOTOS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/moderar/papelera/fotos');
		}

		// Actualizo el estado.
		$model_foto->actualizar_campo('estado', Model_Foto::ESTADO_ACTIVA);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Foto restaurada correctamente.';
		//TODO: agregar suceso.
		Request::redirect('/moderar/papelera/fotos');
	}

	/**
	 * Borramos una foto.
	 * @param int $foto ID de la post a borrar.
	 */
	public function action_borrar_foto($foto)
	{
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Foto incorrecta.';
			Request::redirect('/moderar/papelera/fotos');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_foto->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_FOTOS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/moderar/papelera/fotos');
		}

		// Actualizo el estado.
		$model_foto->actualizar_campo('estado', Model_Foto::ESTADO_BORRADO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Foto borrada correctamente.';
		//TODO: agregar suceso.
		Request::redirect('/moderar/papelera/fotos');
	}
}
