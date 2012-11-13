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

	/**
	 * Listado de pestañas de la foto.
	 * @param int $activo Pestaña seleccionada.
	 */
	protected function submenu($activo)
	{
		$lst = array();
		$lst['index'] = array('link' => '/foto/', 'caption' => 'Fotos', 'active' => $activo == 'index');
		if (Usuario::is_login())
		{
			$lst['nuevo'] = array('link' => '/foto/nueva', 'caption' => 'Agregar Foto', 'active' => $activo == 'nuevo');
			$lst['mis_fotos'] = array('link' => '/foto/mis_fotos', 'caption' => 'Mis Fotos', 'active' => $activo == 'mis_fotos');
		}
		return $lst;
	}

	/**
	 * Mostramos listado de fotos.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_index($pagina)
	{
		// Asignamos el título.
		$this->template->assign('title', 'Fotos');

		// Cargamos la vista.
		$view = View::factory('foto/index');

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de fotos.
		$model_fotos = new Model_Foto;
		$fotos = $model_fotos->obtener_ultimas($pagina, $cantidad_por_pagina);

		// Verifivo validez de la pagina.
		if (count($fotos) == 0 && $pagina != 1)
		{
			Request::redirect('/foto/');
		}

		// Paginación.
		$paginador = new Paginator($model_fotos->cantidad(Model_Foto::ESTADO_ACTIVA), $cantidad_por_pagina);
		$view->assign('paginacion', $paginador->get_view($pagina, '/foto/index/%d'));
		unset($paginador);

		// Procesamos información relevante.
		foreach ($fotos as $key => $value)
		{
			$d = $value->as_array();
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

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('index'));

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
		if ( ! isset($_SESSION['usuario_id']))
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder ver esta sección.';
			Request::redirect('/usuario/login');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Mis Fotos');

		// Cargamos la vista.
		$view = View::factory('foto/index');

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos el listado de fotos.
		$model_fotos = new Model_Foto;
		$fotos = $model_fotos->obtener_ultimas_usuario(Usuario::$usuario_id, $pagina, $cantidad_por_pagina);

		// Verifivo validez de la pagina.
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

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('mis_fotos'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Mostramos una foto.
	 * @param int $foto ID de la foto.
	 */
	public function action_ver($foto)
	{
		// Convertimos la foto a ID.
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto a la que intentas acceder no está disponible.';
			Request::redirect('/foto/');
		}

		// Verifico el estado.
		if ($model_foto->usuario_id !== Usuario::$usuario_id && $model_foto->estado !== Model_Foto::ESTADO_ACTIVA && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO) && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA))
		{
			$_SESSION['flash_error'] == 'La foto a la que intentas acceder no está disponible.';
			Request::redirect('/foto/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Foto - '.$model_foto->as_object()->titulo);

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
			}

			$view->assign('es_favorito', $model_foto->es_favorito(Usuario::$usuario_id));
			$view->assign('ya_vote', ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VOTAR) || $model_foto->ya_voto(Usuario::$usuario_id));
		}

		// Verifico si soporta comentarios.
		$view->assign('puedo_comentar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO) || ($model_foto->soporta_comentarios() && Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR)));
		$view->assign('comentario_eliminar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR));
		$view->assign('comentario_ocultar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR));
		$view->assign('comentario_editar', Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR));

		// Comentarios del post.
		$cmts = $model_foto->comentarios();
		$l_cmt = array();
		foreach ($cmts as $cmt)
		{
			$cl_cmt = $cmt->as_array();
			$cl_cmt['usuario'] = $cmt->usuario()->as_array();
			$l_cmt[] = $cl_cmt;
		}
		$view->assign('comentarios', $l_cmt);
		unset($l_cmt, $cmts);

		$view->assign('comentario_content', isset($_POST['comentario']) ? $_POST['comentario'] : NULL);
		$view->assign('comentario_error', get_flash('post_comentario_error'));
		$view->assign('comentario_success', get_flash('post_comentario_success'));


		// Menu.
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
				$_SESSION['flash_error'] = 'Debes estar logueado para poder seguir usuarios.';
			}
			else
			{
				$_SESSION['flash_error'] = 'Debes estar logueado para poder dejar de seguir usuarios.';
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
				$_SESSION['flash_error'] = 'El usuario al cual quieres seguir no se encuentra disponible.';
			}
			else
			{
				$_SESSION['flash_error'] = 'El usuario al cual quieres dejar de seguir no se encuentra disponible.';
			}
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verificamos no sea uno mismo.
		if (Usuario::$usuario_id == $model_usuario->id)
		{
			if ($seguir)
			{
				$_SESSION['flash_error'] = 'El usuario al cual quieres seguir no se encuentra disponible.';
			}
			else
			{
				$_SESSION['flash_error'] = 'El usuario al cual quieres dejar de seguir no se encuentra disponible.';
			}
			Request::redirect('/foto/ver/'.$foto);
		}
		
		// Verificaciones especiales en funcion si lo voy a seguir o dejar de seguir.
		if ($seguir)
		{
			// Verifico el estado.
			if ($model_usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
			{
				$_SESSION['flash_error'] = 'El usuario al cual quieres seguir no se encuentra disponible.';
				Request::redirect('/foto/ver/'.$foto);
			}
	
			// Verifico no sea seguidor.
			if ($model_usuario->es_seguidor(Usuario::$usuario_id))
			{
				$_SESSION['flash_error'] = 'El usuario al cual quieres seguir no se encuentra disponible.';
				Request::redirect('/foto/ver/'.$foto);
			}
			
			// Sigo al usuario.
			$model_usuario->seguir(Usuario::$usuario_id);
		}
		else
		{
			// Verifico sea seguidor.
			if ( ! $model_usuario->es_seguidor(Usuario::$usuario_id))
			{
				$_SESSION['flash_error'] = 'El usuario al cual quieres dejar de seguir no se encuentra disponible.';
				Request::redirect('/foto/ver/'.$foto);
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
			$_SESSION['flash_success'] = 'Comenzaste a seguir al usuario correctamente.';
		}
		else
		{
			$_SESSION['flash_success'] = 'Dejaste de seguir al usuario correctamente.';
		}
		Request::redirect('/foto/ver/'.$foto);
	}

	/**
	 * Votamos una foto.
	 * @param int $foto ID de la foto.
	 * @param int $voto 1 para positivo, -1 para negativo.
	 */
	public function action_votar($foto, $voto)
	{
		$foto = (int) $foto;
		// Obtenemos el voto.
		$voto = $voto == 1;

		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder ver esta sección.';
			Request::redirect('/usuario/login');
		}

		// Verificamos los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VOTAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos suficientes para votar fotos.';
			Request::redirect('/foto/');
		}

		// Cargamos el comentario.
		$model_foto = new Model_Foto($foto);

		// Verificamos existencia.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que deseas votar no se encuenta disponible.';
			Request::redirect('/foto/');
		}

		// Verifico el estado de la foto.
		if ($model_foto->estado !== Model_Foto::ESTADO_ACTIVA)
		{
			$_SESSION['flash_error'] = 'La foto que deseas votar no se encuenta disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verificamos el autor.
		if ($model_foto->usuario_id === Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'La foto que deseas votar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verificamos si puede votar.
		if ($model_foto->ya_voto(Usuario::$usuario_id))
		{
			$_SESSION['flash_error'] = 'La foto que deseas votar ya la has votado.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Votamos la foto.
		$model_foto->votar(Usuario::$usuario_id, $voto);

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
		$_SESSION['flash_success'] = 'El voto fue guardado correctamente.';
		Request::redirect('/foto/ver/'.$model_foto->foto_id);
	}

	/**
	 * Agregamos la foto como favorita.
	 * @param int $foto ID de la foto.
	 */
	public function action_favorito($foto)
	{
		// Convertimos el post a ID.
		$foto = (int) $foto;

		// Verifico que esté logueado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder agregar la foto a tus favoritos.';
			Request::redirect('/usuario/login');
		}

		// Cargamos el post.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que quiere poner como favorito no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Verifico el estado de la foto.
		if ($model_foto->estado != Model_Foto::ESTADO_ACTIVA)
		{
			$_SESSION['flash_error'] = 'La foto que quiere poner como favorito no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verifica autor.
		if ($model_foto->usuario_id === Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'La foto que quiere poner como favorito no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verificamos que no sea favorito.
		if ($model_foto->es_favorito(Usuario::$usuario_id))
		{
			$_SESSION['flash_error'] = 'La foto ya está en tus favoritos.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Agrego a favoritos.
		$model_foto->agregar_favorito(Usuario::$usuario_id);

		// Envio el suceso.
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
		$_SESSION['flash_success'] = 'Foto agregada a favoritos correctamente.';
		Request::redirect('/foto/ver/'.$foto);
	}

	/**
	 * Agregamos un comentario en la foto.
	 * @param int $foto ID de la foto donde comentar.
	 */
	public function action_comentar($foto)
	{
		// Verificamos el método de envio.
		if (Request::method() != 'POST')
		{
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verifico esté conectado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder realizar comentarios.';
			Request::redirect('/usuario/login');
		}

		// Convertimos el foto a ID.
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que quiere comentar no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Verifico se pueda comentar.
		if ( ! (Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO) || ($model_foto->soporta_comentarios() && Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR))))
		{
			$_SESSION['post_comentario_error'] = 'No tienes permisos para realizar comentarios en esa foto.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Obtenemos el comentario.
		$comentario = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

		// Verificamos el formato.
		$comentario_clean = preg_replace('/\[.*\]/', '', $comentario);
		if ( ! isset($comentario_clean{20}) || isset($comentario{400}))
		{
			$_SESSION['post_comentario_error'] = 'El comentario debe tener entre 20 y 400 caracteres.';

			// Evitamos la visualización de la plantilla.
			$this->template = NULL;

			Dispatcher::call('/foto/ver/'.$foto, TRUE);
		}
		else
		{
			// Evitamos XSS.
			$comentario = htmlentities($comentario, ENT_NOQUOTES, 'UTF-8');

			// Insertamos el comentario.
			$id = $model_foto->comentar(Usuario::$usuario_id, $comentario);

			// Envio el suceso.
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
			$_SESSION['post_comentario_success'] = 'El comentario se ha realizado correctamente.';
			Request::redirect('/foto/ver/'.$foto);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder ocultar/mostrar comentarios en fotos.';
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Foto_Comentario($comentario);

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
			Request::redirect('/foto/ver/'.$model_comentario->foto_id);
		}

		// Verifico los permisos.
		if ($model_comentario->estado == 0 && Usuario::$usuario_id !== $model_comentario->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para ocultar/mostrar comentarios.';
			Request::redirect('/foto/ver/'.$model_comentario->foto_id);
		}
		elseif ($model_comentario->estado == 1 && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para ocultar/mostrar comentarios.';
			Request::redirect('/foto/ver/'.$model_comentario->foto_id);
		}

		//TODO: agregar otro estado para diferenciar usuario de moderador.

		// Actualizo el estado.
		$model_comentario->actualizar_campo('estado', $tipo ? Model_Foto_Comentario::ESTADO_VISIBLE : Model_Foto_Comentario::ESTADO_OCULTO);

		// Envio el suceso.
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

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El comentario se ha ocultado/mostrado correctamente.';
		Request::redirect('/foto/ver/'.$model_comentario->foto_id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder borrar comentarios en fotos.';
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
		$model_comentario = new Model_Foto_Comentario($comentario);

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
			Request::redirect('/foto/ver/'.$model_comentario->foto_id);
		}

		// Actualizo el estado.
		$model_comentario->actualizar_campo('estado', Model_Foto_Comentario::ESTADO_BORRADO);

		// Envio el suceso.
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

		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> El comentario se ha borrado correctamente.';
		Request::redirect('/foto/ver/'.$model_comentario->foto_id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder editar comentarios en fotos.';
			Request::redirect('/usuario/login');
		}

		$comentario = (int) $comentario;

		// Cargamos el comentario.
		$model_comentario = new Model_Foto_Comentario($comentario);

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
			Request::redirect('/foto/ver/'.$model_comentario->foto_id);
		}

		// Verifico permisos estado.
		if ($model_comentario->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR))
		{
			$_SESSION['flash_error'] = 'No tienes los permisos para editar el comentario.';
			Request::redirect('/foto/ver/'.$model_comentario->foto_id);
		}

		// Cargo la vista.
		$vista = View::factory('/foto/editar_comentario');

		// Seteo información del comentario.
		$vista->assign('contenido', $model_comentario->comentario);
		$vista->assign('error_contenido', FALSE);
		$vista->assign('usuario', $model_comentario->usuario()->as_array());
		$vista->assign('foto', $model_comentario->foto()->as_array());
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

				$_SESSION['post_comentario_success'] = 'El comentario se ha actualizado correctamente.';
				Request::redirect('/foto/ver/'.$model_comentario->foto_id);
			}
		}

		// Menu.
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
			$_SESSION['flash_error'] = 'Debes loguearte para poder agregar fotos.';
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos para crear foto.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_CREAR))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para crear fotos.';
			Request::redirect('/foto/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Nueva foto');

		// Cargamos la vista.
		$view = View::factory('foto/nueva');

		// Cargo el listado de categorias.
		$model_categorias = new Model_Categoria;
		$categorias = $model_categorias->lista();

		$view->assign('categorias', $categorias);

		// Elementos por defecto.
		foreach (array('titulo', 'url', 'descripcion', 'comentarios', 'visitantes', 'categoria', 'error_titulo', 'error_url', 'error_descripcion', 'error_categoria') as $k)
		{
			$view->assign($k, '');
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('nuevo'));

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			foreach (array('titulo', 'url', 'descripcion', 'categoria') as $k)
			{
				$$k = isset($_POST[$k]) ? $_POST[$k] : '';
				$view->assign($k, $$k);
			}

			// Obtenemos los checkbox.
			$visitantes = isset($_POST['visitantes']) ? ($_POST['visitantes'] == 1) : FALSE;
			$view->assign('visitantes', $visitantes);

			$comentarios = isset($_POST['comentarios']) ? ($_POST['comentarios'] == 1) : FALSE;
			$view->assign('comentarios', $comentarios);

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', 'El formato del título no es correcto.');
				$error = TRUE;
			}

			// Verificamos quitando BBCODE.
			$descripcion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $descripcion);

			// Verificamos la descripcion.
			if ( ! isset($descripcion_clean{20}) || isset($descripcion{600}))
			{
				$view->assign('error_descripcion', 'La descripción debe tener entre 20 y 600 caractéres.');
				$error = TRUE;
			}
			unset($contenido_clean);

			// Verificamos la URL.
			if ( ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/Di', $url))
			{
				// Verifico IMG.
				if ( ! isset($_FILES['img']) || $_FILES['img']['error'] == UPLOAD_ERR_NO_FILE)
				{
					$view->assign('error_url', 'La dirección de la URL no es válida.');
					$error = TRUE;
				}
			}

			// Verifico la categoria.
			if ( ! $model_categorias->existe_seo($categoria))
			{
				$view->assign('error_categoria', 'La categoria seleccionada es incorrecta.');
				$error = TRUE;
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
							$view->assign('error_url', 'Se produjo un error al cargar la imagen.');
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
							$view->assign('error_url', 'Se produjo un error al cargar la imagen.');
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

				// Obtengo el ID de la categoria.
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

					// Informo el resultado.
					$_SESSION['flash_success'] = 'Foto creada correctamente.';
					Request::redirect('/foto/ver/'.$model_foto->id);
				}
				else
				{
					$view->assign('error', 'Se produjo un error cuando se creaba la foto. Reintente.');
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

		// Verifico esté logueado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder borrar una foto.';
			Request::redirect('/usuario/login/');
		}

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que quieres denunciar no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos que no sea autor.
		if ($model_foto->usuario_id === Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'La foto que quieres denunciar no se encuentra disponible.';
			Request::redirect('/post/ver/'.$post);
		}

		// Verifico que esté activa.
		if ($model_foto->estado !== Model_Foto::ESTADO_ACTIVA)
		{
			$_SESSION['flash_error'] = 'La foto que quieres denunciar no se encuentra disponible.';
			Request::redirect('/post/ver/'.$post);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Denunciar foto');

		// Cargamos la vista.
		$view = View::factory('foto/denunciar');

		$view->assign('foto', $model_foto->id);

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
			$comentario = isset($_POST['comentario']) ? preg_replace('/\s+/', ' ', trim($_POST['comentario'])) : NULL;

			// Valores para cambios.
			$view->assign('motivo', $motivo);
			$view->assign('comentario', $comentario);

			// Verifico el tipo.
			if ( ! in_array($motivo, array(0, 1, 2, 3, 4, 5, 6, 7)))
			{
				$error = TRUE;
				$view->assign('error_motivo', 'No ha seleccionado un motivo válido.');
			}

			// Verifico la razón si corresponde.
			if ($motivo === 7)
			{
				if ( ! isset($comentario{10}) || isset($comentario{400}))
				{
					$error = TRUE;
					$view->assign('error_comentario', 'La descripción de la denuncia debe tener entre 10 y 400 caracteres.');
				}
			}
			else
			{
				if (isset($comentario{400}))
				{
					$error = TRUE;
					$view->assign('error_comentario', 'La descripción de la denuncia debe tener entre 10 y 400 caracteres.');
				}
				$comentario = NULL;
			}

			if ( ! $error)
			{
				// Creo la denuncia.
				$id = $model_foto->denunciar(Usuario::$usuario_id, $motivo, $comentario);

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

				// Seteamos mensaje flash y volvemos.
				$_SESSION['flash_success'] = 'Denuncia enviada correctamente.';
				Request::redirect('/foto/ver/'.$model_foto->id);
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', Controller_Home::submenu());

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para editar fotos.';
			Request::redirect('/usuario/login/', TRUE);
		}

		// Cargamos la foto.
		$foto = (int) $foto;
		$model_foto = new Model_Foto($foto);

		// Verifico la existencia.
		if ( ! $model_foto->existe())
		{
			$_SESSION['flash_error'] = 'La foto que quiere editar no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Verifico los permisos.
		if ($model_foto->usuario_id !== Usuario::$usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_EDITAR))
		{
			$_SESSION['flash_error'] = 'La foto que deseas editar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Editar foto');

		// Cargamos la vista.
		$view = View::factory('foto/editar');

		$view->assign('foto', $model_foto->id);

		// Cargo valores actuales.
		$view->assign('titulo', $model_foto->titulo);
		$view->assign('descripcion', $model_foto->descripcion);
		$view->assign('comentarios', ! $model_foto->comentar);
		$view->assign('visitantes', $model_foto->visitas !== NULL);

		// Inicializo los errores.
		$view->assign('error_titulo', FALSE);
		$view->assign('error_descripcion', FALSE);

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('fotos'));
		$this->template->assign('top_bar', $this->submenu('index'));

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			foreach (array('titulo', 'descripcion') as $k)
			{
				$$k = isset($_POST[$k]) ? $_POST[$k] : '';
				$view->assign($k, $$k);
			}

			// Obtenemos los checkbox.
			$visitantes = isset($_POST['visitantes']) ? ($_POST['visitantes'] == 1) : FALSE;
			$view->assign('visitantes', $visitantes);

			$comentarios = isset($_POST['comentarios']) ? ($_POST['comentarios'] == 1) : FALSE;
			$view->assign('comentarios', $comentarios);

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', 'El formato del título no es correcto.');
				$error = TRUE;
			}

			// Verificamos quitando BBCODE.
			$descripcion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $descripcion);

			// Verificamos la descripcion.
			if ( ! isset($descripcion_clean{20}) || isset($descripcion{600}))
			{
				$view->assign('error_descripcion', 'La descripción debe tener entre 20 y 600 caractéres.');
				$error = TRUE;
			}
			unset($contenido_clean);

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

					$_SESSION['flash_success'] = 'La foto se ha actualizado correctamente.';
					Request::redirect('/foto/ver/'.$model_foto->id);
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
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder ocultar/mostrar fotos.';
			Request::redirect('/usuario/login');
		}

		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que deseas ocultar/mostrar no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Verifico el usuario y sus permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_OCULTAR) && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO) && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS))
		{
			$_SESSION['flash_error'] = 'La foto que deseas ocultar/mostrar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verifico requisitos según permisos y usuario.
		if ($model_foto->estado === Model_Foto::ESTADO_ACTIVA)
		{
			$n_estado = Model_Foto::ESTADO_OCULTA;
		}
		elseif ($model_foto->estado === Model_Foto::ESTADO_OCULTA)
		{
			$n_estado = Model_Foto::ESTADO_ACTIVA;
		}
		else
		{
			$_SESSION['flash_error'] = 'La foto que deseas ocultar/mostrar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
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
		$_SESSION['flash_success'] = '<b>&iexcl;Felicitaciones!</b> Acci&oacute;n realizada correctamente.';
		Request::redirect('/foto/ver/'.$foto);
	}

	/**
	 * Borramos o enviamos a la papelera a una foto
	 * @param int $foto ID de la foto a borrar o enviar a la papelera.
	 * @param bool $tipo 1 la borra, -1 la envia a la papelera.
	 */
	public function action_borrar_foto($foto, $tipo)
	{
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder eliminar una foto.';
			Request::redirect('/usuario/login');
		}

		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que deseas borrar no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_foto->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_ELIMINAR))
		{
			$_SESSION['flash_error'] = 'La foto que deseas borrar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
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
				$_SESSION['flash_error'] = 'La foto que deseas borrar no se encuentra disponible.';
				Request::redirect('/foto/ver/'.$foto);
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
		$_SESSION['flash_success'] = 'Acción realizada correctamente.';
		Request::redirect('/foto/ver/'.$foto);
	}

	/**
	 * Restauramos una foto proveniente de la papelera.
	 * @param int $foto ID de la foto a restaurar.
	 */
	public function action_restaurar_foto($foto)
	{
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder restaurar fotos.';
			Request::redirect('/usuario/login');
		}

		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			$_SESSION['flash_error'] = 'La foto que intentas restaurar no se encuentra disponible.';
			Request::redirect('/foto/');
		}

		// Verifico el usuario y sus permisos.
		if (Usuario::$usuario_id !== $model_foto->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA))
		{
			$_SESSION['flash_error'] = 'La foto que intentas restaurar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
		}

		// Verifico el estado de la foto.
		if ($model_foto->estado !== Model_Foto::ESTADO_PAPELERA)
		{
			$_SESSION['flash_error'] = 'La foto que intentas restaurar no se encuentra disponible.';
			Request::redirect('/foto/ver/'.$foto);
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
		$_SESSION['flash_success'] = 'La foto se ha restaurado correctamente.';
		Request::redirect('/foto/ver/'.$foto);
	}

}
