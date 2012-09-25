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

	/**
	 * Listado de categorias de los posts, fotos y comunidades.
	 */
	public function action_categorias()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/categorias');

		// Noticia Flash.
		if (Session::is_set('categoria_correcto'))
		{
			$vista->assign('success', Session::get_flash('categoria_correcto'));
		}

		if (Session::is_set('categoria_error'))
		{
			$vista->assign('error', Session::get_flash('categoria_error'));
		}

		// Modelo de categorias.
		$model_categorias = new Model_Categoria;

		// Cargamos el listado de categorias.
		$lst = $model_categorias->lista();

		// Seteamos listado de las categorias.
		$vista->assign('categorias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_categorias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Agregamos una nueva categoria.
	 */
	public function action_agregar_categoria()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/nueva_categoria');

		// Cargamos el listado de imagens para rangos disponibles.
		//TODO: implementar funcion para obtener URL completa.
		$imagenes_categorias = scandir(APP_BASE.DS.VIEW_PATH.'default'.DS.'assets'.DS.'img'.DS.'categoria'.DS);
		unset($imagenes_categorias[1], $imagenes_categorias[0]); // Quitamos . y ..

		$vista->assign('imagenes_categorias', $imagenes_categorias);

		// Valores por defecto y errores.
		$vista->assign('nombre', '');
		$vista->assign('error_nombre', FALSE);
		$vista->assign('imagen', '');
		$vista->assign('error_imagen', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('imagen', $imagen);

			// Formateamos el nombre.
			$nombre = preg_replace('/\s+/', ' ', trim($nombre));

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{3,50}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre de la categoria deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_categorias))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			$model_categoria = new Model_Categoria;

			if ( ! $error)
			{
				// Verifico no exista campo con ese nombre.
				if ($model_categoria->existe_seo($model_categoria->make_seo($nombre)))
				{
					$error = TRUE;
					$vista->assign('error_nombre', 'Ya existe una categoria con ese nombre seo.');
				}
			}


			if ( ! $error)
			{
				// Creo la categoria.
				$model_categoria->nueva($nombre, $imagen);

				Session::set('categoria_correcto', 'Categoria creada correctamente.');
				Request::redirect('/admin/contenido/categorias');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_categorias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos una categoria.
	 * @param int $id ID de la categoria a borrar.
	 */
	public function action_eliminar_categoria($id)
	{
		// Cargamos el modelo de la categoria.
		$model_categoria = new Model_Categoria( (int) $id);

		// Verifico que exista.
		if ( ! $model_categoria->existe())
		{
			Session::set('categoria_error', 'No exista la categoria que quiere borrar.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Verifico no tenga posts ni fotos.
		if ($model_categoria->tiene_fotos() || $model_categoria->tiene_posts())
		{
			Session::set('categoria_error', 'No se puede borrar la categoria porque tiene fotos y/o posts asociados.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Borramos la categoria.
		$model_categoria->borrar();

		// Informamos.
		Session::set('categoria_correcto', 'Categoria eliminada correctamente.');
		Request::redirect('/admin/contenido/categorias');
	}

	/**
	 * Editamos una categoria existente.
	 * @param int $id ID de la categoria a editar.
	 */
	public function action_editar_categoria($id)
	{
		// Cargamos el modelo de la categoria.
		$model_categoria = new Model_Categoria( (int) $id);

		// Verifico que exista.
		if ( ! $model_categoria->existe())
		{
			Session::set('categoria_error', 'No exista la categoria que quiere editar.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/editar_categoria');

		// Cargamos el listado de imagens para rangos disponibles.
		//TODO: implementar funcion para obtener URL completa.
		$imagenes_categorias = scandir(APP_BASE.DS.VIEW_PATH.'default'.DS.'assets'.DS.'img'.DS.'categoria'.DS);
		unset($imagenes_categorias[1], $imagenes_categorias[0]); // Quitamos . y ..

		$vista->assign('imagenes_categorias', $imagenes_categorias);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_categoria->nombre);
		$vista->assign('error_nombre', FALSE);
		$vista->assign('imagen', $model_categoria->imagen);
		$vista->assign('error_imagen', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('imagen', $imagen);

			// Formateamos el nombre.
			$nombre = preg_replace('/\s+/', ' ', trim($nombre));

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{3,50}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre de la categoria deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_categorias))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			if ( ! $error)
			{
				// Verifico no exista campo con ese nombre.
				if ($model_categoria->existe_seo($model_categoria->make_seo($nombre), TRUE))
				{
					$error = TRUE;
					$vista->assign('error_nombre', 'Ya existe una categoria con ese nombre seo.');
				}
			}


			if ( ! $error)
			{
				// Actualizo el imagen.
				if ($model_categoria->imagen != $imagen)
				{
					$model_categoria->cambiar_imagen($imagen);
				}

				// Actualizo el nombre.
				if ($model_categoria->nombre != $nombre)
				{
					$model_categoria->cambiar_nombre($nombre);
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
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_categorias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
