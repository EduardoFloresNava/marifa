<?php
/**
 * favoritos.php is part of Marifa.
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
 * Controlador para la gestión de los favoritos del usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Favoritos extends Controller {

	/**
	 * Verificamos los permisos para acceder a la sección.
	 */
	public function before()
	{
		// Verifico que esté logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder acceder a tus favoritos.', FALSE));
			Request::redirect('/usuario/login');
		}
		parent::before();
	}

	/**
	 * Submenu.
	 * @param string $selected Elemento seleccionado.
	 */
	public static function submenu($selected = NULL)
	{
		// Creo el menu.
		$menu = new Menu('favoritos_menu');

		// Arreglo elementos.
		$menu->element_set(__('Posts', FALSE), '/favoritos/', 'posts', NULL, Usuario::usuario()->cantidad_favoritos_posts());
		$menu->element_set(__('Fotos', FALSE), '/favoritos/fotos/', 'fotos', NULL, Usuario::usuario()->cantidad_favoritos_fotos());

		// Devuelvo el menú.
		return $menu->as_array($selected);
	}

	/**
	 * Portada de los favoritos.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_index($pagina)
	{
		// Cargamos la portada.
		$vista = View::factory('favoritos/posts');

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de favoritos.
		$favoritos = Usuario::usuario()->listado_posts_favoritos($pagina, $cantidad_por_pagina);

		// Verifico que la página seleccionada sea válida.
		if (count($favoritos) == 0 && $pagina != 1)
		{
			Request::redirect('/favoritos/');
		}

		// Paginación.
		$paginador = new Paginator(Usuario::usuario()->cantidad_favoritos_posts(), $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/favoritos/index/%i'));
		unset($paginador);

		// Obtengo información de los favoritos.
		foreach ($favoritos as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['categoria'] = $v->categoria()->as_array();

			$favoritos[$k] = $a;
		}

		// Asigno parámetros a la vista.
		$vista->assign('favoritos', $favoritos);
		unset($favoritos);

		// Asigno el menú.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', self::submenu('posts'));

		// Asigno título.
		$this->template->assign('title', __('Favoritos - Posts', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Portada de los favoritos.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_fotos($pagina)
	{
		// Cargamos la portada.
		$vista = View::factory('favoritos/fotos');

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de favoritos.
		$favoritos = Usuario::usuario()->listado_fotos_favoritos($pagina, $cantidad_por_pagina);

		// Verifico que la página seleccionada sea válida.
		if (count($favoritos) == 0 && $pagina != 1)
		{
			Request::redirect('/favoritos/fotos/');
		}

		// Paginación.
		$paginador = new Paginator(Usuario::usuario()->cantidad_favoritos_fotos(), $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/favoritos/fotos/%d'));
		unset($paginador);

		// Obtengo información de los favoritos.
		foreach ($favoritos as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['categoria'] = $v->categoria()->as_array();

			$favoritos[$k] = $a;
		}

		// Asigno parámetros a la vista.
		$vista->assign('favoritos', $favoritos);
		unset($favoritos);

		// Asigno el menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', self::submenu('fotos'));

		// Asigno título.
		$this->template->assign('title', __('Favoritos - Fotos', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Quitamos un post de los favoritos.
	 * @param int $post ID del post a quitar de los favoritos.
	 */
	public function action_borrar_post($post = NULL)
	{
		// Cargo el post.
		$model_post = new Model_Post( (int) $post);

		// Verifico existencia.
		if ( ! $model_post->existe())
		{
			add_flash_message(FLASH_ERROR, __('El post que quiere quitar de sus favoritos no se encuentra disponible.', FALSE));
			Request::redirect('/favoritos/');
		}

		// Verifico sea favorito.
		if ( ! $model_post->es_favorito(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('El post que quiere quitar de sus favoritos no se encuentra disponible.', FALSE));
			Request::redirect('/favoritos/');
		}

		// Quito de favoritos.
		$model_post->quitar_favoritos(Usuario::$usuario_id);

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, __('El post se ha quitado correctamente de sus favoritos.', FALSE));
		Request::redirect('/favoritos/');
	}

	/**
	 * Quitamos una foto de los favoritos.
	 * @param int $foto ID de la foto a quitar de los favoritos.
	 */
	public function action_borrar_foto($foto = NULL)
	{
		// Cargo la foto.
		$model_foto = new Model_Foto( (int) $foto);

		// Verifico existencia.
		if ( ! $model_foto->existe())
		{
			add_flash_message(FLASH_ERROR, __('La foto que quiere quitar de sus favoritos no se encuentra disponible.', FALSE));
			Request::redirect('/favoritos/fotos/');
		}

		// Verifico sea favorito.
		if ( ! $model_foto->es_favorito(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('La foto que quiere quitar de sus favoritos no se encuentra disponible.', FALSE));
			Request::redirect('/favoritos/fotos/');
		}

		// Quito de favoritos.
		$model_foto->quitar_favoritos(Usuario::$usuario_id);

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, __('La foto se ha quitado correctamente de sus favoritos.', FALSE));
		Request::redirect('/favoritos/fotos/');
	}

}
