<?php
/**
 * post.php is part of Marifa.
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
 * Controlador de la portada.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Post extends Controller {

	/**
	 * Generamos la URL del post para redireccionar.
	 * @param Model_Post|int $post Modelo del post o ID del post.
	 * @return string
	 */
	protected function post_url($post, $pagina = 1)
	{
		// Cargo post.
		if ( ! is_object($post))
		{
			$post = new Model_Post( (int) $post);
		}

		// Verifico existencia.
		if ( ! $post->existe())
		{
			add_flash_message(FALSH_ERROR, __('El post no es válido', FALSE));
			Request::redirect('/post/');
		}

		// Genero la URL.
		if (is_string($pagina) || $pagina > 1)
		{
			return '/post/'.$post->categoria()->seo.'/'.$post->id.'/'.Texto::make_seo($post->titulo).'.'.$pagina.'.html';
		}
		else
		{
			return '/post/'.$post->categoria()->seo.'/'.$post->id.'/'.Texto::make_seo($post->titulo).'.html';
		}
	}

	/**
	 * Información de un post.
	 * @param int $post ID del post a visualizar.
	 * @param int $pagina Número de página de los comentarios.
	 */
	public function action_index($post, $pagina)
	{
		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post al que intentas acceder no está disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico el estado de post y permisos necesarios para acceder.
		switch ($model_post->estado)
		{
			case Model_Post::ESTADO_BORRADO:
				add_flash_message(FLASH_ERROR, __('El post al que intentas acceder no existe.', FALSE));
				Request::redirect('/');
				break;
			case Model_Post::ESTADO_PAPELERA:
				if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA))
				{
					add_flash_message(FLASH_ERROR, __('El post al que intentas acceder no se encuentra disponible.', FALSE));
					Request::redirect('/');
				}
				break;
			case Model_Post::ESTADO_BORRADOR:
				if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
				{
					add_flash_message(FLASH_ERROR, __('El post al que intentas acceder no se encuentra disponible.', FALSE));
					Request::redirect('/');
				}
				break;
			case Model_Post::ESTADO_PENDIENTE:
			case Model_Post::ESTADO_OCULTO:
			case Model_Post::ESTADO_RECHAZADO:
				if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
				{
					add_flash_message(FLASH_ERROR, __('El post al que intentas acceder no se encuentra disponible.', FALSE));
					Request::redirect('/');
				}
				break;
		}

		// Cargo información SEO.
		$this->template->assign('meta_description', preg_replace('/\[.*\]/', '', $model_post->contenido));
		$this->template->assign('meta_keywords', implode(', ', array_slice($model_post->etiquetas(), 0, 5)));
		$this->template->assign('meta_author', $model_post->usuario()->nick);


		if ($model_post->as_object()->privado && ! Usuario::is_login())
		{
			// Asignamos el título.
			$this->template->assign('title', __('Post privado', FALSE));

			$view = View::factory('post/privado');
			$view->assign('post', $model_post->as_array());
		}
		else
		{
			// Asignamos el título.
			$this->template->assign('title', $model_post->as_object()->titulo);

			// Cargamos la vista.
			$view = View::factory('post/index');

			// Obtengo el post siguiente, el anterior y uno aleatorio.
			$pa = $model_post->anterior();
			$pad = $pa->as_array();
			$pad['categoria'] = $pa->categoria()->as_array();
			$view->assign('post_anterior', $pad);

			$ps = $model_post->siguiente();
			$psd = $ps->as_array();
			$psd['categoria'] = $ps->categoria()->as_array();
			$view->assign('post_siguiente', $psd);

			$pal = $model_post->aleatorio();
			$pald = $pa->as_array();
			$pald['categoria'] = $pal->categoria()->as_array();
			$view->assign('post_aleatorio', $pald);
			unset($pa, $pad, $ps, $psd, $pal, $pald);

			// Verifico si debo contabilizar la visita.
			if (Usuario::$usuario_id != $model_post->as_object()->usuario_id)
			{
				$model_post->agregar_vista();
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_VISITAS);
			}

			// Mi id.
			$view->assign('me', Usuario::$usuario_id);

			// Verifico si sigo al usuario.
			if ($model_post->usuario_id !== Usuario::$usuario_id)
			{
				$view->assign('sigue_usuario', $model_post->usuario()->es_seguidor(Usuario::$usuario_id));
			}
			else
			{
				$view->assign('sigue_usuario', TRUE);
			}

			// Información del usuario dueño del post.
			$u_data = $model_post->usuario()->as_array();
			$u_data['seguidores'] = $model_post->usuario()->cantidad_seguidores();
			$u_data['posts'] = $model_post->usuario()->cantidad_posts();
			$u_data['comentarios'] = $model_post->usuario()->cantidad_comentarios();
			$u_data['puntos'] = $model_post->usuario()->cantidad_puntos();
			$view->assign('usuario', $u_data);
			unset($u_data);

			// Información del post.
			$pst = $model_post->as_array();
			$pst['contenido_raw'] = $pst['contenido'];
			$pst['contenido'] = Decoda::procesar($pst['contenido']);
			$pst['seguidores'] = $model_post->cantidad_seguidores();
			$pst['puntos'] = $model_post->puntos();
			$pst['favoritos'] = $model_post->cantidad_favoritos();
			$view->assign('post', $pst);
			unset($pst);

			// Verifico las acciones extendidas.
			$view->assign('modificar_ocultar', Usuario::$usuario_id === $model_post->usuario_id || Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_OCULTAR));
			$view->assign('modificar_especiales', Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER));
			$view->assign('modificar_aprobar', Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO) || Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS));
			//TODO: ver si en todo momento es correcto permitir modificaciones.
			$view->assign('modificar_editar', Usuario::$usuario_id === $model_post->usuario_id || Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_EDITAR));
			$view->assign('modificar_borrar', Usuario::$usuario_id === $model_post->usuario_id || Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_ELIMINAR));

			// Verifico si el usuario puede comentar.
			$view->assign('podemos_comentar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO) || ($model_post->comentar && Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR)));

			// Verifico si el usuario puede votar comentarios.
			$view->assign('podemos_votar_comentarios', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR));

			// Verifico acciones generales.
			if ($model_post->as_object()->usuario_id == Usuario::$usuario_id)
			{
				$view->assign('es_favorito', TRUE);
				$view->assign('sigo_post', TRUE);
				$view->assign('puntuacion', FALSE);
			}
			else
			{
				$view->assign('es_favorito', $model_post->es_favorito(Usuario::$usuario_id));
				$view->assign('sigo_post', $model_post->es_seguidor(Usuario::$usuario_id));
				if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_PUNTUAR) || $model_post->dio_puntos(Usuario::$usuario_id))
				{
					$view->assign('puntuacion', FALSE);
				}
				else
				{
					// Obtenemos puntos disponibles.
					$m_user = Usuario::usuario();
					$p_d = $m_user->puntos < $m_user->rango()->puntos_dar ? $m_user->puntos : $m_user->rango()->puntos_dar;

					$p_arr = array();
					for ($i = 1; $i <= $p_d; $i++)
					{
						$p_arr[] = $i;
					}

					$view->assign('puntuacion', $p_arr);
					unset($m_user, $p_d, $p_arr);
				}
			}

			// Categoria del post.
			$view->assign('categoria', $model_post->categoria()->as_array());

			// Etiquetas.
			$view->assign('etiquetas', $model_post->etiquetas());

			// Acciones posibles sobre comentarios.
			$view->assign('comentario_eliminar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR));
			$view->assign('comentario_ocultar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR));
			$view->assign('comentario_editar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR));

			// Formato de la página.
			$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

			// Cantidad de elementos por pagina.
			$model_configuracion = new Model_Configuracion;
			$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

			// Cargo comentarios.
			$cmts = $model_post->comentarios(Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO) ? NULL : Model_Post_Comentario::ESTADO_VISIBLE, $pagina, $cantidad_por_pagina);

			// Verifivo validez de la pagina.
			if (count($cmts) == 0 && $pagina != 1)
			{
				Request::redirect($this->post_url($model_post));
			}

			$l_cmt = array();
			foreach ($cmts as $cmt)
			{
				$cl_cmt = $cmt->as_array();
				$cl_cmt['contenido_raw'] = $cl_cmt['contenido'];
				$cl_cmt['contenido'] = Decoda::procesar($cl_cmt['contenido']);
				if ($cl_cmt['usuario_id'] == Usuario::$usuario_id)
				{
					$cl_cmt['vote'] = TRUE;
				}
				else
				{
					$cl_cmt['vote'] = $cmt->ya_voto(Usuario::$usuario_id);
				}
				$cl_cmt['votos'] = $cmt->cantidad_votos();
				$cl_cmt['usuario'] = $cmt->usuario()->as_array();
				$l_cmt[] = $cl_cmt;
			}
			$view->assign('comentarios', $l_cmt);
			unset($l_cmt, $cmts);

			// Paginación.
			$paginador = new Paginator($model_post->cantidad_comentarios(Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO) ? NULL : Model_Post_Comentario::ESTADO_VISIBLE), $cantidad_por_pagina);
			$view->assign('paginacion', $paginador->get_view($pagina, $this->post_url($model_post, '%d')));
			unset($paginador);

			$view->assign('comentario_content', isset($_POST['comentario']) ? $_POST['comentario'] : NULL);
			$view->assign('comentario_error', get_flash('post_comentario_error'));
			$view->assign('comentario_success', get_flash('post_comentario_success'));
		}


		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('index'));


		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Creamos un nuevo post.
	 * @param int $post ID del post a editar.
	 */
	public function action_editar($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder editar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Limpio la entrada
		$post = (int) $post;

		// Cargo el post.
		$model_post = new Model_Post($post);

		// Verifico exista.
		if ( ! $model_post->existe())
		{
			add_flash_message(FLASH_ERROR, __('El post especificado no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// No podemos editar posts borrados.
		if ($model_post->estado == Model_Post::ESTADO_BORRADO)
		{
			add_flash_message(FLASH_ERROR, __('El post especificado no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico el usuario y el permiso de edición para terceros.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_EDITAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos necesarios para realizar esa edición.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Asignamos el título.
		$this->template->assign('title', __('Editar post', FALSE));

		// Cargamos la vista.
		$view = View::factory('post/editar');

		$view->assign('post', $post);

		// Seteo permisos especiales.
		$view->assign('permisos_especiales', Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER));

		// Cargamos valores por defecto.
		$view->assign('titulo', $model_post->titulo);
		$view->assign('contenido', $model_post->contenido);
		$view->assign('categoria', $model_post->categoria()->seo);
		$view->assign('privado', $model_post->privado);
		$view->assign('patrocinado', $model_post->sponsored);
		$view->assign('sticky', $model_post->sticky);
		$view->assign('comentar', ! $model_post->comentar);
		$view->assign('tags', implode(', ', $model_post->etiquetas()));


		// Elementos por defecto.
		foreach (array('error_titulo', 'error_contenido', 'error_categoria', 'error_tags') as $k)
		{
			$view->assign($k, FALSE);
		}

		// Listado de categorias.
		$model_categoria = new Model_Categoria;
		$view->assign('categorias', $model_categoria->lista());

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('index'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			foreach (array('titulo', 'contenido', 'categoria', 'tags') as $k)
			{
				$$k = isset($_POST[$k]) ? $_POST[$k] : '';
				$view->assign($k, $$k);
			}

			// Obtenemos los checkbox.
			foreach (array('privado', 'patrocinado', 'sticky', 'comentar') as $k)
			{
				$$k = isset($_POST[$k]) ? ($_POST[$k] == 1) : FALSE;
				$view->assign($k, $$k);
			}

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', __('El formato del título no es correcto.', FALSE));
				$error = TRUE;
			}

			// Verificamos el contenido.
			$contenido_clean = preg_replace('/\[.*\]/', '', $contenido);
			if ( ! isset($contenido_clean{20}) || isset($contenido{5000}))
			{
				$view->assign('error_contenido', __('El contenido debe tener entre 20 y 5000 caracteres.', FALSE));
				$error = TRUE;
			}
			unset($contenido_clean);

			// Verificamos la categoría.
			$model_categoria = new Model_Categoria;
			if ( ! $model_categoria->existe_seo($categoria))
			{
				$view->assign('error_categoria', __('La categoría seleccionada es incorrecta.', FALSE));
				$error = TRUE;
			}
			else
			{
				$model_categoria->load_by_seo($categoria);
				$categoria_id = $model_categoria->id;
			}
			unset($model_categoria);

			// Quito espacios adicionales a las etiquetas.
			$tags = preg_replace('/\s+/', ' ', trim($tags));

			// Verificamos las etiquetas.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóúñÑÁÉÍÓÚ, ]{0,}$/D', $tags))
			{
				$view->assign('error_tags', __('Las etiquetas ingresadas con son alpha numéricos.', FALSE));
				$error = TRUE;
			}

			// Procedemos a crear el post.
			if ( ! $error)
			{
				// Configuraciones sobre etiquetas.
				$model_config = new Model_Configuracion;
				$keyword_len = (int) $model_config->get('keyword_largo_minimo', 3);
				$keyword_bloqueadas = unserialize($model_config->get('keyword_palabras_comunes', 'a:0:{}'));

				// Obtengo el listado de etiquetas.
				$tags = explode(',', $tags);
				foreach ($tags as $k => $v)
				{
					// Elimino espacios extra.
					$tags[$k] = trim(strtolower($v));

					// Verifico no sea vacia.
					if ($tags[$k] == '')
					{
						unset($tags[$k]);
						continue;
					}

					// Verifico largo.
					if (strlen($v) < $keyword_len)
					{
						$view->assign('error_tags', sprintf(__('La etiqueta \'%s\' no es válida. Debe tener al menos %s caracteres.', FALSE), $v, $keyword_len));
						$error = TRUE;
						break;
					}

					// Verifico que sea permitida.
					if (in_array($v, $keyword_bloqueadas))
					{
						$view->assign('error_tags', sprintf(__('La etiqueta \'%s\' no está permitida.', FALSE), $v));
						$error = TRUE;
						break;
					}
				}

				// Verifico la cantidad.
				if ( ! $error && count($tags) < 3)
				{
					$view->assign('error_tags', __('Debes insertar un mínimo de 3 etiquetas.', FALSE));
					$error = TRUE;
				}
			}

			// Procedemos a crear el post.
			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Formateamos los campos.
				$titulo = trim(preg_replace('/\s+/', ' ', $titulo));

				// Obtengo listado a agregar, quitar y mantener.
				$delta_etiquetas = array_intersect($model_post->etiquetas(), $tags);
				$etiquetas_eliminadas = array_diff($model_post->etiquetas(), $delta_etiquetas);
				$etiquetas_nuevos = array_diff($tags, $delta_etiquetas);
				unset($tags, $delta_etiquetas);

				$datos = array(
						'titulo' => $titulo,
						'contenido' => $contenido,
						'categoria_id' => $categoria_id,
						'privado' => $privado,
						'sponsored' => $patrocinado,
						'sticky' => $sticky,
						'comentar' => ! $comentar
				);

				// Verifico parámetros especiales.
				if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
				{
					unset($datos['sponsored'], $datos['sticky']);
				}

				// Actualizo los parámetros.
				$rst = $model_post->actualizar_campos($datos);

				// Actualizo las etiquetas.
				if (is_array($etiquetas_eliminadas) && count($etiquetas_eliminadas) > 0)
				{
					$rst = $rst || $model_post->borrar_etiqueta($etiquetas_eliminadas);
				}
				if (is_array($etiquetas_nuevos) && count($etiquetas_nuevos) > 0)
				{
					$rst = $rst || $model_post->agregar_etiqueta($etiquetas_nuevos);
				}

				// Emito suceso para el usuario.
				if ($rst)
				{
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_editado', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_editado', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_editado', FALSE, $model_post->id, Usuario::$usuario_id);
					}
				}

				// Informo que todo fue correcto.
				add_flash_message(FLASH_SUCCESS, __('Actualización del post correcta.', FALSE));
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Agregamos una denuncia a un post.
	 * @param int $post
	 */
	public function action_denunciar($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder denunciar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que desea denunciar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verificamos que no sea autor.
		if ($model_post->usuario_id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El post que desea denunciar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Asignamos el título.
		$this->template->assign('title', __('Denunciar post', FALSE));

		// Cargamos la vista.
		$view = View::factory('post/denunciar');

		$p = $model_post->as_array();
		$p['categoria'] = $model_post->categoria()->as_array();
		$view->assign('post', $p);
		unset($p);

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
			if ( ! in_array($motivo, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)))
			{
				$error = TRUE;
				$view->assign('error_motivo', __('No ha seleccionado un motivo válido.', FALSE));
			}

			// Verifico la razón si corresponde.
			if ($motivo === 12)
			{
				if ( ! isset($motivo{10}) || isset($motivo{400}))
				{
					$error = TRUE;
					$view->assign('error_contenido', __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE));
				}
			}
			else
			{
				if (isset($motivo{400}))
				{
					$error = TRUE;
					$view->assign('error_contenido', __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE));
				}
				$comentario = NULL;
			}

			if ( ! $error)
			{
				// Creo la denuncia.
				$id = $model_post->denunciar(Usuario::$usuario_id, $motivo, $comentario);

				// Actualizo las medallas.
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_DENUNCIAS);

				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $model_post->usuario_id)
				{
					$model_suceso->crear($model_post->usuario_id, 'post_denuncia_crear', TRUE, $id);
					$model_suceso->crear(Usuario::$usuario_id, 'post_denuncia_crear', FALSE, $id);
				}
				else
				{
					$model_suceso->crear($model_post->usuario_id, 'post_denuncia_crear', FALSE, $id);
				}

				// Seteamos mensaje flash y volvemos.
				add_flash_message(FLASH_SUCCESS, __('Denuncia enviada correctamente.', FALSE));
				Request::redirect($this->post_url($model_post));
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu());

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Agregamos un comentario a un post.
	 * @param int $post ID del post donde colocar el comentario.
	 */
	public function action_comentar($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder comentar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verificamos el método de envio.
		if (Request::method() != 'POST')
		{
			Request::redirect($this->post_url($model_post));
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que desea comentar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR) || ( ! $model_post->comentar && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO)))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas comentar no permite comentarios nuevos.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Obtenemos el comentario.
		$comentario = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

		// Verificamos el formato.
		$comentario_clean = preg_replace('/\[.*\]/', '', $comentario);
		if ( ! isset($comentario_clean{10}) || isset($comentario{400}))
		{
			$_SESSION['post_comentario_error'] = __('El comentario debe tener entre 20 y 400 caracteres.', FALSE);

			// Evitamos la salida de la vista actual.
			$this->template = NULL;

			Dispatcher::call($this->post_url($model_post), TRUE);
		}
		else
		{
			// Transformamos entidades HTML.
			$comentario = htmlentities($comentario, ENT_NOQUOTES, 'UTF-8');

			// Insertamos el comentario.
			$id = $model_post->comentar(Usuario::$usuario_id, $comentario);

			if ($id)
			{
				// Agregamos los sucesos.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $model_post->usuario_id)
				{
					$model_suceso->crear($model_post->usuario_id, 'post_comentario_crear', TRUE, $id);
					$model_suceso->crear(Usuario::$usuario_id, 'post_comentario_crear', FALSE, $id);
				}
				else
				{
					$model_suceso->crear($model_post->usuario_id, 'post_comentario_crear', FALSE, $id);
				}

				// Envio sucesos de citas.
				Decoda::procesar($comentario, FALSE);

				// Verifico actualización del rango.
				Usuario::usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_COMENTARIOS);

				// Verifico actualización de medallas.
				Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_POSTS);
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_COMENTARIOS);

				$_SESSION['post_comentario_success'] = __('El comentario se ha realizado correctamente.', FALSE);

				Request::redirect($this->post_url($model_post));
			}
			else
			{
				$_SESSION['post_comentario_error'] = __('Se produjo un error al colocar el comentario. Reintente.', FALSE);

				// Evitamos la salida de la vista actual.
				$this->template = NULL;

				//Dispatcher::call($this->post_url($model_post), TRUE);
			}
		}
	}

	/**
	 * Agregamos el post como favorito.
	 * @param int $post ID del post que se toma como favorito.
	 */
	public function action_favorito($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder agregar posts a favoritos.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que desea agregar a favoritos no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verificamos el autor.
		if ($model_post->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El post que desea agregar a favoritos no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico el estado.
		if ($model_post->estado !== Model_Post::ESTADO_ACTIVO)
		{
			add_flash_message(FLASH_ERROR, __('El post que desea agregar a favoritos no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico no tenerlo como favorito.
		if ($model_post->es_favorito(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('El post ya forma parte de tus favoritos.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Agrego el post a favoritos.
		$model_post->favorito(Usuario::$usuario_id);

		// Verifico medallas.
		$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_FAVORITOS);

		// Creo el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_favorito', TRUE, $post, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'post_favorito', FALSE, $post, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_favorito', FALSE, $post, Usuario::$usuario_id);
		}

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El post fue agregado a favoritos correctamente.', FALSE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Votar un comentario.
	 * @param int $comentario ID del comentario a votar.
	 * @param int $voto 1 para positivo, -1 para negativo.
	 */
	public function action_voto_comentario($comentario, $voto)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder votar comentario en posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Obtenemos el voto.
		$voto = $voto == 1;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas votar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas votar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Post donde se encuentra el comentario.
		$model_post = $model_comentario->post();

		// Verifico estado del post.
		if ($model_post->estado !== Model_Post::ESTADO_ACTIVO)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas votar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}
		unset($model_post);

		// Verifico autor del post.
		if ($model_comentario->usuario_id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas votar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Verifico si ya votó.
		if ($model_comentario->ya_voto(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas votar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Agrego el voto.
		$model_comentario->votar(Usuario::$usuario_id, $voto);

		// Agrego el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_comentario->usuario_id)
		{
			$model_suceso->crear($model_comentario->usuario_id, 'post_comentario_voto', TRUE, $comentario, Usuario::$usuario_id, (int) $voto);
			$model_suceso->crear(Usuario::$usuario_id, 'post_comentario_voto', FALSE, $comentario, Usuario::$usuario_id, (int) $voto);
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, 'post_comentario_voto', FALSE, $comentario, Usuario::$usuario_id, (int) $voto);
		}

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El comentario se ha votado correctamente.', FALSE));
		Request::redirect($this->post_url($model_comentario->post_id));
	}

	/**
	 * Ocultamos un comentario.
	 * @param int $comentario ID del comentario a ocultar.
	 * @param bool $tipo 0 para ocultar, 1 para mostrar.
	 */
	public function action_ocultar_comentario($comentario, $tipo)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder ocultar/mostrar comentarios en posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas ocultar/mostrar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Valido el tipo.
		$tipo = (bool) $tipo;

		// Verifico el estado.
		if (($tipo && $model_comentario->estado !== 1) || ( ! $tipo && $model_comentario->estado !== 0))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas ocultar/mostrar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Verifico los permisos.
		if ($model_comentario->estado == 0 && Usuario::$usuario_id !== $model_comentario->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para ocultar/mostrar comentarios.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}
		elseif ($model_comentario->estado == 1 && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para ocultar/mostrar comentarios.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		//TODO: agregar otro estado para diferenciar usuario de moderador.

		// Actualizo el estado.
		$model_comentario->actualizar_estado($tipo ? Model_Post_Comentario::ESTADO_VISIBLE : Model_Post_Comentario::ESTADO_OCULTO);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id == $model_comentario->usuario_id)
		{
			$model_suceso->crear(Usuario::$usuario_id, $tipo ? 'post_comentario_mostrar' : 'post_comentario_ocultar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id != $model_comentario->post()->usuario_id)
			{
				$model_suceso->crear($model_comentario->post()->usuario_id, $tipo ? 'post_comentario_mostrar' : 'post_comentario_ocultar', TRUE, $comentario, Usuario::$usuario_id);
			}
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, $tipo ? 'post_comentario_mostrar' : 'post_comentario_ocultar', TRUE, $comentario, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, $tipo ? 'post_comentario_mostrar' : 'post_comentario_ocultar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id == $model_comentario->post()->usuario_id)
			{
				$model_suceso->crear($model_comentario->post()->usuario_id, $tipo ? 'post_comentario_mostrar' : 'post_comentario_ocultar', FALSE, $comentario, Usuario::$usuario_id);
			}
		}

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El comentario se ha ocultado/mostrado correctamente.', FALSE));
		Request::redirect($this->post_url($model_comentario->post_id));
	}

	/**
	 * Eliminamos un comentario en un post.
	 * @param int $comentario ID del comentario a eliminar.
	 */
	public function action_eliminar_comentario($comentario)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder borrar comentarios en posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para borrar comentarios.', FALSE));
			Request::redirect('/');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico el estado.
		if ($model_comentario->estado === 2)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Actualizo el estado.
		$model_comentario->actualizar_estado(Model_Post_Comentario::ESTADO_BORRADO);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id == $model_comentario->usuario_id)
		{
			$model_suceso->crear(Usuario::$usuario_id, 'post_comentario_borrar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id != $model_comentario->post()->usuario_id)
			{
				$model_suceso->crear($model_comentario->post()->usuario_id, 'post_comentario_borrar', TRUE, $comentario, Usuario::$usuario_id);
			}
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, 'post_comentario_borrar', TRUE, $comentario, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'post_comentario_borrar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id == $model_comentario->post()->usuario_id)
			{
				$model_suceso->crear($model_comentario->post()->usuario_id, 'post_comentario_borrar', FALSE, $comentario, Usuario::$usuario_id);
			}
		}

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El comentario se ha borrado correctamente.', FALSE));
		Request::redirect($this->post_url($model_comentario->post_id));
	}

	/**
	 * Editamos un comentario.
	 * @param int $comentario ID del comentario a editar.
	 */
	public function action_editar_comentario($comentario)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder editar comentarios en posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas editar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico el estado.
		if ($model_comentario->estado === 2)
		{
			add_flash_message(FLASH_ERROR, __('El comentario que deseas editar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Verifico permisos estado.
		if ($model_comentario->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para editar el comentario.', FALSE));
			Request::redirect($this->post_url($model_comentario->post_id));
		}

		// Cargo la vista.
		$vista = View::factory('/post/editar_comentario');

		// Seteo información del comentario.
		$vista->assign('contenido', $model_comentario->contenido);
		$vista->assign('error_contenido', FALSE);
		$vista->assign('usuario', $model_comentario->usuario()->as_array());

		$p = $model_comentario->post()->as_array();
		$p['categoria'] = $model_comentario->post()->categoria()->as_array();
		$vista->assign('post', $p);
		unset($p);
		$vista->assign('comentario', $model_comentario->as_array());

		if (Request::method() === 'POST')
		{
			// Cargo el comentario.
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';

			// Seteo enviado.
			$vista->assign('contenido', $contenido);

			// Verificamos el formato.
			$comentario_clean = preg_replace('/\[.*\]/', '', $contenido);
			if ( ! isset($comentario_clean{20}) || isset($contenido{400}))
			{
				$vista->assign('error_contenido', __('El comentario debe tener entre 20 y 400 caracteres.', FALSE));
			}
			else
			{
				// Transformamos entidades HTML.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Insertamos el comentario.
				$model_comentario->actualizar_campo('contenido', $contenido);

				// Envio el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id == $model_comentario->usuario_id)
				{
					$model_suceso->crear(Usuario::$usuario_id, 'post_comentario_editar', FALSE, $comentario, Usuario::$usuario_id);
					if (Usuario::$usuario_id != $model_comentario->post()->usuario_id)
					{
						$model_suceso->crear($model_comentario->post()->usuario_id, 'post_comentario_editar', TRUE, $comentario, Usuario::$usuario_id);
					}
				}
				else
				{
					$model_suceso->crear($model_comentario->usuario_id, 'post_comentario_editar', TRUE, $comentario, Usuario::$usuario_id);
					$model_suceso->crear(Usuario::$usuario_id, 'post_comentario_editar', FALSE, $comentario, Usuario::$usuario_id);
					if (Usuario::$usuario_id == $model_comentario->post()->usuario_id)
					{
						$model_suceso->crear($model_comentario->post()->usuario_id, 'post_comentario_editar', FALSE, $comentario, Usuario::$usuario_id);
					}
				}

				$_SESSION['post_comentario_success'] = __('El comentario se ha actualizado correctamente.', FALSE);
				Request::redirect($this->post_url($model_comentario->post_id));
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu());

		// Asignamos la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Seguimos a un usuario.
	 * @param int $post ID del post que estamos viendo.
	 * @param int $usuario ID del usuario a seguir.
	 * @param bool $seguir TRUE para seguir, FALSE para dejar de seguir.
	 */
	public function action_seguir_usuario($post, $usuario, $seguir)
	{
		$seguir = (bool) $seguir;

		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			if ($seguir)
			{
				add_flash_message(FLASH_ERROR, 'Debes iniciar sesión para poder seguir usuarios.');
			}
			else
			{
				add_flash_message(FLASH_ERROR, 'Debes iniciar sesión para poder dejar de seguir usuarios.');
			}
			Request::redirect('/usuario/login');
		}

		// Cargo el usuario.
		$usuario = (int) $usuario;
		$model_usuario = new Model_Usuario($usuario);

		// Verifico existencia.
		if ( ! $model_usuario->existe())
		{
			if ($seguir)
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->post_url($post));
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $model_usuario->id)
		{
			if ($seguir)
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->post_url($post));
		}

		// Verificaciones especiales en función si lo voy a seguir o dejar de seguir.
		if ($seguir)
		{
			// Verifico el estado.
			if ($model_usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				Request::redirect($this->post_url($post));
			}

			// Verifico no sea seguidor.
			if ($model_usuario->es_seguidor(Usuario::$usuario_id))
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				Request::redirect($this->post_url($post));
			}

			// Sigo al usuario.
			$model_usuario->seguir(Usuario::$usuario_id);

			// Actualizo medallas.
			$model_usuario->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_SEGUIDORES);
			Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_SIGUIENDO);
		}
		else
		{
			// Verifico sea seguidor.
			if ( ! $model_usuario->es_seguidor(Usuario::$usuario_id))
			{
				add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
				Request::redirect($this->post_url($post));
			}

			// Dejo de seguir al usuario.
			$model_usuario->fin_seguir(Usuario::$usuario_id);
		}

		// Envio el suceso.
		$tipo = $seguir ? 'usuario_seguir' : 'usuario_fin_seguir';
		$model_suceso = new Model_Suceso;
		if ($model_usuario->id != Usuario::$usuario_id)
		{
			$model_suceso->crear($model_usuario->id, $tipo, TRUE, $model_usuario->id, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, $tipo, FALSE, $model_usuario->id, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_usuario->id, $tipo, TRUE, $model_usuario->id, Usuario::$usuario_id);
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
		Request::redirect($this->post_url($post));
	}

	/**
	 * Nos convertimos en seguidores de un post.
	 * @param int $post ID del post a seguir.
	 */
	public function action_seguir_post($post)
	{
		// Verificamos usuario tenga sesión iniciada.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder seguir posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que quieres seguir no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico el autor.
		if ($model_post->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El post que quieres seguir no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico si ya lo sigue.
		if ($model_post->es_seguidor(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('Ya eres seguidor de ese post.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		$model_post->seguir(Usuario::$usuario_id);

		// Actualizo medallas.
		$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_SEGUIDORES);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_seguir', TRUE, $post, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'post_seguir', FALSE, $post, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_seguir', FALSE, $post, Usuario::$usuario_id);
		}

		add_flash_message(FLASH_SUCCESS, __('Te has convertido en seguidor del post correctamente.', FALSE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Setamos o quitamos el atributo sticky de un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se agrega o quita.
	 */
	public function action_fijar_post($post, $tipo)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder fijar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para fijar un post.', FALSE));
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas fijar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Valido el valor.
		$tipo = $tipo == 1;

		// Verifico el estado del parámetro.
		if ($model_post->sticky === $tipo)
		{
			if ($tipo)
			{
				add_flash_message(FLASH_ERROR, __('El post que quieres fijar ya se encuentra fijo a la portada.', FALSE));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('El post no se encuentra fijo a la portada.', FALSE));
			}
			Request::redirect($this->post_url($model_post));
		}

		// Actualizo el parámetro.
		$model_post->setear_sticky($tipo);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_fijar', TRUE, $post, Usuario::$usuario_id, (int) $tipo);
			$model_suceso->crear(Usuario::$usuario_id, 'post_fijar', FALSE, $post, Usuario::$usuario_id, (int) $tipo);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_fijar', FALSE, $post, Usuario::$usuario_id, (int) $tipo);
		}

		// Informo el resultado.
		if ($tipo)
		{
			add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El post se ha fijado a la portada correctamente.', FALSE));
		}
		else
		{
			add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El post se ha quitado de los posts fijos en la portada correctamente.', FALSE));
		}
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Setamos o quitamos el atributo sponsored de un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se agrega o quita.
	 */
	public function action_patrocinar_post($post, $tipo)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder patrocinar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos necesarios para patrocinar posts.', FALSE));
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas patrocinar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Valido el valor.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($model_post->sponsored === $tipo)
		{
			add_flash_message(FLASH_ERROR, __('El post que quieres patrocinar ya se encuentra patrocinado.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Actualizo el parámetro.
		$model_post->setear_sponsored($tipo);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_patrocinar', TRUE, $post, Usuario::$usuario_id, $tipo);
			$model_suceso->crear(Usuario::$usuario_id, 'post_patrocinar', FALSE, $post, Usuario::$usuario_id, $tipo);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_patrocinar', FALSE, $post, Usuario::$usuario_id, $tipo);
		}

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> Acción realizada correctamente.', FALSE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Oculto o muestro un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se muestra o se oculta.
	 */
	public function action_ocultar_post($post, $tipo)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder ocultar/mostrar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verifico el usuario y sus permisos.
		if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_OCULTAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para ocultar/mostrar posts.', FALSE));
			Request::redirect('/');
		}

		// Verificamos exista el post.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que quieres ocultar/mostrar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if (($tipo && $model_post->estado !== Model_Post::ESTADO_OCULTO) || ( ! $tipo && $model_post->estado !== Model_Post::ESTADO_ACTIVO))
		{
			add_flash_message(FLASH_ERROR, __('El post que quieres ocultar/mostrar no se encuentra disponible', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		if ($tipo)
		{
			// Verifico actualización del rango.
			$model_post->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_POST);
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_OCULTO);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_ocultar', TRUE, $post, Usuario::$usuario_id, (int) $tipo);
			$model_suceso->crear(Usuario::$usuario_id, 'post_ocultar', FALSE, $post, Usuario::$usuario_id, (int) $tipo);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_ocultar', FALSE, $post, Usuario::$usuario_id, (int) $tipo);
		}

		// Informo resultado
		add_flash_message(FLASH_SUCCESS, __('El post se ocultó/mostró correctamente.', FALSE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Apruebo o rechazo un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se aprueba o se rechaza.
	 */
	public function action_aprobar_post($post, $tipo)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sessión para poder aprobar/rechazar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para aprobar/rechazar posts', FALSE));
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_RECHAZADO))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}
		elseif ( ! $tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_ACTIVO))
		{
			add_flash_message(FLASH_ERROR, __('El post que deseas aprobar/rechazar no se encuentra disponible.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_RECHAZADO);

		// Verifico actualización del rango.
		$model_post->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_POST);

		// Verifico actualización medallas.
		$model_post->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_POSTS);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_aprobar', TRUE, $post, Usuario::$usuario_id, $tipo);
			$model_suceso->crear(Usuario::$usuario_id, 'post_aprobar', FALSE, $post, Usuario::$usuario_id, $tipo);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_aprobar', FALSE, $post, Usuario::$usuario_id, $tipo);
		}

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El estado se modificó correctamente.', FALSE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Borramos un post o lo enviamos a la papelera.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Borra o se envia a la papelera.
	 */
	public function action_borrar_post($post, $tipo)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sessión para poder borrar/enviar a la papelera posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que quieres borrar/enviar a la papelera no se encuentra disponible.', FALSE));
			Request::redirect('/');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_ELIMINAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos suficientes para borrar/enviar a la papelera al post.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico requisitos según permisos y usuario.
		if (Usuario::$usuario_id == $model_post->usuario_id)
		{
			if ($model_post->estado === Model_Post::ESTADO_ACTIVO)
			{
				$tipo = $tipo != -1;
			}
			elseif ($model_post->estado !== Model_Post::ESTADO_PAPELERA && $model_post !== Model_Post::ESTADO_OCULTO)
			{
				add_flash_message(FLASH_ERROR, __('El post que quieres borrar/enviar a la papelera no se encuentra disponible.', FALSE));
				Request::redirect($this->post_url($model-post));
			}
			else
			{
				$tipo = TRUE;
			}
		}
		else
		{
			$tipo = TRUE;
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_BORRADO : Model_Post::ESTADO_PAPELERA);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, $tipo ? 'post_borrar' : 'post_papelera', TRUE, $post, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, $tipo ? 'post_borrar' : 'post_papelera', FALSE, $post, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, $tipo ? 'post_borrar' : 'post_papelera', FALSE, $post, Usuario::$usuario_id);
		}

		// Informamos resultado.
		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> Acción realizada correctamente.', FALSE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Restauro un post enviado a la papelera.
	 * @param int $post ID del post a restaurar.
	 */
	public function action_restaurar_post($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder restaurar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('<b>!Error!</b> Post incorrecto.', FALSE));
			Request::redirect('/');
		}

		// Verifico el usuario.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA))
		{
			add_flash_message(FLASH_ERROR, __('<b>!Error!</b> Permisos incorrectos.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico el estado.
		if ($model_post->estado !== Model_Post::ESTADO_PAPELERA)
		{
			add_flash_message(FLASH_ERROR, __('<b>!Error!</b> Permisos incorrectos.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Actualizo el estado.
		$model_post->actualizar_estado(Model_Post::ESTADO_ACTIVO);

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> Acción realizada correctamente.', FALSE));

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_restaurar', TRUE, Usuario::$usuario_id, $post);
			$model_suceso->crear(Usuario::$usuario_id, 'post_restaurar', FALSE, Usuario::$usuario_id, $post);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_restaurar', FALSE, Usuario::$usuario_id, $post);
		}
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Publico un post marcado como borrador.
	 * @param int $post ID del post a publicar.
	 */
	public function action_publicar_post($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder publicar posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('<b>!Error!</b> Post incorrecto.', FALSE));
			Request::redirect('/');
		}

		// Verifico el usuario.
		if (Usuario::$usuario_id !== $model_post->usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('<b>!Error!</b> Permisos incorrectos.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico el estado.
		if ($model_post->estado !== Model_Post::ESTADO_BORRADOR)
		{
			add_flash_message(FLASH_ERROR, __('<b>!Error!</b> Permisos incorrectos.', FALSE));
			Request::redirect($this->post_url($model_post));
		}

		// Actualizo el estado.
		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_REVISAR_CONTENIDO))
		{
			$model_post->actualizar_estado(Model_Post::ESTADO_PENDIENTE);
			$model_post->actualizar_fecha();
		}
		else
		{
			$model_post->actualizar_estado(Model_Post::ESTADO_ACTIVO);
			$model_post->actualizar_fecha();
		}

		// Verifico actualización del rango.
		$model_post->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_POST);

		// Verifico actualización medallas.
		$model_post->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_POSTS);

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> Acción realizada correctamente.', FALSE));

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(Usuario::$usuario_id, 'post_publicado', FALSE, Usuario::$usuario_id, $post);

		Request::redirect($this->post_url($model_post));
	}


	/**
	 * Damos puntos a un post.
	 * @param int $post ID del post al cual darle puntos.
	 * @param int $cantidad Cantidad de puntos. Número entre 1 y 10.
	 */
	public function action_puntuar($post, $cantidad)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder puntuar posts.', FLASE));
			Request::redirect('/usuario/login');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_PUNTUAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para puntuar posts.', FLASE));
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Validamos la cantidad.
		$cantidad = (int) $cantidad;

		// Cantidad de puntos a dar como máximo.
		if ($cantidad < 1 || $cantidad > Usuario::usuario()->rango()->puntos_dar)
		{
			add_flash_message(FLASH_ERROR, __('La petición que has realizado es inválida.', FLASE));
			Request::redirect('/');
		}

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('El post que desea puntuar no se encuentra disponible.', FLASE));
			Request::redirect('/');
		}

		// Verifico el estado del post.
		if ($model_post->estado !== Model_Post::ESTADO_ACTIVO)
		{
			add_flash_message(FLASH_ERROR, __('El post que desea puntuar no se encuentra disponible.', FLASE));
			Request::redirect($this->post_url($model_post));
		}

		// Verifico el autor.
		if ($model_post->usuario_id === Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El post que desea puntuar no se encuentra disponible.', FLASE));
			Request::redirect($this->post_url($model_post));
		}

		// Verificamos si ya dio puntos.
		if ($model_post->dio_puntos(Usuario::$usuario_id))
		{
			add_flash_message(FLASH_ERROR, __('El post que desea puntuar ya ha sido puntuado por usted.', FLASE));
			Request::redirect($this->post_url($model_post));
		}

		// Verificamos la cantidad de puntos.
		if (Usuario::usuario()->puntos < $cantidad)
		{
			add_flash_message(FLASH_ERROR, __('El post que desea puntuar ya ha sido puntuado por usted.', FLASE));
			Request::redirect($this->post_url($model_post));
		}

		// Damos los puntos.
		$model_post->dar_puntos(Usuario::$usuario_id, $cantidad);

		// Verifico actualización del rango.
		$model_post->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_PUNTOS);

		// Verifico actualización medallas.
		$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_PUNTOS);
		$model_post->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_PUNTOS);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_post->usuario_id)
		{
			$model_suceso->crear($model_post->usuario_id, 'post_puntuar', TRUE, $post, Usuario::$usuario_id, $cantidad);
			$model_suceso->crear(Usuario::$usuario_id, 'post_puntuar', FALSE, $post, Usuario::$usuario_id, $cantidad);
		}
		else
		{
			$model_suceso->crear($model_post->usuario_id, 'post_puntuar', FALSE, $post, Usuario::$usuario_id, $cantidad);
		}

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('Has puntuado el post de manera correcta.', FLASE));
		Request::redirect($this->post_url($model_post));
	}

	/**
	 * Creamos un nuevo post.
	 */
	public function action_nuevo()
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder crear posts.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_CREAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para poder crear posts.', FALSE));
			Request::redirect('/');
		}

		// Asignamos el título.
		$this->template->assign('title', __('Nuevo post', FALSE));

		// Cargamos la vista.
		$view = View::factory('post/nuevo');

		// Seteo permisos especiales.
		$view->assign('permisos_especiales', Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER));

		// Elementos por defecto.
		foreach (array('captcha', 'titulo', 'contenido', 'categoria', 'privado', 'patrocinado', 'sticky', 'comentar', 'tags', 'error_titulo', 'error_contenido', 'error_categoria', 'error_tags') as $k)
		{
			$view->assign($k, '');
			$view->assign('error_'.$k, FALSE);
		}

		// Listado de categorías.
		$model_categoria = new Model_Categoria;
		$view->assign('categorias', $model_categoria->lista());

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			foreach (array('titulo', 'contenido', 'categoria', 'tags', 'captcha') as $k)
			{
				$$k = isset($_POST[$k]) ? $_POST[$k] : '';
				$view->assign($k, $$k);
			}

			// Obtenemos los checkbox.
			foreach (array('privado', 'patrocinado', 'sticky', 'comentar') as $k)
			{
				$$k = isset($_POST[$k]) ? ($_POST[$k] == 1) : FALSE;
				$view->assign($k, $$k);
			}

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', __('El formato del título no es correcto.', FALSE));
				$error = TRUE;
			}

			// Verificamos el contenido.
			$contenido_clean = preg_replace('/\[.*\]/', '', $contenido);
			if ( ! isset($contenido_clean{20}) || isset($contenido{5000}))
			{
				$view->assign('error_contenido', __('El contenido debe tener entre 20 y 5000 caracteres.', FALSE));
				$error = TRUE;
			}
			unset($contenido_clean);

			// Verificamos la categoria.
			$model_categoria = new Model_Categoria;
			if ( ! $model_categoria->existe_seo($categoria))
			{
				$view->assign('error_categoria', __('La categoría seleccionada es incorrecta.', FALSE));
				$error = TRUE;
			}
			else
			{
				$model_categoria->load_by_seo($categoria);
				$categoria_id = $model_categoria->id;
			}
			unset($model_categoria);

			// Quito espacios adicionales a las etiquetas.
			$tags = preg_replace('/\s+/', ' ', trim($tags));

			// Verificamos las etiquetas.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóúñÑÁÉÍÓÚ, ]{0,}$/D', $tags))
			{
				$view->assign('error_tags', __('Las etiquetas ingresadas con son alphanuméricas.', FALSE));
				$error = TRUE;
			}

			// Verifico CAPTCHA.
			include_once(VENDOR_PATH.'securimage'.DS.'securimage.php');
			$securimage = new securimage;
			if ($securimage->check($captcha) === FALSE)
			{
				$view->assign('error_captcha', TRUE);
				$error = TRUE;
			}

			if ( ! $error)
			{
				// Configuraciones sobre etiquetas.
				$model_config = new Model_Configuracion;
				$keyword_len = (int) $model_config->get('keyword_largo_minimo', 3);
				$keyword_bloqueadas = unserialize($model_config->get('keyword_palabras_comunes', 'a:0:{}'));

				// Obtengo el listado de etiquetas.
				$tags = explode(',', $tags);
				foreach ($tags as $k => $v)
				{
					// Elimino espacios extra.
					$tags[$k] = trim(strtolower($v));

					// Verifico no sea vacia.
					if ($tags[$k] == '')
					{
						unset($tags[$k]);
						continue;
					}

					// Verifico largo.
					if (strlen($v) < $keyword_len)
					{
						$view->assign('error_tags', sprintf(__('La etiqueta \'%s\' no es válida. Debe tener al menos %s caracteres.', FALSE), $v, $keyword_len));
						$error = TRUE;
						break;
					}

					// Verifico que sea permitida.
					if (in_array($v, $keyword_bloqueadas))
					{
						$view->assign('error_tags', sprintf(__('La etiqueta \'%s\' no está permitida.', FALSE), $v));
						$error = TRUE;
						break;
					}
				}

				// Verifico la cantidad.
				if ( ! $error && count($tags) < 3)
				{
					$view->assign('error_tags', __('Debes insertar un mínimo de 3 etiquetas.', FALSE));
					$error = TRUE;
				}
			}

			// Procedemos a crear el post.
			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Verifico si es borrador.
				$borrador = isset($_POST['submit']) ? ($_POST['submit'] == 'borrador') : FALSE;

				// Obtengo el estado a aplicar.
				if ($borrador)
				{
					$estado = Model_Post::ESTADO_BORRADOR;
				}
				else
				{
					if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_REVISAR_CONTENIDO))
					{
						$estado = Model_Post::ESTADO_PENDIENTE;
					}
					else
					{
						$estado = Model_Post::ESTADO_ACTIVO;
					}
				}
				unset($borrador);

				// Verifico parámetros especiales.
				if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
				{
					$patrocinado = FALSE;
					$sticky = FALSE;
				}

				$model_post = new Model_Post;
				$post_id = $model_post->crear(Usuario::$usuario_id, $titulo, $contenido, $categoria_id, $privado, $patrocinado, $sticky, ! $comentar, $estado);

				if ($post_id > 0)
				{
					// Cargo el post.
					$model_post = new Model_Post($post_id);

					// Agrego las etiquetas.
					$model_post->agregar_etiqueta($tags);

					// Agrego el suceso.
					$model_suceso = new Model_Suceso;
					$model_suceso->crear(Usuario::$usuario_id, 'post_nuevo', FALSE, $post_id);

					// Verifico actualización del rango.
					Usuario::usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_POST);

					// Verifico actualización medallas.
					Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_POSTS);

					// Informo y voy a post.
					add_flash_message(FLASH_SUCCESS, __('El post fue creado correctamente.', FALSE));
					Request::redirect($this->post_url($model_post));
				}
				else
				{
					$view->assign('error', __('Se produjo un error cuando se creaba el post. Reintente.', FALSE));
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Vista preliminar de un comentario.
	 */
	public function action_preview()
	{
		// Obtengo el contenido y evitamos XSS.
		$contenido = isset($_POST['contenido']) ? htmlentities($_POST['contenido'], ENT_NOQUOTES, 'UTF-8') : '';

		// Evito salida por template.
		$this->template = NULL;

		// Proceso contenido.
		die(Decoda::procesar($contenido));
	}

	/**
	 * Obtenemos un listado de etiquetas para el post.
	 */
	public function action_etiquetas()
	{
		// Obtengo el contenido.
		$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';

		// Evito salida por template.
		$this->template = NULL;

		// Obtengo listado de etiquetas.
		$tags = new Keyword;

		// Proceso contenido.
		die(implode(', ', $tags->extract_keywords($contenido, TRUE)));
	}
}
