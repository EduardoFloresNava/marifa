<?php
/**
 * home.php is part of Marifa.
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
 * @subpackage  Controller\Admin
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador para moderar las denuncias.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Moderar
 */
class Base_Controller_Moderar_Denuncias extends Controller {

	/**
	 * Listado de posts con denuncias.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_posts($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Verifico el tipo de denuncias a mostrar.
		if ($tipo == 0 || $tipo == 1 || $tipo == 2)
		{
			$tipo = (int) $tipo;
		}
		else
		{
			$tipo = 0;
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('moderar/denuncias/posts');

		$vista->assign('tipo', $tipo);
		$vista->assign('cantidad_rechazados', Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_RECHAZADA));
		$vista->assign('cantidad_aprobados', Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_APLICADA));

		// Modelo de posts.
		$model_denuncias = new Model_Post_Denuncia;

		// Cargamos el listado de posts.
		$lst = $model_denuncias->listado($pagina, $cantidad_por_pagina, $tipo);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/denuncias/posts');
		}

		// Paginación.
		$total = Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE);
		$vista->assign('cantidad_pendientes', $total);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las denuncias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['post'] = $v->post()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de denuncias.
		$vista->assign('denuncias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vermos los detalles de una denuncia.
	 */
	public function action_detalle_post($denuncia)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Cargo la denuncia.
		$model_denuncia = new Model_Post_Denuncia($denuncia);

		// Verifico exista.
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'Denuncia incorrecta.';
			Request::redirect('/modedar/denuncias/posts');
		}

		// Cargo la vista.
		$vista = View::factory('moderar/denuncias/detalle_post');

		// Seteamos los datos.
		$vista->assign('denuncia', $model_denuncia->as_array());
		$vista->assign('denunciante', $model_denuncia->usuario()->as_array());
		$vista->assign('post', $model_denuncia->post()->as_array());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Cerramos una denuncia del usuario, puede ser rechazada o aceptada.
	 * @param int $denuncia ID de la denuncia a rechazar.
	 * @param bool $tipo Si fue aceptada 1, 0 si fue rechazada.
	 */
	public function action_cerrar_denuncia_post($denuncia, $tipo)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Verifico su existencia.
		$model_denuncia = new Model_Post_Denuncia($denuncia);
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'La denuncia es incorrecta.';
			Request::redirect('/moderar/denuncias/posts');
		}

		//TODO: verificar permisos.

		// Verifico el estado.
		if ($model_denuncia->estado !== Model_Post_Denuncia::ESTADO_PENDIENTE)
		{
			$_SESSION['flash_error'] = 'El estado de la denuncia no es correcto.';
			Request::redirect('/moderar/denuncias/posts');
		}

		if ($tipo == 0)
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_campo('estado', Model_Post_Denuncia::ESTADO_RECHAZADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia rechazada correctamente.';
		}
		else
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_campo('estado', Model_Post_Denuncia::ESTADO_APLICADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia aceptada correctamente.';
		}
		Request::redirect('/moderar/denuncias/posts');
	}

	/**
	 * Borramos un post.
	 * @param int $post ID del post a borrar.
	 */
	public function action_borrar_post($post)
	{
		// Verificamos esté logueado.
		if ( ! Usuario::is_login())
		{
			Request::redirect('/usuario/login');
		}

		// Cargamos el post.
		$post = (int) $post;
		$model_post = new Model_Post($post);

		// Verifico existencia del post.
		if ( ! $model_post->existe())
		{
			$_SESSION['flash_error'] = 'Post erroneo.';
			Request::redirect('/moderar/denuncias/posts');
		}

		// Verifico los permisos.
		if (Usuario::$usuario_id !== $model_post->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_POSTS))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para realizar esa acción.';
			Request::redirect('/moderar/denuncias/posts');
		}

		// Verifico el estado.
		if ($model_post->estado === Model_Post::ESTADO_BORRADO)
		{
			$_SESSION['flash_error'] = 'El estado es incorrecto.';
			Request::redirect('/moderar/denuncias/posts');
		}

		// Actualizo el post.
		$model_post->actualizar_campo('estado', Model_Post::ESTADO_BORRADO);

		//TODO: generar suceso.

		$_SESSION['flash_success'] = 'Post eliminado correctamente.';
		Request::redirect('/moderar/denuncias/posts');
	}

	/**
	 * Listado de fotos con denuncias.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_fotos($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Verifico el tipo de denuncias a mostrar.
		if ($tipo == 0 || $tipo == 1 || $tipo == 2)
		{
			$tipo = (int) $tipo;
		}
		else
		{
			$tipo = 0;
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('moderar/denuncias/fotos');

		$vista->assign('tipo', $tipo);
		$vista->assign('cantidad_rechazados', Model_Foto_Denuncia::cantidad(Model_Foto_Denuncia::ESTADO_RECHAZADA));
		$vista->assign('cantidad_aprobados', Model_Foto_Denuncia::cantidad(Model_Foto_Denuncia::ESTADO_APLICADA));

		// Modelo de denuncias de fotos.
		$model_denuncias = new Model_Foto_Denuncia;

		// Cargamos el listado de denuncias.
		$lst = $model_denuncias->listado($pagina, $cantidad_por_pagina, $tipo);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/denuncias/fotos');
		}

		// Paginación.
		$total = Model_Foto_Denuncia::cantidad(Model_Foto_Denuncia::ESTADO_PENDIENTE);
		$vista->assign('cantidad_pendientes', $total);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las denuncias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['foto'] = $v->foto()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de denuncias.
		$vista->assign('denuncias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_fotos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vermos los detalles de una denuncia a una foto.
	 */
	public function action_detalle_foto($denuncia)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Cargo la denuncia.
		$model_denuncia = new Model_Foto_Denuncia($denuncia);

		// Verifico exista.
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'Denuncia incorrecta.';
			Request::redirect('/modedar/denuncias/fotos');
		}

		// Cargo la vista.
		$vista = View::factory('moderar/denuncias/detalle_foto');

		// Seteamos los datos.
		$vista->assign('denuncia', $model_denuncia->as_array());
		$vista->assign('denunciante', $model_denuncia->usuario()->as_array());
		$vista->assign('foto', $model_denuncia->foto()->as_array());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_fotos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Cerramos una denuncia del usuario, puede ser rechazada o aceptada.
	 * @param int $denuncia ID de la denuncia a rechazar.
	 * @param bool $tipo Si fue aceptada 1, 0 si fue rechazada.
	 */
	public function action_cerrar_denuncia_foto($denuncia, $tipo)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Verifico su existencia.
		$model_denuncia = new Model_Foto_Denuncia($denuncia);
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'La denuncia es incorrecta.';
			Request::redirect('/moderar/denuncias/fotos');
		}

		//TODO: verificar permisos.

		// Verifico el estado.
		if ($model_denuncia->estado !== Model_Foto_Denuncia::ESTADO_PENDIENTE)
		{
			$_SESSION['flash_error'] = 'El estado de la denuncia no es correcto.';
			Request::redirect('/moderar/denuncias/fotos');
		}

		if ($tipo == 0)
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_campo('estado', Model_Foto_Denuncia::ESTADO_RECHAZADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia rechazada correctamente.';
		}
		else
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_campo('estado', Model_Foto_Denuncia::ESTADO_APLICADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia aceptada correctamente.';
		}
		Request::redirect('/moderar/denuncias/fotos');
	}

	/**
	 * Borramos una foto.
	 * @param int $post ID de la foto a borrar.
	 */
	public function action_borrar_foto($foto)
	{
		// Verificamos esté logueado.
		if ( ! Usuario::is_login())
		{
			Request::redirect('/usuario/login');
		}

		// Cargamos el post.
		$foto = (int) $foto;
		$model_foro = new Model_Foto($foto);

		// Verifico existencia del post.
		if ( ! $model_foro->existe())
		{
			$_SESSION['flash_error'] = 'Foto erronea.';
			Request::redirect('/moderar/denuncias/fotos');
		}

		// Verifico los permisos.
		if (Usuario::$usuario_id !== $model_foro->usuario_id && ! Usuario::permiso(Model_Usuario_Rango::PERMISO_ELIMINAR_FOTOS))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para realizar esa acción.';
			Request::redirect('/moderar/denuncias/fotos');
		}

		// Verifico el estado.
		if ($model_foro->estado === Model_Foto::ESTADO_BORRADA)
		{
			$_SESSION['flash_error'] = 'El estado es incorrecto.';
			Request::redirect('/moderar/denuncias/fotos');
		}

		// Actualizo la foto
		$model_foro->actualizar_campo('estado', Model_Foto::ESTADO_BORRADA);

		//TODO: generar suceso.

		$_SESSION['flash_success'] = 'Post eliminado correctamente.';
		Request::redirect('/moderar/denuncias/fotos');
	}

	/**
	 * Listado de usuarios con denuncias.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_usuarios($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Verifico el tipo de denuncias a mostrar.
		if ($tipo == 0 || $tipo == 1 || $tipo == 2)
		{
			$tipo = (int) $tipo;
		}
		else
		{
			$tipo = 0;
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('moderar/denuncias/usuarios');

		$vista->assign('tipo', $tipo);
		$vista->assign('cantidad_rechazados', Model_Usuario_Denuncia::cantidad(Model_Usuario_Denuncia::ESTADO_RECHAZADA));
		$vista->assign('cantidad_aprobados', Model_Usuario_Denuncia::cantidad(Model_Usuario_Denuncia::ESTADO_APLICADA));

		// Modelo de denuncias de usuarios.
		$model_denuncias = new Model_Usuario_Denuncia;

		// Cargamos el listado de denuncias.
		$lst = $model_denuncias->listado($pagina, $cantidad_por_pagina, $tipo);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/denuncias/usuarios');
		}

		// Paginación.
		$total = Model_Usuario_Denuncia::cantidad(Model_Usuario_Denuncia::ESTADO_PENDIENTE);
		$vista->assign('cantidad_pendientes', $total);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las denuncias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['denunciado'] = $v->denunciado()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de denuncias.
		$vista->assign('denuncias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_usuarios'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vermos los detalles de una denuncia a una foto.
	 */
	public function action_detalle_usuario($denuncia)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Cargo la denuncia.
		$model_denuncia = new Model_Usuario_Denuncia($denuncia);

		// Verifico exista.
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'Denuncia incorrecta.';
			Request::redirect('/modedar/denuncias/usuarios');
		}

		// Cargo la vista.
		$vista = View::factory('moderar/denuncias/detalle_usuario');

		// Seteamos los datos.
		$vista->assign('denuncia', $model_denuncia->as_array());
		$vista->assign('denunciante', $model_denuncia->usuario()->as_array());
		$vista->assign('denunciado', $model_denuncia->denunciado()->as_array());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_usuarios'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Cerramos una denuncia del usuario, puede ser rechazada o aceptada.
	 * @param int $denuncia ID de la denuncia a rechazar.
	 * @param bool $tipo Si fue aceptada 1, 0 si fue rechazada.
	 */
	public function action_cerrar_denuncia_usuario($denuncia, $tipo)
	{
		// Valido la denuncia.
		$denuncia = (int) $denuncia;

		// Verifico su existencia.
		$model_denuncia = new Model_Usuario_Denuncia($denuncia);
		if ( ! $model_denuncia->existe())
		{
			$_SESSION['flash_error'] = 'La denuncia es incorrecta.';
			Request::redirect('/moderar/denuncias/usuarios');
		}

		//TODO: verificar permisos.

		// Verifico el estado.
		if ($model_denuncia->estado !== Model_Usuario_Denuncia::ESTADO_PENDIENTE)
		{
			$_SESSION['flash_error'] = 'El estado de la denuncia no es correcto.';
			Request::redirect('/moderar/denuncias/usuarios');
		}

		if ($tipo == 0)
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_campo('estado', Model_Usuario_Denuncia::ESTADO_RECHAZADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia rechazada correctamente.';
		}
		else
		{
			// Actualizo el estado.
			$model_denuncia->actualizar_campo('estado', Model_Usuario_Denuncia::ESTADO_APLICADA);

			//TODO: enviar suceso.

			$_SESSION['flash_success'] = 'Denuncia aceptada correctamente.';
		}
		Request::redirect('/moderar/denuncias/usuarios');
	}

	/**
	 * Advertimos a un usuario.
	 * @param int $id ID del usuario a advertir.
	 */
	public function action_advertir_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'No puedes enviarte una advertencia a vos mismo.';
			Request::redirect('/moderar/denuncias/usuarios/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'Usuario incorrecto.';
			Request::redirect('/moderar/denuncias/usuarios/');
		}

		// Cargamos la vista.
		$vista = View::factory('/moderar/denuncias/advertir_usuario');

		// Información del usuario a advertir.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('asunto', '');
		$vista->assign('error_asunto', FALSE);
		$vista->assign('contenido', '');
		$vista->assign('error_contenido', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$asunto = isset($_POST['asunto']) ? preg_replace('/\s+/', ' ', trim($_POST['asunto'])) : NULL;
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : NULL;

			// Valores para cambios.
			$vista->assign('asunto', $asunto);
			$vista->assign('contenido', $contenido);

			// Verifico el asunto.
			if ( ! preg_match('/^[a-záéíóúñ ,.:;\-_]{5,100}$/Di', $asunto))
			{
				$error = TRUE;
				$vista->assign('error_asunto', 'El asunto de la advertencia debe tener entre 5 y 100 caractéres alphanuméricos.');
			}

			// Quitamos BBCode para dimenciones.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);

			if ( ! isset($contenido_clean{10}) || isset($contenido_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'El contenido debe tener entre 10 y 200 caractéres');
			}
			unset($contenido_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Cargamos el modelo de advertencias.
				$model_advertencia = new Model_Usuario_Aviso;
				$model_advertencia->nueva($id, (int) $_SESSION['usuario_id'], $asunto, $contenido);

				//TODO: agregar el suceso.

				// Seteamos mensaje flash y volvemos.
				$_SESSION['flash_success'] = 'Advertencia enviada correctamente.';
				Request::redirect('/moderar/denuncias/usuarios/');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_usuarios'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Suspendemos a un usuario.
	 * @param int $id ID del usuario a suspender.
	 */
	public function action_suspender_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'No puedes enviarte una advertencia a vos mismo.';
			Request::redirect('/moderar/denuncias/usuarios/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'Usuario incorrecto.';
			Request::redirect('/moderar/denuncias/usuarios/');
		}

		// Verifico no esté suspendido.
		$s = $model_usuario->suspension();
		if ($s !== NULL)
		{
			if ($s->restante() <= 0)
			{
				$s->anular();
			}
			else
			{
				$_SESSION['flash_error'] = 'Usuario con suspensión en efecto.';
				Request::redirect('/moderar/denuncias/usuarios/');
			}
		}
		unset($s);

		// Cargamos la vista.
		$vista = View::factory('/moderar/denuncias/suspender_usuario');

		// Información del usuario a suspender.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('motivo', '');
		$vista->assign('error_motivo', FALSE);
		$vista->assign('fin', '');
		$vista->assign('error_fin', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;
			$fin = isset($_POST['fin']) ? $_POST['fin'] : NULL;

			// Valores para cambios.
			$vista->assign('motivo', $motivo);
			$vista->assign('fin', $fin);

			// Quitamos BBCode para dimenciones.
			$motivo_clean = preg_replace('/\[([^\[\]]+)\]/', '', $motivo);

			if ( ! isset($motivo_clean{10}) || isset($motivo_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_motivo', 'El motivo debe tener entre 10 y 200 caractéres');
			}
			unset($motivo_clean);

			// Verificamos la fecha.
			if (empty($fin))
			{
				$error = TRUE;
				$vista->assign('error_fin', 'La fecha de finalización no es correcta.');
			}
			else
			{
				$fin = strtotime($fin);

				if ($fin <= time())
				{
					$error = TRUE;
					$vista->assign('error_fin', 'La fecha de finalización no es correcta.');
				}
			}

			if ( ! $error)
			{
				// Evitamos XSS.
				$motivo = htmlentities($motivo, ENT_NOQUOTES, 'UTF-8');

				// Cargamos el modelo de suspensiones.
				$model_suspension = new Model_Usuario_Suspension;
				$model_suspension->nueva($id, (int) $_SESSION['usuario_id'], $motivo	, $fin);

				// Seteamos mensaje flash y volvemos.
				$_SESSION['flash_success'] = 'Usuario suspendido correctamente.';
				Request::redirect('/moderar/denuncias/usuarios/');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('denuncias_usuarios'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
