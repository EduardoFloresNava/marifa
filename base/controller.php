<?php
/**
 * controller.php is part of Marifa.
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
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador base, sirve para exponer un método a todos los controladores.
 * También permite iniciar varaibles comunes a todos los controladores.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Controller {

	/**
	 * Plantilla Base.
	 * @var RainTPL
	 */
	protected $template;

	/**
	 * Cargamos la plantilla base.
	 */
	public function before()
	{
		// Cargamos la plantilla base.
		$this->template = View::factory('template');

		// Cargo las noticias.
		$noticia = Model_Noticia::get_active();
		if ($noticia !== NULL)
		{
			$this->template->assign('noticia', Decoda::procesar($noticia->contenido));
		}
		unset($noticia);

		// Cargo nombre del sitio.
		$model_config = new Model_Configuracion;
		$this->template->assign('brand', $model_config->get('nombre', 'Marifa'));
		$this->template->assign('brand_title', $model_config->get('nombre', 'Marifa'));

		// Acciones para menu offline.
		if ( ! Usuario::is_login())
		{
			// Seteamos menu offline.
			$this->template->assign('user_header', View::factory('header/logout')->parse());
		}
		else
		{
			$this->template->assign('user_header', $this->make_user_header()->parse());
		}
		$this->template->assign('contenido', '');

		// Seteo si es mantenimiento.
		$m = new Mantenimiento;
		$this->template->assign('is_locked', $m->is_locked());
		unset($m);
	}

	/**
	 * Generamos el menu de usuario a colocar en la cabecera.
	 * @return RainTPL
	 */
	protected function make_user_header()
	{
		// Cargamos la vista.
		$vista = View::factory('header/login');

		// Cargamos el usuario y sus datos.
		$vista->assign('usuario', Usuario::usuario()->as_array());

		// Sucesos.
		$lst = Suceso_Barra::obtener_listado(Usuario::$usuario_id, 1, 20);

		$eventos = array();
		foreach ($lst as $v)
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
			$suceso_vista = View::factory('suceso/barra/'.$tipo);

			// Asigno los datos del usuario actual.
			$suceso_vista->assign('actual', Usuario::usuario()->as_array());

			// Asigno información del suceso.
			$suceso_vista->assign('suceso', $s_data);

			// Datos del suceso.
			$suceso_vista->assign('fecha', $v->fecha);

			// Agregamos el evento.
			$eventos[] = array('id' => $v->id, 'desplegado' => $v->desplegado, 'html' => $suceso_vista->parse());
		}
		$vista->assign('sucesos', $eventos);

		// Cantidad de sucesos nuevos.
		$vista->assign('cantidad_sucesos', count($eventos));
		unset($lst, $eventos);

		// Listado de mensajes.
		$model_mensajes = new Model_Mensaje;
		$vista->assign('mensajes_nuevos', $model_mensajes->total_recibidos(Usuario::$usuario_id, Model_Mensaje::ESTADO_NUEVO));
		$msg_rst = $model_mensajes->recibidos(Usuario::$usuario_id, 1, 5);

		$msg_event = array();
		foreach ($msg_rst as $v)
		{
			// Datos del post.
			$aux = $v->as_array();
			$aux['emisor'] = $v->emisor()->as_array();

			switch ($aux['estado'])
			{
				case 0:
					$aux['estado_string'] = 'nuevo';
					break;
				case 1:
					$aux['estado_string'] = 'leido';
					break;
				case 2:
					$aux['estado_string'] = 'respondido';
					break;
				case 3:
					$aux['estado_string'] = 'reenviado';
					break;
			}

			$msg_event[] = $aux;
		}
		$vista->assign('mensajes', $msg_event);
		unset($msg_event, $msg_rst);

		return $vista;
	}

	/**
	 * Mostramos el template.
	 */
	public function after()
	{
		// Eventos flash.
		foreach (array('flash_success', 'flash_info', 'flash_error') as $k)
		{
			if (isset($_SESSION[$k]))
			{
				$this->template->assign($k, get_flash($k));
			}
		}

		if (is_object($this->template) && ! Request::is_ajax())
		{
			DEBUG || $this->template->assign('execution', get_readable_file_size(memory_get_peak_usage() - START_MEMORY));
			$this->template->show();
		}
	}

	/**
	 * Menu principal.
	 * @param string $selected Clave seleccionada.
	 * @return array
	 */
	protected function base_menu($selected = NULL)
	{
		$data = array();

		// Listado de elementos ONLINE.
		if (Usuario::is_login())
		{
			$data['inicio'] = array('link' => '/perfil/', 'caption' => 'Inicio', 'icon' => 'home', 'active' => FALSE);
		}

		// Listado de elemento OFFLINE.
		$data['posts'] = array('link' => '/', 'caption' => 'Posts', 'icon' => 'book', 'active' => FALSE);
		$data['fotos'] = array('link' => '/foto/', 'caption' => 'Fotos', 'icon' => 'picture', 'active' => FALSE);
		$data['tops'] = array('link' => '/tops/', 'caption' => 'TOPs', 'icon' => 'signal', 'active' => FALSE);

		// Listado elemento por permisos.
		if (Controller_Moderar_Home::permisos_acceso())
		{
			$data['moderar'] = array('link' => '/moderar/', 'caption' => 'Moderación', 'icon' => 'eye-open', 'active' => FALSE, 'tipo' => 'important', 'cantidad' => Controller_Moderar_Home::cantidad_pendiente());
		}

		if (Controller_Admin_Home::permisos_acceso())
		{
			$data['admin'] = array('link' => '/admin/', 'caption' => 'Administración', 'icon' => 'certificate', 'active' => FALSE);
		}

		// Seleccionamos elemento.
		if ($selected !== NULL && isset($data[$selected]))
		{
			$data[$selected]['active'] = TRUE;
		}
		else
		{
			$data['posts']['active'] = TRUE;
		}

		return $data;
	}
}
