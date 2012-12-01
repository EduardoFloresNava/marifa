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
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para poder acceder a esta sección.');
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR))
		{
			add_flash_message(FLASH_ERROR, 'No tienes permisos para acceder a esa sección.');
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

		// Evito salida por template.
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
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

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

		// Seteo las cantidad.
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

		// Seteamos listado de usuarios.
		$vista->assign('usuarios', $lst);
		unset($lst);

		// Cargamos listado de rangos que podemos asignar.
		$lst = Usuario::usuario()->rango()->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

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
			add_flash_message(FLASH_ERROR, 'El usuario del que quieres ver las advertencias no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Verifico cantidad de advertencias.
		if ($model_usuario->cantidad_avisos() <= 0)
		{
			add_flash_message(FLASH_ERROR, 'El usuario no posee ninguna advertencia.');
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

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

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
			add_flash_message(FLASH_ERROR, 'La advertencia que deseas eliminar no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Verifico donde tengo que regresar.
		$m_u = $model_advertencia->usuario();
		if ($m_u->cantidad_avisos() > 1)
		{
			// Elimino la advertencia.
			$model_advertencia->delete();

			// Envio notificacion.
			add_flash_message(FLASH_SUCCESS, 'La advertencia se ha eliminado correctamente.');
			Request::redirect('/admin/usuario/advertencias_usuario/'.$m_u->id);
		}
		else
		{
			// Elimino la advertencia.
			$model_advertencia->delete();

			// Envio notificacion.
			add_flash_message(FLASH_SUCCESS, 'La advertencia se ha eliminado correctamente.');
			Request::redirect('/admin/usuario/');
		}
	}

	/**
	 * Activamos la cuenta del usuario.
	 * @param type $id
	 */
	public function action_activar_usuario($id)
	{
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres activar no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres activar no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Su estado.
		if ($model_usuario->estado !== Model_Usuario::ESTADO_PENDIENTE)
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres activar no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Configuraciones del sitio.
		$model_config = new Model_Configuracion;

		// Verifico tipo de activación del usuario.
		$t_act = (int) $model_config->get('activacion_usuario', 1);

		if ($t_act == 1)
		{
			// Creo el mensaje de correo.
			$message = Email::get_message();
			$message->setSubject('Cuenta de '.$model_config->get('nombre', 'Marifa').' activada');
			$message->setTo($model_usuario->email, $model_usuario->nick);

			// Cargo la vista.
			$message_view = View::factory('emails/activada');
			$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
			$message->setBody($message_view->parse());
			unset($message_view);

			// Envio el email.
			$mailer = Email::get_mailer();
			$mailer->send($message);
		}

		// Actualizo es estado.
		$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, 'La cuenta del usuario ha sido activada correctamente.');
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
			add_flash_message(FLASH_ERROR, 'El usuario que quieres suspender no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres suspender no se encuentra disponible.');
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
				add_flash_message(FLASH_ERROR, 'El usuario que quieres suspender no se encuentra disponible.');
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
				$s_id = $model_suspension->nueva($id, Usuario::$usuario_id, $motivo, $fin);

				// Envio el suceso.
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
				add_flash_message(FLASH_SUCCESS, 'Usuario suspendido correctamente.');
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

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
			add_flash_message(FLASH_ERROR, 'El usuario al que quieres quitar la suspensión no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario al que quieres quitar la suspensión no se encuentra disponible.');
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

			// Envio el suceso.
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
		add_flash_message(FLASH_SUCCESS, 'Suspensión anulada correctamente.');
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
			add_flash_message(FLASH_ERROR, 'El usuario que quieres advertir no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres advertir no se encuentra disponible.');
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

				// Creamos la advertencia.
				$model_advertencia = new Model_Usuario_Aviso;
				$adv_id = $model_advertencia->nueva($id, Usuario::$usuario_id, $asunto, $contenido);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $id)
				{
					$model_suceso->crear($id, 'usuario_suspender', TRUE, $adv_id);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_suspender', FALSE, $adv_id);
				}
				else
				{
					$model_suceso->crear($id, 'usuario_suspender', FALSE, $adv_id);
				}

				// Seteamos mensaje flash y volvemos.
				add_flash_message(FLASH_SUCCESS, 'Advertencia enviada correctamente.');
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Baneamos a un usuario
	 * @param int $id ID del usuario a banear.
	 */
	public function action_banear_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres banear no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario que quieres banear no se encuentra disponible.');
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
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$razon = isset($_POST['razon']) ? $_POST['razon'] : NULL;

			// Valores para cambios.
			$vista->assign('razon', $razon);

			// Quitamos BBCode para dimenciones.
			$razon_clean = preg_replace('/\[([^\[\]]+)\]/', '', $razon);

			if ( ! isset($razon_clean{10}) || isset($razon_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'La razón debe tener entre 10 y 200 caractéres');
			}
			unset($razon_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$razon = htmlentities($razon, ENT_NOQUOTES, 'UTF-8');

				// Baneamos al usuario.
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
				add_flash_message(FLASH_SUCCESS, 'Baneo realizado correctamente.');
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

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
			add_flash_message(FLASH_ERROR, 'El usuario del que deseas ver los detalles del baneo no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$baneo = $model_usuario->baneo();

		if ($baneo === NULL)
		{
			add_flash_message(FLASH_ERROR, 'El usuario del que deseas ver los detalles del baneo no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/detalles_baneo_usuario');

		// Cargo información del baneo.
		$vista->assign('moderador', $baneo->moderador()->as_array());
		$vista->assign('baneo', $baneo->as_array());
		$vista->assign('usuario', $model_usuario->as_array());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);

		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

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
			add_flash_message(FLASH_ERROR, 'El usuario del que deseas ver los detalles de la suspensión no se encuentra disponible.');
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

			add_flash_message(FLASH_ERROR, 'El usuario del que deseas ver los detalles de la suspensión no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/detalles_suspension_usuario');

		// Cargo información de la suspensión.
		$vista->assign('moderador', $suspension->moderador()->as_array());
		$vista->assign('suspension', $suspension->as_array());
		$vista->assign('usuario', $model_usuario->as_array());
		$vista->assign('restante', $suspension->restante());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);

		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Quitamos lel baneo de un usuario.
	 * @param int $id ID del usuario a quitar la suspensión.
	 */
	public function action_desbanear_usuario($id)
	{
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario que deseas banear no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario que deseas banear no se encuentra disponible.');
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
		add_flash_message(FLASH_SUCCESS, 'El baneo fue anulado correctamente.');
		Request::redirect('/admin/usuario');
	}

	/**
	 * Cambiamos el rango de un usuario.
	 * @param int $usuario ID del usuario al que se le cambia el rango.
	 * @param int $rango ID del rango a setear.
	 */
	public function action_cambiar_rango($usuario, $rango)
	{
		$usuario = (int) $usuario;

		// Verificamos no sea actual.
		if ($usuario == Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, 'El usuario que deseas cambiarle el rango no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($usuario);
		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario que deseas cambiarle el rango no se encuentra disponible.');
			Request::redirect('/admin/usuario/');
		}

		// Verifico su orden.
		if ($model_usuario->rango()->es_superior(Usuario::usuario()->rango))
		{
			add_flash_message(FLASH_ERROR, 'El usuario que deseas cambiarle el rango no se encuentra disponible.');
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
				add_flash_message(FLASH_ERROR, 'Rango que deseas asignar no se encuentra disponible.');
				Request::redirect('/admin/usuario/');
			}

			// Actualizo el rango.
			$model_usuario->actualizar_campo('rango', $rango);

			// Envio el suceso.
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
			add_flash_message(FLASH_SUCCESS, 'El rango fue cambiado correctamente correctamente.');
			Request::redirect('/admin/usuario');
		}

		// Cargo la vista.
		$vista = View::factory('admin/usuario/cambiar_rango');

		// Seteo la información.
		$vista->assign('usuario', $model_usuario->as_array());

		// Cargamos los rangos.
		$lst = $model_rango->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

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

		// Seteamos listado de rangos.
		$vista->assign('rangos', $lst);
		unset($lst);

		// Rango por defecto para nuevos usuario, evitamos que se borre.
		$model_config = new Model_Configuracion;
		$vista->assign('rango_defecto', (int) $model_config->get('rango_defecto', 1));

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

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
			add_flash_message(FLASH_ERROR, 'El rango que desea visualizar es incorrecto.');
			Request::redirect('/admin/usuario/rangos/');
		}

		// Formato de la página.
		$pagina = ($pagina > 0) ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

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

		// Seteamos listado de usuarios.
		$vista->assign('usuarios', $listado);
		unset($listado);

		// Cargamos listado de rangos que podemos asignar.
		$lst = $model_rango->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

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
			add_flash_message(FLASH_ERROR, 'La posición que deseas asignar no es correcta.');
			Request::redirect('/admin/usuario/rangos');
		}

		$rango = (int) $rango;

		// Verifico existencia del rango.
		$model_rango = new Model_Usuario_Rango($rango);
		if ( ! $model_rango->existe())
		{
			add_flash_message(FLASH_ERROR, 'El rango que deseas mover no se encuentra disponible.');
			Request::redirect('/admin/usuario/rangos');
		}

		// Verifico la posición.
		if ($model_rango->orden === $posicion || $posicion > $model_rango->cantidad())
		{
			add_flash_message(FLASH_ERROR, 'La posición que deseas asignar no es correcta.');
			Request::redirect('/admin/usuario/rangos');
		}

		// Asignamos la posición.
		$model_rango->posicionar($posicion);

		// Informamos.
		add_flash_message(FLASH_SUCCESS, 'El rango se ha movido correctamente.');
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

		// Cargamos el listado de imagens para rangos disponibles.
		$imagenes_rangos = scandir(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		unset($imagenes_rangos[1], $imagenes_rangos[0]); // Quitamos . y ..

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
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
				$vista->assign('error_nombre', 'El nombre del rango deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-f]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', 'El color debe ser HEXADECIMAL de 6 digitos. Por ejemplo: 00FF00.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_rangos))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			// Verificamos los puntos.
			if ($puntos === NULL || $puntos < 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos', 'La cantidad de puntos debe ser mayor o igual a cero.');
			}

			// Verificamos los puntos máximos a que se puede dar a un post.
			if ($puntos === NULL || $puntos <= 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos', 'La cantidad de puntos a dar en un post debe ser mayor a cero.');
			}

			// Verificamos el tipo.
			if ($tipo < 0 || $tipo > 4)
			{
				$error = TRUE;
				$vista->assign('error_tipo', 'El tipo de rango es incorrecto.');
			}

			// Verificamos la cantidad.
			if ($tipo !== 0 && $cantidad <= 0)
			{
				$error = TRUE;
				$vista->assign('error_cantidad', 'Debe ingresar una cantidad positiva.');
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
				$model_rango->nuevo_rango($nombre, $color, $imagen, $puntos, $tipo, $cantidad, $puntos_dar);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				add_flash_message(FLASH_SUCCESS, 'El rango se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/usuario/rangos');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

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

		// Cargamos el listado de imagens para rangos disponibles.
		$imagenes_rangos = scandir(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		unset($imagenes_rangos[1], $imagenes_rangos[0]); // Quitamos . y ..

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_rango->nombre);
		$vista->assign('error_nombre', FALSE);
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
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
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
				$vista->assign('error_nombre', 'El nombre del rango deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-f]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', 'El color debe ser HEXADECIMAL de 6 digitos. Por ejemplo: 00FF00.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_rangos))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			// Verificamos los puntos.
			if ($puntos === NULL || $puntos < 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos', 'La cantidad de puntos debe ser mayor o igual a cero.');
			}

			// Verificamos los puntos a dar.
			if ($puntos === NULL || $puntos <= 0)
			{
				$error = TRUE;
				$vista->assign('error_puntos_dar', 'La cantidad de puntos a dar por post debe ser mayor a cero.');
			}

			// Verificamos el tipo.
			if ($tipo < 0 || $tipo > 4)
			{
				$error = TRUE;
				$vista->assign('error_tipo', 'El tipo de rango es incorrecto.');
			}

			// Verificamos la cantidad.
			if ($tipo !== 0 && ($cantidad <= 0 || $cantidad === NULL))
			{
				$error = TRUE;
				$vista->assign('error_cantidad', 'Debe ingresar una cantidad positiva.');
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

				// Actualizo la cantidads.
				if ($model_rango->cantidad != $cantidad)
				{
					$model_rango->actualizar_campo('cantidad', $cantidad);
				}

				// Informamos suceso.
				$vista->assign('success', 'Información actualizada correctamente');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

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
		$permisos[0] = array('Usuario ver denuncias', 'Ver las denuncias de usuarios y actuar sobre ellas.');
		$permisos[1] = array('Usuario suspender', 'Ver suspensiones de usuarios y modificarlas.');
		$permisos[2] = array('Usuario banear', 'Ver baneos a usuarios y modificarlos.');
		$permisos[3] = array('Usuario advertir', 'Enviar advertencias a usuarios.');
		$permisos[4] = array('Usuario revisar contenido', 'Revisar posts y fotos agregadas por el usuario. Es decir, el contenido creado por el usuario va a revisión antes de postearse.');
		$permisos[5] = array('Usuario administrar', 'Permite realizar tareas de administración de usuarios. Entre ellas está la asignación de rangos, su creación, etc.');
		$permisos[20] = array('Post crear', 'Puede crear un post.');
		$permisos[21] = array('Post puntuar', 'Puede dar puntos a un post.');
		$permisos[22] = array('Post eliminar', 'Eliminar posts de todos los usuarios.');
		$permisos[23] = array('Post ocultar', 'Oculta/muestra posts de todos los usuarios.');
		$permisos[24] = array('Post ver denuncias', 'Ver las denuncias de posts y actuar sobre ellas.');
		$permisos[25] = array('Post ver desaprobado', 'Ver los posts que no se encuentran aprobados.');
		$permisos[26] = array('Post fijar promover', 'Modificar el parámetro sticky y sponsored de los posts.');
		$permisos[27] = array('Post editar', 'Editar posts de todos los usuarios.');
		$permisos[28] = array('Post ver papelera', 'Ver los posts que se encuentran en la papelera de todos los usuarios.');
		$permisos[40] = array('Foto crear', 'Puede agregar fotos.');
		$permisos[41] = array('Foto votar', 'Puede votar las fotos.');
		$permisos[42] = array('Foto eliminar', 'Eliminar fotos de todos los usuarios.');
		$permisos[43] = array('Foto ocultar', 'Oculta/muestra fotos de todos los usuarios.');
		$permisos[44] = array('Foto ver denuncias', 'Ver las denuncias y actuar sobre ellas.');
		$permisos[45] = array('Foto ver desaprobado', 'Ver el contenido que no se encuentra aprobado.');
		$permisos[46] = array('Foto editar', 'Editar fotos de todos los usuarios.');
		$permisos[47] = array('Foto ver papelera', 'Ver la papelera de TODOS los usuarios.');
		$permisos[60] = array('Comentario comentar', 'Crear comentarios.');
		$permisos[61] = array('Comentario comentar cerrado', 'Comentar aún cuando están cerrados.');
		$permisos[62] = array('Comentario votar', 'Puede votar comentarios.');
		$permisos[63] = array('Comentario eliminar', 'Puede eliminar comentarios de todos los usuarios.');
		$permisos[64] = array('Comentario ocultar', 'Ocultar y mostrar comentarios de todos los usuarios.');
		$permisos[65] = array('Comentario editar', 'Editar comentarios de todos los usuarios.');
		$permisos[66] = array('Comentario ver desaprobado', 'Ver los comentarios que se encuentran desaprobados y tomar acciones sobre ellos.');
		$permisos[80] = array('Sitio acceso mantenimiento', 'Puede ingresar aún con el sitio en mantenimiento.');
		$permisos[81] = array('Sitio configurar', 'Permisos para modificar configuraciones globales, acciones sobre temas y plugins. modificar la publicidades y todo lo relacionado a configuracion general.');
		$permisos[82] = array('Sitio administrar contenido', 'Acceso a la administración de contenido del panel de administración.');

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

			$vista->assign('success', 'Permisos actualizados correctamente.');
		}

		// Rango por defecto para nuevos usuario, evitamos que se borre.
		$model_config = new Model_Configuracion;
		$vista->assign('rango_defecto', (int) $model_config->get('rango_defecto', 1));

		// Seteamos datos del rango.
		$vista->assign('rango', $model_rango->as_array());

		// Permisos del rango.
		$vista->assign('permisos_rango', $model_rango->permisos());

		// Usuarios del rango.
		$lst = $model_rango->usuarios();
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

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
				add_flash_message(FLASH_ERROR, 'El rango tiene usuarios y no puede ser eliminado.');
				Request::redirect('/admin/usuario/rangos');
			}

			// Verifico que no sea el único.
			if ($model_rango->cantidad() < 2)
			{
				add_flash_message(FLASH_ERROR, 'No se puede eliminar al único rango existente.');
				Request::redirect('/admin/usuario/rangos');
			}

			// Verifico no sea por defecto.
			$model_config = new Model_Configuracion;
			if ($id == (int) $model_config->get('rango_defecto', 1))
			{
				add_flash_message(FLASH_ERROR, 'No se puede eliminar al rango por defecto para los nuevos usuarios.');
				Request::redirect('/admin/usuario/rangos');
			}

			// Borramos la noticia.
			$model_rango->borrar_rango();
			add_flash_message(FLASH_SUCCESS, 'Se borró correctamente el rango.');
		}
		Request::redirect('/admin/usuario/rangos');
	}

	/**
	 * Listado de sessiones de usuarios activas.
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

		// Modelo de sessiones.
		$model_session = new Model_Session(session_id());

		// Quitamos sessiones terminadas.
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

		// Seteamos listado de noticias.
		$vista->assign('sesiones', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_sesiones'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos un rango.
	 * @param string $id ID de la session a borrar.
	 */
	public function action_terminar_session($id)
	{
		// Cargamos el modelo del session.
		$model_session = new Model_Session( (int) $id);

		// Verificamos exista.
		if ($model_session->existe())
		{
			// Terminamos la session.
			$model_session->borrar();
			add_flash_message(FLASH_SUCCESS, 'Se terminó correctamente la sessión.');
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
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);
		unset($model_configuracion);

		// Modelo de medallas.
		$model_medallas = new Model_Medalla;

		// Cargamos el listado de medallas.
		$lst = $model_medallas->listado($pagina, $cantidad_por_pagina);

		// Obtenemos datos de las medallas.
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Seteamos listado de medallas.
		$vista->assign('medallas', $lst);
		unset($lst);

		// Paginación.
		$paginador = new Paginator(Model_Medalla::cantidad(), $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/medallas/%s/'));

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_medallas'));

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

		// Cargamos el listado de imagenws para las medallas disponibles.
		$imagenes_medallas = glob(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS.'*_16.{png,jpg,gif}', GLOB_BRACE);
		if ( ! is_array($imagenes_medallas))
		{
			$imagenes_medallas = array();
		}
		else
		{
			foreach ($imagenes_medallas as $k => $v)
			{
				$imagenes_medallas[$k] = substr($v, strlen(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS));
			}
		}
		$vista->assign('imagenes_medallas', $imagenes_medallas);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
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
				$vista->assign('error_nombre', 'El nombre de la medalla deben ser entre 5 y 32 caractéres alphanuméricos o espacios.');
			}

			// Verificamos la descripción.
			if ( ! isset($descripcion{6}) || isset($descripcion{300}))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', 'La descripción debe tener entre 6 y 300 caractéres.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_medallas))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			// Verificamos el tipo.
			if ($condicion < 0 || $condicion > 22)
			{
				$error = TRUE;
				$vista->assign('error_condicion', 'El tipo de medalla es incorrecto.');
			}

			// Verificamos la cantidad.
			if ($cantidad <= 0)
			{
				$error = TRUE;
				$vista->assign('error_cantidad', 'Debe ingresar una cantidad positiva.');
			}

			if ( ! $error)
			{
				// Obtenemos tipo.
				$tipo = $condicion <= 8 ? 0 : ($condicion > 8 && $condicion <= 16 ? 1 : 2);

				// Creamos la medalla.
				$model_medalla = new Model_Medalla;
				$model_medalla->crear($nombre, $descripcion, $imagen, $tipo, $condicion, $cantidad);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				add_flash_message(FLASH_SUCCESS, 'La medalla se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/usuario/medallas/');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_medallas'));

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

		// Cargamos el listado de imagenws para las medallas disponibles.
		$img_medallas = glob(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS.'*_32.{png,jpg,gif}', GLOB_BRACE);
		$imagenes_medallas = array();
		if (is_array($img_medallas))
		{
			foreach ($img_medallas as $v)
			{
				$imagenes_medallas[substr($v, strlen(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS), -6).'16'.substr($v, -4)] = substr($v, strlen(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS));
			}
		}
		unset($img_medallas);
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
			// Seteamos sin error.
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
				$vista->assign('error_nombre', 'El nombre de la medalla deben ser entre 5 y 32 caractéres alphanuméricos o espacios.');
			}

			// Verificamos la descripción.
			if ( ! isset($descripcion{6}) || isset($descripcion{300}))
			{
				$error = TRUE;
				$vista->assign('error_descripcion', 'La descripción debe tener entre 6 y 300 caractéres.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_medallas))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			// Verificamos el tipo.
			if ($condicion < 0 || $condicion > 24)
			{
				$error = TRUE;
				$vista->assign('error_condicion', 'El tipo de medalla es incorrecto.');
			}

			// Verificamos la cantidad.
			if ($cantidad <= 0)
			{
				$error = TRUE;
				$vista->assign('error_cantidad', 'Debe ingresar una cantidad positiva.');
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

				// Actualizo descripcion.
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

				// Actualizo el condicion.
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
				$vista->assign('success', 'Información actualizada correctamente');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_medallas'));

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
			add_flash_message(FLASH_ERROR, 'La medalla que desea visualizar es incorrecto.');
			Request::redirect('/admin/usuario/medallas/');
		}

		// Formato de la página.
		$pagina = ($pagina > 0) ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/usuarios_medalla');

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

		// Seteamos listado de usuarios.
		$vista->assign('usuarios', $listado);
		unset($listado);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_medallas'));

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
			add_flash_message(FLASH_SUCCESS, 'Se borró correctamente la medalla.');
		}
		Request::redirect('/admin/usuario/medallas');
	}
}
