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
class Base_Controller_Moderar_Desaprobado extends Controller {

	/**
	 * Verificamos que el usuario esté identificado.
	 */
	public function before()
	{
		// Verifico que esté identificado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder acceder a esta sección.', FALSE));
			Request::redirect('/usuario/login');
		}
		parent::before();
	}

	/**
	 * Listado de posts que se encuentran desaprobados.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $tipo Tipo de posts a mostrar.
	 */
	public function action_posts($pagina, $tipo)
	{
		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// 0: Pendiente y Rechazado.
		// 1: Pendiente.
		// 2: Rechazado.

		// Verifico el tipo de denuncias a mostrar.
		if ($tipo == 0 || $tipo == 1 || $tipo == 2)
		{
			$tipo = (int) $tipo;
		}
		else
		{
			$tipo = 0;
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('/moderar/desaprobado/posts');

		// Asignamos datos.
		$vista->assign('tipo', $tipo);

		// Modelo de posts.
		$model_post = new Model_Post;

		// Cargamos el listado de posts.
		$lst = $model_post->listado($pagina, $cantidad_por_pagina, ($tipo == 0) ? array(Model_Post::ESTADO_PENDIENTE, Model_Post::ESTADO_RECHAZADO) : (($tipo == 1) ? Model_Post::ESTADO_PENDIENTE : Model_Post::ESTADO_RECHAZADO));

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Calculo las cantidades.
		$c_pendientes = $model_post->cantidad(Model_Post::ESTADO_PENDIENTE);
		$c_rechazados = $model_post->cantidad(Model_Post::ESTADO_RECHAZADO);
		$c_total = $c_pendientes + $c_rechazados;

		// Paso datos para barra.
		$vista->assign('cantidad_pendientes', $c_pendientes);
		$vista->assign('cantidad_rechazados', $c_rechazados);
		$vista->assign('cantidad_total', $c_total);
		$vista->assign('actual', $pagina);

		// Paginación.
		$total = ($tipo == 0) ? $c_total : (($tipo == 1) ? $c_pendientes : $c_rechazados);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/moderar/desaprobado/posts/%s/'.$tipo));

		// Obtenemos datos de los posts.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['categoria'] = $v->categoria()->as_array();
			$lst[$k] = $a;
		}

		// Asigno listado de posts.
		$vista->assign('posts', $lst);
		unset($lst);

		// Asigno el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('desaprobado.posts'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Contenido desaprobado', FALSE).' - '.__('Posts', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}


	/**
	 * Apruebo o rechazo un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se aprueba o se rechaza.
	 */
	public function action_aprobar_post($post, $tipo)
	{
		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El posts que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Verifico el usuario y sus permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('El posts que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_RECHAZADO))
		{
			add_flash_message(FLASH_ERROR, __('El posts que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/posts');
		}
		elseif ( ! $tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_ACTIVO))
		{
			add_flash_message(FLASH_ERROR, __('El posts que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_RECHAZADO);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_aprobar', TRUE, $post, Usuario::$usuario_id, (int) $tipo);
			$model_suceso->crear(Usuario::$usuario_id, 'post_aprobar', FALSE, $post, Usuario::$usuario_id, (int) $tipo);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_aprobar', FALSE, $post, Usuario::$usuario_id, (int) $tipo);
		}

		// Verifico actualización del rango.
		$model_post->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_POST);

		// Verifico actualización medallas.
		$model_post->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_POSTS);

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('El estado se modificó correctamente.', FALSE));
		Request::redirect('/moderar/desaprobado/posts');
	}

	/**
	 * Borramos un post.
	 * @param int $post ID del post a modificar el atributo.
	 */
	public function action_borrar_post($post)
	{
		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas eliminar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_ELIMINAR))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas eliminar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Actualizo el estado.
		$model_post->actualizar_estado(Model_Post::ESTADO_BORRADO);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $post, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $post, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $post, Usuario::$usuario_id);
		}

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('El post fue eliminado correctamente.', FALSE));
		Request::redirect('/moderar/desaprobado/posts');
	}

	/**
	 * Listado de comentarios que se encuentran desaprobados.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $tipo Tipo de comentarios a mostrar.
	 */
	public function action_comentarios($pagina, $tipo)
	{
		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// 0: Posts y fotos
		// 1: Fotos.
		// 2: Posts.

		// Verifico el tipo de comentarios a mostrar.
		if ($tipo == 0 || $tipo == 1 || $tipo == 2)
		{
			$tipo = (int) $tipo;
		}
		else
		{
			$tipo = 0;
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('/moderar/desaprobado/comentarios');

		// Asignamos datos.
		$vista->assign('tipo', $tipo);

		// Cargo datos en función del tipo.
		if ($tipo === 0)
		{
			// Cargo el modelo.
			$model_comentario = new Model_Comentario;

			// Cargo el listado de comentarios.
			$lst = $model_comentario->listado($pagina, $cantidad_por_pagina, Model_Comentario::ESTADO_OCULTO);

			if (count($lst) == 0 && $pagina != 1)
			{
				Request::redirect('/moderar/desaprobado/comentarios');
			}
		}
		elseif ($tipo === 1)
		{
			// Cargo el modelo.
			$model_comentario = new Model_Foto_Comentario;

			// Cargo el listado de comentarios.
			$lst = $model_comentario->listado($pagina, $cantidad_por_pagina, Model_Foto_Comentario::ESTADO_OCULTO);

			if (count($lst) == 0 && $pagina != 1)
			{
				Request::redirect('/moderar/desaprobado/comentarios/1/1');
			}
		}
		else
		{
			// Cargo el modelo.
			$model_comentario = new Model_Post_Comentario;

			// Cargo el listado de comentarios.
			$lst = $model_comentario->listado($pagina, $cantidad_por_pagina, Model_Post_Comentario::ESTADO_OCULTO);

			if (count($lst) == 0 && $pagina != 1)
			{
				Request::redirect('/moderar/desaprobado/comentarios/1/2');
			}
		}

		// Calculo las cantidades.
		$c_foto = Model_Foto_Comentario::cantidad(Model_Foto_Comentario::ESTADO_OCULTO);
		$c_post = Model_Post_Comentario::cantidad(Model_Post_Comentario::ESTADO_OCULTO);
		$c_total = $c_foto + $c_post;

		// Paso datos para barra.
		$vista->assign('cantidad_fotos', $c_foto);
		$vista->assign('cantidad_posts', $c_post);
		$vista->assign('cantidad_total', $c_total);
		$vista->assign('actual', $pagina);

		// Paginación.
		$total = ($tipo == 0) ? $c_total : (($tipo == 1) ? $c_foto : $c_post);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/moderar/desaprobado/comentario/%s/'.$tipo));

		// Obtenemos datos de los comentarios.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			if ($v instanceof Model_Foto_Comentario)
			{
				$a['foto'] = $v->foto()->as_array();
			}
			else
			{
				$a['post'] = $v->post()->as_array();
			}
			$a['usuario'] = $v->usuario()->as_array();
			$lst[$k] = $a;
		}

		// Asignamos listado de comentarios.
		$vista->assign('comentarios', $lst);
		unset($lst);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('desaprobado.comentarios'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Contenido desaprobado', FALSE).' - '.__('Comentarios', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Mostramos el comentario de un post o una foto
	 * @param int $comentario ID del comentario.
	 * @param int $tipo 1: post, 2: foto.
	 */
	public function action_mostrar_comentario($comentario, $tipo)
	{
		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Verifico el tipo.
		$tipo = (int) $tipo;
		if ($tipo !== 1 && $tipo !== 2)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas mostrar/ocultar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/comentarios');
		}

		// Cargo el comentario.
		$comentario = (int) $comentario;
		if ($tipo == 1)
		{
			$model_comentario = new Model_Post_Comentario($comentario);
		}
		else
		{
			$model_comentario = new Model_Foto_Comentario($comentario);
		}

		// Verifico existencia.
		if ( ! $model_comentario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas mostrar/ocultar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/comentarios');
		}

		// Verifico el estado.
		if ($model_comentario->estado !== Model_Comentario::ESTADO_OCULTO)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas mostrar/ocultar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/comentarios');
		}

		// Actualizo.
		$model_comentario->actualizar_campo('estado', Model_Comentario::ESTADO_VISIBLE);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_comentario->usuario_id)
		{
			$model_suceso->crear($model_comentario->usuario_id, ($tipo == 1) ? 'post_comentario_mostrar' : 'foto_comentario_mostrar', TRUE, $model_comentario->id, Usuario::$usuario_id);
			$model_suceso->crear($model_comentario->usuario_id, ($tipo == 1) ? 'post_comentario_mostrar' : 'foto_comentario_mostrar', FALSE, $model_comentario->id, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, ($tipo == 1) ? 'post_comentario_mostrar' : 'foto_comentario_mostrar', FALSE, $model_comentario->id, Usuario::$usuario_id);
		}

		// Verifico actualización del rango.
		$model_comentario->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_COMENTARIOS);

		if ($model_comentario instanceof Model_Post_Comentario)
		{
			// Verifico actualización de medallas.
			$model_comentario->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_POSTS);
			$model_comentario->post()->actualizar_medallas(Model_Medalla::CONDICION_POST_COMENTARIOS);
		}
		else
		{
			// Actualizo las medallas.
			$model_comentario->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_FOTOS);
			$model_comentario->foto()->actualizar_medallas(Model_Medalla::CONDICION_FOTO_COMENTARIOS);
		}

		// Informamos resultado.
		add_flash_message(FLASH_SUCCESS, __('El comentario se ha aprobado correctamente.', FALSE));
		Request::redirect('/moderar/desaprobado/comentarios');
	}

	/**
	 * Corramos el comentario de un post o una foto
	 * @param int $comentario ID del comentario.
	 * @param int $tipo 1: post, 2: foto.
	 */
	public function action_borrar_comentario($comentario, $tipo)
	{
		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Verifico el tipo.
		$tipo = (int) $tipo;
		if ($tipo !== 1 && $tipo !== 2)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/comentarios');
		}

		// Cargo el comentario.
		$comentario = (int) $comentario;
		if ($tipo == 1)
		{
			$model_comentario = new Model_Post_Comentario($comentario);
		}
		else
		{
			$model_comentario = new Model_Foto_Comentario($comentario);
		}

		// Verifico existencia.
		if ( ! $model_comentario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/comentarios');
		}

		// Verifico el estado.
		if ($model_comentario->estado !== Model_Comentario::ESTADO_OCULTO)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/desaprobado/comentarios');
		}

		// Actualizo.
		$model_comentario->actualizar_campo('estado', Model_Comentario::ESTADO_BORRADO);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_comentario->usuario_id)
		{
			$model_suceso->crear($model_comentario->usuario_id, ($tipo == 1) ? 'post_comentario_borrar' : 'foto_comentario_borrar', TRUE, $model_comentario->id, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, ($tipo == 1) ? 'post_comentario_borrar' : 'foto_comentario_borrar', FALSE, $model_comentario->id, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, ($tipo == 1) ? 'post_comentario_borrar' : 'foto_comentario_borrar', FALSE, $model_comentario->id, Usuario::$usuario_id);
		}

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('El comentario se ha eliminado correctamente.', FALSE));
		Request::redirect('/moderar/desaprobado/comentarios');
	}

}