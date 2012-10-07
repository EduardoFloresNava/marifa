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
	 * Listado de posts que se encuentran desaprobados.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $tipo Tipo de posts a mostrar.
	 */
	public function action_posts($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

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
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('/moderar/desaprobado/posts');

		// Asignamos datos.
		$vista->assign('tipo', $tipo);

		// Modelo de posts.
		$model_post = new Model_Post;

		// Cargamos el listado de posts.
		$lst = $model_post->listado($pagina, $cantidad_por_pagina, $tipo == 0 ? array(Model_Post::ESTADO_PENDIENTE, Model_Post::ESTADO_RECHAZADO) : ($tipo == 1 ? Model_Post::ESTADO_PENDIENTE : Model_Post::ESTADO_RECHAZADO));

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

		// Paginación.
		$total = $tipo == 0 ? $c_total : ($tipo == 1 ? $c_pendientes : $c_rechazados);
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
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('desaprobado_posts'));

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
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Post incorrecto.';
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Verifico el usuario y sus permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_VER_POSTS_DESAPROBADOS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No tienes permisos para realizar esa acci&oacute;n.';
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_RECHAZADO))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No se puede realizar esa acci&oacute;n, el estado es incorrecto.';
			Request::redirect('/moderar/desaprobado/posts');
		}
		elseif ( ! $tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_ACTIVO))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No se puede realizar esa acci&oacute;n, el estado es incorrecto.';
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_RECHAZADO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El estado se modific&oacute; correctamente.';
		//TODO: agregar suceso.
		Request::redirect('/moderar/desaprobado/posts');
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
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_POSTS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/moderar/desaprobado/posts');
		}

		// Actualizo el estado.
		$model_post->actualizar_estado(Model_Post::ESTADO_BORRADO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';
		//TODO: agregar suceso.
		Request::redirect('/moderar/desaprobado/posts');
	}

}