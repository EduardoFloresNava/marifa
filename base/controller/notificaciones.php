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
	public function before()
	{
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para poder acceder a tus notificaciones.');
			Request::redirect('/usuario/login');
		}
		parent::before();
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
			$s_data = Suceso_Barra::procesar($v);

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

			// Verifico vista superior para sucesos.
			try {
				$vc = View::factory('/suceso/notificaciones');
				$vc->assign('contenido', $suceso_vista->parse());

				// Datos del suceso.
				$vc->assign('fecha', $v->fecha);
				$vc->assign('visto', $v->visto);
				$eventos[] = $vc->parse();
			}
			catch (Exception $e)
			{
				$eventos[] = $suceso_vista->parse();
			}
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
		add_flash_message(FLASH_SUCCESS, 'Las notificaciones han sido marcadas como leidas correctamente.');
		Request::redirect('/notificaciones/');
	}

	/**
	 * Marcamos como desplegadas las notificaciones especificadas.
	 */
	public function action_desplegadas()
	{
		// Solo ajax.
		/**if ( ! Request::is_ajax())
		{
			Request::redirect('/notificaciones/');
		}*/

		// Solo POST.
		if (Request::method() != 'POST')
		{
			Request::redirect('/notificaciones/');
		}

		// Evito plantilla base.
		$this->template = NULL;

		// Listado de sucesos.
		$sucesos = isset($_POST['sucesos']) ? $_POST['sucesos']: NULL;

		// Verifico parámetro.
		if ( ! is_array($sucesos))
		{
			die('false');
		}

		// Proceso el listado.
		$model_suceso = new Model_Suceso;

		$rst = array();

		foreach ($sucesos as $s)
		{
			$s = (int) $s;

			// Verifico existencia.
			if ( ! $model_suceso->existe(array('id' => $s, 'usuario_id' => Usuario::$usuario_id)))
			{
				continue;
			}

			$rst[] = $s;

			// Actualizo el suceso.
			$model_suceso->desplegado($s);
		}

		// Informo resultado correcto.
		die(json_encode($rst));
	}

	/**
	 * Obtenemos listado de notificaciones no desplegadas.
	 */
	public function action_sin_desplegar()
	{
		// Solo ajax.
		/**if ( ! Request::is_ajax())
		{
			Request::redirect('/notificaciones/');
		}*/

		// Cargamos la portada.
		$view = View::factory('notificaciones/sin_desplegar');

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos notificaciones.
		$sucesos = Suceso_Barra::obtener_listado_sin_desplegar(Usuario::$usuario_id, 1, $cantidad_por_pagina);

		// Proceso el listado de sucesos.
		$eventos = array();
		foreach ($sucesos as $v)
		{
			// Obtengo información del suceso.
			$s_data = Suceso_Barra::procesar($v);

			// Verifico su existencia.
			if ($s_data === NULL)
			{
				continue;
			}

			// Obtenemos el tipo de suceso.
			$tipo = $v->tipo;

			// Cargamos la vista.
			$suceso_vista = View::factory('/suceso/barra/'.$tipo);

			// Asigno los datos del usuario actual.
			$suceso_vista->assign('actual', Usuario::usuario()->as_array());

			// Asigno información del suceso.
			$suceso_vista->assign('suceso', $s_data);

			// Datos del suceso.
			$suceso_vista->assign('fecha', $v->fecha);
			$suceso_vista->assign('visto', $v->visto);

			// Agregamos el evento.
			$eventos[] = array('id' => $v->id, 'html' => json_encode($suceso_vista->parse()));
		}

		$view->assign('sucesos', $eventos);
		unset($sucesos);

		// Evito plantilla base.
		$this->template = NULL;

		// Envio resultado.
		$view->show();

		exit;
	}
}
