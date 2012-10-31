<?php
/**
 * buscador.php is part of Marifa.
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
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador para la busqueda de contenido en la web.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Buscador extends Controller {

	/**
	 * Alias de action_q
	 * @param string $query Cadena a buscar.
	 * @param int $pagina Numero de página a mostrar.
	 * @param string $categoria Nombre de la categoria a utilizar.
	 * @param string $usuario Nombre del usuario a utilizar.
	 * @see action_q
	 * @return mixed
	 */
	public function action_index($query, $pagina, $categoria, $usuario)
	{
		return $this->action_q($query, $pagina, $categoria, $usuario);
	}

	/**
	 * Buscamos un elemento.
	 * @param string $query Cadena a buscar.
	 * @param int $pagina Numero de página a mostrar.
	 * @param string $categoria Nombre de la categoria a utilizar.
	 * @param string $usuario Nombre del usuario a utilizar.
	 * @return mixed
	 */
	public function action_q($query, $pagina, $categoria, $usuario)
	{
		// Verificamos si es POST y redireccionamos.
		if (Request::method() == 'POST')
		{
			// Query.
			$q = isset($_POST['q']) ? urlencode($_POST['q']) : '';

			// Categoria.
			$c = isset($_POST['categoria']) ? urlencode($_POST['categoria']) : '';

			// Usuario
			$u = isset($_POST['usuario']) ? urlencode($_POST['usuario']) : '';

			if ( ! empty($u))
			{
				$url = sprintf('/buscador/q/%s/1/%s/%s', $q, $c, $u);
			}
			elseif ( ! empty($c))
			{
				$url = sprintf('/buscador/q/%s/1/%s', $q, $c);
			}
			else
			{
				$url = sprintf('/buscador/q/%s', $q);
			}

			Request::redirect($url);
		}

		// Limpiamos la consulta.
		$query = preg_replace('/\s+/', ' ', urldecode(trim($query)));

		// Limpiamos la categoria.
		$categoria = urldecode(trim($categoria));

		if ($categoria !== 'todos')
		{
			// Cargamos la categoria.
			$model_categoria = new Model_Categoria;
			if ($model_categoria->existe_seo($categoria))
			{
				$model_categoria->load_by_seo($categoria);
			}
			else
			{
				unset($model_categoria);
			}
		}
		unset($categoria);

		// Limpiamos nombre de usuario.
		$usuario = urldecode(trim($usuario));

		// Cargamos el usuario.
		$model_usuario = new Model_Usuario;
		if ($model_usuario->exists_nick($usuario))
		{
			$model_usuario->load_by_nick($usuario);
		}
		else
		{
			unset($model_usuario);
		}
		unset($usuario);

		// Cargamos la vista.
		$vista = View::factory('buscador/index');

		// Verificamos si hay consulta.
		if ( ! empty($query))
		{
			// Cantidad de elementos por pagina.
			$model_configuracion = new Model_Configuracion;
			$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

			// Formato de la página.
			$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

			// Realizamos la busqueda.
			$model_post = new Model_Post;
			list($listado, $cantidad) = $model_post->buscar($query, $pagina, $cantidad_por_pagina, isset($model_categoria) ? $model_categoria->id : NULL, isset($model_usuario) ? $model_usuario->id : NULL);

			// Verifivo que la página seleccionada sea válida.
			if (count($listado) == 0 && $pagina != 1)
			{
				Request::redirect(sprintf('/buscador/q/%s/1/%s/%s', $query, isset($model_categoria) ? $model_categoria->seo : '', isset($model_usuario) ? $model_usuario->id : ''));
			}

			// Paginación.
			$paginador = new Paginator($cantidad, $cantidad_por_pagina);
			$vista->assign('paginacion', $paginador->get_view($pagina, "/buscador/q/$query/%s/".(isset($model_categoria) ? $model_categoria->seo : '').'/'.(isset($model_usuario) ? $model_usuario->id : '')));
			unset($paginador);
			$vista->assign('total', $cantidad);

			// Procesamos listado de post.
			foreach ($listado as $k => $v)
			{
				$a = $v->as_array();
				$a['usuario'] = $v->usuario()->as_array();
				$a['puntos'] = $v->puntos();
				$a['comentarios'] = $v->cantidad_comentarios();
				$a['categoria'] = $v->categoria()->as_array();

				$listado[$k] = $a;
			}

			$vista->assign('resultados', $listado);
			unset($listado);
		}

		// Armamos la vista.
		$vista->assign('q', $query);

		// Listado de categorias.
		$mc = new Model_Categoria;
		$vista->assign('categorias', $mc->lista());
		unset($mc);
		$vista->assign('categoria', isset($model_categoria) ? $model_categoria->seo : 'todos');

		// Usuario actual.
		$vista->assign('usuario', isset($model_usuario) ? $model_usuario->nick : '');

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('buscador'));

		// Asignamos la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Post con las mismas etiquetas que el selecionado.
	 * @param int $post ID del post a mostrar relacionados.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_relacionados($post, $pagina)
	{
		// Transformamos a entero.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos existencia.
		if ( ! $model_post->existe())
		{
			Request::redirect('/buscador/q/');
		}

		// Cargamos la vista.
		$vista = View::factory('buscador/index');

		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Realizamos la busqueda.
		list($listado, $cantidad) = $model_post->buscar_relacionados($pagina, $cantidad_por_pagina);

		// Verifivo que la página seleccionada sea válida.
		if (count($listado) == 0 && $pagina != 1)
		{
			Request::redirect('/buscador/relacionados/'.$post);
		}

		// Paginación.
		$paginador = new Paginator($cantidad, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, "/buscador/relacionados/$post/%s/"));
		unset($paginador);
		$vista->assign('total', $cantidad);

		// Procesamos listado de post.
		foreach ($listado as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['puntos'] = $v->puntos();
			$a['comentarios'] = $v->cantidad_comentarios();
			$a['categoria'] = $v->categoria()->as_array();
			$listado[$k] = $a;
		}
		$vista->assign('resultados', $listado);
		unset($listado);

		$vista->assign('q', '');

		// Vista de relacionado.
		$view_relacionado = View::factory('buscador/relacionado');
		$view_relacionado->assign('post', $model_post->as_array());
		$vista->assign('relacionado', $view_relacionado->parse());
		unset($view_relacionado);

		// Listado de categorias.
		$mc = new Model_Categoria;
		$vista->assign('categorias', $mc->lista());
		unset($mc);
		$vista->assign('categoria', 'todos');
		$vista->assign('usuario', '');

		// Menu.
		$this->template->assign('master_bar', parent::base_menu());
		$this->template->assign('top_bar', Controller_Home::submenu('buscador'));

		// Asignamos la vista.
		$this->template->assign('contenido', $vista->parse());
	}

}