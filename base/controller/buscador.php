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
	 * @param string $query Valores a buscar.
	 * @see action_q
	 * @return mixed
	 */
	public function action_index($query)
	{
		return $this->action_q($query);
	}

	/**
	 * Buscamos un elemento.
	 * @param string $query Cadena a buscar.
	 * @return mixed
	 */
	public function action_q($query, $pagina)
	{
		// Verificamos si es POST y redireccionamos.
		if (Request::method() == 'POST')
		{
			Request::redirect('/buscador/q/'.isset($_POST['q']) ? urlencode($_POST['q']) : NULL);
		}

		// Limpiamos la consulta.
		$query = preg_replace('/\s+/', ' ', urldecode(trim($query)));

		// Cargamos la vista.
		$vista = View::factory('buscador/index');

		// Verificamos si hay consulta.
		if ( ! empty($query))
		{
			// Formateamos la pagina.
			$pagina = abs( (int) $pagina);

			// Cantidad por pagina.
			$cpp = 20;

			// Realizamos la busqueda.
			$model_post = new Model_Post;
			list($listado, $cantidad) = $model_post->buscar($query, ($pagina > 0) ? $pagina : 1, $cpp);

			// Armamos paginacion.
			$paginacion = new Paginator($cantidad, $cpp);

			// Setamos paginador.
			$vista->assign('paginacion', $paginacion->paginate($pagina));
			$vista->assign('total', $cantidad);
			$vista->assign('cantidad', ($cpp < $cantidad) ? $cpp : $cantidad);
			$vista->assign('actual', ($pagina > 0) ? $pagina : 1);

			// Limpieza de sobrantes.
			unset($paginacion);

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

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		//$this->template->assign('top_bar', $this->submenu('buscador'));

		// Asignamos la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Post con las mismas etiquetas que el selecionado.
	 */
	public function action_relacionados()
	{

	}

}