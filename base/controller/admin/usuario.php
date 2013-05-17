<?php
/**
 * usuario.php is part of Marifa.
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

//TODO: VERIFICAR LAS ACCIONES DEPENDIENDO DEL ESTADO DE LOS USUARIOS.

/**
 * Controlador de administración de usuarios.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Usuario extends Controller {

	/**
	 * Verificamos permisos para acceder a la sección.
	 */
	public function before()
	{
		// Verifico estar identificado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder acceder a esta sección.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		parent::before();
	}

	/**
	 * Vista preliminar de BBCode para denuncias, etc.
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

	/**
	 * Listado de usuarios.
	 * @param int $pagina Número de página.
	 * @param int $tipo Tipo de usuarios a mostrar.
	 */
	public function action_index($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = ($pagina > 0) ? ( (int) $pagina) : 1;

		// TIPO, 0->todos, 1->activos, 2->suspendidos, 3->baneados
		$tipo = (int) $tipo;
		if ($tipo !== 0 && $tipo !== 1 && $tipo !== 2 && $tipo !== 3 && $tipo !== 4)
		{
			Request::redirect('/admin/usuario/');
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/index');

		// Asigno el tipo.
		$vista->assign('tipo', $tipo);

		// Limpio antiguos.
		Model_Usuario_Suspension::clean();

		// Modelo de usuarios.
		$model_usuarios = Usuario::usuario();

		// Cargo las cantidades.
		$cantidad_activas = $model_usuarios->cantidad(Model_Usuario::ESTADO_ACTIVA);
		$cantidad_suspendidas = $model_usuarios->cantidad(Model_Usuario::ESTADO_SUSPENDIDA);
		$cantidad_baneadas = $model_usuarios->cantidad(Model_Usuario::ESTADO_BANEADA);
		$cantidad_pendientes = $model_usuarios->cantidad(Model_Usuario::ESTADO_PENDIENTE);
		$cantidad_total = $cantidad_activas + $cantidad_suspendidas + $cantidad_baneadas;

		// Asigno las cantidad.
		$vista->assign('cantidad_activas', $cantidad_activas);
		$vista->assign('cantidad_suspendidas', $cantidad_suspendidas);
		$vista->assign('cantidad_baneadas', $cantidad_baneadas);
		$vista->assign('cantidad_pendientes', $cantidad_pendientes);
		$vista->assign('cantidad_total', $cantidad_total);
		$vista->assign('actual', $pagina);

		// Cargamos el listado de usuarios.
		switch ($tipo)
		{
			case 0:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina);
				$total = $cantidad_total;
				break;
			case 1:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_ACTIVA);
				$total = $cantidad_activas;
				break;
			case 2:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_SUSPENDIDA);
				$total = $cantidad_suspendidas;
				break;
			case 3:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_BANEADA);
				$total = $cantidad_baneadas;
				break;
			case 4:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_PENDIENTE);
				$total = $cantidad_pendientes;
				break;
		}

		// Paginación.
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/index/%s/'.$tipo));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['rango_id'] = $v->rango;
			$a['rango'] = $v->rango()->as_array();
			$a['avisos'] = $v->cantidad_avisos();
			if ($v->estado === Model_Usuario::ESTADO_SUSPENDIDA)
			{
				$a['restante'] = $v->suspension()->restante();
			}
			$lst[$k] = $a;
		}

		// Asignamos listado de usuarios.
		$vista->assign('usuarios', $lst);
		unset($lst);

		// Cargamos listado de rangos que podemos asignar.
		$lst = Usuario::usuario()->rango()->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuarios', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de advertencias que tiene un usuario.
	 * @param int $id ID del usuario.
	 */
	public function action_advertencias_usuario($id)
	{
		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario del que quieres ver las advertencias no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verifico cantidad de advertencias.
		if ($model_usuario->cantidad_avisos() <= 0)
		{
			add_flash_message(FLASH_ERROR, __('El usuario no posee ninguna advertencia.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/advertencias_usuario');

		// Cargo listado de avisos
		$avisos = $model_usuario->avisos();

		// Proceso para obtener información.
		$lst = array();
		foreach ($avisos as $v)
		{
			$a = $v->as_array();
			$a['moderador'] = $v->moderador()->as_array();
			$lst[] = $a;
		}
		// Listado de advertencias.
		$vista->assign('advertencias', $lst);
		unset($lst);

		// Información del usuario del que se ven las advertencias.
		$vista->assign('usuario', $model_usuario->as_array());

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Advertencias', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borro la advertencia de un usuario.
	 * @param int $id ID de la advertencia.
	 */
	public function action_borrar_advertencia_usuario($id)
	{
		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo de la advertencia.
		$model_advertencia = new Model_Usuario_Aviso($id);
		if ( ! $model_advertencia->existe())
		{
			add_flash_message(FLASH_ERROR, __('La advertencia que deseas eliminar no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verifico donde tengo que regresar.
		$m_u = $model_advertencia->usuario();
		if ($m_u->cantidad_avisos() > 1)
		{
			// Elimino la advertencia.
			$model_advertencia->delete();

			// Envío notificación.
			add_flash_message(FLASH_SUCCESS, __('La advertencia se ha eliminado correctamente.', FALSE));
			Request::redirect('/admin/usuario/advertencias_usuario/'.$m_u->id);
		}
		else
		{
			// Elimino la advertencia.
			$model_advertencia->delete();

			// Envío notificación.
			add_flash_message(FLASH_SUCCESS, __('La advertencia se ha eliminado correctamente.', FALSE));
			Request::redirect('/admin/usuario/');
		}
	}

	/**
	 * Activamos la cuenta del usuario.
	 * @param int $id
	 */
	public function action_activar_usuario($id)
	{
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres activar no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres activar no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Su estado.
		if ($model_usuario->estado !== Model_Usuario::ESTADO_PENDIENTE)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres activar no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Configuraciones del sitio.
		$model_config = Model_Configuracion::get_instance();
		$model_config->load_list(array('nombre', 'activacion_usuario'));

		// Verifico tipo de activación del usuario.
		$t_act = (int) $model_config->get('activacion_usuario', 1);

		if ($t_act == 1)
		{
			// Creo el mensaje de correo.
			$message = Email::get_message();
			$message->setSubject(sprintf(__('Cuenta de %s activada', FALSE), $model_config->get('nombre', 'Marifa')));
			$message->setTo($model_usuario->email, $model_usuario->nick);

			// Cargo la vista.
			$message_view = View::factory('emails/activada');
			$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
			$message->setBody($message_view->parse());
			unset($message_view);

			// Envío el email.
			Email::send_queue_online($message);
		}

		// Actualizo es estado.
		$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, __('La cuenta del usuario ha sido activada correctamente.', FALSE));
		Request::redirect('/admin/usuario');
	}

	/**
	 * Suspendemos a un usuario.
	 * @param int $id ID del usuario a suspender.
	 */
	public function action_suspender_usuario($id)
	{
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres suspender no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres suspender no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
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
				add_flash_message(FLASH_ERROR, __('El usuario que quieres suspender no se encuentra disponible.', FALSE));
				Request::redirect('/admin/usuario/');
			}
		}
		unset($s);

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/suspender_usuario');

		// Información del usuario a suspender.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('motivo', '');
		$vista->assign('error_motivo', FALSE);
		$vista->assign('fin', '');
		$vista->assign('error_fin', FALSE);

		if (Request::method() == 'POST')
		{
			// Marco sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;
			$fin = isset($_POST['fin']) ? $_POST['fin'] : NULL;

			// Valores para cambios.
			$vista->assign('motivo', $motivo);
			$vista->assign('fin', $fin);

			// Quitamos BBCode para dimensiones.
			$motivo_clean = preg_replace('/\[([^\[\]]+)\]/', '', $motivo);

			if ( ! isset($motivo_clean{10}) || isset($motivo_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_motivo', __('El motivo debe tener entre 10 y 200 caracteres', FALSE));
			}
			unset($motivo_clean);

			// Verificamos la fecha.
			if (empty($fin))
			{
				$error = TRUE;
				$vista->assign('error_fin', __('La fecha de finalización no es correcta.', FALSE));
			}
			else
			{
				$fin = strtotime($fin);

				if ($fin <= time())
				{
					$error = TRUE;
					$vista->assign('error_fin', __('La fecha de finalización no es correcta.', FALSE));
				}
			}

			if ( ! $error)
			{
				// Evitamos XSS.
				$motivo = htmlentities($motivo, ENT_NOQUOTES, 'UTF-8');

				// Cargamos el modelo de suspensiones.
				$model_suspension = new Model_Usuario_Suspension;
				$s_id = $model_suspension->nueva($id, Usuario::$usuario_id, $motivo, $fin);

				// Envío el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $id)
				{
					$model_suceso->crear($id, 'usuario_suspender', TRUE, $s_id);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_suspender', FALSE, $s_id);
				}
				else
				{
					$model_suceso->crear($id, 'usuario_suspender', FALSE, $s_id);
				}

				// Informamos el resultado.
				add_flash_message(FLASH_SUCCESS, __('Usuario suspendido correctamente.', FALSE));
				Request::redirect('/admin/usuario');
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Suspender', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Quitamos la suspensión de un usuario.
	 * @param int $id ID del usuario a quitar la suspensión.
	 */
	public function action_quitar_suspension_usuario($id)
	{
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario al que quieres quitar la suspensión no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario al que quieres quitar la suspensión no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$suspension = $model_usuario->suspension();

		if ($suspension === NULL)
		{
			// Verifico el estado.
			if ($model_usuario->estado === Model_Usuario::ESTADO_SUSPENDIDA)
			{
				// Actualizamos el estado.
				$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
			}
		}
		else
		{
			// Anulamos la suspensión.
			$suspension->anular();

			// Actualizamos el estado.
			$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);

			// Envío el suceso.
			$model_suceso = new Model_Suceso;
			if (Usuario::$usuario_id != $id)
			{
				$model_suceso->crear($id, 'usuario_fin_suspension', TRUE, $id, Usuario::$usuario_id);
				$model_suceso->crear(Usuario::$usuario_id, 'usuario_fin_suspension', FALSE, $id, Usuario::$usuario_id);
			}
			else
			{
				$model_suceso->crear($id, 'usuario_fin_suspension', FALSE, $id, Usuario::$usuario_id);
			}
		}
		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('Suspensión anulada correctamente.', FALSE));
		Request::redirect('/admin/usuario');
	}

	/**
	 * Enviamos una advertencia a un usuario.
	 * @param int $id ID del usuario a advertir.
	 */
	public function action_advertir_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres advertir no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres advertir no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/advertir_usuario');

		// Información del usuario a advertir.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('asunto', '');
		$vista->assign('error_asunto', FALSE);
		$vista->assign('contenido', '');
		$vista->assign('error_contenido', FALSE);

		if (Request::method() == 'POST')
		{
			// Marcamos sin error.
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
				$vista->assign('error_asunto', __('El asunto de la advertencia debe tener entre 5 y 100 caracteres alphanuméricos.', FALSE));
			}

			// Quitamos BBCode para dimensiones.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);

			if ( ! isset($contenido_clean{10}) || isset($contenido_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', __('El contenido debe tener entre 10 y 200 caracteres', FALSE));
			}
			unset($contenido_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Creamos la advertencia.
				$model_advertencia = new Model_Usuario_Aviso;
				$adv_id = $model_advertencia->nueva($id, Usuario::$usuario_id, $asunto, $contenido);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $id)
				{
					$model_suceso->crear($id, 'usuario_advertir', TRUE, $adv_id);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_advertir', FALSE, $adv_id);
				}
				else
				{
					$model_suceso->crear($id, 'usuario_advertir', FALSE, $adv_id);
				}

				// Asignamos mensaje flash y volvemos.
				add_flash_message(FLASH_SUCCESS, __('Advertencia enviada correctamente.', FALSE));
				Request::redirect('/admin/usuario');
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Advertir', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Baneamos a un usuario
	 * @param int $id ID del usuario a bloquear.
	 */
	public function action_banear_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres banear no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario que quieres banear no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/banear_usuario');

		//TODO: implementar tipo.

		// Información del usuario a advertir.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('razon', '');
		$vista->assign('error_razon', FALSE);

		if (Request::method() == 'POST')
		{
			// Marcamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$razon = isset($_POST['razon']) ? $_POST['razon'] : NULL;

			// Valores para cambios.
			$vista->assign('razon', $razon);

			// Quitamos BBCode para dimensiones.
			$razon_clean = preg_replace('/\[([^\[\]]+)\]/', '', $razon);

			if ( ! isset($razon_clean{10}) || isset($razon_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', __('La razón debe tener entre 10 y 200 caracteres', FALSE));
			}
			unset($razon_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$razon = htmlentities($razon, ENT_NOQUOTES, 'UTF-8');

				// Bloqueamos al usuario.
				$model_baneos = new Model_Usuario_Baneo;
				$ban_id = $model_baneos->nuevo($id, Usuario::$usuario_id, 0, $razon);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $id)
				{
					$model_suceso->crear($id, 'usuario_baneo', TRUE, $ban_id);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_baneo', FALSE, $ban_id);
				}
				else
				{
					$model_suceso->crear($id, 'usuario_baneo', FALSE, $ban_id);
				}

				// Informamos el resultado.
				add_flash_message(FLASH_SUCCESS, __('Baneo realizado correctamente.', FALSE));
				Request::redirect('/admin/usuario');
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Banear', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Detalles del baneo del usuario.
	 * @param int $id ID del usuario del cual se quieren ver los detalles del baneo.
	 */
	public function action_detalles_baneo_usuario($id)
	{
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario del que deseas ver los detalles del baneo no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$baneo = $model_usuario->baneo();

		if ($baneo === NULL)
		{
			add_flash_message(FLASH_ERROR, __('El usuario del que deseas ver los detalles del baneo no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/detalles_baneo_usuario');

		// Cargo información del baneo.
		$vista->assign('moderador', $baneo->moderador()->as_array());
		$vista->assign('baneo', $baneo->as_array());
		$vista->assign('usuario', $model_usuario->as_array());

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);

		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Detalles baneo', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Detalles de la suspensión del usuario.
	 * @param int $id ID del usuario del cual se quieren ver los detalles de la suspensión.
	 */
	public function action_detalles_suspension_usuario($id)
	{
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario del que deseas ver los detalles de la suspensión no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$suspension = $model_usuario->suspension();

		if ($suspension === NULL)
		{
			// Verifico el estado.
			if ($model_usuario->estado === Model_Usuario::ESTADO_SUSPENDIDA)
			{
				// Actualizamos el estado.
				$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
			}

			add_flash_message(FLASH_ERROR, __('El usuario del que deseas ver los detalles de la suspensión no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/detalles_suspension_usuario');

		// Cargo información de la suspensión.
		$vista->assign('moderador', $suspension->moderador()->as_array());
		$vista->assign('suspension', $suspension->as_array());
		$vista->assign('usuario', $model_usuario->as_array());
		$vista->assign('restante', $suspension->restante());

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);

		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Detalles suspensión', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Quitamos el baneo de un usuario.
	 * @param int $id ID del usuario a quitar la suspensión.
	 */
	public function action_desbanear_usuario($id)
	{
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que deseas banear no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario que deseas banear no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$baneo = $model_usuario->baneo();

		if ($baneo !== NULL)
		{
			// Quito el baneo.
			$baneo->borrar();

			// Genero el suceso.
			$model_suceso = new Model_Suceso;
			if (Usuario::$usuario_id != $id)
			{
				$model_suceso->crear($id, 'usuario_fin_baneo', TRUE, $id, Usuario::$usuario_id);
				$model_suceso->crear(Usuario::$usuario_id, 'usuario_fin_baneo', FALSE, $id, Usuario::$usuario_id);
			}
			else
			{
				$model_suceso->crear($id, 'usuario_fin_baneo', FALSE, $id, Usuario::$usuario_id);
			}
		}

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('El baneo fue anulado correctamente.', FALSE));
		Request::redirect('/admin/usuario');
	}

	/**
	 * Cambiamos el rango de un usuario.
	 * @param int $usuario ID del usuario al que se le cambia el rango.
	 * @param int $rango ID del rango a asignar.
	 */
	public function action_cambiar_rango($usuario, $rango)
	{
		$usuario = (int) $usuario;

		// Verificamos no sea actual.
		if ($usuario == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El usuario que deseas cambiarle el rango no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($usuario);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario que deseas cambiarle el rango no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		// Verifico su orden.
		if ($model_usuario->rango()->es_superior(Usuario::usuario()->rango))
		{
			add_flash_message(FLASH_ERROR, __('El usuario que deseas cambiarle el rango no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/');
		}

		$rango = (int) $rango;

		// Verifico el rango.
		$model_rango = new Model_Usuario_Rango($rango);
		if ($model_rango->existe())
		{
			// Verifico el nivel.
			if ($model_rango->es_superior(Usuario::usuario()->rango))
			{
				add_flash_message(FLASH_ERROR, __('Rango que deseas asignar no se encuentra disponible.', FALSE));
				Request::redirect('/admin/usuario/');
			}

			// Actualizo el rango.
			$model_usuario->actualizar_campo('rango', $rango);

			// Envío el suceso.
			$model_suceso = new Model_Suceso;
			if (Usuario::$usuario_id != $model_usuario->id)
			{
				$model_suceso->crear($model_usuario->id, 'usuario_cambio_rango', TRUE, $model_usuario->id, $rango, Usuario::$usuario_id);
				$model_suceso->crear(Usuario::$usuario_id, 'usuario_cambio_rango', FALSE, $model_usuario->id, $rango, Usuario::$usuario_id);
			}
			else
			{
				$model_suceso->crear($model_usuario->id, 'usuario_cambio_rango', FALSE, $model_usuario->id, $rango, Usuario::$usuario_id);
			}

			// Informo el resultado.
			add_flash_message(FLASH_SUCCESS, __('El rango fue cambiado correctamente correctamente.', FALSE));
			Request::redirect('/admin/usuario');
		}

		// Cargo la vista.
		$vista = View::factory('admin/usuario/cambiar_rango');

		// Asigno la información.
		$vista->assign('usuario', $model_usuario->as_array());

		// Cargamos los rangos.
		$lst = $model_rango->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.usuario'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Cambiar rango', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de rangos.
	 */
	public function action_rangos()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/rangos');

		// Modelo de rangos.
		$model_rangos = new Model_Usuario_Rango;

		// Cargamos el listado de rangos.
		$lst = $model_rangos->listado();

		// Obtenemos datos de los rangos.
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
			$lst[$k]['usuarios'] = $v->cantidad_usuarios();
		}

		// Asignamos listado de rangos.
		$vista->assign('rangos', $lst);
		unset($lst);

		// Rango por defecto para nuevos usuario, evitamos que se borre.
		$vista->assign('rango_defecto', (int) Model_Configuracion::get_instance()->get('rango_defecto', 1));

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.rangos'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Rangos', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Mostramos el listado de usuarios pertenecientes a un rango.
	 * @param int $rango ID del rango del cual se muestran los usuarios.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_usuarios_rango($rango, $pagina = 1)
	{
		// Cargo el rango.
		$rango = (int) $rango;
		$model_rango = new Model_Usuario_Rango($rango);

		// Verifico la existencia.
		if ( ! $model_rango->existe())
		{
			add_flash_message(FLASH_ERROR, __('El rango que desea visualizar es incorrecto.', FALSE));
			Request::redirect('/admin/usuario/rangos/');
		}

		// Formato de la página.
		$pagina = ($pagina > 0) ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/usuarios_rango');

		// Asigno el rango.
		$vista->assign('rango', $model_rango->as_array());

		// Cargo el listado.
		$listado = $model_rango->usuarios($pagina, $cantidad_por_pagina);

		if (count($listado) <= 0 && $pagina > 1)
		{
			Request::redirect('/admin/usuario/usuarios_rango/'.$rango);
		}

		// Cargo el total de usuarios.
		$total = $model_rango->cantidad_usuarios();

		// Paginación.
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/usuarios_rango/'.$rango.'/%s/'));

		// Obtenemos datos de los usuarios.
		foreach ($listado as $k => $v)
		{
			$listado[$k] = $v->as_array();
		}

		// Asignamos listado de usuarios.
		$vista->assign('usuarios', $listado);
		unset($listado);

		// Cargamos listado de rangos que podemos asignar.
		$lst = $model_rango->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.rangos'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Rango', FALSE).' - '.__('Listado de usuarios', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Cambiamos el orden de un rango.
	 * @param int $rango ID del rango al que cambiar su orden.
	 * @param int $posicion Posición que debe adoptar el nuevo rango. Empieza en 1.
	 */
	public function action_mover_rango($rango, $posicion)
	{
		// Verifico la posición.
		$posicion = (int) $posicion;
		if ($posicion <= 0)
		{
			add_flash_message(FLASH_ERROR, __('La posición que deseas asignar no es correcta.', FALSE));
			Request::redirect('/admin/usuario/rangos');
		}

		$rango = (int) $rango;

		// Verifico existencia del rango.
		$model_rango = new Model_Usuario_Rango($rango);
		if ( ! $model_rango->existe())
		{
			add_flash_message(FLASH_ERROR, __('El rango que deseas mover no se encuentra disponible.', FALSE));
			Request::redirect('/admin/usuario/rangos');
		}

		// Verifico la posición.
		if ($model_rango->orden === $posicion || $posicion > $model_rango->cantidad())
		{
			add_flash_message(FLASH_ERROR, __('La posición que deseas asignar no es correcta.', FALSE));
			Request::redirect('/admin/usuario/rangos');
		}

		// Asignamos la posición.
		$model_rango->posicionar($posicion);

		// Informamos.
		add_flash_message(FLASH_SUCCESS, __('El rango se ha movido correctamente.', FALSE));
		Request::redirect('/admin/usuario/rangos');
	}

	/**
	 * Creamos un nuevo rango.
	 * @todo Verificar no existan 2 rangos con el mismo tipo y cantidad.
	 */
	public function action_nuevo_rango()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/nuevo_rango');

		// Valores por defecto y errores.
		$vista->assign('nombre', '');
		$vista->assign('error_nombre', FALSE);
		$vista->assign('descripcion', '');
		$vista->assign('error_descripcion', FALSE);
		$vista->assign('color', '');
		$vista->assign('error_color', FALSE);
		$vista->assign('imagen', '');
		$vista->assign('error_imagen', FALSE);
		$vista->assign('puntos', 10);
		$vista->assign('error_puntos', FALSE);
		$vista->assign('puntos_dar', 10);
		$vista->assign('error_puntos_dar', FALSE);
		$vista->assign('tipo', 0);
		$vista->assign('error_tipo', FALSE);
		$vista->assign('cantidad', '');
		$vista->assign('error_cantidad', FALSE);

		// Cargamos el listado de imágenes para rangos disponibles.
		$o_iconos = new Icono(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		$imagenes_rangos = $o_iconos->listado_elementos('small');

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		if (Request::method() == 'POST')
		{
			// Marcamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
			$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : NULL;
			$color = isset($_POST['color']) ? $_POST['color'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;
			$puntos = isset($_POST['puntos']) ? (int) $_POST['puntos'] : NULL;
			$puntos_dar = isset($_POST['puntos_dar']) ? (int) $_POST['puntos_dar'] : NULL;
			$tipo = isset($_POST['tipo']) ? (int) $_POST['tipo'] : NULL;
			$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('descripcion', $descripcion);
			$vista->assign('color', $color);
			$vista->assign('imagen', $imagen);
			$vista->assign('puntos', $puntos);
			$vista->assign('puntos_dar', $puntos_dar);
			$vista->assign('tipo', $tipo);
			$vista->assign('cantidad', $cantidad);

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', __('El nombre del rango deben ser entre 5 y 32 caracteres alphanuméricos.', FALSE));
			}

			// Verifico la descripción.
			if (isset($descripcion{300}))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', __('La descripción no puede tener más de 300 caracteres.', FALSE));
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-f]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', __('El color debe ser HEXADECIMAL de 6 dígitos. Por ejemplo: 00FF00.', FALSE));
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, array_keys($imagenes_rangos)))
			{
				$error = TRUE;
				$vista->assign('error_imagen', __('No ha seleccionado una imagen válida.', FALSE));
			}

			// Verificamos los puntos.
			if ($puntos === NULL || $puntos < 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos', __('La cantidad de puntos debe ser mayor o igual a cero.', FALSE));
			}

			// Verificamos los puntos máximos a que se puede dar a un post.
			if ($puntos === NULL || $puntos <= 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos', __('La cantidad de puntos a dar en un post debe ser mayor a cero.', FALSE));
			}

			// Verificamos el tipo.
			if ($tipo < 0 || $tipo > 4)
			{
				$error = TRUE;
				$vista->assign('error_tipo', __('El tipo de rango es incorrecto.', FALSE));
			}

			// Verificamos la cantidad.
			if ($tipo !== 0 && $cantidad <= 0)
			{
				$error = TRUE;
				$vista->assign('error_cantidad', __('Debe ingresar una cantidad positiva.', FALSE));
			}

			if ( ! $error)
			{
				// Convertimos el color a entero.
				$color = hexdec($color);

				// Cantidad para tipo especial.
				if ($tipo == 0)
				{
					$cantidad = NULL;
				}
				else
				{
					$cantidad = (int) $cantidad;
				}

				// Creamos el rango.
				$model_rango = new Model_Usuario_Rango;
				$model_rango->nuevo_rango($nombre, htmlentities($descripcion, ENT_QUOTES, 'UTF-8'), $color, $imagen, $puntos, $tipo, $cantidad, $puntos_dar);

				//TODO: agregar suceso de administración.

				// Informo y vuelvo.
				add_flash_message(FLASH_SUCCESS, __('El rango se creó correctamente', FALSE));
				Request::redirect('/admin/usuario/rangos');
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.rangos'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Rangos', FALSE).' - '.__('Nuevo', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Editamos el rango.
	 * @param int $id ID del rango a editar.
	 * @todo Verificar no existan 2 rangos con el mismo tipo y cantidad.
	 */
	public function action_editar_rango($id)
	{
		$id = (int) $id;
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango($id);
		if ( ! $model_rango->existe())
		{
			Request::redirect('/admin/usuario/rangos');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/editar_rango');

		// Cargamos el listado de imágenes para rangos disponibles.
		$o_iconos = new Icono(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		$imagenes_rangos = $o_iconos->listado_elementos('small');

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_rango->nombre);
		$vista->assign('error_nombre', FALSE);
		$vista->assign('descripcion', $model_rango->descripcion);
		$vista->assign('error_descripcion', FALSE);
		$vista->assign('color', strtoupper(sprintf('%06s', dechex($model_rango->color))));
		$vista->assign('error_color', FALSE);
		$vista->assign('imagen', $model_rango->imagen);
		$vista->assign('error_imagen', FALSE);
		$vista->assign('puntos', $model_rango->puntos);
		$vista->assign('error_puntos', FALSE);
		$vista->assign('puntos_dar', $model_rango->puntos_dar);
		$vista->assign('error_puntos_dar', FALSE);
		$vista->assign('tipo', $model_rango->tipo);
		$vista->assign('error_tipo', FALSE);
		$vista->assign('cantidad', $model_rango->cantidad);
		$vista->assign('error_cantidad', FALSE);


		if (Request::method() == 'POST')
		{
			// Marcamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
			$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : NULL;
			$color = isset($_POST['color']) ? $_POST['color'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;
			$puntos = isset($_POST['puntos']) ? (int) $_POST['puntos'] : NULL;
			$puntos_dar = isset($_POST['puntos_dar']) ? (int) $_POST['puntos_dar'] : NULL;
			$tipo = isset($_POST['tipo']) ? (int) $_POST['tipo'] : NULL;
			$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('color', $color);
			$vista->assign('imagen', $imagen);
			$vista->assign('puntos', $puntos);
			$vista->assign('puntos_dar', $puntos_dar);
			$vista->assign('tipo', $tipo);
			$vista->assign('cantidad', $cantidad);

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', __('El nombre del rango deben ser entre 5 y 32 caracteres alphanuméricos.', FALSE));
			}

			// Verifico la descripción.
			if (isset($descripcion{300}))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', __('La descripción no puede tener más de 300 caracteres.', FALSE));
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-f]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', __('El color debe ser HEXADECIMAL de 6 dígitos. Por ejemplo: 00FF00.', FALSE));
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, array_keys($imagenes_rangos)))
			{
				$error = TRUE;
				$vista->assign('error_imagen', __('No ha seleccionado una imagen válida.', FALSE));
			}

			// Verificamos los puntos.
			if ($puntos === NULL || $puntos < 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos', __('La cantidad de puntos debe ser mayor o igual a cero.', FALSE));
			}

			// Verificamos los puntos a dar.
			if ($puntos === NULL || $puntos <= 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos_dar', __('La cantidad de puntos a dar por post debe ser mayor a cero.', FALSE));
			}

			// Verificamos el tipo.
			if ($tipo < 0 || $tipo > 4)
			{
				$error = TRUE;
				$vista->assign('error_tipo', __('El tipo de rango es incorrecto.', FALSE));
			}

			// Verificamos la cantidad.
			if ($tipo !== 0 && ($cantidad <= 0 || $cantidad === NULL))
			{
				$error = TRUE;
				$vista->assign('error_cantidad', __('Debe ingresar una cantidad positiva.', FALSE));
			}

			if ( ! $error)
			{
				// Convertimos el color a entero.
				$color = hexdec($color);

				// Cantidad para tipo especial.
				if ($tipo == 0)
				{
					$cantidad = NULL;
				}
				else
				{
					$cantidad = (int) $cantidad;
				}

				// Actualizo el color.
				if ($model_rango->color != $color)
				{
					$model_rango->cambiar_color($color);
				}

				// Actualizo el imagen.
				if ($model_rango->imagen != $imagen)
				{
					$model_rango->cambiar_imagen($imagen);
				}

				// Actualizo el nombre.
				if ($model_rango->nombre != $nombre)
				{
					$model_rango->renombrar($nombre);
				}

				// Actualizo los puntos.
				if ($model_rango->puntos != $puntos)
				{
					$model_rango->actualizar_campo('puntos', $puntos);
				}

				// Actualizo los puntos a dar.
				if ($model_rango->puntos_dar != $puntos_dar)
				{
					$model_rango->actualizar_campo('puntos_dar', $puntos_dar);
				}

				// Actualizo el tipo.
				if ($model_rango->tipo != $tipo)
				{
					$model_rango->actualizar_campo('tipo', $tipo);
				}

				// Actualizo la cantidades.
				if ($model_rango->cantidad != $cantidad)
				{
					$model_rango->actualizar_campo('cantidad', $cantidad);
				}

				// Actualizo descripción.
				if ($model_rango->descripcion != $descripcion)
				{
					$model_rango->actualizar_campo('descripcion', $descripcion);
				}

				// Informamos suceso.
				add_flash_message(FLASH_SUCCESS, __('Información actualizada correctamente', FALSE));
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.rangos'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Rango', FALSE).' - '.__('Editar', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vemos detalles de rango. Como los permisos y usuarios asignados.
	 * @param int $id ID del rango a ver.
	 */
	public function action_ver_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);
		if ( ! $model_rango->existe())
		{
			Request::redirect('/admin/usuario/rangos');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/ver_rango');

		// Listado de permisos.
		$permisos = array();
		$permisos[0] = array(__('Usuario ver denuncias', FALSE), __('Ver las denuncias de usuarios y actuar sobre ellas.', FALSE));
		$permisos[1] = array(__('Usuario suspender', FALSE), __('Ver suspensiones de usuarios y modificarlas.', FALSE));
		$permisos[2] = array(__('Usuario banear', FALSE), __('Ver baneos a usuarios y modificarlos.', FALSE));
		$permisos[3] = array(__('Usuario advertir', FALSE), __('Enviar advertencias a usuarios.', FALSE));
		$permisos[4] = array(__('Usuario revisar contenido', FALSE), __('Revisar posts y fotos agregadas por el usuario. Es decir, el contenido creado por el usuario va a revisión antes de postearse.', FALSE));
		$permisos[5] = array(__('Usuario administrar', FALSE), __('Permite realizar tareas de administración de usuarios. Entre ellas está la asignación de rangos, su creación, etc.', FALSE));
		$permisos[20] = array(__('Post crear', FALSE), __('Puede crear un post.', FALSE));
		$permisos[21] = array(__('Post puntuar', FALSE), __('Puede dar puntos a un post.', FALSE));
		$permisos[22] = array(__('Post eliminar', FALSE), __('Eliminar posts de todos los usuarios.', FALSE));
		$permisos[23] = array(__('Post ocultar', FALSE), __('Oculta/muestra posts de todos los usuarios.', FALSE));
		$permisos[24] = array(__('Post ver denuncias', FALSE), __('Ver las denuncias de posts y actuar sobre ellas.', FALSE));
		$permisos[25] = array(__('Post ver desaprobado', FALSE), __('Ver los posts que no se encuentran aprobados.', FALSE));
		$permisos[26] = array(__('Post fijar promover', FALSE), __('Modificar el parámetro sticky y sponsored de los posts.', FALSE));
		$permisos[27] = array(__('Post editar', FALSE), __('Editar posts de todos los usuarios.', FALSE));
		$permisos[28] = array(__('Post ver papelera', FALSE), __('Ver los posts que se encuentran en la papelera de todos los usuarios.', FALSE));
		$permisos[40] = array(__('Foto crear', FALSE), __('Puede agregar fotos.', FALSE));
		$permisos[41] = array(__('Foto votar', FALSE), __('Puede votar las fotos.', FALSE));
		$permisos[42] = array(__('Foto eliminar', FALSE), __('Eliminar fotos de todos los usuarios.', FALSE));
		$permisos[43] = array(__('Foto ocultar', FALSE), __('Oculta/muestra fotos de todos los usuarios.', FALSE));
		$permisos[44] = array(__('Foto ver denuncias', FALSE), __('Ver las denuncias y actuar sobre ellas.', FALSE));
		$permisos[45] = array(__('Foto ver desaprobado', FALSE), __('Ver el contenido que no se encuentra aprobado.', FALSE));
		$permisos[46] = array(__('Foto editar', FALSE), __('Editar fotos de todos los usuarios.', FALSE));
		$permisos[47] = array(__('Foto ver papelera', FALSE), __('Ver la papelera de TODOS los usuarios.', FALSE));
		$permisos[60] = array(__('Comentario comentar', FALSE), __('Crear comentarios.', FALSE));
		$permisos[61] = array(__('Comentario comentar cerrado', FALSE), __('Comentar aún cuando están cerrados.', FALSE));
		$permisos[62] = array(__('Comentario votar', FALSE), __('Puede votar comentarios.', FALSE));
		$permisos[63] = array(__('Comentario eliminar', FALSE), __('Puede eliminar comentarios de todos los usuarios.', FALSE));
		$permisos[64] = array(__('Comentario ocultar', FALSE), __('Ocultar y mostrar comentarios de todos los usuarios.', FALSE));
		$permisos[65] = array(__('Comentario editar', FALSE), __('Editar comentarios de todos los usuarios.', FALSE));
		$permisos[66] = array(__('Comentario ver desaprobado', FALSE), __('Ver los comentarios que se encuentran desaprobados y tomar acciones sobre ellos.', FALSE));
		$permisos[80] = array(__('Sitio acceso mantenimiento', FALSE), __('Puede ingresar aún con el sitio en mantenimiento.', FALSE));
		$permisos[81] = array(__('Sitio configurar', FALSE), __('Permisos para modificar configuraciones globales, acciones sobre temas y plugins. modificar la publicidades y todo lo relacionado a configuracion general.', FALSE));
		$permisos[82] = array(__('Sitio administrar contenido', FALSE), __('Acceso a la administración de contenido del panel de administración.', FALSE));

		$vista->assign('permisos', $permisos);

		if (Request::method() == 'POST')
		{
			// Obtenemos permisos usuario.
			$permisos_usuario = $model_rango->permisos();

			// Obtenemos permisos marcados.
			$activos = array();
			foreach (array_keys($permisos) as $k)
			{
				if (isset($_POST[$k]))
				{
					$activos[$k] = $k;
				}
			}

			// Calculamos nuevos y quitados.
			$i = array_intersect($permisos_usuario, $activos);
			$quitar = array_diff($permisos_usuario, $i);
			$nuevos = array_diff($activos, $i);

			// Realizamos modificaciones.
			foreach ($quitar as $q)
			{
				$model_rango->borrar_permiso($q);
			}

			foreach ($nuevos as $q)
			{
				$model_rango->agregar_permiso($q);
			}

			add_flash_message(FLASH_SUCCESS, __('Permisos actualizados correctamente.', FALSE));
		}

		// Rango por defecto para nuevos usuario, evitamos que se borre.
		$vista->assign('rango_defecto', (int) Model_Configuracion::get_instance()->get('rango_defecto', 1));

		// Asignamos datos del rango.
		$vista->assign('rango', $model_rango->as_array());

		// Permisos del rango.
		$vista->assign('permisos_rango', $model_rango->permisos());

		// Usuarios del rango.
		$lst = $model_rango->usuarios();
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.rangos'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Rango', FALSE).' - '.__('Ver', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos un rango.
	 * @param int $id ID del rango a borrar.
	 */
	public function action_borrar_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);

		// Verificamos exista.
		if ($model_rango->existe())
		{
			// Verificamos exista otro y además no tenga usuarios.
			if ($model_rango->tiene_usuarios())
			{
				add_flash_message(FLASH_ERROR, __('El rango tiene usuarios y no puede ser eliminado.', FALSE));
				Request::redirect('/admin/usuario/rangos');
			}

			// Verifico que no sea el único.
			if ($model_rango->cantidad() < 2)
			{
				add_flash_message(FLASH_ERROR, __('No se puede eliminar al único rango existente.', FALSE));
				Request::redirect('/admin/usuario/rangos');
			}

			// Verifico no sea por defecto.
			if ($id == (int) Model_Configuracion::get_instance()->get('rango_defecto', 1))
			{
				add_flash_message(FLASH_ERROR, __('No se puede eliminar al rango por defecto para los nuevos usuarios.', FALSE));
				Request::redirect('/admin/usuario/rangos');
			}

			// Borramos la noticia.
			$model_rango->borrar_rango();
			add_flash_message(FLASH_SUCCESS, __('Se borró correctamente el rango.', FALSE));
		}
		Request::redirect('/admin/usuario/rangos');
	}

	/**
	 * Listado de sesiones de usuarios activas.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_sesiones($pagina)
	{
		// Formato de la página.
		$pagina = ( (int) $pagina > 0) ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/sesiones');

		// Modelo de sesiones.
		$model_session = new Model_Session(session_id());

		// Quitamos sesiones terminadas.
		$model_session->limpiar();

		// Cargamos el listado de usuarios.
		$lst = $model_session->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$paginador = new Paginator($model_session->cantidad(), $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/sessiones/%s/'));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['ip_raw'] = $a['ip'];
			$a['ip'] = long2ip($a['ip']);

			$lst[$k] = $a;
		}

		// Asignamos listado de noticias.
		$vista->assign('sesiones', $lst);
		unset($lst);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.sesiones'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Sessiones', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos un rango.
	 * @param string $id ID de la sesión a borrar.
	 */
	public function action_terminar_session($id)
	{
		// Cargamos el modelo del sesión.
		$model_session = new Model_Session( (int) $id);

		// Verificamos exista.
		if ($model_session->existe())
		{
			// Terminamos la sesión.
			$model_session->borrar();
			add_flash_message(FLASH_SUCCESS, __('Se terminó correctamente la sesión.', FALSE));
		}
		Request::redirect('/admin/usuario/sesiones');
	}

	/**
	 * Mostramos el listado de medallas.
	 */
	public function action_medallas($pagina = 1)
	{
		// Formato de la página.
		$pagina = ($pagina > 0) ? ( (int) $pagina) : 1;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/medallas');

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Modelo de medallas.
		$model_medallas = new Model_Medalla;

		// Cargamos el listado de medallas.
		$lst = $model_medallas->listado($pagina, $cantidad_por_pagina);

		// Obtenemos datos de las medallas.
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Asignamos listado de medallas.
		$vista->assign('medallas', $lst);
		unset($lst);

		// Paginación.
		$paginador = new Paginator(Model_Medalla::cantidad(), $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/medallas/%s/'));

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.medallas'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Medallas', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Creamos un nuevo rango.
	 * @todo Agregar soporte para BBCode en la descripción.
	 */
	public function action_nueva_medalla()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/nueva_medalla');

		// Valores por defecto y errores.
		$vista->assign('nombre', '');
		$vista->assign('error_nombre', FALSE);
		$vista->assign('descripcion', '');
		$vista->assign('error_descripcion', FALSE);
		$vista->assign('imagen', '');
		$vista->assign('error_imagen', FALSE);
		$vista->assign('condicion', '');
		$vista->assign('error_condicion', FALSE);
		$vista->assign('cantidad', '');
		$vista->assign('error_cantidad', FALSE);

		// Cargo listado de imágenes.
		$o_iconos = new Icono(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS);
		$imagenes_medallas = $o_iconos->listado_elementos('small');

		$vista->assign('imagenes_medallas', $imagenes_medallas);

		if (Request::method() == 'POST')
		{
			// Marco sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
			$descripcion = isset($_POST['descripcion']) ? preg_replace('/\s+/', ' ', trim($_POST['descripcion'])) : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;
			$condicion = isset($_POST['condicion']) ? (int) $_POST['condicion'] : NULL;
			$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('descripcion', $descripcion);
			$vista->assign('imagen', $imagen);
			$vista->assign('condicion', $condicion);
			$vista->assign('cantidad', $cantidad);

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', __('El nombre de la medalla deben ser entre 5 y 32 caracteres alphanuméricos o espacios.', FALSE));
			}

			// Verificamos la descripción.
			if ( ! isset($descripcion{6}) || isset($descripcion{300}))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', __('La descripción debe tener entre 6 y 300 caracteres.', FALSE));
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, array_keys($imagenes_medallas)))
			{
				$error = TRUE;
				$vista->assign('error_imagen', __('No ha seleccionado una imagen válida.', FALSE));
			}

			// Verificamos el tipo.
			if ($condicion < 0 || $condicion > 22)
			{
				$error = TRUE;
				$vista->assign('error_condicion', __('El tipo de medalla es incorrecto.', FALSE));
			}

			// Verificamos la cantidad.
			if ($cantidad <= 0)
			{
				$error = TRUE;
				$vista->assign('error_cantidad', __('Debe ingresar una cantidad positiva.', FALSE));
			}

			if ( ! $error)
			{
				// Obtenemos tipo.
				$tipo = $condicion <= 8 ? 0 : ($condicion > 8 && $condicion <= 16 ? 1 : 2);

				// Creamos la medalla.
				$model_medalla = new Model_Medalla;
				$model_medalla->crear($nombre, $descripcion, $imagen, $tipo, $condicion, $cantidad);

				//TODO: agregar suceso de administración.

				// Informamos y volvemos.
				add_flash_message(FLASH_SUCCESS, __('La medalla se creó correctamente', FALSE));
				Request::redirect('/admin/usuario/medallas/');
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.medallas'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Medallas', FALSE).' - '.__('Nueva', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Editamos la medalla
	 * @param int $id ID de la medalla a editar.
	 * @todo Verificar no existan 2 medallas iguales.
	 */
	public function action_editar_medalla($id)
	{
		$id = (int) $id;
		// Cargamos el modelo de la medalla.
		$model_medalla = new Model_Medalla($id);

		// Verifico existencia.
		if ( ! $model_medalla->existe())
		{
			Request::redirect('/admin/usuario/medallas');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/editar_medalla');

		// Cargamos el listado de imágenes para las medallas disponibles.
		$o_iconos = new Icono(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS);
		$imagenes_medallas = $o_iconos->listado_elementos('small');
		$vista->assign('imagenes_medallas', $imagenes_medallas);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_medalla->nombre);
		$vista->assign('error_nombre', FALSE);
		$vista->assign('descripcion', $model_medalla->descripcion);
		$vista->assign('error_descripcion', FALSE);
		$vista->assign('imagen', $model_medalla->imagen);
		$vista->assign('error_imagen', FALSE);
		$vista->assign('condicion', $model_medalla->condicion);
		$vista->assign('error_condicion', FALSE);
		$vista->assign('cantidad', $model_medalla->cantidad);
		$vista->assign('error_cantidad', FALSE);

		if (Request::method() == 'POST')
		{
			// Marcamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
			$descripcion = isset($_POST['descripcion']) ? preg_replace('/\s+/', ' ', trim($_POST['descripcion'])) : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;
			$condicion = isset($_POST['condicion']) ? (int) $_POST['condicion'] : NULL;
			$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('descripcion', $descripcion);
			$vista->assign('imagen', $imagen);
			$vista->assign('condicion', $condicion);
			$vista->assign('cantidad', $cantidad);

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', __('El nombre de la medalla deben ser entre 5 y 32 caracteres alphanuméricos o espacios.', FALSE));
			}

			// Verificamos la descripción.
			if ( ! isset($descripcion{6}) || isset($descripcion{300}))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', __('La descripción debe tener entre 6 y 300 caracteres.', FALSE));
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, array_keys($imagenes_medallas)))
			{
				$error = TRUE;
				$vista->assign('error_imagen', __('No ha seleccionado una imagen válida.', FALSE));
			}

			// Verificamos el tipo.
			if ($condicion < 0 || $condicion > 24)
			{
				$error = TRUE;
				$vista->assign('error_condicion', __('El tipo de medalla es incorrecto.', FALSE));
			}

			// Verificamos la cantidad.
			if ($cantidad <= 0)
			{
				$error = TRUE;
				$vista->assign('error_cantidad', __('Debe ingresar una cantidad positiva.', FALSE));
			}

			if ( ! $error)
			{
				// Obtenemos tipo.
				$tipo = $condicion <= 8 ? 0 : ($condicion > 8 && $condicion <= 16 ? 1 : 2);

				// Actualizo nombre.
				if ($model_medalla->nombre != $nombre)
				{
					$model_medalla->actualizar_campo('nombre', $nombre);
				}

				// Actualizo descripción.
				if ($model_medalla->descripcion != $descripcion)
				{
					$model_medalla->actualizar_campo('descripcion', $descripcion);
				}

				// Actualizo el imagen.
				if ($model_medalla->imagen != $imagen)
				{
					$model_medalla->actualizar_campo('imagen', $imagen);
				}

				// Actualizo el tipo.
				if ($model_medalla->tipo != $tipo)
				{
					$model_medalla->actualizar_campo('tipo', $tipo);
				}

				// Actualizo el condición.
				if ($model_medalla->condicion != $condicion)
				{
					$model_medalla->actualizar_campo('condicion', $condicion);
				}

				// Actualizo la cantidad.
				if ($model_medalla->cantidad != $cantidad)
				{
					$model_medalla->actualizar_campo('cantidad', $cantidad);
				}

				// Informamos suceso.
				add_flash_message(FLASH_SUCCESS, __('Información actualizada correctamente', FALSE));
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.medallas'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Medalla', FALSE).' - '.__('Editar', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Mostramos el listado de usuarios que poseen una medalla.
	 * @param int $rango ID de la medalla de la cual se muestran los usuarios.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_usuarios_medalla($medalla, $pagina = 1)
	{
		// Cargo la medalla.
		$medalla = (int) $medalla;
		$model_medalla = new Model_Medalla($medalla);

		// Verifico la existencia.
		if ( ! $model_medalla->existe())
		{
			add_flash_message(FLASH_ERROR, __('La medalla que desea visualizar es incorrecto.', FALSE));
			Request::redirect('/admin/usuario/medallas/');
		}

		// Formato de la página.
		$pagina = ($pagina > 0) ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('/admin/usuario/usuarios_medalla');

		// Asigno el rango.
		$vista->assign('medalla', $model_medalla->as_array());

		// Cargo el listado.
		$listado = $model_medalla->usuarios($pagina, $cantidad_por_pagina);

		if (count($listado) <= 0 && $pagina > 1)
		{
			Request::redirect('/admin/usuario/usuarios_medalla/'.$medalla);
		}

		// Cargo el total de usuarios.
		$total = $model_medalla->cantidad_usuarios();

		// Paginación.
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/usuarios_medalla/'.$medalla.'/%s/'));

		// Obtenemos datos de los usuarios.
		foreach ($listado as $k => $v)
		{
			$listado[$k] = $v->as_array();
		}

		// Asignamos listado de usuarios.
		$vista->assign('usuarios', $listado);
		unset($listado);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuarios.medallas'));

		// Asigno el título.
		$this->template->assign('title', __('Administración', FALSE).' - '. __('Usuario', FALSE).' - '.__('Medallas', FALSE).' - '.__('Usuarios con la medalla', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos una medalla.
	 * @param int $id ID de la medalla a borrar.
	 */
	public function action_borrar_medalla($id)
	{
		// Cargamos el modelo de la medalla.
		$model_medalla = new Model_Medalla( (int) $id);

		// Verificamos exista.
		if ($model_medalla->existe())
		{
			//TODO: Enviar sucesos a los usuarios.

			// Borramos la medalla.
			$model_medalla->borrar();
			add_flash_message(FLASH_SUCCESS, __('Se borró correctamente la medalla.', FALSE));
		}
		Request::redirect('/admin/usuario/medallas');
	}
}
