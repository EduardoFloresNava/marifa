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
		if ($usuario == NULL)
		{
			// Verificamos si estamos logueados.
			if ( ! Session::is_set('usuario_id'))
			{
				Request::redirect('/');
			}
			$model_usuario = new Model_Usuario( (int) Session::get('usuario_id'));
		}
		else
		{
			// Cargamos el modelo del usuario
			$model_usuario = new Model_Usuario;

			// Tratamos de cargar el usuario por su nick
			if ( ! $model_usuario->load_by_nick($usuario))
			{
				Request::redirect('/');
			}
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

		$usuario = (Session::get('usuario_id') == $this->usuario->id) ? '' : $this->usuario->get('nick');
		return array(
			'muro' => array('link' => '/perfil/index/'.$usuario, 'caption' => __('Muro', FALSE), 'active' => $activo == 'muro' || $activo == 'index'),
			'informacion' => array('link' => '/perfil/informacion/'.$usuario, 'caption' => __('Información', FALSE), 'active' =>  $activo == 'informacion'),
			'posts' => array('link' => '/perfil/posts/'.$usuario, 'caption' => __('Posts', FALSE), 'active' =>  $activo == 'posts'),
			'seguidores' => array('link' => '/perfil/seguidores/'.$usuario, 'caption' => __('Seguidores', FALSE), 'active' =>  $activo == 'seguidores'),
			// 'medallas' => array('link' => '/perfil/medallas/'.$usuario, 'caption' => __('Medallas', FALSE), 'active' =>  $activo == 'medallas'),
		);
	}

	/**
	 * Obtenemos el bloque superior del perfil.
	 * @param string $contenido Contenido de la plantilla.
	 * @return string Bloque parseado.
	 */
	protected function header_block($contenido)
	{
		// Cargamos la vista base.
		$base_view = View::factory('perfil/base');

		// Información general del usuario.
		$usuario = $this->usuario->as_array();
		$usuario['puntos'] = $this->usuario->cantidad_puntos();
		$usuario['seguidores'] = $this->usuario->cantidad_seguidores();
		$usuario['posts'] = $this->usuario->cantidad_posts();
		$usuario['fotos'] = $this->usuario->cantidad_fotos();
		$usuario['comentarios'] = $this->usuario->cantidad_comentarios();

		// Cargamos campos del usuario.
		$this->usuario->perfil()->load_list(array('nombre', 'mensaje_personal'));

		// Nombre completo.
		$usuario['nombre'] = Utils::prop($this->usuario->perfil(), 'nombre');
		$base_view->assign('usuario', $usuario);
		unset($usuario);

		// Mensaje personal.
		$base_view->assign('mensaje_personal', Utils::prop($this->usuario->perfil(), 'mensaje_personal'));

		// Listado de categorias.
		$base_view->assign('menu', $this->submenu_categorias());

		// Agregamos el contenido.
		$base_view->assign('contenido', $contenido);

		return $base_view->parse();
	}

	/**
	 * Portada del perfil del usuario.
	 * @param string $usuario ID o nick del usuario.
	 */
	public function action_index($usuario)
	{
		return $this->action_muro($usuario);
	}

	/**
	 * Informacion del perfil del usuario.
	 * @param string $usuario ID o nick del usuario.
	 */
	public function action_informacion($usuario)
	{
		// Cargamos el usuario.
		$usuario = $this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/index');

		// Campos a cargar.
		$fields = array(
			'general' => array(
				'nombre',
				'web',
				'twitter',
				'facebook',
				'estudios',
			),
			'vida_personal' => array(
				'hacer_amigos',
				'conocer_gente_intereses',
				'conocer_gente_negocios',
				'encontrar_pareja',
				'de_todo',
				'estado_civil',
				'hijos',
				'vivo_con',
			),
			'idioma' => array(
				'idioma_espanol',
				'idioma_ingles',
				'idioma_portugues',
				'idioma_frances',
				'idioma_italiano',
				'idioma_aleman',
				'idioma_otro',
			),
			'datos_profesionales' => array(
				'profesion',
				'empresa',
				'sector',
				'nivel_ingresos',
				'intereses_personales',
				'habilidades_profesionales',
			),
			'como_es' => array(
				'mi_altura',
				'mi_peso',
				'color_pelo',
				'color_ojos',
				'complexion',
				'tatuajes',
				'piercings',
			),
			'habitos_personales' => array(
				'mi_dieta',
				'fumo',
				'tomo_alcohol',
			),
			'intereses_y_preferencias' => array(
				'mis_intereses',
				'hobbies',
				'series_tv_favoritas',
				'musica_favorita',
				'deportes_y_equipos_favoritos',
				'libros_favoritos',
				'peliculas_favoritas',
				'comida_favorita',
				'mis_heroes',
			),
			/**
			'sexo',
			'nacimiento',
			'mensaje_personal',*/
		);

		// Cargamos todos los datos del perfil.
		$load_array = array();
		foreach ($fields as $ff)
		{
			$load_array = array_merge($load_array, $ff);
		}
		$this->usuario->perfil()->load_list($load_array);
		unset($load_array);

		// Obtenemos el valor de los campos.
		foreach ($fields as $k => $field)
		{
			$aa = array();
			foreach ($field as $v)
			{
				if (isset($this->usuario->perfil()->$v))
				{
					$aa[$v] = $this->usuario->perfil()->$v;
				}
			}
			$information_view->assign($k, $aa);
		}
		$information_view->assign('usuario', $this->usuario->as_array());

		// Procesamos pais.
		$country = Utils::prop($this->usuario->perfil(), 'origen');
		if ($country !== NULL)
		{
			$lista_pais = Configuraciones::obtener(CONFIG_PATH.DS.'geonames.'.FILE_EXT);
			$country = explode('.', $country);
			$information_view->assign('origen', $lista_pais[$country[0]][0].', '.$lista_pais[$country[0]][1][$country[1]]);
			unset($lista_pais);
		}
		unset($country);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
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
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * Perfiles del usuario.
	 * @param string $usuario ID o nick del usuario.
	 */
	public function action_posts($usuario)
	{
		// Cargamos el usuario.
		$usuario = $this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/post');

		// Datos del usuario a la vista.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Cargamos listado de posts.
		$post_list = $this->usuario->posts_perfil_by_fecha(10);

		// Transformamos a arreglo.
		foreach ($post_list as $k => $v)
		{
			$post_list[$k] = array_merge($v->as_array(), array('puntos' => $v->puntos()));
		}

		$information_view->assign('posts', $post_list);
		unset($post_list);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
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
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * A quien sigue y quienes lo siguen del usuario.
	 * @param string $usuario ID o nick del usuario.
	 */
	public function action_seguidores($usuario)
	{
		// Cargamos el usuario.
		$usuario = $this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/seguidores');

		// Información del usuario actual.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Seguidores.
		$seguidores = $this->usuario->seguidores();

		// Transformamos a arreglo.
		foreach ($seguidores as $k => $v)
		{
			$seguidores[$k] = $v->as_array();
		}
		$information_view->assign('seguidores', $seguidores);
		unset($seguidores);

		// A quienes sigue.
		$sigue = $this->usuario->sigue();

		// Transformamos a arreglo.
		foreach ($sigue as $k => $v)
		{
			$sigue[$k] = $v->as_array();
		}
		$information_view->assign('sigue', $sigue);
		unset($sigue);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
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
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * Muro del usuario.
	 * @param int $usuario ID del usuario.
	 */
	public function action_muro($usuario)
	{
		// Cargamos el usuario.
		$usuario = $this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/muro');

		// Información del usuario actual.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Listado de eventos.
		$model_sucesos = new Model_Suceso;

		$lst = $model_sucesos->obtener_by_usuario($this->usuario->as_object()->id);

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
			$suceso_vista->assign('actual', $this->usuario->as_array());

			// Asigno información del suceso.
			$suceso_vista->assign('suceso', $s_data);

			// Datos del suceso.
			$suceso_vista->assign('fecha', $v->fecha);

			// Agregamos el evento.
			$eventos[] = $suceso_vista->parse();
		}
		//TODO: agregar listado de eventos.
		$information_view->assign('eventos', $eventos);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
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
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

}
