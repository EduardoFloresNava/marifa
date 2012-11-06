<?php
/**
 * notificaciones.php is part of Marifa.
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
 * Controlador para manejo de las notificaciones.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Notificaciones extends Controller {

	/**
	 * Constructor de la clase.
	 * Verificamos este logueado para ver sus sucesos.
	 */
	public function __construct()
	{
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder acceder a tus notificaciones.';
			Request::redirect('/usuario/login');
		}
		parent::__construct();
	}

	/**
	 * Portada del sitio.
	 * @param int $pagina Número de página para lo últimos posts.
	 */
	public function action_index($pagina)
	{
		// Cargamos la portada.
		$view = View::factory('notificaciones/index');

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos notificaciones.
		$sucesos = Suceso_Barra::obtener_listado_completo(Usuario::$usuario_id, $pagina, $cantidad_por_pagina);

		// Verifivo validez de la pagina.
		if (count($sucesos) == 0 && $pagina != 1)
		{
			Request::redirect('/notificaciones/');
		}

		// Paginación.
		$paginador = new Paginator(Suceso_Barra::cantidad_completa(Usuario::$usuario_id), $cantidad_por_pagina);
		$view->assign('paginacion', $paginador->get_view($pagina, '/notificaciones/index/%d'));
		unset($paginador);

		// Proceso el listado de sucesos.
		$eventos = array();
		foreach ($sucesos as $v)
		{
			// Obtengo información del suceso.
			$s_data = Suceso_Perfil::procesar($v);

			// Verifico su existencia.
			if ($s_data === NULL)
			{
				continue;
			}

			// Obtenemos el tipo de suceso.
			$tipo = $v->tipo;

			// Cargamos la vista.
			$suceso_vista = View::factory('/suceso/notificaciones/'.$tipo);

			// Asigno los datos del usuario actual.
			$suceso_vista->assign('actual', Usuario::usuario()->as_array());

			// Asigno información del suceso.
			$suceso_vista->assign('suceso', $s_data);

			// Datos del suceso.
			$suceso_vista->assign('fecha', $v->fecha);
			$suceso_vista->assign('visto', $v->visto);


			// Agregamos el evento.
			$eventos[] = $suceso_vista->parse();
		}

		$view->assign('sucesos', $eventos);
		unset($sucesos);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Marcamos las notificaciones como vistas.
	 */
	public function action_vistas()
	{
		// Cargo sucesos.
		$model_suceso = new Model_Suceso;

		// Seteo como vistas.
		$model_suceso->vistas(Usuario::$usuario_id);

		// Notifico y redirecciono.
		$_SESSION['flash_message'] = 'Las notificaciones han sido marcadas como leidas correctamente.';
		Request::redirect('/notificaciones/');
	}
}
