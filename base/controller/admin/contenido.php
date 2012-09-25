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

		// Verifico cual es el estado actual.
		if ($model_post->estado === Model_Post::ESTADO_BORRADO)
		{
			Session::set('post_error', 'El post ya se encuentra moderado.');
			Request::redirect('/admin/contenido/posts/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/eliminar_post');

		// Valores por defecto y errores.
		$vista->assign('tipo', '');
		$vista->assign('error_tipo', FALSE);
		$vista->assign('razon', '');
		$vista->assign('error_razon', FALSE);
		$vista->assign('borrador', FALSE);
		$vista->assign('error_borrador', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$tipo = isset($_POST['tipo']) ? (int) $_POST['tipo'] : NULL;
			$razon = isset($_POST['razon']) ? preg_replace('/\s+/', ' ', trim($_POST['razon'])) : NULL;
			$borrador = isset($_POST['borrador']) ? $_POST['borrador'] == 1 : FALSE;

			// Valores para cambios.
			$vista->assign('tipo', $tipo);
			$vista->assign('razon', $razon);
			$vista->assign('borrador', $borrador);

			// Verifico el tipo.
			if ( ! in_array($tipo, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)))
			{
				$error = TRUE;
				$vista->assign('error_tipo', 'No ha seleccionado un tipo válido.');
			}
			else
			{
				// Verifico la razón si corresponde.
				if ($tipo === 12)
				{
					// Verificamos el nombre.
					if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{10,200}$/iD', $razon))
					{
						$error = TRUE;
						$vista->assign('error_razon', 'La razón dete tener entre 10 y 200 caractéres alphanuméricos.');
					}
				}
				else
				{
					$razon = NULL;
				}
			}

			if ( ! $error)
			{
				// Creo la moderación.
				$model_post->moderar($model_post->id, $tipo, $razon, $borrador);

				// Seteamos mensaje flash y volvemos.
				Session::set('post_correcto', 'Post borrado correctamente.');
				Request::redirect('/admin/contenido/posts/');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_post'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
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
		$lst = $model_fotos->listado($pagina, $cantidad_por_pagina, TRUE);

		// Si no hay elementos y no estamos en la inicial redireccionamos (Puso página incorrecta).
		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/admin/contenido/fotos');
		}

		// Paginación.
		$total = $model_fotos->cantidad(TRUE);
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
	 * Ocultamos una foto.
	 * @param int $id ID de la foto a ocultar
	 */
	public function action_ocultar_foto($id)
	{
		// Cargamos el modelo de la foto.
		$model_foto = new Model_Foto( (int) $id);

		// Verifico que exista.
		if ( ! $model_foto->existe())
		{
			Session::set('fotos_error', 'No existe la foto que quiere ocultar.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Verifico que esté activa.
		if ($model_foto->estado == Model_Foto::ESTADO_OCULTA)
		{
			Session::set('fotos_error', 'La foto ya se encuentra oculta.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Ocultamos la foto.
		$model_foto->actualizar_estado(Model_Foto::ESTADO_OCULTA);

		// Informamos.
		Session::set('fotos_correcto', 'Foto ocultada correctamente.');
		Request::redirect('/admin/contenido/fotos');
	}

	/**
	 * Seteamos como visible una foto.
	 * @param int $id ID de la foto a mostrar
	 */
	public function action_mostrar_foto($id)
	{
		// Cargamos el modelo de la foto.
		$model_foto = new Model_Foto( (int) $id);

		// Verifico que exista.
		if ( ! $model_foto->existe())
		{
			Session::set('fotos_error', 'No existe la foto que quiere mostrar.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Verifico que esté oculta.
		if ($model_foto->estado == Model_Foto::ESTADO_ACTIVA)
		{
			Session::set('fotos_error', 'La foto ya se encuentra visible.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Mostramos la foto.
		$model_foto->actualizar_estado(Model_Foto::ESTADO_ACTIVA);

		// Informamos.
		Session::set('fotos_correcto', 'Foto seteada como visible correctamente.');
		Request::redirect('/admin/contenido/fotos');
	}

	/**
	 * La foto se ha borrado correctamente.
	 * @param int $id ID de la foto a borrar.
	 */
	public function action_eliminar_foto($id)
	{
		// Cargamos el modelo de la foto.
		$model_foto = new Model_Foto( (int) $id);

		// Verifico que exista.
		if ( ! $model_foto->existe())
		{
			Session::set('fotos_error', 'No existe la foto que quiere mostrar.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Borramos la foto.
		$model_foto->borrar();

		// Informamos.
		Session::set('fotos_correcto', 'Foto borrrada correctamente.');
		Request::redirect('/admin/contenido/fotos');
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

	/**
	 * Listado de noticias.
	 * @param int $pagina Número de página de la cual mostrar noticias.
	 */
	public function action_noticias($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/noticias');

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
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	public function action_nueva_noticia()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/nueva_noticia');

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
				Request::redirect('/admin/contenido/noticias');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_noticias'));

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
		Request::redirect('/admin/contenido/noticias');
	}

	/**
	 * Desctivamos todas las noticias.
	 */
	public function action_ocultar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->desactivar_todas();
		Session::set('noticia_correcta', 'Se han ocultado correctamente todas las noticias.');
		Request::redirect('/admin/contenido/noticias');
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
		Request::redirect('/admin/contenido/noticias');
	}

	/**
	 * Borramos todas las noticias.
	 */
	public function action_limpiar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->eliminar_todas();
		Session::set('noticia_correcta', 'Se han borrado correctamente todas las noticias.');
		Request::redirect('/admin/contenido/noticias');
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
			Request::redirect('/admin/contenido/noticias');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/editar_noticia');

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
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
