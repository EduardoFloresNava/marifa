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
		if ( ! Session::is_set('usuario_id'))
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
		$usuario = new Model_Usuario( (int) Session::get('usuario_id'));
		$vista->assign('usuario', $usuario->as_array());

		// Sucesos.
		$model_sucesos = new Model_Suceso;
		$lst = $model_sucesos->obtener_by_usuario( (int) Session::get('usuario_id'));

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

		return $vista;
	}

	/**
	 * Mostramos el template.
	 */
	public function __destruct()
	{
		if (is_object($this->template) && ! Request::is_ajax())
		{
			$this->template->show();
		}
	}

	/**
	 * Menu principal para el estado desconectado.
	 * @param string $activo Clave activa.
	 * @return array
	 */
	protected function base_menu_logout($activo = NULL)
	{
		return array(
			'posts' => array('link' => '/', 'caption' => 'Posts', 'active' => $activo == 'posts'),
			'comunidades' => array('link' => '/', 'caption' => 'Comunidades', 'active' =>  $activo == 'comunidades'),
			'fotos' => array('link' => '/foto/', 'caption' => 'Fotos', 'active' =>  $activo == 'fotos'),
			'tops' => array('link' => '/', 'caption' => 'TOPs', 'active' =>  $activo == 'tops'),
		);
	}

	/**
	 * Menu principal para el estado conectado.
	 * @param string $activo Clave activa.
	 * @return array
	 */
	protected function base_menu_login($activo = NULL)
	{
		//TODO: administración y moderación.
		return array(
			'inicio' => array('link' => '/mi', 'caption' => 'Inicio', 'active' => $activo == 'inicio'),
			'posts' => array('link' => '/', 'caption' => 'Posts', 'active' => $activo == 'posts'),
			'comunidades' => array('link' => '/', 'caption' => 'Comunidades', 'active' =>  $activo == 'comunidades'),
			'fotos' => array('link' => '/foto/', 'caption' => 'Fotos', 'active' =>  $activo == 'fotos'),
			'tops' => array('link' => '/', 'caption' => 'TOPs', 'active' =>  $activo == 'tops'),
		);
	}
}
