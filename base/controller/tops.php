<?php
/**
 * tops.php is part of Marifa.
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
 * Controlador de la portada.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Tops extends Controller {

	/**
	 * Menu secundario.
	 * @param string $activo Elemento activo actualmente.
	 * @return array
	 */
	protected function submenu($activo = NULL)
	{
		if ($activo === NULL)
		{
			$call = Request::current();
			$activo = $call['action'];
			unset($call);
		}

		return array(
			'posts' => array('link' => '/tops/', 'caption' => __('Posts', FALSE), 'active' => $activo == 'posts' || $activo == 'index'),
			'usuarios' => array('link' => '/tops/usuarios/', 'caption' => __('Usuarios', FALSE), 'active' =>  $activo == 'usuarios'),
		);
	}

	/**
	 * Mostramos tops de posts.
	 * @param string $categoria Categoria para filtar los tops de post.
	 * @param int $periodo Período para filtar post.
	 */
	public function action_index($categoria, $periodo)
	{
		// Cargamos la portada.
		$portada = View::factory('tops/index');

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu('tops'));
		$this->template->assign('top_bar', $this->submenu());

		// Cargo las categorias.
		$model_categorias = new Model_Categoria;

		// Seteo el listado en la vista.
		$portada->assign('categorias', $model_categorias->lista());

		// Obtengo la categoria por POST.
		//TODO: hacer una mejora con jQuery.
		if (isset($_POST['categoria']))
		{
			$categoria = $_POST['categoria'];
		}

		// Verifico si existe la categoria.
		$categoria = (trim($categoria) == '') ? NULL : trim($categoria);
		if ($categoria !== NULL && $categoria != 'todas')
		{
			if ( ! $model_categorias->load_by_seo($categoria))
			{
				Request::redirect('/tops');
			}
			else
			{
				$categoria_id = $model_categorias->id;
				$categoria = $model_categorias->seo;
			}
		}
		else
		{
			$categoria = 'todas';
			$categoria_id = NULL;
		}

		// Seteo la categoria actual.
		$portada->assign('categoria', $categoria);

		// Obtengo el período.
		$periodo = (int) $periodo;

		// Verifico por un válido.
		if ($periodo != 0 && $periodo != 1 && $periodo != 2 && $periodo != 3 && $periodo != 4)
		{
			Request::redirect('/tops');
		}

		$portada->assign('periodo', $periodo);

		// Cargo modelo de posts.
		$model_post = new Model_Post;

		$portada->assign('puntos', $model_post->top_puntos($categoria_id, $periodo));
		$portada->assign('favoritos', $model_post->top_favoritos($categoria_id, $periodo));
		$portada->assign('seguidores', $model_post->top_seguidores($categoria_id, $periodo));
		$portada->assign('comentarios', $model_post->top_comentarios($categoria_id, $periodo));


		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

	/**
	 * Mostramos tops de usuarios.
	 * @param string $categoria Categoria para filtar los tops de usuario.
	 * @param int $periodo Período para filtar usuarios.
	 */

	public function action_usuarios($categoria, $periodo)
	{
		// Cargamos la portada.
		$portada = View::factory('tops/usuarios');

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu('tops'));
		$this->template->assign('top_bar', $this->submenu());

		// Cargo las categorias.
		$model_categorias = new Model_Categoria;

		// Seteo el listado en la vista.
		$portada->assign('categorias', $model_categorias->lista());

		// Obtengo la categoria por POST.
		//TODO: hacer una mejora con jQuery.
		if (isset($_POST['categoria']))
		{
			$categoria = $_POST['categoria'];
		}

		// Verifico si existe la categoria.
		$categoria = (trim($categoria) == '') ? NULL : trim($categoria);
		if ($categoria !== NULL && $categoria != 'todas')
		{
			if ( ! $model_categorias->load_by_seo($categoria))
			{
				Request::redirect('/tops');
			}
			else
			{
				$categoria_id = $model_categorias->id;
				$categoria = $model_categorias->seo;
			}
		}
		else
		{
			$categoria = 'todas';
			$categoria_id = NULL;
		}

		// Seteo la categoria actual.
		$portada->assign('categoria', $categoria);

		// Obtengo el período.
		$periodo = (int) $periodo;

		// Verifico por un válido.
		if ($periodo != 0 && $periodo != 1 && $periodo != 2 && $periodo != 3 && $periodo != 4)
		{
			Request::redirect('/tops');
		}

		$portada->assign('periodo', $periodo);

		// Cargo modelo de usuarios.
		$model_usuario = new Model_Usuario;

		$portada->assign('puntos', $model_usuario->top_puntos($categoria_id, $periodo));
		$portada->assign('seguidores', $model_usuario->top_seguidores($periodo));


		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

}
