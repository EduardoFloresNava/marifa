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
	 * Bloque con elementos que deben cargarse en la cabecera.
	 * Cada elemento debe ser un objeto del tipo View.
	 * @var array
	 */
	public $header = array();

	/**
	 * Bloque con elementos que deben cargarse en el pie de página.
	 * Cada elemento debe ser un objeto del tipo View.
	 * @var array
	 */
	public $footer = array();

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
		$this->template->assign('brand', $model_config->get('nombre', __('Marifa', FALSE)));
		$this->template->assign('brand_title', $model_config->get('nombre', __('Marifa', FALSE)));
		$this->template->assign('descripcion', $model_config->get('descripcion', __('Tu comunidad de forma simple', FALSE)));

		// Cargo datos de contacto.
		if ($model_config->get('contacto_tipo', 1) == 0)
		{
			$this->template->assign('contacto_url', trim($model_config->get('contacto_valor', '')));
		}
		else
		{
			$this->template->assign('contacto_url', SITE_URL.'/contacto');
		}

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
		$this->template->assign('is_locked', Mantenimiento::is_locked() || Mantenimiento::is_locked(FALSE));
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

		// Listado de favoritos.
		$vista->assign('favoritos', array());

		return $vista;
	}

	/**
	 * Mostramos el template.
	 */
	public function after()
	{
		// Verificamos que sea un objeto.
		if (is_object($this->template))
		{
			// Eventos flash.
			foreach (array('flash_success', 'flash_info', 'flash_error') as $k)
			{
				if (isset($_SESSION[$k]))
				{
					$this->template->assign($k, get_flash($k));
				}
			}

			// Proceso los elementos de la cabecera y el pie.
			$header = '';
			foreach ($this->header as $v)
			{
				$header .= (string) $v;
			}
			$this->template->assign('header', $header);
			unset($v, $header);

			$footer = '';
			foreach ($this->footer as $v)
			{
				$footer .= (string) $v;
			}
			$this->template->assign('footer', $footer);
			unset($v, $footer);
		}

		// Compilo y muestro la plantilla.
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
		// Creo el menu.
		$menu = new Menu('base_menu');

		// Listado de elementos ONLINE.
		if (Usuario::is_login())
		{
			$menu->element_set(__('Inicio', FALSE), '/perfil/', 'inicio');
		}

		// Listado de elemento OFFLINE.
		$menu->element_set(__('Posts', FALSE), '/', 'posts');

		// Verifico sección de fotos habilitada y su privacidad.
		if (Utils::configuracion()->get('habilitar_fotos', 1) && (Utils::configuracion()->get('privacidad_fotos', 1) || Usuario::is_login()))
		{
			$menu->element_set(__('Fotos', FALSE), '/foto/', 'fotos');
		}
		$menu->element_set(__('TOPs', FALSE), '/tops/', 'tops');

		// Listado elemento por permisos.
		if (Controller_Moderar_Home::permisos_acceso())
		{
			$menu->element_set(__('Moderar', FALSE), '/moderar/', 'moderar', NULL, Controller_Moderar_Home::cantidad_pendiente());
		}

		if (Controller_Admin_Home::permisos_acceso())
		{
			$menu->element_set(__('Administración', FALSE), '/admin/', 'admin');
		}

		return $menu->as_array($selected == NULL ? 'posts' : $selected);
	}
}
