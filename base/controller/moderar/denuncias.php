<?php
/**
 * home.php is part of Marifa.
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
 * Controlador para moderar las denuncias.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Moderar
 */
class Base_Controller_Moderar_Denuncias extends Controller {

	public function action_index()
	{
		//TODO: hacer portada con estadisticas.
	}

	/**
	 * Listado de posts con denuncias.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_posts($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

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
		$vista = View::factory('moderar/denuncias/posts');

		$vista->assign('tipo', $tipo);
		$vista->assign('cantidad_rechazados', Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_RECHAZADA));
		$vista->assign('cantidad_aprobados', Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_APLICADA));

		// Modelo de posts.
		$model_denuncias = new Model_Post_Denuncia;

		// Cargamos el listado de posts.
		$lst = $model_denuncias->listado($pagina, $cantidad_por_pagina, $tipo);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/denuncias/posts');
		}

		// Paginación.
		$total = Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE);
		$vista->assign('cantidad_pendientes', $total);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las denuncias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['post'] = $v->post()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de denuncias.
		$vista->assign('denuncias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vermos los detalles de una denuncia.
	 */
	public function action_detalle_post($denuncia)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Cargo la denuncia.
		$model_denuncia = new Model_Post_Denuncia($denuncia);

		// Verifico exista.
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'Denuncia incorrecta.';
			Request::redirect('/modedar/denuncias/posts');
		}

		// Cargo la vista.
		$vista = View::factory('moderar/denuncias/detalle_post');

		// Seteamos los datos.
		$vista->assign('denuncia', $model_denuncia->as_array());
		$vista->assign('denunciante', $model_denuncia->usuario()->as_array());
		$vista->assign('post', $model_denuncia->post()->as_array());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Cerramos una denuncia del usuario, puede ser rechazada o aceptada.
	 * @param int $denuncia ID de la denuncia a rechazar.
	 * @param bool $tipo Si fue aceptada 1, 0 si fue rechazada.
	 */
	public function action_cerrar_denuncia($denuncia, $tipo)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Verifico su existencia.
		$model_denuncia = new Model_Post_Denuncia($denuncia);
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'La denuncia es incorrecta.';
			Request::redirect('/moderar/denuncias/posts');
		}

		//TODO: verificar permisos.

		// Verifico el estado.
		if ($model_denuncia->estado !== Model_Post_Denuncia::ESTADO_PENDIENTE)
		{
			$_SESSION['flash_error'] = 'El estado de la denuncia no es correcto.';
			Request::redirect('/moderar/denuncias/posts');
		}

		if ($tipo == 0)
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_estado(Model_Post_Denuncia::ESTADO_RECHAZADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia rechazada correctamente.';
		}
		else
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_estado(Model_Post_Denuncia::ESTADO_APLICADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia aceptada correctamente.';
		}
		Request::redirect('/moderar/denuncias/posts');
	}

}
