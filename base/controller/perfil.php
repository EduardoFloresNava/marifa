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
	 * Constructor de la clase. Seteamos el elemento del menu actual.
	 */
	public function before()
	{
		parent::before();

		// Cargo el menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
	}

	/**
	 * Cargamos el usuario. En caso de no existir vamos a la portada.
	 * @param string $usuario ID o nick del usuario.
	 */
	protected function cargar_usuario($usuario)
	{
		if ($usuario == NULL)
		{
			// Verificamos si estamos logueados.
			if ( ! Usuario::is_login())
			{
				add_flash_message(FLASH_ERROR, 'El usuario del que quieres ver el perfil no está disponible.');
				Request::redirect('/');
			}
			$model_usuario = Usuario::usuario();
		}
		else
		{
			// Verifico no sea yo.
			if (Usuario::is_login() && Usuario::usuario()->nick == $usuario)
			{
				$model_usuario = Usuario::usuario();
			}
			else
			{
				// Cargamos el modelo del usuario
				$model_usuario = new Model_Usuario;

				// Tratamos de cargar el usuario por su nick
				if ( ! $model_usuario->load_by_nick($usuario))
				{
					add_flash_message(FLASH_ERROR, 'El usuario del que quieres ver el perfil no está disponible.');
					Request::redirect('/');
				}

				// Verifico bloqueo.
				if ($model_usuario->esta_bloqueado(Usuario::$usuario_id))
				{
					add_flash_message(FLASH_ERROR, 'El usuario del que quieres ver el perfil te tiene bloqueado.');
					Request::redirect('/');
				}
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

		$usuario = (Usuario::$usuario_id == $this->usuario->id) ? '' : $this->usuario->get('nick');
		return array(
			'muro' => array('link' => '/perfil/index/'.$usuario, 'caption' => __('Muro', FALSE), 'active' => $activo == 'muro' || $activo == 'index'),
			'informacion' => array('link' => '/perfil/informacion/'.$usuario, 'caption' => __('Información', FALSE), 'active' =>  $activo == 'informacion'),
			'posts' => array('link' => '/perfil/posts/'.$usuario, 'caption' => __('Posts', FALSE), 'active' =>  $activo == 'posts'),
			'seguidores' => array('link' => '/perfil/seguidores/'.$usuario, 'caption' => __('Seguidores', FALSE), 'active' =>  $activo == 'seguidores'),
			'medallas' => array('link' => '/perfil/medallas/'.$usuario, 'caption' => __('Medallas', FALSE), 'active' =>  $activo == 'medallas'),
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
		$usuario['sigue'] = $this->usuario->cantidad_sigue();
		$usuario['posts'] = $this->usuario->cantidad_posts();
		$usuario['fotos'] = $this->usuario->cantidad_fotos();
		$usuario['comentarios'] = $this->usuario->cantidad_comentarios();
		$usuario['rango'] = $this->usuario->rango()->as_array();

		// Listado de medallas.
		$base_view->assign('medallas', array_map(create_function('$v', '$v[\'medalla\'] = $v[\'medalla\']->as_array(); return $v;'), $this->usuario->medallas()));

		// Listado de seguidores.
		$seguidores = $this->usuario->seguidores(1, 10);
		foreach ($seguidores as $k => $v)
		{
			$seguidores[$k] = $v->as_array();
		}
		$base_view->assign('seguidores', $seguidores);

		// Listado de quienes sigue.
		$sigue = $this->usuario->sigue(1, 10);
		foreach ($sigue as $k => $v)
		{
			$sigue[$k] = $v->as_array();
		}
		$base_view->assign('sigue', $sigue);

		// Cargamos campos del usuario.
		$this->usuario->perfil()->load_list(array('nombre', 'mensaje_personal'));

		// Nombre completo.
		$usuario['nombre'] = Utils::prop($this->usuario->perfil(), 'nombre');
		$base_view->assign('usuario', $usuario);
		unset($usuario);

		// Si está bloqueado y/o lo sigo.
		if ( ! Usuario::is_login())
		{
			$base_view->assign('bloqueado', TRUE);
			$base_view->assign('seguidor', TRUE);
		}
		elseif (Usuario::$usuario_id !== $this->usuario->id)
		{
			$base_view->assign('bloqueado', Usuario::usuario()->esta_bloqueado($this->usuario->id));
			$base_view->assign('seguidor', $this->usuario->es_seguidor(Usuario::$usuario_id));
		}

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
		$this->cargar_usuario($usuario);

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
			$lista_pais = configuracion_obtener(CONFIG_PATH.DS.'geonames.'.FILE_EXT);
			$country = explode('.', $country);
			$information_view->assign('origen', $lista_pais[$country[0]][0].', '.$lista_pais[$country[0]][1][$country[1]]);
			unset($lista_pais);
		}
		unset($country);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
		unset($information_view);

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * Perfiles del usuario.
	 * @param string $usuario ID o nick del usuario.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_posts($usuario, $pagina)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/post');

		// Datos del usuario a la vista.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Cargamos listado de posts.
		$post_list = $this->usuario->posts_perfil_by_fecha($pagina, $cantidad_por_pagina);

		// Verifico validez de la pagina.
		if (count($post_list) == 0 && $pagina != 1)
		{
			Request::redirect('/perfil/posts/'.$usuario);
		}

		// Paginación.
		$paginador = new Paginator($this->usuario->cantidad_posts(), $cantidad_por_pagina);
		$information_view->assign('paginacion', $paginador->get_view($pagina, '/perfil/posts/'.$usuario.'/%d/'));
		unset($paginador);

		// Transformamos a arreglo.
		foreach ($post_list as $k => $v)
		{
			$post_list[$k] = array_merge($v->as_array(), array('puntos' => $v->puntos()));
		}

		$information_view->assign('post', $post_list);
		unset($post_list);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
		unset($information_view);

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * A quien sigue y quienes lo siguen del usuario.
	 * @param string $usuario ID o nick del usuario.
	 * @param int $pagina_sigo Número de página de quienes estoy siguiendo.
	 * @param int $pagina_siguen Número de página de quienes me siguen.
	 */
	public function action_seguidores($usuario, $pagina_sigo, $pagina_siguen)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina_sigo = ( (int) $pagina_sigo) > 0 ? ( (int) $pagina_sigo) : 1;
		$pagina_siguen = ( (int) $pagina_siguen) > 0 ? ( (int) $pagina_siguen) : 1;

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/seguidores');

		// Información del usuario actual.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Seguidores.
		$seguidores = $this->usuario->seguidores($pagina_sigo, $cantidad_por_pagina);

		// Verifico validez de la pagina.
		if (count($seguidores) == 0 && $pagina_sigo != 1)
		{
			Request::redirect('/perfil/seguidores/'.$usuario.'/1/'.$pagina_siguen);
		}

		// Paginación.
		$paginador = new Paginator($this->usuario->cantidad_seguidores(), $cantidad_por_pagina);
		$information_view->assign('paginacion_seguidores', $paginador->get_view($pagina_sigo, '/perfil/seguidores/'.$usuario.'/%d/'.$pagina_siguen));
		unset($paginador);

		// Transformamos a arreglo.
		foreach ($seguidores as $k => $v)
		{
			$seguidores[$k] = $v->as_array();
		}
		$information_view->assign('seguidores', $seguidores);
		unset($seguidores);

		// A quienes sigue.
		$sigue = $this->usuario->sigue($pagina_siguen, $cantidad_por_pagina);

		// Verifico validez de la pagina.
		if (count($sigue) == 0 && $pagina_siguen != 1)
		{
			Request::redirect('/perfil/seguidores/'.$usuario.'/'.$pagina_sigo.'/1');
		}

		// Paginación.
		$paginador = new Paginator($this->usuario->cantidad_sigue(), $cantidad_por_pagina);
		$information_view->assign('paginacion_sigue', $paginador->get_view($pagina_siguen, '/perfil/seguidores/'.$usuario.'/'.$pagina_sigo.'/%d/'));
		unset($paginador);

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

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * Muro del usuario.
	 * Puede publicar un estado/foto/link/video si se envia por POST.
	 * @param int $usuario ID del usuario.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_muro($usuario, $pagina = 1)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/muro');

		// Procesamos publicaciones.
		if (Usuario::is_login() && Request::method() == 'POST')
		{
			// Verifico si puedo publicar.
			if ( ! Usuario::puedo_referirlo($this->usuario->id))
			{
				add_flash_message(FLASH_ERROR, 'No puedes publicar en el muro de ese usuario.');
				Request::redirect('/perfil/index/'.$this->usuario->nick);
			}

			// Obtengo publicacion.
			$publicacion = isset($_POST['publicacion']) ? trim($_POST['publicacion']) : '';

			$error = FALSE;

			// Verificamos el contenido.
			$publicacion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $publicacion);
			if ( ! isset($publicacion_clean{10}) || isset($publicacion{600}))
			{
				$information_view->assign('error_publicacion', 'La publicación debe tener entre 10 y 400 caractéres.');
				$error = TRUE;
			}
			unset($publicacion_clean);

			//TODO: Implementar BBCode reducido.
			if ( ! $error)
			{
				// Evitamos XSS.
				$publicacion = htmlentities($publicacion, ENT_NOQUOTES, 'UTF-8');

				// Cargo modelo del shout.
				$model_shout = new Model_Shout;

				// Obtengo citas.
				$model_shout->procesar_etiquetas($publicacion);

				// Obtengo citas.
				$users = $model_shout->procesar_usuarios($publicacion);

				// Creo la publicación.
				$id = $model_shout->crear(Usuario::$usuario_id, $publicacion);

				// Cargo modelo de sucesos.
				$model_suceso = new Model_Suceso;

				// Envio sucesos de citas a los usuarios.
				foreach ($users as $v)
				{
					if ($v !== Usuario::$usuario_id && $v !== $this->usuario->id)
					{
						$model_suceso->crear($v, 'usuario_shout_cita', TRUE, $id, $this->usuario->id);
					}
				}

				// Envio el suceso correspondiente.
				if ($this->usuario->id !== Usuario::$usuario_id)
				{
					$model_suceso->crear($this->usuario->id, 'usuario_shout', TRUE, $id, $this->usuario->id);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_shout', FALSE, $id, $this->usuario->id);
				}
				else
				{
					$model_suceso->crear($this->usuario->id, 'usuario_shout', FALSE, $id, $this->usuario->id);
				}

				// Notifico que fue correcto.
				add_flash_message(FLASH_SUCCESS, 'Publicación realizada correctamente.');
			}
			else
			{
				$information_view->assign('publicacion', $publicacion);
			}
		}

		// Información del usuario actual.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Listado de eventos.
		$lst = Suceso_Perfil::obtener_listado($this->usuario->id, $pagina, $cantidad_por_pagina);

		// Que sea un número de página válido.
		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/perfil/muro/'.$usuario);
		}

		// Paginación.
		$paginador = new Paginator(Suceso_Perfil::cantidad($this->usuario->id), $cantidad_por_pagina);
		$information_view->assign('paginacion', $paginador->get_view($pagina, '/perfil/muro/'.$usuario.'/%d'));
		unset($paginador);

		$eventos = array();
		foreach ($lst as $v)
		{
			// Obtengo información del suceso.
			$s_data = Suceso_Perfil::procesar($v);

			// Verifico su existencia.
			if ($s_data === NULL)
			{
				continue;
			}

			// Obtenemos el tipo de suceso.
			$tipo = $v->as_object()->tipo;

			// Cargamos la vista.
			$suceso_vista = View::factory('/suceso/perfil/'.$tipo);

			// Asigno los datos del usuario actual.
			$suceso_vista->assign('actual', $this->usuario->as_array());

			// Asigno información del suceso.
			$suceso_vista->assign('suceso', $s_data);

			// Datos del suceso.
			$suceso_vista->assign('fecha', $v->fecha);

			// Agregamos el evento.
			$eventos[] = $suceso_vista->parse();
		}
		$information_view->assign('eventos', $eventos);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
		unset($information_view);

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * Denunciamos a un usuario.
	 * @param int $usuario ID del usuario a denunciar.
	 */
	public function action_denunciar($usuario)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes estar logueado para poder realizar denuncias.');
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres denunciar no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Verifico el estado.
		if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres denunciar no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Denunciar usuario');

		// Cargamos la vista.
		$view = View::factory('perfil/denunciar');

		$view->assign('usuario', $this->usuario->nick);

		// Elementos por defecto.
		$view->assign('motivo', '');
		$view->assign('comentario', '');
		$view->assign('error_motivo', FALSE);
		$view->assign('error_comentario', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$motivo = isset($_POST['motivo']) ? (int) $_POST['motivo'] : NULL;
			$comentario = isset($_POST['comentario']) ? preg_replace('/\s+/', '', trim($_POST['comentario'])) : NULL;

			// Valores para cambios.
			$view->assign('motivo', $motivo);
			$view->assign('comentario', $comentario);

			// Verifico el tipo.
			if ( ! in_array($motivo, array(0, 1, 2, 3, 4, 5)))
			{
				$error = TRUE;
				$view->assign('error_motivo', 'No ha seleccionado un motivo válido.');
			}

			// Verifico la razón si corresponde.
			if ($motivo === 5)
			{
				if ( ! isset($comentario{10}) || isset($comentario{400}))
				{
					$error = TRUE;
					$view->assign('error_contenido', 'La descripción de la denuncia debe tener entre 10 y 400 caracteres.');
				}
			}
			else
			{
				if (isset($comentario{400}))
				{
					$error = TRUE;
					$view->assign('error_contenido', 'La descripción de la denuncia debe tener entre 10 y 400 caracteres.');
				}
				$comentario = NULL;
			}

			if ( ! $error)
			{
				// Creo la denuncia.
				$id = $this->usuario->denunciar(Usuario::$usuario_id, $motivo, $comentario);

				// Envio el suceso
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $this->usuario->id)
				{
					$model_suceso->crear($this->usuario->id, 'usuario_denuncia_crear', TRUE, $id);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_denuncia_crear', FALSE, $id);
				}
				else
				{
					$model_suceso->crear($this->usuario->id, 'usuario_denuncia_crear', FALSE, $id);
				}

				// Informo el resultado.
				add_flash_message(FLASH_SUCCESS, 'El usuario ha sido denunciado correctamente.');
				Request::redirect('/perfil/index/'.$this->usuario->nick);
			}
		}

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $view->parse());
		unset($view);

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick'));
	}

	/**
	 * Comenzamos a seguir al usuario.
	 * @param string $usuario Usuario a seguir.
	 * @param bool $seguir TRUE para seguir, FALSE para dejar de seguir.
	 */
	public function action_seguir($usuario, $seguir)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		$seguir = (bool) $seguir;

		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			if ($seguir)
			{
				add_flash_message(FLASH_ERROR, 'Debes estar logueado para poder seguir usuarios.');
			}
			else
			{
				add_flash_message(FLASH_ERROR, 'Debes estar logueado para poder dejar de seguir usuarios.');
			}
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			if ($seguir)
			{
				add_flash_message(FLASH_ERROR, 'El usuario al cual quieres seguir no se encuentra disponible.');
			}
			else
			{
				add_flash_message(FLASH_ERROR, 'El usuario al cual quieres dejar de seguir no se encuentra disponible.');
			}
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Verificaciones especiales en funcion si lo voy a seguir o dejar de seguir.
		if ($seguir)
		{
			// Verifico el estado.
			if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
			{
				add_flash_message(FLASH_ERROR, 'El usuario al cual quieres seguir no se encuentra disponible.');
				Request::redirect('/perfil/index/'.$this->usuario->nick);
			}

			// Verifico no sea seguidor.
			if ($this->usuario->es_seguidor(Usuario::$usuario_id))
			{
				add_flash_message(FLASH_ERROR, 'El usuario al cual quieres seguir no se encuentra disponible.');
				Request::redirect('/perfil/index/'.$this->usuario->nick);
			}

			// Sigo al usuario.
			$this->usuario->seguir(Usuario::$usuario_id);

			// Actualizo medallas.
			$this->usuario->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_SEGUIDORES);
			Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_SIGUIENDO);
		}
		else
		{
			// Verifico sea seguidor.
			if ( ! $this->usuario->es_seguidor(Usuario::$usuario_id))
			{
				add_flash_message(FLASH_ERROR, 'El usuario al cual quieres dejar de seguir no se encuentra disponible.');
				Request::redirect('/perfil/index/'.$this->usuario->nick);
			}

			// Dejo de seguir al usuario.
			$this->usuario->fin_seguir(Usuario::$usuario_id);
		}

		// Envio el suceso.
		$tipo = $seguir ? 'usuario_seguir' : 'usuario_fin_seguir';
		$model_suceso = new Model_Suceso;
		if ($this->usuario->id != Usuario::$usuario_id)
		{
			$model_suceso->crear($this->usuario->id, $tipo, TRUE, $this->usuario->id, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, $tipo, FALSE, $this->usuario->id, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($this->usuario->id, $tipo, TRUE, $this->usuario->id, Usuario::$usuario_id);
		}

		// Informo resultado.
		if ($seguir)
		{
			add_flash_message(FLASH_SUCCESS, 'Comenzaste a seguir al usuario correctamente.');
		}
		else
		{
			add_flash_message(FLASH_SUCCESS, 'Dejaste de seguir al usuario correctamente.');
		}
		Request::redirect('/perfil/index/'.$this->usuario->nick);
	}

	/**
	 * Bloqueamos el acceso a mi perfil del usuario.
	 * @param string $usuario Usuario a bloquear.
	 */
	public function action_bloquear($usuario)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes estar logueado para poder bloquear usuarios.');
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres bloquear no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$usuario->nick);
		}

		// Verifico el estado.
		if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres bloquear no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Verifico no esté bloqueado.
		if (Usuario::usuario()->esta_bloqueado($this->usuario->id))
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres bloquear no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Bloqueo al usuario.
		Usuario::usuario()->bloquear($this->usuario->id);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if ($this->usuario->id != Usuario::$usuario_id)
		{
			$model_suceso->crear($this->usuario->id, 'usuario_bloqueo', TRUE, Usuario::$usuario_id, $this->usuario->id, 0);
			$model_suceso->crear(Usuario::$usuario_id, 'usuario_bloqueo', FALSE, Usuario::$usuario_id, $this->usuario->id, 0);
		}
		else
		{
			$model_suceso->crear($this->usuario->id, 'usuario_bloqueo', FALSE, Usuario::$usuario_id, $this->usuario->id, 0);
		}

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, 'El usuario se ha bloqueado correctamente.');
		Request::redirect('/perfil/index/'.$this->usuario->nick);
	}

	/**
	 * Desbloqueamos el acceso a mi perfil del usuario.
	 * @param string $usuario Usuario a desbloquear.
	 */
	public function action_desbloquear($usuario)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes estar logueado para poder desbloquear usuarios.');
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres desbloquear no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$usuario->nick);
		}

		// Verifico el estado.
		if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres desbloquear no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Verifico esté bloqueado.
		if ( ! Usuario::usuario()->esta_bloqueado($this->usuario->id))
		{
			add_flash_message(FLASH_ERROR, 'El usuario al cual quieres desbloquear no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$this->usuario->nick);
		}

		// Desbloqueo al usuario.
		Usuario::usuario()->desbloquear($this->usuario->id);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if ($this->usuario->id != Usuario::$usuario_id)
		{
			$model_suceso->crear($this->usuario->id, 'usuario_bloqueo', TRUE, Usuario::$usuario_id, $this->usuario->id, 1);
			$model_suceso->crear(Usuario::$usuario_id, 'usuario_bloqueo', FALSE, Usuario::$usuario_id, $this->usuario->id, 1);
		}
		else
		{
			$model_suceso->crear($this->usuario->id, 'usuario_bloqueo', FALSE, Usuario::$usuario_id, $this->usuario->id, 1);
		}

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, 'El usuario se ha desbloqueado correctamente.');
		Request::redirect('/perfil/index/'.$this->usuario->nick);
	}

	/**
	 * Medallas del usuario.
	 * @param int $usuario ID del usuario.
	 */
	public function action_medallas($usuario)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/medallas');

		// Información del usuario actual.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Listado de medallas.
		$medallas = $this->usuario->medallas();

		// Las proceso.
		foreach ($medallas as $k => $v)
		{
			$medallas[$k]['medalla'] = $v['medalla']->as_array();
		}

		// Envio las medallas a la vista.
		$information_view->assign('medallas', $medallas);
		unset($medallas);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
		unset($information_view);

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->get('nick').' - Medallas');
	}

	/**
	 * Vemos la publicación.
	 * @param int $usuario ID del usuario al que pertenece el shout.
	 * @param int $shout_id ID del shout a ver.
	 */
	public function action_publicacion($usuario, $shout_id)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout_id = (int) $shout_id;
		$model_shout = new Model_Shout($shout_id);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'La publicación no se encuentra disponible.');
			Request::redirect('/perfil/index/'.$usuario);
		}

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/shout');

		// Información del usuario actual.
		$information_view->assign('usuario', $this->usuario->as_array());

		// Cargamos datos.
		$shout = $model_shout->as_array();

		// Proceso BBCode.
		$decoda = new Decoda($shout['mensaje']);
		$decoda->addFilter(new TagFilter());
		$decoda->addFilter(new UserFilter());
		$shout['mensaje_bbcode'] = $decoda->parse(FALSE);

		// Datos extra
		$shout['usuario'] = $model_shout->usuario()->as_array();
		$shout['votos'] = $model_shout->cantidad_votos();
		$shout['comentario'] = $model_shout->cantidad_comentarios();
		$shout['favoritos'] = $model_shout->cantidad_favoritos();
		$shout['compartido'] = $model_shout->cantidad_compartido();

		// Comentarios.
		$shout['comentarios'] = array();
		foreach ($model_shout->comentarios(-1) as $v)
		{
			$aux = $v->as_array();
			$aux['usuario'] = $v->usuario()->as_array();
			$shout['comentarios'][] = $aux;
		}
		$information_view->assign('shout', $shout);

		unset($aux, $shout, $model_shout);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
		unset($information_view);

		// Seteamos el titulo.
		$this->template->assign('title', 'Perfil - '.$this->usuario->nick.' - Muro');
	}

	/**
	 * Comentamos un shout.
	 * @param int $usuario ID del usuario al que pertenece el shout.
	 * @param int $shout ID del shout a comentar.
	 */
	public function action_comentar_publicacion($usuario, $shout)
	{
		// Verifico método de envio.
		if ( ! Usuario::is_login() || Request::method() !== 'POST')
		{
			add_flash_message(FLASH_ERROR, 'La petición no es correcta.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea comentar no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Obtengo el comentario.
		$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

		// Verificamos el contenido.
		$comentario_clean = preg_replace('/\[([^\[\]]+)\]/', '', $comentario);
		if ( ! isset($comentario_clean{10}) || isset($comentario{600}))
		{
			add_flash_message(FLASH_ERROR, 'El comentario debe tener entre 10 y 400 caractéres.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}
		else
		{
			unset($comentario_clean);
			// Evitamos XSS.
			$comentario = htmlentities($comentario, ENT_NOQUOTES, 'UTF-8');

			// Creamos el comentario.
			$id = $model_shout->comentar(Usuario::$usuario_id, $comentario);

			// Enviamos suceso.
			$model_suceso = new Model_Suceso;
			if (Usuario::$usuario_id == $model_shout->usuario_id)
			{
				$model_suceso->crear(Usuario::$usuario_id, 'usuario_shout_comentario', FALSE, $model_shout->id, Usuario::$usuario_id, $id);
			}
			else
			{
				$model_suceso->crear($model_shout->usuario_id, 'usuario_shout_comentario', TRUE, $model_shout->id, Usuario::$usuario_id, $id);
				$model_suceso->crear(Usuario::$usuario_id, 'usuario_shout_comentario', FALSE, $model_shout->id, Usuario::$usuario_id, $id);
			}

			// Informo resultado.
			add_flash_message(FLASH_SUCCESS, 'El comentario se ha realizado correctamente.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}
	}

	/**
	 * Agrego voto o lo quito del shout.
	 * @param int $usuario ID del usuario al que pertenece el shout.
	 * @param int $shout ID del shout a comentar.
	 * @param int $voto 1 positivo, 0 negativo.
	 */
	public function action_votar_publicacion($usuario, $shout, $voto)
	{
		// Verifico método de envio.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'La petición no es correcta.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea votar no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Verifico no sea mia.
		if ($model_shout->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea votar no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Verifico voto.
		$voto = (bool) $voto;
		if ($model_shout->ya_voto(Usuario::$usuario_id) && $voto)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea votar no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Realizo la votación.
		if ($voto)
		{
			$model_shout->votar(Usuario::$usuario_id);
		}
		else
		{
			$model_shout->quitar_voto(Usuario::$usuario_id);
		}

		// Agregamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear($model_shout->usuario_id, 'usuario_shout_voto', TRUE, $model_shout->id, Usuario::$usuario_id, $voto);
		$model_suceso->crear(Usuario::$usuario_id, 'usuario_shout_voto', FALSE, $model_shout->id, Usuario::$usuario_id, $voto);

		//TODO: Agregar suceso.

		add_flash_message(FLASH_SUCCESS, 'El voto se ha realizado correctamente.');
		Request::redirect("/perfil/publicacion/$usuario/$shout");
	}

	/**
	 * Agrego o quito la publicación de los favoritos.
	 * @param int $usuario ID del usuario al que pertenece el shout.
	 * @param int $shout ID del shout a agregar/quitar de los favoritos.
	 * @param int $agregar 1 para agregar, 0 para quitar.
	 */
	public function action_favorito_publicacion($usuario, $shout, $agregar)
	{
		// Verifico método de envio.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'La petición no es correcta.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea agregar/quitar de los favoritos no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Verifico no sea mia.
		if ($model_shout->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea agregar/quitar de los favoritos no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Verifico si hay que agregar o quitar.
		$agregar = (bool) $agregar;
		if ($model_shout->es_favorito(Usuario::$usuario_id) && $agregar)
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea agregar/quitar de los favoritos no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Agrego/Quito de favoritos.
		if ($agregar)
		{
			$model_shout->favorito(Usuario::$usuario_id);
		}
		else
		{
			$model_shout->quitar_favorito(Usuario::$usuario_id);
		}

		// Agregamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear($model_shout->usuario_id, 'usuario_shout_favorito', TRUE, $model_shout->id, Usuario::$usuario_id, $agregar);
		$model_suceso->crear(Usuario::$usuario_id, 'usuario_shout_favorito', FALSE, $model_shout->id, Usuario::$usuario_id, $agregar);

		// Notifico el resultado.
		add_flash_message(FLASH_SUCCESS, 'La publicación se ha agregado/quitado de los favoritos correctamente.');
		Request::redirect("/perfil/publicacion/$usuario/$shout");
	}

	/**
	 * Comparto la publicación.
	 * @param int $usuario ID del usuario al que pertenece el shout.
	 * @param int $shout ID del shout a agregar/quitar de los favoritos.
	 */
	public function action_compartir_publicacion($usuario, $shout)
	{
		// Verifico método de envio.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'La petición no es correcta.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe())
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea compartir no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Verifico no lo haya compartido.
		if ($model_shout->fue_compartido(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, 'La publicación que desea compartir no se encuentra disponible.');
			Request::redirect("/perfil/publicacion/$usuario/$shout");
		}

		// Lo comparto.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear($model_shout->usuario_id, 'usuario_shout_compartir', TRUE, $shout, Usuario::$usuario_id, $this->usuario->id);
		if ($model_shout->usuario_id !== $this->usuario->id)
		{
			$model_suceso->crear($model_shout->usuario_id, 'usuario_shout_compartir', TRUE, $shout, Usuario::$usuario_id, $this->usuario->id);
		}
		$model_suceso->crear(Usuario::$usuario_id, 'usuario_shout_compartir', FALSE, $shout, Usuario::$usuario_id, $this->usuario->id);

		// Notifico el resultado.
		add_flash_message(FLASH_SUCCESS, 'La publicación se ha compartido correctamente.');
		Request::redirect("/perfil/publicacion/$usuario/$shout");
	}
}
