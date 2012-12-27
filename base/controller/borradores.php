<?php
/**
 * borradores.php is part of Marifa.
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
 * Controlador para la gestión de los borradores del usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Borradores extends Controller {

	/**
	 * Verifico que esté logueado para poder acceder a las secciones.
	 */
	public function before()
	{
		// Verifico que esté logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para poder ver los borradores.');
			Request::redirect('/usuario/login');
		}
		parent::before();
	}

	/**
	 * Portada de los borradores.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_index($pagina)
	{
		// Cargamos la portada.
		$vista = View::factory('borradores/index');

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos datos de posts.
		$model_post = new Model_Post;

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de borradores.
		list($borradores, $total) = $model_post->borradores(Usuario::$usuario_id, $pagina, $cantidad_por_pagina);

		// Que sea un número de página válido.
		if (count($borradores) == 0 && $pagina != 1)
		{
			Request::redirect('/borradores');
		}

		// Paginación.
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/borradores/index/%d'));
		unset($paginador);

		// Obtengo información de los borradores.
		foreach ($borradores as $k => $v)
		{
			if (is_array($v))
			{
				$a = $v['post']->as_array();
				$a['categoria'] = $v['post']->categoria()->as_array();
				// $a['moderado'] = $v['moderado']->as_array();
				// $a['motivo'] = $v['moderado']->moderacion()->as_array();
			}
			else
			{
				$a = $v->as_array();
				$a['categoria'] = $v->categoria()->as_array();
			}
			$borradores[$k] = $a;
		}

		// Seteo parámetros a la vista.
		$vista->assign('borradores', $borradores);
		unset($borradores);

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $vista->parse());

		// Título de la página.
		$this->template->assign('title', 'Borradores');
	}

}
