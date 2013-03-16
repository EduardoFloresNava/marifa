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
	 * Constructor de la clase. Seteamos el elemento del menú actual.
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
				add_flash_message(FLASH_ERROR, __('El usuario del que quieres ver el perfil no está disponible.', FALSE));
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
					add_flash_message(FLASH_ERROR, __('El usuario del que quieres ver el perfil no está disponible.', FALSE));
					Request::redirect('/');
				}

				// Verifico bloqueo.
				if ($model_usuario->esta_bloqueado(Usuario::$usuario_id))
				{
					add_flash_message(FLASH_ERROR, __('El usuario del que quieres ver el perfil te tiene bloqueado.', FALSE));
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
		// Obtengo acción automática.
		if ($activo === NULL)
		{
			$call = Request::current();
			$activo = $call['action'];
			$activo = $activo == 'index' || $activo == 'publicacion' ? 'muro' : $activo;
			unset($call);
		}

		// Creo el menú.
		$menu = new Menu('perfil_menu');

		// Agrego elementos.
		$menu->element_set(__('Muro', FALSE), "/@{$this->usuario->nick}", 'muro');
		$menu->element_set(__('Información', FALSE), "/@{$this->usuario->nick}/informacion/", 'informacion');
		$menu->element_set(__('Posts', FALSE), "/@{$this->usuario->nick}/posts/", 'posts');
		$menu->element_set(__('Seguidores', FALSE), "/@{$this->usuario->nick}/seguidores/", 'seguidores');
		$menu->element_set(__('Medallas', FALSE), "/@{$this->usuario->nick}/medallas/", 'medallas');

		// Devuelvo el menú.
		return $menu->as_array($activo);
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

		// Listado de categorías.
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
	 * Información del perfil del usuario.
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
			)
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

		// Procesamos país.
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
		$this->template->assign('title_raw', sprintf(__('Información de %s en ', FALSE), $this->usuario->get('nick')));
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
			Request::redirect("/@$usuario/posts/");
		}

		// Paginación.
		$paginador = new Paginator($this->usuario->cantidad_posts(), $cantidad_por_pagina);
		$information_view->assign('paginacion', $paginador->get_view($pagina, "/@$usuario/posts/%d"));
		unset($paginador);

		// Transformamos a arreglo.
		foreach ($post_list as $k => $v)
		{
			$a = $v->as_array();
			$a['puntos'] = $v->puntos();
			$a['categoria'] = $v->categoria()->as_array();
			$post_list[$k] = $a;
		}

		$information_view->assign('post', $post_list);
		unset($post_list);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $this->header_block($information_view->parse()));
		unset($information_view);

		// Seteamos el titulo.
		$this->template->assign('title_raw', sprintf(__('Posts de %s en ', FALSE), $this->usuario->get('nick')));
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
			Request::redirect("/@$usuario/seguidores/1/$pagina_siguen");
		}

		// Paginación.
		$paginador = new Paginator($this->usuario->cantidad_seguidores(), $cantidad_por_pagina);
		$information_view->assign('paginacion_seguidores', $paginador->get_view($pagina_sigo, "/@$usuario/seguidores/%d/$pagina_siguen"));
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
			Request::redirect("/@$usuario/seguidores/$pagina_sigo");
		}

		// Paginación.
		$paginador = new Paginator($this->usuario->cantidad_sigue(), $cantidad_por_pagina);
		$information_view->assign('paginacion_sigue', $paginador->get_view($pagina_siguen, "/@$usuario/seguidores/$pagina_sigo/%d/"));
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
		$this->template->assign('title_raw', sprintf(__('Seguidores de %s en ', FALSE), $this->usuario->get('nick')));
	}

	/**
	 * Muro del usuario.
	 * Puede publicar un estado/foto/link/video si se envía por POST.
	 * @param int $usuario ID del usuario.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_muro($usuario, $pagina = 1)
	{
		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargamos la vista de información.
		$information_view = View::factory('perfil/muro');

		// Seteo valores por defecto.
		$information_view->assign('publicacion', '');
		$information_view->assign('tipo', 'texto');
		$information_view->assign('url', '');

		// Procesamos publicaciones.
		if (Usuario::is_login() && Request::method() == 'POST')
		{
			// Verifico si puedo publicar.
			if ($this->usuario->id !== Usuario::$usuario_id && ! Usuario::puedo_referirlo($this->usuario->id))
			{
				add_flash_message(FLASH_ERROR, __('No puedes publicar en el muro de ese usuario.', FALSE));
				Request::redirect('/@'.$this->usuario->nick);
			}

			// Obtengo datos.
			$publicacion = isset($_POST['publicacion']) ? trim($_POST['publicacion']) : '';
			$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
			$url = isset($_POST['url']) ? trim($_POST['url']) : '';

			// Los seteo en la vista.
			$information_view->assign('publicacion', $publicacion);
			$information_view->assign('tipo', $tipo);
			$information_view->assign('url', $url);

			$error = FALSE;

			// Verifico tipo.
			if ( ! in_array($tipo, array('texto', 'foto', 'enlace', 'video')))
			{
				$information_view->assign('error_publicacion', __('La publicación no es correcta.', FALSE));
				$error = TRUE;
			}
			else
			{
				// Verificamos el contenido.
				$publicacion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $publicacion);
				if ( ( ! isset($publicacion_clean{10}) && $tipo == 'texto') || isset($publicacion{600}))
				{
					$information_view->assign('error_publicacion', __('La publicación debe tener entre 10 y 400 caracteres.', FALSE));
					$error = TRUE;
				}
				unset($publicacion_clean);

				// Verifico URL y tipo si no es texto.
				if ($tipo !== 'texto')
				{
					// Verifico sea una URL válida.
					if (isset($url{200}) || ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/Di', $url))
					{
						$information_view->assign('error_url', __('La URL ingresada no es válida, la misma no debe superar los 200 caracteres.', FALSE));
						$error = TRUE;
					}
					else
					{
						switch ($tipo)
						{
							case 'foto':
								// Tamaño de la imagen (también verificamos si es válida).
								$size = @getimagesize($url);
								if ( ! is_array($size))
								{
									$information_view->assign('error_url', __('La URL ingresada no es una foto válida.', FALSE));
									$error = TRUE;
								}
								else
								{
									// Verificar TAMAÑO.
									if ($size[0] < 100 || $size[1] < 100)
									{
										$information_view->assign('error_url', __('La imagen debe tener como mínimo un tamaño de 100x100px.', FALSE));
										$error = TRUE;
									}
									elseif ($size[0] > 1600 || $size[1] > 1600)
									{
										$information_view->assign('error_url', __('La imagen debe tener como máximo un tamaño de 1600x1600px.', FALSE));
										$error = TRUE;
									}
									else
									{
										$valor = $url;
									}
								}
								break;
							case 'enlace':
								// Obtengo HTML del sitio.
								$html = Utils::remote_call($url);

								// Obtengo titulo.
								if (isset($html{0}))
								{
									if (preg_match("/\<title\>(.*)\<\/title\>/",$html, $title))
									{
										// Genero valor.
										$valor = serialize(array($url, htmlspecialchars(substr($title[1], 0, 200))));
									}
									else
									{
										$information_view->assign('error_url', __('La URL no es un sitio válido.', FALSE));
										$error = TRUE;
									}
								}
								else
								{
									$information_view->assign('error_url', __('La URL no es un sitio válido.', FALSE));
									$error = TRUE;
								}
								break;
							case 'video':
								preg_match('@^(?:(http|https)://)?([^/]+)@i', $url, $matches);
								$domain = isset($matches[2]) ? $matches[2] : '';

								// Proceso según el tipo de vídeo.
								switch ($domain) {
									case 'www.youtube.com':
									case 'youtube.com':
										if ( ! preg_match('#^http://\w{0,3}.?youtube+\.\w{2,3}/watch\?v=([\w-]{11})#', $url, $match))
										{
											$information_view->assign('error_url', __('La URL del vídeo de youtube no es válida.', FALSE));
											$error = TRUE;
										}
										else
										{
											$valor = 'youtube:'.$match[1];
										}
										break;
									case 'vimeo.com':
									case 'player.vimeo.com':
										if ( ! preg_match('#http://(?:\w+.)?vimeo.com/(?:video/|moogaloop\.swf\?clip_id=|)(\w+)#i', $url, $match))
										{
											$information_view->assign('error_url', __('La URL del vídeo de vimeo no es válida.', FALSE));
											$error = TRUE;
										}
										else
										{
											if (preg_match('/^[0-9]+$/', $match[1]))
											{
												$valor = 'vimeo:'.$match[1];
											}
											else
											{
												$information_view->assign('error_url', __('La URL del vídeo de vimeo no es válida.', FALSE));
												$error = TRUE;
											}
										}
										break;
									default:
										$information_view->assign('error_url', __('La URL del vídeo no es válida.', FALSE));
										$error = TRUE;
								}
								break;
						}
					}
				}
			}

			//TODO: Implementar BBCode reducido.
			if ( ! $error)
			{
				// Transformo el tipo.
				switch ($tipo)
				{
					case 'texto':
						$tipo = 0;
						break;
					case 'foto':
						$tipo = 1;
						break;
					case 'enlace':
						$tipo = 2;
						break;
					case 'video':
						$tipo = 3;
						break;
				}

				// Evitamos XSS.
				$publicacion = htmlentities($publicacion, ENT_NOQUOTES, 'UTF-8');

				// Cargo modelo del shout.
				$model_shout = new Model_Shout;

				// Obtengo citas.
				$tags = $model_shout->procesar_etiquetas($publicacion);

				// Obtengo citas.
				$users = $model_shout->procesar_usuarios($publicacion);

				// Creo la publicación.
				$id = $model_shout->crear(Usuario::$usuario_id, $publicacion, $tipo, isset($valor) ? $valor : NULL);

				// Cargo modelo de sucesos.
				$model_suceso = new Model_Suceso;

				// Envío sucesos de citas a los usuarios.
				foreach ($users as $v)
				{
					if ($v !== Usuario::$usuario_id && $v !== $this->usuario->id)
					{
						$model_suceso->crear($v, 'usuario_shout_cita', TRUE, $id, $this->usuario->id);
					}
				}

				// Agrego etiquetas.
				foreach ($tags as $tag)
				{
					Database::get_instance()->insert('INSERT INTO shout_tag (tag, shout_id) VALUES (?, ?)', array($tag, $id));
				}

				// Envío el suceso correspondiente.
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
				add_flash_message(FLASH_SUCCESS, __('Publicación realizada correctamente.', FALSE));

				// Redirecciono para evitar re-post.
				Request::redirect('/@'.$this->usuario->nick);
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
			Request::redirect("/@$usuario/muro");
		}

		// Paginación.
		$paginador = new Paginator(Suceso_Perfil::cantidad($this->usuario->id), $cantidad_por_pagina);
		$information_view->assign('paginacion', $paginador->get_view($pagina, "/@$usuario/muro/%d"));
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
		$this->template->assign('title_raw', sprintf(__('Perfil de %s en ', FALSE), $this->usuario->get('nick')));
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
			if (Request::is_ajax())
			{
				Request::http_response_code(303);
				echo SITE_URL;
				return;
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder realizar denuncias.', FALSE));
				Request::redirect('/usuario/login');
			}
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			if (Request::is_ajax())
			{
				echo json_encode(array(
					'reponse' => 'ERROR',
					'body' => __('El usuario al cual quieres denunciar no se encuentra disponible.', FALSE)
				));
				return;
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres denunciar no se encuentra disponible.', FALSE));
				Request::redirect("/@{$this->usuario->nick}");
			}
		}

		// Verifico el estado.
		if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
		{
			if (Request::is_ajax())
			{
				echo json_encode(array(
					'reponse' => 'ERROR',
					'body' => __('El usuario al cual quieres denunciar no se encuentra disponible.', FALSE)
				));
				return;
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres denunciar no se encuentra disponible.', FALSE));
				Request::redirect("/@{$this->usuario->nick}");
			}
		}

		// Asignamos el título.
		$this->template->assign('title', __('Denunciar usuario', FALSE));

		// Cargamos la vista.
		if (Request::is_ajax())
		{
			$view = View::factory('perfil/denunciar_modal_ajax');
		}
		else
		{
			$view = View::factory('perfil/denunciar');
		}

		$view->assign('usuario', $this->usuario->nick);

		// Elementos por defecto.
		$view->assign('motivo', '');
		$view->assign('comentario', '');
		$view->assign('error_motivo', FALSE);
		$view->assign('error_comentario', FALSE);

		if (Request::method() == 'POST')
		{
			// Marcamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$motivo = isset($_POST['motivo']) ? (int) $_POST['motivo'] : 0;
			$comentario = isset($_POST['comentario']) ? preg_replace('/\s+/', '', trim($_POST['comentario'])) : NULL;

			// Valores para cambios.
			$view->assign('motivo', $motivo);
			$view->assign('comentario', $comentario);

			// Marcas para los errores AJAX.
			$error_motivo = FALSE;
			$error_comentario = FALSE;

			// Verifico el tipo.
			if ( ! in_array($motivo, array(0, 1, 2, 3, 4, 5)))
			{
				$error = TRUE;
				$error_motivo = __('No ha seleccionado un motivo válido.', FALSE);
				$view->assign('error_motivo', __('No ha seleccionado un motivo válido.', FALSE));
			}

			// Verifico la razón si corresponde.
			if ($motivo === 5)
			{
				if ( ! isset($comentario{10}) || isset($comentario{400}))
				{
					$error = TRUE;
					$error_contenido = __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE);
					$view->assign('error_contenido', __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE));
				}
			}
			else
			{
				if (empty($comentario))
				{
					$comentario = NULL;
				}
				elseif ( ! isset($comentario{10}) || isset($comentario{400}))
				{
					$error = TRUE;
					$error_contenido = __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE);
					$view->assign('error_contenido', __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE));
				}
			}

			// Verifico si hay errores y es AJAX para informar.
			if ($error && Request::is_ajax())
			{
				echo json_encode(array('response' => 'ERROR', 'body' => array('error_motivo' => $error_motivo, 'error_comentario' => $error_comentario)));
				return;
			}

			if ( ! $error)
			{
				// Creo la denuncia.
				$id = $this->usuario->denunciar(Usuario::$usuario_id, $motivo, $comentario);

				// Envío el suceso
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

				if (Request::is_ajax())
				{
					echo json_encode(array('response' => 'OK', 'body' => __('El usuario ha sido denunciado correctamente.', FALSE)));
					return;
				}
				else
				{
					// Informo el resultado.
					add_flash_message(FLASH_SUCCESS, __('El usuario ha sido denunciado correctamente.', FALSE));
					Request::redirect("/@{$this->usuario->nick}");
				}
			}
		}

		if (Request::is_ajax())
		{
			$view->show();
		}
		else
		{
			// Asignamos la vista a la plantilla base.
			$this->template->assign('contenido', $view->parse());
			unset($view);

			// Asignamos el titulo.
			$this->template->assign('title_raw', sprintf(__('Denunciar a %s en ', FALSE), $this->usuario->get('nick')));
		}
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
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder seguir usuarios.', FALSE));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder dejar de seguir usuarios.', FALSE));
			}
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			if ($seguir)
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
			}
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Verificaciones especiales en función si lo voy a seguir o dejar de seguir.
		if ($seguir)
		{
			// Verifico el estado.
			if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				Request::redirect("/@{$this->usuario->nick}");
			}

			// Verifico no sea seguidor.
			if ($this->usuario->es_seguidor(Usuario::$usuario_id))
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				Request::redirect("/@{$this->usuario->nick}");
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
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
				Request::redirect("/@{$this->usuario->nick}");
			}

			// Dejo de seguir al usuario.
			$this->usuario->fin_seguir(Usuario::$usuario_id);
		}

		// Envío el suceso.
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
			add_flash_message(FLASH_SUCCESS, __('Comenzaste a seguir al usuario correctamente.', FALSE));
		}
		else
		{
			add_flash_message(FLASH_SUCCESS, __('Dejaste de seguir al usuario correctamente.', FALSE));
		}
		Request::redirect("/@{$this->usuario->nick}");
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
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder bloquear usuarios.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario al cual quieres bloquear no se encuentra disponible.', FALSE));
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Verifico el estado.
		if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
		{
			add_flash_message(FLASH_ERROR, __('El usuario al cual quieres bloquear no se encuentra disponible.', FALSE));
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Verifico no esté bloqueado.
		if (Usuario::usuario()->esta_bloqueado($this->usuario->id))
		{
			add_flash_message(FLASH_ERROR, __('El usuario al cual quieres bloquear no se encuentra disponible.', FALSE));
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Bloqueo al usuario.
		Usuario::usuario()->bloquear($this->usuario->id);

		// Envío el suceso.
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
		add_flash_message(FLASH_SUCCESS, __('El usuario se ha bloqueado correctamente.', FALSE));
		Request::redirect("/@{$this->usuario->nick}");
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
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder desbloquear usuarios.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario al cual quieres desbloquear no se encuentra disponible.', FALSE));
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Verifico el estado.
		if ($this->usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
		{
			add_flash_message(FLASH_ERROR, __('El usuario al cual quieres desbloquear no se encuentra disponible.', FALSE));
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Verifico esté bloqueado.
		if ( ! Usuario::usuario()->esta_bloqueado($this->usuario->id))
		{
			add_flash_message(FLASH_ERROR, __('El usuario al cual quieres desbloquear no se encuentra disponible.', FALSE));
			Request::redirect("/@{$this->usuario->nick}");
		}

		// Desbloqueo al usuario.
		Usuario::usuario()->desbloquear($this->usuario->id);

		// Envío el suceso.
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
		add_flash_message(FLASH_SUCCESS, __('El usuario se ha desbloqueado correctamente.', FALSE));
		Request::redirect("/@{$this->usuario->nick}");
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
		$this->template->assign('title_raw', sprintf(__('Medallas de %s en ', FALSE), $this->usuario->get('nick')));
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
			add_flash_message(FLASH_ERROR, __('La publicación no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario");
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

		// Proceso valor si es tipo especial.
		if ($model_shout->tipo == Model_Shout::TIPO_VIDEO)
		{
			// Obtengo clase de vídeo.
			$shout['valor'] = explode(':', $model_shout->valor);
		}
		elseif($model_shout->tipo == Model_Shout::TIPO_ENLACE)
		{
			$shout['valor'] = unserialize($shout['valor']);
		}

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
		$this->template->assign('title_raw', sprintf(__('Publicación de %s en ', FALSE), $this->usuario->get('nick')));
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
			add_flash_message(FLASH_ERROR, __('La petición no es correcta.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea comentar no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Obtengo el comentario.
		$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

		// Verificamos el contenido.
		$comentario_clean = preg_replace('/\[([^\[\]]+)\]/', '', $comentario);
		if ( ! isset($comentario_clean{10}) || isset($comentario{600}))
		{
			add_flash_message(FLASH_ERROR, __('El comentario debe tener entre 10 y 400 caracteres.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
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
			add_flash_message(FLASH_SUCCESS, __('El comentario se ha realizado correctamente.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
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
			add_flash_message(FLASH_ERROR, __('La petición no es correcta.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea votar no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Verifico no sea mía.
		if ($model_shout->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea votar no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Verifico voto.
		$voto = (bool) $voto;
		if ($model_shout->ya_voto(Usuario::$usuario_id) && $voto)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea votar no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
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

		add_flash_message(FLASH_SUCCESS, __('El voto se ha realizado correctamente.', FALSE));
		Request::redirect("/@$usuario/publicacion/$shout");
	}

	/**
	 * Agrego o quito la publicación de los favoritos.
	 * @param int $usuario ID del usuario al que pertenece el shout.
	 * @param int $shout ID del shout a agregar/quitar de los favoritos.
	 * @param int $agregar 1 para agregar, 0 para quitar.
	 */
	public function action_favorito_publicacion($usuario, $shout, $agregar)
	{
		// Verifico método de envío.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('La petición no es correcta.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe() || $model_shout->usuario_id !== $this->usuario->id)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea agregar/quitar de los favoritos no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Verifico no sea mia.
		if ($model_shout->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea agregar/quitar de los favoritos no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Verifico si hay que agregar o quitar.
		$agregar = (bool) $agregar;
		if ($model_shout->es_favorito(Usuario::$usuario_id) && $agregar)
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea agregar/quitar de los favoritos no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
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
		add_flash_message(FLASH_SUCCESS, __('La publicación se ha agregado/quitado de los favoritos correctamente.', FALSE));
		Request::redirect("/@$usuario/publicacion/$shout");
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
			add_flash_message(FLASH_ERROR, __('La petición no es correcta.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Cargamos el usuario.
		$this->cargar_usuario($usuario);

		// Cargo el shout.
		$shout = (int) $shout;
		$model_shout = new Model_Shout($shout);

		// Verifico existencia.
		if ( ! $model_shout->existe())
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea compartir no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
		}

		// Verifico no lo haya compartido.
		if ($model_shout->fue_compartido(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('La publicación que desea compartir no se encuentra disponible.', FALSE));
			Request::redirect("/@$usuario/publicacion/$shout");
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
		add_flash_message(FLASH_SUCCESS, __('La publicación se ha compartido correctamente.', FALSE));
		Request::redirect("/@$usuario/publicacion/$shout");
	}

	/**
	 * Lista de usuarios para auto-completado AJAX.
	 */
	public function action_usuarios_permitidos()
	{
		// Verifico estar conectado.
		if ( ! Usuario::is_login())
		{
			header(':', TRUE, 403);
			die('Forbiden');
		}

		// Envio el listado.
		die (json_encode(Usuario::usuarios_referir()));
	}
}
