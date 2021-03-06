<?php
/**
 * foto.php is part of Marifa.
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
class Base_Controller_Foto extends Controller {

	public function before()
	{
		parent::before();

		// Verifico permisos.
		if ( ! (Utils::configuracion()->get('habilitar_fotos', 1) && (Utils::configuracion()->get('privacidad_fotos', 1) || Usuario::is_login())))
		{
			add_flash_message(FLASH_ERROR, __('No tienes acceso a está sección.', FALSE));
			Request::redirect('/');
		}
	}

	/**
	 * Listado de pestañas de la foto.
	 * @param int $activo Pestaña seleccionada.
	 */
	protected function submenu($activo)
	{
		// Creo el menú.
		$menu = new Menu('foto_menu');

		// Agrego elementos.
		$menu->element_set(__('Fotos', FALSE), '/foto/', 'index');
		if (Usuario::is_login())
		{
			$menu->element_set(__('Agregar Foto', FALSE), '/foto/nueva/', 'nuevo');
			$menu->element_set(__('Mis Fotos', FALSE), '/foto/mis_fotos/', 'mis_fotos');
		}

		// Devuelvo el menú.
		return $menu->as_array($activo);
	}

	/**
	 * Generamos la URL de la foto para redireccionar.
	 * @param Model_Foto|int $foto Modelo de la foto o ID de la foto.
	 * @return int
	 */
	protected function foto_url($foto, $pagina = 1)
	{
		// Cargo foto.
		if ( ! is_object($foto))
		{
			$foto = new Model_Foto( (int) $foto);
		}

		// Verifico existencia.
		if ( ! $foto->existe())
		{
			add_flash_message(FALSH_ERROR, __('La foto no es válida', FALSE));
			Request::redirect('/foto/');
		}

		// Genero la URL.
		if (is_string($pagina) || $pagina > 1)
		{
			return '/foto/'.$foto->categoria()->seo.'/'.$foto->id.'/'.Texto::make_seo($foto->titulo).'.'.$pagina.'.html';
		}
		else
		{
			return '/foto/'.$foto->categoria()->seo.'/'.$foto->id.'/'.Texto::make_seo($foto->titulo).'.html';
		}
	}

	/**
	 * Mostramos listado de fotos.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_index($pagina, $categoria)
	{
		// Verifico categoría.
		if ($categoria !== NULL)
		{
			// Verifico formato.
			if ( ! preg_match('/[a-z0-9_]+/i', $categoria))
			{
				add_flash_message(FLASH_ERROR, __('La categoría no es correcta.', FALSE));
				Request::redirect('/foto/'.$pagina);
			}

			// Cargo la categoría.
			$model_categoria = new Model_Categoria;

			// Verifico sea válida.
			if ( ! $model_categoria->existe_seo($categoria))
			{
				add_flash_message(FLASH_ERROR, __('La categoría no es correcta.', FALSE));
				Request::redirect('/foto/'.$pagina);
			}
			else
			{
				// Cargo la categoría.
				$model_categoria->load_by_seo($categoria);
			}
		}

		// Seteo id de la categoría.
		$categoria_id = isset($model_categoria) ? $model_categoria->id : NULL;

		// Cargo menú superior.
		$menu = View::factory('foto/header');
		if (isset($model_categoria))
		{
			$menu->assign('categorias', $model_categoria->lista());
		}
		else
		{
			$model_categoria = new Model_Categoria;
			$menu->assign('categorias', $model_categoria->lista());
			unset($model_categoria);
		}
		$menu->assign('active', 'index');
		$menu->assign('categoria', $categoria);

		// Cargamos la vista.
		$view = View::factory('foto/index');

		$view->assign('header', $menu->parse());
		unset($menu);

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de fotos.
		$model_fotos = new Model_Foto;
		$fotos = $model_fotos->obtener_ultimas($pagina, $cantidad_por_pagina, $categoria_id);

		// Verifico validez de la pagina.
		if (count($fotos) == 0 && $pagina != 1)
		{
			if ($categoria_id === NULL)
			{
				Request::redirect('/foto/');
			}
			else
			{
				Request::redirect('/foto/categoria/'.$categoria);
			}
		}

		// Paginación.
		$paginador = new Paginator($model_fotos->cantidad(Model_Foto::ESTADO_ACTIVA, NULL, $categoria_id), $cantidad_por_pagina);
		if ($categoria_id === NULL)
		{
			$view->assign('paginacion', $paginador->get_view($pagina, '/foto/%d/'));
		}
		else
		{
			$view->assign('paginacion', $paginador->get_view($pagina, '/foto/categoria/'.$categoria.'/%d/'));
		}
		unset($paginador);

		// Procesamos información relevante.
		foreach ($fotos as $key => $value)
		{
			$d = $value->as_array();
			$d['descripcion_raw'] = $d['descripcion'];
			$d['descripcion_clean'] = preg_replace('/\[([^\[\]]+)\]/', '', $d['descripcion']);
			$d['descripcion'] = Decoda::procesar($d['descripcion']);
			$d['categoria'] = $value->categoria()->as_array();
			$d['votos'] = $value->votos();
			$d['favoritos'] = $value->favoritos();
			$d['comentarios'] = $value->cantidad_comentarios(Model_Foto::ESTADO_ACTIVA);
			$d['usuario'] = $value->usuario()->as_array();

			// Acciones.
			if (Usuario::is_login())
			{
				if (Usuario::$usuario_id == $value->usuario_id)
				{
					$d['favorito'] = TRUE;
					$d['voto'] = TRUE;
					$d['denunciar'] = FALSE;
				}
				else
				{
					$d['favorito'] = $value->es_favorito(Usuario::$usuario_id);
					$d['voto'] = ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VOTAR) || $value->ya_voto(Usuario::$usuario_id);
					$d['denunciar'] = TRUE;
				}
			}
			else
			{
				$d['favorito'] = TRUE;
				$d['voto'] = TRUE;
				$d['denunciar'] = FALSE;
			}
			$fotos[$key] = $d;
		}

		$view->assign('fotos', $fotos);
		unset($fotos);

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		//$this->template->assign('top_bar', $this->submenu('index'));

		// Asignamos el título.
		if ($categoria_id === NULL)
		{
			if ($pagina > 1)
			{
				$this->template->assign('title', sprintf(__('Fotos - Página %s', FALSE), $pagina));
			}
			else
			{
				$this->template->assign('title', __('Fotos', FALSE));
			}
		}
		else
		{
			if ($pagina > 1)
			{
				$this->template->assign('title', sprintf(__('Fotos en %s - Página %i', FALSE), $model_categoria->nombre, $pagina));
			}
			else
			{
				$this->template->assign('title', sprintf(__('Fotos en %s', FALSE), $model_categoria->nombre));
			}
		}

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Mostramos listado de fotos del usuario conectado
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_mis_fotos($pagina)
	{
		// Verificamos si esta conectado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder ver esta sección.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Asignamos el título.
		$this->template->assign('title', __('Mis Fotos', FALSE));

		// Cargamos la vista.
		$view = View::factory('foto/index');

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de fotos.
		$model_fotos = new Model_Foto;
		$fotos = $model_fotos->obtener_ultimas_usuario(Usuario::$usuario_id, $pagina, $cantidad_por_pagina);

		// Verifico validez de la pagina.
		if (count($fotos) == 0 && $pagina != 1)
		{
			Request::redirect('/foto/');
		}

		// Paginación.
		$paginador = new Paginator($model_fotos->cantidad(Model_Foto::ESTADO_ACTIVA, Usuario::$usuario_id), $cantidad_por_pagina);
		$view->assign('paginacion', $paginador->get_view($pagina, '/foto/mis_fotos/%d'));
		unset($paginador);

		// Procesamos información relevante.
		foreach ($fotos as $key => $value)
		{
			$d = $value->as_array();
			$d['descripcion_raw'] = $d['descripcion'];
			$d['descripcion_clean'] = preg_replace('/\[([^\[\]]+)\]/', '', $d['descripcion']);
			$d['descripcion'] = Decoda::procesar($d['descripcion']);
			$d['categoria'] = $value->categoria()->as_array();
			$d['votos'] = $value->votos();
			$d['favoritos'] = $value->favoritos();
			$d['comentarios'] = $value->cantidad_comentarios(Model_Foto::ESTADO_ACTIVA);
			$d['usuario'] = $value->usuario()->as_array();

			// Acciones. Como son nuestras fotos no hacen falta acciones.
			$d['favorito'] = TRUE;
			$d['voto'] = TRUE;
			$d['denunciar'] = FALSE;

			$fotos[$key] = $d;
		}

		$view->assign('fotos', $fotos);
		unset($fotos);

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('mis_fotos'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Mostramos una foto.
	 * @param int $foto ID de la foto.
	 * @param int $pagina Número de página de los comentarios.
	 */
	public function action_ver($foto, $pagina)
	{
		// Convertimos la foto a ID.
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('La foto a la que intentas acceder no está disponible.', FALSE));
			Request::redirect('/foto/');
		}

		// Verifico el estado.
		if ($model_foto->usuario_id !== Usuario::$usuario_id && $model_foto->estado !== Model_Foto::ESTADO_ACTIVA && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO) && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA))
		{
			add_flash_message(FLASH_ERROR, __('La foto a la que intentas acceder no está disponible.', FALSE));
			Request::redirect('/foto/');
		}

		// Asignamos el título.
		$this->template->assign('title', sprintf(__('Foto - %s', FALSE), $model_foto->as_object()->titulo));

		// Cargamos la vista.
		$view = View::factory('foto/ver');

		// Mi id.
		$view->assign('me', Usuario::$usuario_id);

		// Verifico si sigo al usuario.
		if ($model_foto->usuario_id !== Usuario::$usuario_id)
		{
			$view->assign('sigue_usuario', $model_foto->usuario()->es_seguidor(Usuario::$usuario_id));
		}
		else
		{
			$view->assign('sigue_usuario', TRUE);
		}

		// Informamos los permisos a la vista.
		$view->assign('permiso_borrar', Usuario::$usuario_id === $model_foto->usuario_id || Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_ELIMINAR));
		$view->assign('permiso_editar', Usuario::$usuario_id === $model_foto->usuario_id || Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_EDITAR));
		$view->assign('permiso_ocultar', Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_OCULTAR) || Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO) || Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS));
		$view->assign('permiso_papelera', Usuario::$usuario_id === $model_foto->usuario_id);

		// Información del usuario dueño del post.
		$u_data = $model_foto->usuario()->as_array();
		$u_data['seguidores'] = $model_foto->usuario()->cantidad_seguidores();
		$u_data['posts'] = $model_foto->usuario()->cantidad_posts();
		$u_data['comentarios'] = $model_foto->usuario()->cantidad_comentarios();
		$u_data['puntos'] = $model_foto->usuario()->cantidad_puntos();
		$view->assign('usuario', $u_data);
		unset($u_data);

		// Información de la foto.
		$ft = $model_foto->as_array();
		$ft['descripcion_raw'] = $ft['descripcion'];
		$ft['descripcion_clean'] = preg_replace('/\[([^\[\]]+)\]/', '', $ft['descripcion']);
		$ft['descripcion'] = Decoda::procesar($ft['descripcion']);
		$ft['votos'] = (int) $model_foto->votos();
		$ft['favoritos'] = (int) $model_foto->favoritos();
		$view->assign('foto', $ft);
		unset($ft);

		if ( ! Usuario::is_login() || $model_foto->as_object()->usuario_id == Usuario::$usuario_id)
		{
			$view->assign('es_favorito', TRUE);
			$view->assign('ya_vote', TRUE);
		}
		else
		{
			// Computamos la visita si es necesario.
			if ($model_foto->visitas !== NULL)
			{
				$model_foto->agregar_visita();

				// Actualizamos medallas.
				$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_VISITAS);
			}

			$view->assign('es_favorito', $model_foto->es_favorito(Usuario::$usuario_id));
			$view->assign('ya_vote', ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VOTAR) || $model_foto->ya_voto(Usuario::$usuario_id));
		}

		// Verifico si soporta comentarios.
		$view->assign('puedo_comentar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO) || ($model_foto->soporta_comentarios() && Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR)));
		$view->assign('comentario_eliminar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR));
		$view->assign('comentario_ocultar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR));
		$view->assign('comentario_editar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR));

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Cargo comentarios.
		$cmts = $model_foto->comentarios($pagina, $cantidad_por_pagina);

		// Verifico validez de la pagina.
		if (count($cmts) == 0 && $pagina != 1)
		{
			Request::redirect($this->foto_url($model_foto));
		}

		// Comentarios del post.
		$l_cmt = array();
		foreach ($cmts as $cmt)
		{
			$cl_cmt = $cmt->as_array();
			$cl_cmt['usuario'] = $cmt->usuario()->as_array();
			$l_cmt[] = $cl_cmt;
		}
		$view->assign('comentarios', $l_cmt);
		unset($l_cmt, $cmts);

		// Paginación.
		$paginador = new Paginator($model_foto->cantidad_comentarios(Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO) ? NULL : Model_Comentario::ESTADO_VISIBLE), $cantidad_por_pagina);
		$view->assign('paginacion', $paginador->get_view($pagina, $this->foto_url($model_foto, '%d')));
		unset($paginador);

		$view->assign('comentario_content', isset($_POST['comentario']) ? $_POST['comentario'] : NULL);
		$view->assign('comentario_error', get_flash('post_comentario_error'));
		$view->assign('comentario_success', get_flash('post_comentario_success'));


		// Menú.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('index'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Seguimos a un usuario.
	 * @param int $foto ID de la foto que estamos viendo.
	 * @param int $usuario ID del usuario a seguir.
	 * @param bool $seguir TRUE para seguir, FALSE para dejar de seguir.
	 */
	public function action_seguir_usuario($foto, $usuario, $seguir)
	{
		$seguir = (bool) $seguir;

		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			if ($seguir)
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('Debes iniciar sesión para poder seguir usuarios.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder seguir usuarios.', FALSE));
				}
			}
			else
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('Debes iniciar sesión para poder dejar de seguir usuarios.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder dejar de seguir usuarios.', FALSE));
				}
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
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				}
			}
			else
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
				}
			}
			Request::redirect($this->foto_url($foto));
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $model_usuario->id)
		{
			if ($seguir)
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				}
			}
			else
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
				}
			}
			Request::redirect($this->foto_url($foto));
		}

		// Verificaciones especiales en función si lo voy a seguir o dejar de seguir.
		if ($seguir)
		{
			// Verifico el estado.
			if ($model_usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				}
				Request::redirect($this->foto_url($foto));
			}

			// Verifico no sea seguidor.
			if ($model_usuario->es_seguidor(Usuario::$usuario_id))
			{
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('Debes iniciar sesión para poder ver esta sección.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres seguir no se encuentra disponible.', FALSE));
				}
				Request::redirect($this->foto_url($foto));
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
				if (Request::is_ajax())
				{
					header('Content-Type: application/json');
					die(json_encode(array('response' => 'error', 'content' => __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE))));
				}
				else
				{
					add_flash_message(FLASH_ERROR, __('El usuario al cual quieres dejar de seguir no se encuentra disponible.', FALSE));
				}
				Request::redirect($this->foto_url($foto));
			}

			// Dejo de seguir al usuario.
			$model_usuario->fin_seguir(Usuario::$usuario_id);
		}

		// Envío el suceso.
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

		// Informo el resultado.
		if (Request::is_ajax())
		{
			header('Content-Type: application/json');
			$view = View::factory('foto/ajax_seguir_usuario');
			$view->assign('foto_id', (int) $foto);
			$view->assign('usuario_id', $model_usuario->id);
			$view->assign('sigue_usuario', $seguir);

			if ($seguir)
			{
				die(json_encode(array('response' => 'ok', 'content' => array('html' => $view->parse(), 'message' => __('Comenzaste a seguir al usuario correctamente.', FALSE)))));
			}
			else
			{
				die(json_encode(array('response' => 'ok', 'content' => array('html' => $view->parse(), 'message' => __('Dejaste de seguir al usuario correctamente.', FALSE)))));
			}
		}
		else
		{
			if ($seguir)
			{
				add_flash_message(FLASH_SUCCESS, __('Comenzaste a seguir al usuario correctamente.', FALSE));
			}
			else
			{
				add_flash_message(FLASH_SUCCESS, __('Dejaste de seguir al usuario correctamente.', FALSE));
			}
		}
		Request::redirect($this->foto_url($foto));
	}

	/**
	 * Votamos una foto.
	 * @param int $foto ID de la foto.
	 * @param int $voto 1 para positivo, -1 para negativo.
	 * @param bool $cantidad Si debe devolver la cantidad en caso de que sea AJAX.
	 */
	public function action_votar($foto, $voto, $cantidad)
	{
		$foto = (int) $foto;
		// Obtenemos el voto.
		$voto = $voto == 1;

		if ( ! Usuario::is_login())
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('Debes iniciar sesión para poder ver esta sección.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder ver esta sección.', FALSE));
			}
			Request::redirect('/usuario/login');
		}

		// Verificamos los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VOTAR))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('No tienes los permisos suficientes para votar fotos.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('No tienes los permisos suficientes para votar fotos.', FALSE));
			}
			Request::redirect('/foto/');
		}

		// Cargamos el comentario.
		$model_foto = new Model_Foto($foto);

		// Verificamos existencia.
		if ( ! is_array($model_foto->as_array()))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas votar no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas votar no se encuentra disponible.', FALSE));
			}
			Request::redirect('/foto/');
		}

		// Verifico el estado de la foto.
		if ($model_foto->estado !== Model_Foto::ESTADO_ACTIVA)
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas votar no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas votar no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Verificamos el autor.
		if ($model_foto->usuario_id === Usuario::$usuario_id)
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas votar no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas votar no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Verificamos si puede votar.
		if ($model_foto->ya_voto(Usuario::$usuario_id))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas votar ya la has votado.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas votar ya la has votado.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Votamos la foto.
		$model_foto->votar(Usuario::$usuario_id, $voto);

		// Actualizo medallas.
		$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_VOTOS_NETOS);
		if ($voto > 0)
		{
			$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_VOTOS_POSITIVOS);
		}
		else
		{
			$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_VOTOS_NEGATIVOS);
		}

		// Creamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_votar', TRUE, $foto, Usuario::$usuario_id, (int) $voto);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_votar', FALSE, $foto, Usuario::$usuario_id, (int) $voto);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_votar', FALSE, $foto, Usuario::$usuario_id, (int) $voto);
		}

		// Informamos el resultado.
		if (Request::is_ajax())
		{
			header('Content-Type: application/json');
			if ($cantidad == 1)
			{
				$view = View::factory('foto/ajax_cantidad_votos');
				$view->assign('votos', $model_foto->votos());
				die(json_encode(array('response' => 'ok', 'content' => array('html' => $view->parse(), 'message' => __('El voto fue guardado correctamente.', FALSE)))));
			}
			else
			{
				die(json_encode(array('response' => 'ok', 'content' => array('message' => __('El voto fue guardado correctamente.', FALSE)))));
			}
		}
		else
		{
			add_flash_message(FLASH_SUCCESS, __('El voto fue guardado correctamente.', FALSE));
		}
		Request::redirect($this->foto_url($model_foto));
	}

	/**
	 * Agregamos la foto como favorita.
	 * @param int $foto ID de la foto.
	 * @param bool $cantidad Si debe devolver la cantidad en caso de que sea AJAX.
	 */
	public function action_favorito($foto, $cantidad)
	{
		// Convertimos el post a ID.
		$foto = (int) $foto;

		// Verifico que esté logueado.
		if ( ! Usuario::is_login())
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('Debes iniciar sesión para poder agregar la foto a tus favoritos.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder agregar la foto a tus favoritos.', FALSE));
			}
			Request::redirect('/usuario/login');
		}

		// Cargamos el post.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que quiere poner como favorito no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que quiere poner como favorito no se encuentra disponible.', FALSE));
			}
			Request::redirect('/foto/');
		}

		// Verifico el estado de la foto.
		if ($model_foto->estado != Model_Foto::ESTADO_ACTIVA)
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que quiere poner como favorito no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que quiere poner como favorito no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Verifica autor.
		if ($model_foto->usuario_id === Usuario::$usuario_id)
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que quiere poner como favorito no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que quiere poner como favorito no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Verificamos que no sea favorito.
		if ($model_foto->es_favorito(Usuario::$usuario_id))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto ya está en tus favoritos.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto ya está en tus favoritos.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Agrego a favoritos.
		$model_foto->agregar_favorito(Usuario::$usuario_id);

		// Agregamos medallas.
		$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_FAVORITOS);

		// Envío el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_favorito', TRUE, $foto, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, FALSE, 'foto_favorito', $foto, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_favorito', FALSE, $foto, Usuario::$usuario_id);
		}

		// Informo el resultado.
		if (Request::is_ajax())
		{
			header('Content-Type: application/json');
			if ($cantidad == 1)
			{
				$view = View::factory('foto/ajax_cantidad_favoritos');
				$view->assign('favoritos', $model_foto->favoritos());
				die(json_encode(array('response' => 'ok', 'content' => array('html' => $view->parse(), 'message' => __('Foto agregada a favoritos correctamente.', FALSE)))));
			}
			else
			{
				die(json_encode(array('response' => 'ok', 'content' => array('message' => __('Foto agregada a favoritos correctamente.', FALSE)))));
			}
		}
		else
		{
			add_flash_message(FLASH_SUCCESS, __('Foto agregada a favoritos correctamente.', FALSE));
		}
		Request::redirect($this->foto_url($model_foto));
	}

	/**
	 * Agregamos un comentario en la foto.
	 * @param int $foto ID de la foto donde comentar.
	 */
	public function action_comentar($foto)
	{
		// Verificamos el método de envío.
		if (Request::method() != 'POST')
		{
			Request::redirect($this->foto_url($foto));
		}

		// Verifico esté conectado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder realizar comentarios.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Convertimos el foto a ID.
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('La foto que quiere comentar no se encuentra disponible.', FALSE));
			Request::redirect('/foto/');
		}

		// Verifico se pueda comentar.
		if ( ! (Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO) || ($model_foto->soporta_comentarios() && Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR))))
		{
			$_SESSION['post_comentario_error'] = __('No tienes permisos para realizar comentarios en esa foto.', FALSE);
			Request::redirect($this->foto_url($foto));
		}

		// Obtenemos el comentario.
		$comentario = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

		// Verificamos el formato.
		$comentario_clean = preg_replace('/\[.*\]/', '', $comentario);
		if ( ! isset($comentario_clean{10}) || isset($comentario{400}))
		{
			$_SESSION['post_comentario_error'] = __('El comentario debe tener entre 20 y 400 caracteres.', FALSE);

			// Evitamos la visualización de la plantilla.
			$this->template = NULL;

			Dispatcher::call($this->foto_url($foto), TRUE);
		}
		else
		{
			// Evitamos XSS.
			$comentario = htmlentities($comentario, ENT_NOQUOTES, 'UTF-8');

			// Insertamos el comentario.
			$id = $model_foto->comentar(Usuario::$usuario_id, $comentario);

			// Envío sucesos de citas.
			Decoda::procesar($comentario, FALSE);

			// Verifico actualización del rango.
			Usuario::usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_COMENTARIOS);

			// Actualizo las medallas.
			$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_COMENTARIOS);
			Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_FOTOS);

			// Envío el suceso.
			$model_suceso = new Model_Suceso;
			if (Usuario::$usuario_id != $model_foto->usuario_id)
			{
				$model_suceso->crear($model_foto->usuario_id, 'foto_comentario_crear', TRUE, $id);
				$model_suceso->crear(Usuario::$usuario_id, 'foto_comentario_crear', FALSE, $id);
			}
			else
			{
				$model_suceso->crear($model_foto->usuario_id, 'foto_comentario_crear', FALSE, $id);
			}

			// Informo el resultado.
			$_SESSION['post_comentario_success'] = __('El comentario se ha realizado correctamente.', FALSE);
			Request::redirect($this->foto_url($foto));
		}
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
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder ocultar/mostrar comentarios en fotos.', FALSE));
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Foto_Comentario($comentario);

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
			Request::redirect($this->foto_url($model_comentario->foto()));
		}

		// Verifico los permisos.
		if ($model_comentario->estado == 0 && Usuario::$usuario_id !== $model_comentario->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para ocultar/mostrar comentarios.', FALSE));
			Request::redirect($this->foto_url($model_comentario->foto()));
		}
		elseif ($model_comentario->estado == 1 && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para ocultar/mostrar comentarios.', FALSE));
			Request::redirect($this->foto_url($model_comentario->foto()));
		}

		//TODO: agregar otro estado para diferenciar usuario de moderador.

		// Actualizo el estado.
		$model_comentario->actualizar_campo('estado', $tipo ? Model_Foto_Comentario::ESTADO_VISIBLE : Model_Foto_Comentario::ESTADO_OCULTO);

		// Envío el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id == $model_comentario->usuario_id)
		{
			$model_suceso->crear(Usuario::$usuario_id, $tipo ? 'foto_comentario_mostrar' : 'foto_comentario_ocultar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id != $model_comentario->foto()->usuario_id)
			{
				$model_suceso->crear($model_comentario->foto()->usuario_id, $tipo ? 'foto_comentario_mostrar' : 'foto_comentario_ocultar', TRUE, $comentario, Usuario::$usuario_id);
			}
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, $tipo ? 'foto_comentario_mostrar' : 'foto_comentario_ocultar', TRUE, $comentario, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, $tipo ? 'foto_comentario_mostrar' : 'foto_comentario_ocultar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id == $model_comentario->foto()->usuario_id)
			{
				$model_suceso->crear($model_comentario->foto()->usuario_id, $tipo ? 'foto_comentario_mostrar' : 'foto_comentario_ocultar', FALSE, $comentario, Usuario::$usuario_id);
			}
		}

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El comentario se ha ocultado/mostrado correctamente.', FALSE));
		Request::redirect($this->foto_url($model_comentario->foto()));
	}

	/**
	 * Eliminamos un comentario en una foto.
	 * @param int $comentario ID del comentario a eliminar.
	 */
	public function action_eliminar_comentario($comentario)
	{
		// Verificamos usuario logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder borrar comentarios en fotos.', FALSE));
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
		$model_comentario = new Model_Foto_Comentario($comentario);

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
			Request::redirect($this->foto_url($model_comentario->foto()));
		}

		// Actualizo el estado.
		$model_comentario->actualizar_campo('estado', Model_Foto_Comentario::ESTADO_BORRADO);

		// Envío el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id == $model_comentario->usuario_id)
		{
			$model_suceso->crear(Usuario::$usuario_id, 'foto_comentario_borrar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id != $model_comentario->foto()->usuario_id)
			{
				$model_suceso->crear($model_comentario->foto()->usuario_id, 'foto_comentario_borrar', TRUE, $comentario, Usuario::$usuario_id);
			}
		}
		else
		{
			$model_suceso->crear($model_comentario->usuario_id, 'foto_comentario_borrar', TRUE, $comentario, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_comentario_borrar', FALSE, $comentario, Usuario::$usuario_id);
			if (Usuario::$usuario_id == $model_comentario->foto()->usuario_id)
			{
				$model_suceso->crear($model_comentario->foto()->usuario_id, 'foto_comentario_borrar', FALSE, $comentario, Usuario::$usuario_id);
			}
		}

		add_flash_message(FLASH_SUCCESS, __('<b>!Felicitaciones!</b> El comentario se ha borrado correctamente.', FALSE));
		Request::redirect($this->foto_url($model_comentario->foto()));
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
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder editar comentarios en fotos.', FALSE));
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Foto_Comentario($comentario);

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
			Request::redirect($this->foto_url($model_comentario->foto()));
		}

		// Verifico permisos estado.
		if ($model_comentario->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes los permisos para editar el comentario.', FALSE));
			Request::redirect($this->foto_url($model_comentario->foto()));
		}

		// Cargo la vista.
		$vista = View::factory('/foto/editar_comentario');

		// Asigno título.
		$this->template->assign('title', __('Foto - Editar comentario', FALSE));

		// Seteo información del comentario.
		$vista->assign('contenido', $model_comentario->comentario);
		$vista->assign('error_contenido', FALSE);
		$vista->assign('usuario', $model_comentario->usuario()->as_array());
		$vista->assign('foto', $model_comentario->foto()->as_array());

		$cmt = $model_comentario->as_array();
		$cmt['foto'] = $model_comentario->foto()->as_array();
		$cmt['foto']['categoria'] = $model_comentario->foto()->categoria()->as_array();
		$vista->assign('comentario', $cmt);

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
				$model_comentario->actualizar_campo('comentario', $contenido);

				// Envio el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id == $model_comentario->usuario_id)
				{
					$model_suceso->crear(Usuario::$usuario_id, 'foto_comentario_editar', FALSE, $comentario, Usuario::$usuario_id);
					if (Usuario::$usuario_id != $model_comentario->foto()->usuario_id)
					{
						$model_suceso->crear($model_comentario->foto()->usuario_id, 'foto_comentario_editar', TRUE, $comentario, Usuario::$usuario_id);
					}
				}
				else
				{
					$model_suceso->crear($model_comentario->usuario_id, 'foto_comentario_editar', TRUE, $comentario, Usuario::$usuario_id);
					$model_suceso->crear(Usuario::$usuario_id, 'foto_comentario_editar', FALSE, $comentario, Usuario::$usuario_id);
					if (Usuario::$usuario_id == $model_comentario->foto()->usuario_id)
					{
						$model_suceso->crear($model_comentario->foto()->usuario_id, 'foto_comentario_editar', FALSE, $comentario, Usuario::$usuario_id);
					}
				}

				$_SESSION['post_comentario_success'] = __('El comentario se ha actualizado correctamente.', FALSE);
				Request::redirect($this->foto_url($model_comentario->foto()));
			}
		}

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('foto'));
		$this->template->assign('top_bar', Controller_Home::submenu());

		// Asignamos la vista.
		$this->template->assign('contenido', $vista->parse());
	}

	/**
	 * Agregamos una nueva foto.
	 */
	public function action_nueva()
	{
		// Verificamos usuario conectado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder agregar fotos.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos para crear foto.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_CREAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para crear fotos.', FALSE));
			Request::redirect('/foto/');
		}

		// Asignamos el título.
		$this->template->assign('title', __('Nueva foto', FALSE));

		// Cargamos la vista.
		$view = View::factory('foto/nueva');

		// Cargo el listado de categorías.
		$model_categorias = new Model_Categoria;
		$categorias = $model_categorias->lista();

		$view->assign('categorias', $categorias);

		// Elementos por defecto.
		foreach (array('captcha', 'titulo', 'url', 'descripcion', 'comentarios', 'visitantes', 'categoria', 'captcha') as $k)
		{
			$view->assign($k, '');
			$view->assign('error_'.$k, FALSE);
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('nuevo'));

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			$titulo = arr_get($_POST, 'titulo', '');
			$view->assign('titulo', $titulo);
			$url = arr_get($_POST, 'url', '');
			$view->assign('url', $url);
			$descripcion = arr_get($_POST, 'descripcion', '');
			$view->assign('descripcion', $descripcion);
			$categoria = arr_get($_POST, 'categoria', '');
			$view->assign('categoria', $categoria);
			$captcha = arr_get($_POST, 'captcha', '');
			$view->assign('captcha', $captcha);

			// Obtenemos los checkbox.
			$visitantes = isset($_POST['visitantes']) ? ($_POST['visitantes'] == 1) : FALSE;
			$view->assign('visitantes', $visitantes);

			$comentarios = isset($_POST['comentarios']) ? ($_POST['comentarios'] == 1) : FALSE;
			$view->assign('comentarios', $comentarios);

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', __('El formato del título no es correcto.', FALSE));
				$error = TRUE;
			}

			// Verificamos quitando BBCODE.
			$descripcion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $descripcion);

			// Verificamos la descripción.
			if ( ! isset($descripcion_clean{20}) || isset($descripcion{600}))
			{
				$view->assign('error_descripcion', __('La descripción debe tener entre 20 y 600 caracteres.', FALSE));
				$error = TRUE;
			}
			unset($descripcion_clean);

			// Verificamos la URL.
			if ( ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/Di', $url))
			{
				// Verifico IMG.
				if ( ! isset($_FILES['img']) || $_FILES['img']['error'] == UPLOAD_ERR_NO_FILE)
				{
					$view->assign('error_url', __('La dirección de la URL no es válida.', FALSE));
					$error = TRUE;
				}
			}

			// Verifico la categoría.
			if ( ! $model_categorias->existe_seo($categoria))
			{
				$view->assign('error_categoria', __('La categoría seleccionada es incorrecta.', FALSE));
				$error = TRUE;
			}

			// Verifico CAPTCHA.
			include_once(VENDOR_PATH.'securimage'.DS.'securimage.php');
			$securimage = new securimage;
			if ($securimage->check($captcha) === FALSE)
			{
				$view->assign('error_captcha', __('El código introducido no es correcto.', FALSE));
				$error = TRUE;
			}

			// Verifico titulo.
			if ( ! $error)
			{
				// Formateamos los campos.
				$titulo = trim(preg_replace('/\s+/', ' ', $titulo));

				$model_foto = new Model_Foto;
				if ($model_foto->existe(array('titulo' => $titulo)))
				{
					$view->assign('error_titulo', __('Ya existe una foto con ese título.', FALSE));
					$error = TRUE;
				}
			}


			// Proceso de verificación de método de carga de la imagen.
			if ( ! $error)
			{
				if ( ! isset($_FILES['img']) || $_FILES['img']['error'] == UPLOAD_ERR_NO_FILE)
				{
					$upload = new Upload_Imagen;
					try {
						$rst = $upload->from_url($url);

						if ($rst)
						{
							$url = $rst;
						}
						else
						{
							$view->assign('error_url', __('Se produjo un error al cargar la imagen.', FALSE));
							$error = TRUE;
						}
					}
					catch (Exception $e)
					{
						$view->assign('error_url', $e->getMessage());
						$error = TRUE;
					}
				}
				else
				{
					// Verifico la imagen.
					$upload = new Upload_Imagen;
					try {
						$rst = $upload->procesar_imagen('img');

						if ($rst)
						{
							$url = $rst;
						}
						else
						{
							$view->assign('error_url', __('Se produjo un error al cargar la imagen.', FALSE));
							$error = TRUE;
						}
					}
					catch (Exception $e)
					{
						$view->assign('error_url', $e->getMessage());
						$error = TRUE;
					}
				}
			}

			// Procedemos a crear la imagen.
			if ( ! $error)
			{
				// Evitamos XSS.
				$descripcion = htmlentities($descripcion, ENT_NOQUOTES, 'UTF-8');

				// Formateamos los campos.
				$titulo = trim(preg_replace('/\s+/', ' ', $titulo));

				// Obtengo el ID de la categoría.
				$model_categorias->load_by_seo($categoria);

				//TODO: implementar en revisión.
				// $estado = Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_REVISAR_CONTENIDO) ? Model_Foto::ESTADO_OCULTA : Model_Foto::ESTADO_ACTIVA;
				$estado = Model_Foto::ESTADO_ACTIVA;

				$model_foto = new Model_Foto;
				$foto_id = $model_foto->crear(Usuario::$usuario_id, $titulo, $descripcion, $url, $model_categorias->id, $visitantes, ! $comentarios, $estado);

				if ($foto_id > 0)
				{
					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					$model_suceso->crear(Usuario::$usuario_id, 'foto_nueva', FALSE, $model_foto->id);

					// Verifico actualización del rango.
					Usuario::usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_FOTOS);

					// Actualizo medalla.
					Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_FOTOS);

					// Informo el resultado.
					add_flash_message(FLASH_SUCCESS, __('Foto creada correctamente.', FALSE));
					Request::redirect($this->foto_url($model_foto));
				}
				else
				{
					$view->assign('error', __('Se produjo un error cuando se creaba la foto. Reintente.', FALSE));
				}
			}
		}

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Agregamos una denuncia a una foto.
	 * @param int $foto ID de la foto a denunciar.
	 */
	public function action_denunciar($foto)
	{
		$foto = (int) $foto;

		// Verificamos que el usuario este identificado.
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
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder denunciar fotos.', FALSE));
				Request::redirect('/usuario/login');
			}
		}

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			if (Request::is_ajax())
			{
				echo json_encode(array(
					'reponse' => 'ERROR',
					'body' => __('La foto que quieres denunciar no se encuentra disponible.', FALSE)
				));
				return;
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que quieres denunciar no se encuentra disponible.', FALSE));
				Request::redirect('/foto/');
			}
		}

		// Verificamos que no sea autor.
		if ($model_foto->usuario_id === Usuario::$usuario_id)
		{
			if (Request::is_ajax())
			{
				echo json_encode(array(
					'reponse' => 'ERROR',
					'body' => __('La foto que quieres denunciar no se encuentra disponible.', FALSE)
				));
				return;
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que quieres denunciar no se encuentra disponible.', FALSE));
				Request::redirect($this->foto_url($model_foto));
			}
		}

		// Verifico que esté activa.
		if ($model_foto->estado !== Model_Foto::ESTADO_ACTIVA)
		{
			if (Request::is_ajax())
			{
				echo json_encode(array(
					'reponse' => 'ERROR',
					'body' => __('La foto que quieres denunciar no se encuentra disponible.', FALSE)
				));
				return;
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que quieres denunciar no se encuentra disponible.', FALSE));
				Request::redirect($this->foto_url($model_foto));
			}
		}

		// Asignamos el título.
		$this->template->assign('title', __('Denunciar foto', FALSE));

		// Cargamos la vista.
		if (Request::is_ajax())
		{
			$view = View::factory('foto/denunciar_modal_ajax');
		}
		else
		{
			$view = View::factory('foto/denunciar');
		}

		$ft = $model_foto->as_array();
		$ft['categoria'] = $model_foto->categoria()->as_array();
		$view->assign('foto', $ft);
		unset($ft);

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
			$comentario = isset($_POST['comentario']) ? preg_replace('/\s+/', ' ', trim($_POST['comentario'])) : NULL;

			// Valores para cambios.
			$view->assign('motivo', $motivo);
			$view->assign('comentario', $comentario);

			// Marcas para los errores AJAX.
			$error_motivo = FALSE;
			$error_comentario = FALSE;

			// Verifico el tipo.
			if ( ! in_array($motivo, array(0, 1, 2, 3, 4, 5, 6, 7)))
			{
				$error = TRUE;
				$view->assign('error_motivo', __('No ha seleccionado un motivo válido.', FALSE));
				$error_motivo = __('No ha seleccionado un motivo válido.', FALSE);
			}

			// Verifico la razón si corresponde.
			if ($motivo === 7)
			{
				if ( ! isset($comentario{10}) || isset($comentario{400}))
				{
					$error = TRUE;
					$view->assign('error_comentario', __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE));
					$error_comentario = __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE);
				}
			}
			else
			{
				if (isset($comentario{400}))
				{
					$error = TRUE;
					$view->assign('error_comentario', __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE));
					$error_comentario = __('La descripción de la denuncia debe tener entre 10 y 400 caracteres.', FALSE);
				}
				$comentario = NULL;
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
				$id = $model_foto->denunciar(Usuario::$usuario_id, $motivo, $comentario);

				// Actualizo medallas.
				$model_foto->actualizar_medallas(Model_Medalla::CONDICION_FOTO_DENUNCIAS);

				// Agregamos el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $model_foto->usuario_id)
				{
					$model_suceso->crear($model_foto->usuario_id, TRUE, 'foto_denuncia_crear', $id);
					$model_suceso->crear(Usuario::$usuario_id, FALSE, 'foto_denuncia_crear', $id);
				}
				else
				{
					$model_suceso->crear($model_foto->usuario_id, FALSE, 'foto_denuncia_crear', $id);
				}

				if (Request::is_ajax())
				{
					echo json_encode(array('response' => 'OK', 'body' => __('La foto ha sido denunciado correctamente.', FALSE)));
					return;
				}
				else
				{
					// Informo el resultado.
					add_flash_message(FLASH_SUCCESS, __('La foto ha sido denunciado correctamente.', FALSE));
					Request::redirect($this->foto_url($model_foto));
				}
			}
		}

		if (Request::is_ajax())
		{
			$view->show();
		}
		else
		{
			// Menú.
			$this->template->assign('master_bar', parent::base_menu('fotos'));
			$this->template->assign('top_bar', Controller_Home::submenu());

			// Asignamos la vista.
			$this->template->assign('contenido', $view->parse());
		}
	}


	/**
	 * Editamos una foto.
	 * @param int $foto ID de la foto a editar.
	 */
	public function action_editar($foto)
	{
		// Verificamos usuario conectado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para editar fotos.', FALSE));
			Request::redirect('/usuario/login/', TRUE);
		}

		// Cargamos la foto.
		$foto = (int) $foto;
		$model_foto = new Model_Foto($foto);

		// Verifico la existencia.
		if ( ! $model_foto->existe())
		{
			add_flash_message(FLASH_ERROR, __('La foto que quiere editar no se encuentra disponible.', FALSE));
			Request::redirect('/foto/');
		}

		// Verifico los permisos.
		if ($model_foto->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_EDITAR))
		{
			add_flash_message(FLASH_ERROR, __('La foto que deseas editar no se encuentra disponible.', FALSE));
			Request::redirect($this->foto_url($model_foto));
		}

		// Asignamos el título.
		$this->template->assign('title', __('Editar foto', FALSE));

		// Cargamos la vista.
		$view = View::factory('foto/editar');

		$ft = $model_foto->as_array();
		$ft['categoria'] = $model_foto->categoria()->as_array();
		$view->assign('foto', $ft);
		unset($ft);

		// Cargo valores actuales.
		$view->assign('titulo', $model_foto->titulo);
		$view->assign('descripcion', $model_foto->descripcion);
		$view->assign('comentarios', ! $model_foto->comentar);
		$view->assign('visitantes', $model_foto->visitas !== NULL);
		$view->assign('categoria', $model_foto->categoria()->seo);

		// Asigno valores por defecto a los errores.
		$view->assign('error_titulo', FALSE);
		$view->assign('error_descripcion', FALSE);
		$view->assign('error_categoria', FALSE);

		// Listado de categorías.
		$model_categoria = new Model_Categoria;
		$view->assign('categorias', $model_categoria->lista());

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('index'));

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			$titulo = arr_get($_POST, 'titulo', '');
			$view->assign('titulo', $titulo);
			$descripcion = arr_get($_POST, 'descripcion', '');
			$view->assign('descripcion', $descripcion);
			$categoria = arr_get($_POST, 'categoria', '');
			$view->assign('categoria', $categoria);

			// Obtenemos los checkbox.
			$visitantes = isset($_POST['visitantes']) ? ($_POST['visitantes'] == 1) : FALSE;
			$view->assign('visitantes', $visitantes);

			$comentarios = isset($_POST['comentarios']) ? ($_POST['comentarios'] == 1) : FALSE;
			$view->assign('comentarios', $comentarios);

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', __('El formato del título no es correcto.', FALSE));
				$error = TRUE;
			}

			// Verificamos quitando BBCODE.
			$descripcion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $descripcion);

			// Verificamos la descripción.
			if ( ! isset($descripcion_clean{20}) || isset($descripcion{600}))
			{
				$view->assign('error_descripcion', __('La descripción debe tener entre 20 y 600 caracteres.', FALSE));
				$error = TRUE;
			}
			unset($descripcion_clean);

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

			// Actualizamos los datos.
			if ( ! $error)
			{
				// Evitamos XSS.
				$descripcion = htmlentities($descripcion, ENT_NOQUOTES, 'UTF-8');

				// Formateamos los campos.
				$titulo = trim(preg_replace('/\s+/', ' ', $titulo));

				// Listado de campos a actualizar.
				$campos = array(
					'titulo' => $titulo,
					'descripcion' => $descripcion,
					'comentar' => ! $comentarios,
					'visitas' => $visitantes ? (($model_foto->visitas !== NULL) ? ($model_foto->visitas) : 0) : NULL,
					'categoria_id' => $categoria_id
				);

				// Actualizo los datos.
				if ($model_foto->actualizar_campos($campos))
				{
					// Agregamos el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_foto->usuario_id)
					{
						$model_suceso->crear($model_foto->usuario_id, 'foto_editar', TRUE, $model_foto->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'foto_editar', FALSE, $model_foto->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_foto->usuario_id, 'foto_editar', FALSE, $model_foto->id, Usuario::$usuario_id);
					}

					add_flash_message(FLASH_SUCCESS, __('La foto se ha actualizado correctamente.', FALSE));
					Request::redirect($this->foto_url($model_foto));
				}
			}
		}

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Ocultamos o mostramos una foto.
	 * @param int $foto ID de la foto a ocultar o mostrar.
	 */
	public function action_ocultar_foto($foto)
	{
		if ( ! Usuario::is_login())
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('Debes iniciar sesión para poder ocultar/mostrar fotos.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder ocultar/mostrar fotos.', FALSE));
			}
			Request::redirect('/usuario/login');
		}

		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas ocultar/mostrar no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas ocultar/mostrar no se encuentra disponible.', FALSE));
			}
			Request::redirect('/foto/');
		}

		// Verifico el usuario y sus permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_OCULTAR) && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO) && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS))
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas ocultar/mostrar no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas ocultar/mostrar no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Verifico requisitos según permisos y usuario.
		if ($model_foto->estado === Model_Foto::ESTADO_ACTIVA)
		{
			$n_estado = Model_Foto::ESTADO_OCULTA;
		}
		elseif ($model_foto->estado === Model_Foto::ESTADO_OCULTA)
		{
			// Verifico actualización del rango.
			$model_foto->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_FOTOS);

			$n_estado = Model_Foto::ESTADO_ACTIVA;
		}
		else
		{
			if (Request::is_ajax())
			{
				header('Content-Type: application/json');
				die(json_encode(array('response' => 'error', 'content' => __('La foto que deseas ocultar/mostrar no se encuentra disponible.', FALSE))));
			}
			else
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas ocultar/mostrar no se encuentra disponible.', FALSE));
			}
			Request::redirect($this->foto_url($model_foto));
		}

		// Actualizo el estado.
		$model_foto->actualizar_estado($n_estado);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_ocultar', TRUE, $foto, Usuario::$usuario_id, ($n_estado == Model_Foto::ESTADO_OCULTA) ? 0 : 1);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_ocultar', FALSE, $foto, Usuario::$usuario_id, ($n_estado == Model_Foto::ESTADO_OCULTA) ? 0 : 1);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_ocultar', FALSE, $foto, Usuario::$usuario_id, ($n_estado == Model_Foto::ESTADO_OCULTA) ? 0 : 1);
		}

		// Informo el resultado.
		if (Request::is_ajax())
		{
			header('Content-Type: application/json');
			$view = View::factory('foto/ajax_mostrar_ocultar_foto');
			$view->assign('foto_id', $model_foto->id);
			$view->assign('ocultar', $model_foto->estado === Model_Foto::ESTADO_OCULTA);
			die(json_encode(array('response' => 'ok', 'content' => array('html' => $view->parse(), 'message' => __('Acción realizada correctamente.', FALSE)))));
		}
		else
		{
			add_flash_message(FLASH_SUCCESS, __('Acción realizada correctamente.', FALSE));
		}
		Request::redirect($this->foto_url($model_foto));
	}

	/**
	 * Borramos o enviamos a la papelera a una foto
	 * @param int $foto ID de la foto a borrar o enviar a la papelera.
	 * @param bool $tipo 1 la borra, -1 la envía a la papelera.
	 */
	public function action_borrar_foto($foto, $tipo)
	{
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder eliminar una foto.', FALSE));
			Request::redirect('/usuario/login');
		}

		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('La foto que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/foto/');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_foto->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_ELIMINAR))
		{
			add_flash_message(FLASH_ERROR, __('La foto que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect($this->foto_url($model_foto));
		}

		// Verifico requisitos según permisos y usuario.
		if (Usuario::$usuario_id === $model_foto->usuario_id)
		{
			if ($model_foto->estado === Model_Foto::ESTADO_ACTIVA)
			{
				$tipo = $tipo != 1;
			}
			elseif ($model_foto->estado === Model_Foto::ESTADO_PAPELERA || $model_foto === Model_Foto::ESTADO_BORRADA)
			{
				add_flash_message(FLASH_ERROR, __('La foto que deseas borrar no se encuentra disponible.', FALSE));
				Request::redirect($this->foto_url($model_foto));
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
		$model_foto->actualizar_estado($tipo ? Model_Foto::ESTADO_BORRADA : Model_Foto::ESTADO_PAPELERA);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, $tipo ? 'foto_borrar' : 'foto_papelera', TRUE, $foto, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, $tipo ? 'foto_borrar' : 'foto_papelera', FALSE, $foto, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, $tipo ? 'foto_borrar' : 'foto_papelera', FALSE, $foto, Usuario::$usuario_id);
		}

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('Acción realizada correctamente.', FALSE));
		Request::redirect($this->foto_url($model_foto));
	}

	/**
	 * Restauramos una foto proveniente de la papelera.
	 * @param int $foto ID de la foto a restaurar.
	 */
	public function action_restaurar_foto($foto)
	{
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder restaurar fotos.', FALSE));
			Request::redirect('/usuario/login');
		}

		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			add_flash_message(FLASH_ERROR, __('La foto que intentas restaurar no se encuentra disponible.', FALSE));
			Request::redirect('/foto/');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_foto->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA))
		{
			add_flash_message(FLASH_ERROR, __('La foto que intentas restaurar no se encuentra disponible.', FALSE));
			Request::redirect($this->foto_url($model_foto));
		}

		// Verifico el estado de la foto.
		if ($model_foto->estado !== Model_Foto::ESTADO_PAPELERA)
		{
			add_flash_message(FLASH_ERROR, __('La foto que intentas restaurar no se encuentra disponible.', FALSE));
			Request::redirect($this->foto_url($model_foto));
		}

		// Actualizo el estado.
		$model_foto->actualizar_estado(Model_Foto::ESTADO_ACTIVA);

		// Enviamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_restaurar', TRUE, $foto, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_restaurar', FALSE, $foto, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_restaurar', FALSE, $foto, Usuario::$usuario_id);
		}

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('La foto se ha restaurado correctamente.', FALSE));
		Request::redirect($this->foto_url($model_foto));
	}

	/**
	 * Vista preliminar de un comentario.
	 */
	public function action_preview()
	{
		// Obtengo el contenido y evitamos XSS.
		$contenido = isset($_POST['contenido']) ? htmlentities($_POST['contenido'], ENT_NOQUOTES, 'UTF-8') : '';

		// Evito salida de la plantilla base.
		$this->template = NULL;

		// Proceso contenido.
		die(Decoda::procesar($contenido));
	}

}
