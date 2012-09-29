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
	public function __construct()
	{
		// Cargamos la plantilla base.
		$this->template = View::factory('template');

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
		/**
		$model_sucesos = new Model_Suceso;
		$lst = $model_sucesos->obtener_by_usuario( (int) Session::get('usuario_id'));
		unset($model_sucesos);

		$eventos = array();
		foreach ($lst as $v)
		{
			// Obtengo información del suceso.
			$s_data = $v->get_data();

			// Verifico su existencia.
			if ($s_data === NULL)
			{
				continue;
			}

			// Obtenemos el tipo de suceso.
			$tipo = $v->as_object()->tipo;

			// Cargamos la vista.
			$suceso_vista = View::factory('suceso/'.$tipo);

			// Asigno los datos del usuario actual.
			$suceso_vista->assign('actual', $usuario->as_array());

			// Asigno información del suceso.
			$suceso_vista->assign('suceso', $s_data);

			// Datos del suceso.
			$suceso_vista->assign('fecha', $v->fecha);

			// Agregamos el evento.
			$eventos[] = $suceso_vista->parse();
		}
		$vista->assign('sucesos', $eventos);
		unset($lst, $eventos);*/
		// Su carga va a ir por AJAX.
		$vista->assign('sucesos', array());


		// Listado de mensajes.
		$model_mensajes = new Model_Mensaje;
		$msg_rst = $model_mensajes->recibidos(Usuario::usuario()->id);

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
	public function __destruct()
	{
		if (is_object($this->template) && ! Request::is_ajax() && error_get_last() !== NULL)
		{
			if (DEBUG)
			{
				$this->template->assign('execution', get_readable_file_size(memory_get_peak_usage() - START_MEMORY));
			}
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
			$data['inicio'] = array('link' => '/perfil/', 'caption' => 'Inicio', 'active' => FALSE);
		}

		// Listado de elemento OFFLINE.
		$data['posts'] = array('link' => '/', 'caption' => 'Posts', 'active' => FALSE);
		$data['comunidades'] = array('link' => '/comunidad/', 'caption' => 'Comunidades', 'active' => FALSE);
		$data['fotos'] = array('link' => '/foto/', 'caption' => 'Fotos', 'active' => FALSE);
		$data['tops'] = array('link' => '/tops/', 'caption' => 'TOPs', 'active' => FALSE);

		// Listado elemento por permisos.
		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_ACCESO_PANEL_MODERACION))
		{
			$data['moderar'] = array('link' => '/moderar/', 'caption' => 'Moderación', 'active' => FALSE);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_ADMINISTRADOR))
		{
			$data['admin'] = array('link' => '/admin/', 'caption' => 'Administración', 'active' => FALSE);
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
