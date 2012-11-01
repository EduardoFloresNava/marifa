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
	 * Información de un post.
	 * @param int $post ID del post a visualizar.
	 */
	public function action_index($post)
	{
		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post al que intentas acceder no está disponible.';
			Request::redirect('/');
		}

		// Verifico el estado de post y permisos necesarios para acceder.
		switch ($model_post->estado)
		{
			case Model_Post::ESTADO_BORRADO:
				$_SESSION['flash_error'] = 'El post al que intentas acceder no existe.';
				Request::redirect('/');
				break;
			case Model_Post::ESTADO_PAPELERA:
				if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA))
				{
					$_SESSION['flash_error'] = 'El post al que intentas acceder no se encuentra disponible.';
					Request::redirect('/');
				}
				break;
			case Model_Post::ESTADO_BORRADOR:
				if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
				{
					$_SESSION['flash_error'] = 'El post al que intentas acceder no se encuentra disponible.';
					Request::redirect('/');
				}
				break;
			case Model_Post::ESTADO_PENDIENTE:
			case Model_Post::ESTADO_OCULTO:
			case Model_Post::ESTADO_RECHAZADO:
				if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
				{
					$_SESSION['flash_error'] = 'El post al que intentas acceder no se encuentra disponible.';
					Request::redirect('/');
				}
				break;
		}


		if ($model_post->as_object()->privado && ! Usuario::is_login())
		{
			// Asignamos el título.
			$this->template->assign('title', 'Post privado');

			$view = View::factory('post/privado');
			$view->assign('post', $model_post->as_array());
		}
		else
		{
			// Asignamos el título.
			$this->template->assign('title', $model_post->as_object()->titulo);

			// Cargamos la vista.
			$view = View::factory('post/index');

			// Verifico si debo contabilizar la visita.
			if (Usuario::$usuario_id != $model_post->as_object()->usuario_id)
			{
				$model_post->agregar_vista();
			}

			// Mi id.
			$view->assign('me', Usuario::$usuario_id);

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
			$view->assign('podemos_comentar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO) || $model_post->comentar && Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR));

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
					$p_d = $m_user->puntos_disponibles;

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

			// Comentarios del post.
			$cmts = $model_post->comentarios(NULL);
			$l_cmt = array();
			foreach ($cmts as $cmt)
			{
				// Verifico omito los no visibles si el usuario no puede verlos.
				if ($cmt->estado !== Model_Comentario::ESTADO_VISIBLE && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
				{
					continue;
				}

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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder editar posts.';
			Request::redirect('/usuario/login');
		}

		// Limpio la entrada
		$post = (int) $post;

		// Cargo el post.
		$model_post = new Model_Post($post);

		// Verifico exista.
		if ( ! $model_post->existe())
		{
			$_SESSION['flash_error'] = 'El post especificado no se encuentra disponible.';
			Request::redirect('/');
		}

		// No podemos editar posts borrados.
		if ($model_post->estado == Model_Post::ESTADO_BORRADO)
		{
			$_SESSION['flash_error'] = 'El post especificado no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico el usuario y el permiso de edición para terceros.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_EDITAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos necesarios para realizar esa edición.';
			Request::redirect('/post/index/'.$post);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Editar post');

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
		$view->assign('comentar', $model_post->comentar);
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
				$view->assign('error_titulo', 'El formato del título no es correcto.');
				$error = TRUE;
			}

			// Verificamos el contenido.
			$contenido_clean = preg_replace('/\[.*\]/', '', $contenido);
			if ( ! isset($contenido_clean{20}) || isset($contenido{5000}))
			{
				$view->assign('error_contenido', 'El contenido debe tener entre 20 y 5000 caractéres.');
				$error = TRUE;
			}
			unset($contenido_clean);

			// Verificamos la categoria.
			$model_categoria = new Model_Categoria;
			if ( ! $model_categoria->existe_seo($categoria))
			{
				$view->assign('error_categoria', 'La categoría seleccionada es incorrecta.');
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
				$view->assign('error_tags', 'Las etiquetas ingresadas con son alphanuméricas..');
				$error = TRUE;
			}

			// Procedemos a crear el post.
			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Formateamos los campos.
				$titulo = trim(preg_replace('/\s+/', ' ', $titulo));

				// Obtengo el listado de etiquetas.
				$tags = explode(',', $tags);
				foreach ($tags as $k => $v)
				{
					$tags[$k] = trim($v);
					if ($tags[$k] == '')
					{
						unset($tags[$k]);
					}
				}

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
						'comentar' => $comentar
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
					$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_editado', $model_post->id, Usuario::$usuario_id);
				}

				// Informo que todo fue correcto.
				$_SESSION['flash_success'] = 'Actualización del post correcta.';
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder denunciar posts.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que desea denunciar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verificamos que no sea autor.
		if ($model_post->usuario_id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El post que desea denunciar no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Denunciar post');

		// Cargamos la vista.
		$view = View::factory('post/denunciar');

		$view->assign('post', $model_post->id);

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
				$view->assign('error_motivo', 'No ha seleccionado un motivo válido.');
			}

			// Verifico la razón si corresponde.
			if ($motivo === 12)
			{
				if ( ! isset($motivo{10}) || isset($motivo{400}))
				{
					$error = TRUE;
					$view->assign('error_contenido', 'La descripción de la denuncia debe tener entre 10 y 400 caracteres.');
				}
			}
			else
			{
				if (isset($motivo{400}))
				{
					$error = TRUE;
					$view->assign('error_contenido', 'La descripción de la denuncia debe tener entre 10 y 400 caracteres.');
				}
				$comentario = NULL;
			}

			if ( ! $error)
			{
				// Creo la denuncia.
				$id = $model_post->denunciar(Usuario::$usuario_id, $motivo, $comentario);

				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_denuncia_crear', $id);

				// Seteamos mensaje flash y volvemos.
				$_SESSION['flash_success'] = 'Denuncia enviada correctamente.';
				Request::redirect('/post/index/'.$model_post->id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder comentar posts.';
			Request::redirect('/usuario/login');
		}

		// Verificamos el método de envio.
		if (Request::method() != 'POST')
		{
			Request::redirect('/post/index/'.$post);
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que desea comentar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR) || ( ! $model_post->comentar && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO)))
		{
			$_SESSION['flash_error'] = 'El post que deseas comentar no permite comentarios nuevos.';
			Request::redirect('/post/index/'.$post);
		}

		// Obtenemos el comentario.
		$comentario = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

		// Verificamos el formato.
		$comentario_clean = preg_replace('/\[.*\]/', '', $comentario);
		if ( ! isset($comentario_clean{20}) || isset($comentario{400}))
		{
			$_SESSION['post_comentario_error'] = 'El comentario debe tener entre 20 y 400 caracteres.';

			// Evitamos la salida de la vista actual.
			$this->template = NULL;

			Dispatcher::call('/post/index/'.$post, TRUE);
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
				$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_comentario_crear', $id);

				$_SESSION['post_comentario_success'] = 'El comentario se ha realizado correctamente.';

				Request::redirect('/post/index/'.$post);
			}
			else
			{
				$_SESSION['post_comentario_error'] = 'Se produjo un error al colocar el comentario. Reintente.';

				// Evitamos la salida de la vista actual.
				$this->template = NULL;

				Dispatcher::call('/post/index/'.$post, TRUE);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder agregar posts a favoritos.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que desea agregar a favoritos no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verificamos el autor.
		if ($model_post->usuario_id === Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El post que desea agregar a favoritos no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico el estado.
		if ($model_post->estado !== Model_Post::ESTADO_ACTIVO)
		{
			$_SESSION['flash_error'] = 'El post que desea agregar a favoritos no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico no tenerlo como favorito.
		if ($model_post->es_favorito(Usuario::$usuario_id))
		{
			$_SESSION['flash_error'] = 'El post ya forma parte de tus favoritos.';
			Request::redirect('/post/index/'.$post);
		}

		// Agrego el post a favoritos.
		$model_post->favorito(Usuario::$usuario_id);

		// Creo el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_favorito', $post, Usuario::$usuario_id);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El post fue agregado a favoritos correctamente.';
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder votar comentario en posts.';
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
			$_SESSION['flash_error'] = 'El comentario que deseas votar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR))
		{
			$_SESSION['flash_error'] = 'El comentario que deseas votar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Post donde se encuentra el comentario.
		$model_post = $model_comentario->post();

		// Verifico estado del post.
		if ($model_post->estado !== Model_Post::ESTADO_ACTIVO)
		{
			$_SESSION['flash_error'] = 'El comentario que deseas votar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}
		unset($model_post);

		// Verifico autor del post.
		if ($model_comentario->usuario_id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El comentario que deseas votar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Verifico si ya votó.
		if ($model_comentario->ya_voto(Usuario::$usuario_id))
		{
			$_SESSION['flash_error'] = 'El comentario que deseas votar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Agrego el voto.
		$model_comentario->votar(Usuario::$usuario_id, $voto);

		// Agrego el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_comentario->usuario_id), 'post_comentario_voto', $comentario, Usuario::$usuario_id, (int) $voto);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El comentario se ha votado correctamente.';
		Request::redirect('/post/index/'.$model_comentario->post_id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder ocultar/mostrar comentarios en posts.';
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			$_SESSION['flash_error'] = 'El comentario que deseas ocultar/mostrar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Valido el tipo.
		$tipo = (bool) $tipo;

		// Verifico el estado.
		if (($tipo && $model_comentario->estado !== 1) || ( ! $tipo && $model_comentario->estado !== 0))
		{
			$_SESSION['flash_error'] = 'El comentario que deseas ocultar/mostrar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Verifico los permisos.
		if ($model_comentario->estado == 0 && Usuario::$usuario_id !== $model_comentario->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para ocultar/mostrar comentarios.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}
		elseif ($model_comentario->estado == 1 && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para ocultar/mostrar comentarios.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		//TODO: agregar otro estado para diferenciar usuario de moderador.

		// Actualizo el estado.
		$model_comentario->actualizar_estado($tipo ? Model_Post_Comentario::ESTADO_VISIBLE : Model_Post_Comentario::ESTADO_OCULTO);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_comentario->usuario_id, $model_comentario->post()->usuario_id), $tipo ? 'post_comentario_mostrar' : 'post_comentario_ocultar', $comentario, Usuario::$usuario_id);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El comentario se ha ocultado/mostrado correctamente.';
		Request::redirect('/post/index/'.$model_comentario->post_id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder borrar comentarios en posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para borrar comentarios.';
			Request::redirect('/');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			$_SESSION['flash_error'] = 'El comentario que deseas borrar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico el estado.
		if ($model_comentario->estado === 2)
		{
			$_SESSION['flash_error'] = 'El comentario que deseas borrar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Actualizo el estado.
		$model_comentario->actualizar_estado(Model_Post_Comentario::ESTADO_BORRADO);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_comentario->usuario_id, $model_comentario->post()->usuario_id), 'post_comentario_borrar', $comentario, Usuario::$usuario_id);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El comentario se ha borrado correctamente.';
		Request::redirect('/post/index/'.$model_comentario->post_id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder editar comentarios en posts.';
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			$_SESSION['flash_error'] = 'El comentario que deseas editar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico el estado.
		if ($model_comentario->estado === 2)
		{
			$_SESSION['flash_error'] = 'El comentario que deseas editar no se encuentra disponible.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Verifico permisos estado.
		if ($model_comentario->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para editar el comentario.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Cargo la vista.
		$vista = View::factory('/post/editar_comentario');

		// Seteo información del comentario.
		$vista->assign('contenido', $model_comentario->contenido);
		$vista->assign('error_contenido', FALSE);
		$vista->assign('usuario', $model_comentario->usuario()->as_array());
		$vista->assign('post', $model_comentario->post()->as_array());
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
				$vista->assign('error_contenido', 'El comentario debe tener entre 20 y 400 caracteres.');
			}
			else
			{
				// Transformamos entidades HTML.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Insertamos el comentario.
				$model_comentario->actualizar_campo('contenido', $contenido);

				// Envio el suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array(Usuario::$usuario_id, $model_comentario->usuario_id, $model_comentario->post()->usuario_id), 'post_comentario_editar', $comentario, Usuario::$usuario_id);

				$_SESSION['post_comentario_success'] = 'El comentario se ha actualizado correctamente.';
				Request::redirect('/post/index/'.$model_comentario->post_id);
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu());

		// Asignamos la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Nos convertimos en seguidores de un post.
	 * @param int $post ID del post a seguir.
	 */
	public function action_seguir_post($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder seguir posts.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que quieres seguir no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico el autor.
		if ($model_post->usuario_id === Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El post que quieres seguir no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico si ya lo sigue.
		if ($model_post->es_seguidor(Usuario::$usuario_id))
		{
			$_SESSION['flash_error'] = 'Ya eres seguidor de ese post.';
			Request::redirect('/post/index/'.$post);
		}

		$model_post->seguir(Usuario::$usuario_id);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_seguir', $post, Usuario::$usuario_id);

		$_SESSION['flash_success'] = 'Te has convertido en seguidor del post correctamente.';
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder fijar posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para fijar un post.';
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que deseas fijar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Valido el valor.
		$tipo = $tipo == 1;

		// Verifico el estado del parámetro.
		if ($model_post->sticky === $tipo)
		{
			if ($tipo)
			{
				$_SESSION['flash_error'] = 'El post que quieres fijar ya se encuentra fijo a la portada.';
			}
			else
			{
				$_SESSION['flash_error'] = 'El post no se encuentra fijo a la portada.';
			}
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el parámetro.
		$model_post->setear_sticky($tipo);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_fijar', $post, Usuario::$usuario_id, (int) $tipo);

		// Informo el resultado.
		if ($tipo)
		{
			$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El post se ha fijado a la portada correctamente.';
		}
		else
		{
			$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El post se ha quitado de los posts fijos en la portada correctamente.';
		}
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder patrocinar posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos necesarios para patrocinar posts.';
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que deseas patrocinar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Valido el valor.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($model_post->sponsored === $tipo)
		{
			$_SESSION['flash_error'] = 'El post que quieres patrocinar ya se encuentra patrocinado.';
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el parámetro.
		$model_post->setear_sponsored($tipo);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_patrocinar', $post, Usuario::$usuario_id, $tipo);

		// Informo el resultado.
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder ocultar/mostrar posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico el usuario y sus permisos.
		if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_OCULTAR))
		{
			$_SESSION['flash_error'] = 'No tienes permiso para ocultar/mostrar posts.';
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista el post.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que quieres ocultar/mostrar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if (($tipo && $model_post->estado !== Model_Post::ESTADO_OCULTO) || ( ! $tipo && $model_post->estado !== Model_Post::ESTADO_ACTIVO))
		{
			$_SESSION['flash_error'] = 'El post que quieres ocultar/mostrar no se encuentra disponible';
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_OCULTO);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_ocultar', $post, Usuario::$usuario_id, (int) $tipo);

		// Informo resultado
		$_SESSION['flash_success'] = 'El post se ocultó/mostró correctamente.';
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder aprobar/rechazar posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para aprobar/rechazar posts';
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que deseas aprobar/rechazar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_RECHAZADO))
		{
			$_SESSION['flash_error'] = 'El post que deseas aprobar/rechazar no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}
		elseif ( ! $tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_ACTIVO))
		{
			$_SESSION['flash_error'] = 'El post que deseas aprobar/rechazar no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_RECHAZADO);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_aprobar', $post, Usuario::$usuario_id, $tipo);

		// Informo resultado.
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El estado se modific&oacute; correctamente.';
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder borrar/enviar a la papelera posts.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que quieres borrar/enviar a la papelera no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_POSTS))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos suficientes para borrar/enviar a la papelera al post.';
			Request::redirect('/post/index/'.$post);
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
				$_SESSION['flash_error'] = 'El post que quieres borrar/enviar a la papelera no se encuentra disponible.';
				Request::redirect('/post/index/'.$post);
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
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), $tipo ? 'post_borrar' : 'post_papelera', $post, Usuario::$usuario_id);

		// Informamos resultado.
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder restaurar posts.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Post incorrecto.';
			Request::redirect('/');
		}

		// Verifico el usuario.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico el estado.
		if ($model_post->estado !== Model_Post::ESTADO_PAPELERA)
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el estado.
		$model_post->actualizar_estado(Model_Post::ESTADO_ACTIVO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(
				array(
					Usuario::$usuario_id,
					$model_post->usuario_id
				),
				'post_restaurar',
				Usuario::$usuario_id,
				$post
			);
		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder publicar posts.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Post incorrecto.';
			Request::redirect('/');
		}

		// Verifico el usuario.
		if (Usuario::$usuario_id !== $model_post->usuario_id)
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico el estado.
		if ($model_post->estado !== Model_Post::ESTADO_BORRADOR)
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
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

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(
				Usuario::$usuario_id,
				'post_publicado',
				Usuario::$usuario_id,
				$post
			);

		Request::redirect('/post/index/'.$post);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder puntuar posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_PUNTUAR))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para puntuar posts.';
			Request::redirect('/');
		}

		// Convertimos el post a ID.
		$post = (int) $post;

		// Validamos la cantidad.
		$cantidad = (int) $cantidad;

		if ($cantidad < 1 || $cantidad > 10)
		{
			$_SESSION['flash_error'] = 'La petición que has realizado es inválida.';
			Request::redirect('/');
		}

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = 'El post que desea puntuar no se encuentra disponible.';
			Request::redirect('/');
		}

		// Verifico el estado del post.
		if ($model_post->estado !== Model_Post::ESTADO_ACTIVO)
		{
			$_SESSION['flash_error'] = 'El post que desea puntuar no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Verifico el autor.
		if ($model_post->usuario_id === Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El post que desea puntuar no se encuentra disponible.';
			Request::redirect('/post/index/'.$post);
		}

		// Verificamos si ya dio puntos.
		if ($model_post->dio_puntos(Usuario::$usuario_id))
		{
			$_SESSION['flash_error'] = 'El post que desea puntuar ya ha sido puntuado por usted.';
			Request::redirect('/post/index/'.$post);
		}

		// Verificamos la cantidad de puntos.
		if (Usuario::usuario()->puntos_disponibles < $cantidad)
		{
			$_SESSION['flash_error'] = 'El post que desea puntuar ya ha sido puntuado por usted.';
			Request::redirect('/post/index/'.$post);
		}

		// Damos los puntos.
		$model_post->dar_puntos(Usuario::$usuario_id, $cantidad);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_puntuar', $post, Usuario::$usuario_id, $cantidad);

		// Informamos el resultado.
		$_SESSION['flash_success'] = 'Has puntuado el post de manera correcta.';
		Request::redirect('/post/index/'.$post);
	}

	/**
	 * Creamos un nuevo post.
	 */
	public function action_nuevo()
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder crear posts.';
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_CREAR))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para poder crear posts.';
			Request::redirect('/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Nuevo post');

		// Cargamos la vista.
		$view = View::factory('post/nuevo');

		// Seteo permisos especiales.
		$view->assign('permisos_especiales', Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER));

		// Elementos por defecto.
		foreach (array('titulo', 'contenido', 'categoria', 'privado', 'patrocinado', 'sticky', 'comentar', 'tags', 'error_titulo', 'error_contenido', 'error_categoria', 'error_tags') as $k)
		{
			$view->assign($k, '');
		}

		// Listado de categorias.
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
				$view->assign('error_titulo', 'El formato del título no es correcto.');
				$error = TRUE;
			}

			// Verificamos el contenido.
			$contenido_clean = preg_replace('/\[.*\]/', '', $contenido);
			if ( ! isset($contenido_clean{20}) || isset($contenido{5000}))
			{
				$view->assign('error_contenido', 'El contenido debe tener entre 20 y 5000 caractéres.');
				$error = TRUE;
			}
			unset($contenido_clean);

			// Verificamos la categoria.
			$model_categoria = new Model_Categoria;
			if ( ! $model_categoria->existe_seo($categoria))
			{
				$view->assign('error_categoria', 'La categoría seleccionada es incorrecta.');
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
				$view->assign('error_tags', 'Las etiquetas ingresadas con son alphanuméricas.');
				$error = TRUE;
			}

			if ( ! $error)
			{
				// Obtengo el listado de etiquetas.
				$tags = explode(',', $tags);
				foreach ($tags as $k => $v)
				{
					$tags[$k] = trim($v);
					if ($tags[$k] == '')
					{
						unset($tags[$k]);
					}
				}

				// Verifico la cantidad.
				if (count($tags) < 3)
				{
					$view->assign('error_tags', 'Debes insertar un mínimo de 3 etiquetas.');
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
				$post_id = $model_post->crear(Usuario::$usuario_id, $titulo, $contenido, $categoria_id, $privado, $patrocinado, $sticky, $comentar, $estado);

				if ($post_id > 0)
				{
					// Cargo el post.
					$model_post = new Model_Post($post_id);

					// Agrego las etiquetas.
					$model_post->agregar_etiqueta($tags);

					// Agrego el suceso.
					$model_suceso = new Model_Suceso;
					$model_suceso->crear(Usuario::$usuario_id, 'post_nuevo', $post_id);

					// Informo y voy a post.
					$_SESSION['flash_success'] = 'El post fue creado correctamente.';
					Request::redirect('/post/index/'.$post_id);
				}
				else
				{
					$view->assign('error', 'Se produjo un error cuando se creaba el post. Reintente.');
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', Controller_Home::submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}
}
