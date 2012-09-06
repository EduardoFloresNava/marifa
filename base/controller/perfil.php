<?php
/**
 * perfil.php is part of Marifa.
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
 * Visión del perfil del usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Perfil extends Controller {

	/**
	 * Modelo del usuario dueño del perfil.
	 * @var Model_Usuario
	 */
	protected $usuario;

	/**
	 * Cargamos el usuario. En caso de no existir vamos a la portada.
	 * @param string $usuario ID o nick del usuario.
	 */
	protected function cargar_usuario($usuario)
	{
		// Cargamos el modelo del usuario
		$model_usuario = new Model_Usuario;

		// Tratamos de cargar el usuario por su nick
		if ( ! $model_usuario->load_by_nick($usuario))
		{
			Request::redirect('/');
		}

		// Hacemos global para trabajar.
		$this->usuario = $model_usuario;
	}

	/**
	 * Obtenemos el menu del perfil del usuario.
	 * @param string $activo Elemento activo.
	 * @return array
	 */
	protected function submenu_categorias($activo = NULL)
	{
		if ($activo === NULL)
		{
			$call = Request::current();
			$activo = $call['action'];
			unset($call);
		}

		$usuario = $this->usuario->get('nick');
		return array(
			'muro' => array('link' => '/perfil/muro/'.$usuario, 'caption' => 'Muro', 'active' => $activo == 'muro'),
			'actividad' => array('link' => '/perfil/actividad/'.$usuario, 'caption' => 'Actividad', 'active' => $activo == 'actividad'),
			'informacion' => array('link' => '/perfil/index/'.$usuario, 'caption' => 'Información', 'active' =>  $activo == 'informacion' || $activo == 'index'),
			'posts' => array('link' => '/perfil/posts/'.$usuario, 'caption' => 'Posts', 'active' =>  $activo == 'posts'),
			'seguidores' => array('link' => '/perfil/seguidores/'.$usuario, 'caption' => 'Seguidores', 'active' =>  $activo == 'seguidores'),
			'siguiendo' => array('link' => '/perfil/siguiendo/'.$usuario, 'caption' => 'Siguiendo', 'active' =>  $activo == 'siguiendo'),
			'medallas' => array('link' => '/perfil/medallas/'.$usuario, 'caption' => 'Medallas', 'active' =>  $activo == 'medallas'),
		);
	}

	/**
	 * Portada del perfil del usuario.
	 * @param string $usuario ID o nick del usuario.
	 */
	public function action_index($usuario)
	{
		// Cargamos el usuario.
		$usuario = $this->cargar_usuario($usuario);

		// Cargamos la vista base.
		$base_view = View::factory('perfil/base');

		// Información general del usuario.
		$base_view->assign('usuario', $this->usuario->as_array());

		$base_view->assign('menu', $this->submenu_categorias());

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/index');



		// Seteamos la información en la vista base.
		$base_view->assign('contenido', $information_view->parse());
		unset($information_view);

		// Acciones para menu offline.
		if ( ! Session::is_set('usuario_id'))
		{
			// Seteamos menu offline.
			$this->template->assign('master_bar', parent::base_menu_logout('posts'));
			// $this->template->assign('top_bar', $this->submenu_logout('inicio'));
		}
		else
		{
			// Seteamos menu offline.
			$this->template->assign('master_bar', parent::base_menu_login('posts'));
			// $this->template->assign('top_bar', $this->submenu_login('inicio'));
		}

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$usuario);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $base_view->parse());
	}

}
