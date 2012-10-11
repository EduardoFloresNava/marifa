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
 * Controlador para gestionar usuarios y buscar contenido.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Moderar
 */
class Base_Controller_Moderar_Gestion extends Controller {

	public function action_usuarios($pagina)
	{
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			$_SESSION['flash_error'] = 'No tienes permiso para acceder a esa sección.';
			Request::redirect('/');
		}

		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('moderar/gestion/usuarios');

		// Modelo de suspensiones.
		$model_suspension = new Model_Usuario_Suspension;

		// Limpio antiguos.
		Model_Usuario_Suspension::clean();

		// Cargamos el listado de posts.
		$lst = $model_suspension->listado($pagina, $cantidad_por_pagina);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Paginación.
		$total = Model_Usuario_Suspension::cantidad();
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
			$a['motivo'] = Decoda::procesar($a['motivo']);
			$a['usuario'] = $v->usuario()->as_array();
			$a['moderador'] = $v->moderador()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de suspensiones.
		$vista->assign('suspensiones', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion_usuarios'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Terminamos la suspensión de un usuario.
	 * @param int $usuario ID del usuario a quitar la suspensión.
	 */
	public function action_terminar_suspension($usuario)
	{
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			$_SESSION['flash_error'] = 'No tienes permiso para acceder a esa sección.';
			Request::redirect('/');
		}

		// Valido el ID.
		$usuario = (int) $usuario;

		// Verifico que exista el usuario.
		$model_usuario = new Model_Usuario($usuario);

		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario es incorrecto.';
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Verifico el estado.
		if ($model_usuario->estado !== Model_Usuario::ESTADO_SUSPENDIDA)
		{
			$_SESSION['flash_error'] = 'La cuenta no se encuentra suspendida.';
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Borramos la suspensión.
		$model_usuario->suspension()->anular();

		$_SESSION['flash_success'] = 'Suspensión anulada correctamente.';
		Request::redirect('/moderar/gestion/usuarios');
	}

}