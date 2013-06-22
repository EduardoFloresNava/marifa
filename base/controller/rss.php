<?php
/**
 * rss.php is part of Marifa.
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
 * @since		Versión 0.3
 * @filesource
 * @package		Marifa\Base
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador para generar contenido RSS
 *
 * @since      Versión 0.3
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Rss extends Controller {

	public function after()
	{
		// Indico que la salida es XML.
		header("Content-type: text/xml");

		// Realizo tareas post-controlador.
		parent::after();
	}

	/**
	 * Obtengo listado de posts recientes para RSS.
	 */
	public function action_posts()
	{
		// Cargo la vista.
		$vista = View::factory('rss/posts');

		// Obtengo la cantidad de posts a mostrar.
		$cantidad_posts = Utils::configuracion()->get('elementos_pagina', 20);

		// Modelos de posts.
		$model_post = new Model_Post;

		// Últimos posts
		$post_list = $model_post->obtener_ultimos(1, $cantidad_posts, NULL);

		// Extendemos la información de los posts.
		foreach ($post_list as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['puntos'] = $v->puntos();
			$a['comentarios'] = $v->cantidad_comentarios(Model_Post_Comentario::ESTADO_VISIBLE);
			$a['categoria'] = $v->categoria()->as_array();

			$post_list[$k] = $a;
		}

		// Asigno a la vista.
		$vista->assign('posts', $post_list);

		// Evito salida de la plantilla base.
		$this->template = NULL;

		// Muestro la salida.
		$vista->show();
	}

	/**
	 * Obtengo listado de fotos recientes para RSS.
	 */
	public function action_fotos()
	{
		if (Utils::configuracion()->get('habilitar_fotos', 1) != 1 || Utils::configuracion()->get('privacidad_fotos', 1) != 1)
		{
			// Indico que no se puede mostrar.
			header('HTTP/1.1 404 Not Found');
			echo "Not Found";
			die();
		}

		// Cargo la vista.
		$vista = View::factory('rss/fotos');

		// Obtengo la cantidad de fotos a mostrar.
		$cantidad_fotos = Utils::configuracion()->get('elementos_pagina', 20);

		// Modelos de fotos.
		$model_foto = new model_Foto;

		// Últimos fotos
		$foto_list = $model_foto->obtener_ultimas(1, $cantidad_fotos);

		// Extendemos la información de los fotos.
		foreach ($foto_list as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['categoria'] = $v->categoria()->as_array();

			$foto_list[$k] = $a;
		}

		// Asigno a la vista.
		$vista->assign('fotos', $foto_list);

		// Evito salida de la plantilla base.
		$this->template = NULL;

		// Muestro la salida.
		$vista->show();
	}
}
