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
				if ($model_post->usuario_id !== Usuario::$usuario_id)
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

			// Comentarios del post.
			$cmts = $model_post->comentarios(NULL);
			$l_cmt = array();
			foreach ($cmts as $cmt)
			{
				// Verifico omito los no visibles si el usuario no puede verlos.
				if ($cmt->estado !== Model_Comentario::ESTADO_VISIBLE &&  ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
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
	 */
	public function action_editar($post)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			Request::redirect('/usuario/login');
		}

		// Limpio la entrada
		$post = (int) $post;

		// Cargo el post.
		$model_post = new Model_Post($post);

		// Verifico exista.
		if ( ! $model_post->existe())
		{
			$_SESSION['flash_error'] = 'El post especificado no existe.';
			Request::redirect('/');
		}

		// No podemos editar posts borrados.
		if ($model_post->estado == Model_Post::ESTADO_BORRADO)
		{
			$_SESSION['flash_error'] = 'El post especificado no es válido.';
			Request::redirect('/');
		}

		// Verifico el usuario y el permiso de edición para terceros.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_EDITAR))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para realizar esa edición.';
			Request::redirect('/');
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
				if ($rst && Usuario::$usuario_id !== $model_post->usuario_id)
				{
					$model_suceso = new Model_Suceso;
					$model_suceso->crear($model_post->usuario_id, 'post_editado', $model_post->id);
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
		// Convertimos el post a ID.
		$post = (int) $post;

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			Request::redirect('/');
		}

		// Verificamos que no sea autor.
		if ($model_post->usuario_id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No puedes denunciar tu propio post.';
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
				$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'post_denunciado', $id);

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
			Request::redirect('/');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR) || ( ! $model_post->comentar && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO)))
		{
			$_SESSION['flash_error'] = 'No puedes realizar comentarios en posts porque se encuentran cerrados.';
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
				$model_suceso->crear(array(Usuario::$usuario_id, $model_post->usuario_id), 'comentario_post', $id);

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

		// Verifica autor.
		if ($model_post->usuario_id !== Usuario::$usuario_id)
		{
			// Verificamos el voto.
			if ( ! $model_post->es_favorito(Usuario::$usuario_id))
			{
				$model_post->favorito(Usuario::$usuario_id);
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(
						array(
							Usuario::$usuario_id,
							$model_post->usuario_id
						),
						'favorito_post',
						Usuario::$usuario_id,
						$post
					);
			}
		}
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
		// Obtenemos el voto.
		$voto = $voto == 1;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Comentario incorrecto.';
			Request::redirect('/');
		}

		// Verifico permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No tienes permiso para realizar esta acci&oacute;n.';
			Request::redirect('/post/index/'.$model_comentario->post_id);
		}

		// Cargamos usuario.
		$usuario_id = Usuario::$usuario_id;

		// Verificamos autor.
		if ($model_comentario->usuario_id != $usuario_id)
		{
			// Verificamos puntuación.
			if ( ! $model_comentario->ya_voto($usuario_id))
			{
				$model_comentario->votar($usuario_id, $voto);
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(
						array(
							$usuario_id,
							$model_comentario->usuario_id,
							$model_comentario->post()->usuario_id
						),
						'voto_comentario_post',
						$usuario_id,
						(int) $comentario
					);
			}
		}
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El comentario se ha votado correctamente.';
		Request::redirect('/post/index/'.$model_comentario->post_id);
	}

	/**
	 * Ocultamos un comentario.
	 * @param int $comentario ID del comentario a ocultar.
	 */
	public function action_ocultar_comentario($comentario)
	{
		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($comentario);

		// Verificamos existencia.
		if ( ! is_array($model_comentario->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Comentario incorrecto.';
			Request::redirect('/');
		}

		// Cargamos usuario.
		$usuario_id = Usuario::$usuario_id;

		// Verificamos autor y permisos.
		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			// Seteo el estado como borrado.
			if ( ! $model_comentario->estado !== Model_Post_Comentario::ESTADO_OCULTO)
			{
				// Actualizo el estado.
				$model_comentario->actualizar_estado(Model_Post_Comentario::ESTADO_OCULTO);

				// Envio suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(
						array(
							$usuario_id,
							$model_comentario->usuario_id,
							$model_comentario->post()->usuario_id
						),
						'voto_comentario_post',
						$usuario_id,
						$comentario
				);
			}
			$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&ocute;n realizada correctamente.';
		}
		else
		{
			$_SESSION['flash_error'] = 'No tienes permiso para realizar esta acción.';
		}
		Request::redirect('/post/index/'.$model_comentario->post_id);
	}

	/**
	 * Nos convertimos en seguidores de un post.
	 * @param int $post ID del post a seguir.
	 */
	public function action_seguir_post($post)
	{
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

		// Cargamos usuario.
		$usuario_id = Usuario::$usuario_id;

		// Verifica autor.
		if ($model_post->usuario_id !== $usuario_id)
		{
			// Verificamos el voto.
			if ( ! $model_post->es_seguidor($usuario_id))
			{
				// Empezamos a seguir.
				$model_post->seguir($usuario_id);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(
						array(
							$usuario_id,
							$model_post->usuario_id
						),
						'seguir_post',
						$usuario_id,
						$post
					);
			}
		}
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Ahora eres seguidor del post.';
		Request::redirect('/post/index/'.$post);
	}

	/**
	 * Setamos o quitamos el atributo sticky de un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se agrega o quita.
	 */
	public function action_fijar_post($post, $tipo)
	{
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

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el parametro sticky.
		if ($model_post->sticky !== $tipo)
		{
			// Modificamos el parámetro.
			$model_post->setear_sticky($tipo);

			// Enviamos el suceso.
			$model_suceso = new Model_Suceso;
			$model_suceso->crear(
					array(
						Usuario::$usuario_id,
						$model_post->usuario_id
					),
					'post_fijar',
					Usuario::$usuario_id,
					$post,
					$tipo
				);
		}
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acción realizada correctamente.';
		Request::redirect('/post/index/'.$post);
	}

	/**
	 * Setamos o quitamos el atributo sponsored de un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se agrega o quita.
	 */
	public function action_patrocinar_post($post, $tipo)
	{
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

		// Verifico el permiso.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el parametro sticky.
		if ($model_post->sponsored !== $tipo)
		{
			// Actualizo campo.
			$model_post->setear_sponsored($tipo);

			// Enviamos el suceso.
			$model_suceso = new Model_Suceso;
			$model_suceso->crear(
					array(
						Usuario::$usuario_id,
						$model_post->usuario_id
					),
					'post_sponsored',
					Usuario::$usuario_id,
					$post,
					$tipo
				);
		}
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

		// Verifico el usuario y sus permisos.
		if ($model_post->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_OCULTAR))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			Request::redirect('/post/index/'.$post);
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if (($tipo && $model_post->estado !== Model_Post::ESTADO_OCULTO) || ( ! $tipo && $model_post->estado !== Model_Post::ESTADO_ACTIVO))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Estado incorrecto.';
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_OCULTO);

		// Agrego mensaje.
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(
				array(
					Usuario::$usuario_id,
					$model_post->usuario_id
				),
				'post_ocultar',
				Usuario::$usuario_id,
				$post,
				$tipo
			);

		Request::redirect('/post/index/'.$post);
	}

	/**
	 * Apruebo o rechazo un post.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Si se aprueba o se rechaza.
	 */
	public function action_aprobar_post($post, $tipo)
	{
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

		// Verifico el usuario y sus permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No tienes permisos para realizar esa acci&oacute;n.';
			Request::redirect('/post/index/'.$post);
		}

		// Valido el valor actual.
		$tipo = $tipo == 1;

		// Verifico el estado actual.
		if ($tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_RECHAZADO))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No se puede realizar esa acci&oacute;n, el estado es incorrecto.';
			Request::redirect('/post/index/'.$post);
		}
		elseif ( ! $tipo && ! ($model_post->estado === Model_Post::ESTADO_PENDIENTE || $model_post->estado === Model_Post::ESTADO_ACTIVO))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> No se puede realizar esa acci&oacute;n, el estado es incorrecto.';
			Request::redirect('/post/index/'.$post);
		}

		// Actualizo el estado.
		$model_post->actualizar_estado($tipo ? Model_Post::ESTADO_ACTIVO : Model_Post::ESTADO_RECHAZADO);

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El estado se modific&oacute; correctamente.';

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(
				array(
					Usuario::$usuario_id,
					$model_post->usuario_id
				),
				'post_aprobar',
				Usuario::$usuario_id,
				$post,
				$tipo
			);
		Request::redirect('/post/index/'.$post);
	}

	/**
	 * Borramos un post o lo enviamos a la papelera.
	 * @param int $post ID del post a modificar el atributo.
	 * @param bool $tipo Borra o se envia a la papelera.
	 */
	public function action_borrar_post($post, $tipo)
	{
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

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_POSTS))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
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
				$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Acci&oacute;n incorrecta.';
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

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		$model_suceso->crear(
				array(
					Usuario::$usuario_id,
					$model_post->usuario_id
				),
				'post_borrar',
				Usuario::$usuario_id,
				$post,
				$tipo
			);

		Request::redirect('/post/index/'.$post);
	}

	/**
	 * Restauro un post enviado a la papelera.
	 * @param int $post ID del post a restaurar.
	 */
	public function action_restaurar_post($post)
	{
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
		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_REVISAR_POST))
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
		// Convertimos el post a ID.
		$post = (int) $post;

		// Validamos la cantidad.
		$cantidad = (int) $cantidad;

		if ($cantidad < 1 || $cantidad > 10)
		{
			Request::redirect('/');
		}

		// Cargamos el post.
		$model_post = new Model_Post($post);

		// Verificamos exista.
		if ( ! is_array($model_post->as_array()))
		{
			$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Post incorrecto.';
			Request::redirect('/');
		}

		// Cargamos usuario.
		$usuario_id = Usuario::$usuario_id;

		// Verifica autor.
		if ($model_post->usuario_id != $usuario_id)
		{
			// Verifico permisos.
			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_PUNTUAR))
			{
				// Verificamos el voto.
				if ( ! $model_post->dio_puntos($usuario_id))
				{
					// Verificamos la cantidad de puntos.
					$model_usuario = new Model_Usuario($usuario_id);
					if ($model_usuario->puntos_disponibles >= $cantidad)
					{
						$model_post->dar_puntos($usuario_id, $cantidad);
						$model_suceso = new Model_Suceso;
						$model_suceso->crear(
								array(
									$usuario_id,
									$model_post->usuario_id
								),
								'punto_post',
								$usuario_id,
								$post
							);
						$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Se ha realizado la puntuación correctamente.';
					}
					else
					{
						$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Los puntos que posees no son suficientes.';
					}
				}
				else
				{
					$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Ya has puntuado al post.';
				}
			}
			else
			{
				$_SESSION['flash_error'] = '<b>&iexcl;Error!</b> Permisos incorrectos.';
			}
		}
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
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_CREAR))
		{
			$_SESSION['flash_error'] = 'No puedes crear posts.';
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

				// Verifico si es borrador.
				$borrador = isset($_POST['submit']) ? $_POST['submit'] == 'borrador' : FALSE;

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
					$model_suceso->crear(Usuario::$usuario_id, 'nuevo_post', $post_id, $estado);

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
