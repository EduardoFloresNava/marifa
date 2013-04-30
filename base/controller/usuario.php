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
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador para el manejo de usuarios.
 * Permite el inicio de sesión, registro, validar cuentas y recuperar contraseña.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Usuario extends Controller {

	/**
	 * Verificamos que barra utilizar.
	 */
	public function before()
	{
		parent::before();

		// Asigno el menú.
		$this->template->assign('master_bar', parent::base_menu());
	}

	/**
	 * Inicio de sesión de un usuario.
	 */
	public function action_login()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			if (Request::is_ajax())
			{
				Request::http_response_code(303);
				echo SITE_URL;
				return;
			}
			else
			{
				// Lo enviamos al perfil.
				Request::redirect('/', FALSE, TRUE);
			}
		}

		// Asignamos el título.
		$this->template->assign('title', __('Ingreso', FALSE));

		// Cargamos la vista del usuario.
		if (Request::is_ajax())
		{
			$view_usuario = View::factory('usuario/login_modal_ajax');
		}
		else
		{
			$view_usuario = View::factory('usuario/login');
		}

		$view_usuario->assign('error', NULL);
		$view_usuario->assign('error_nick', FALSE);
		$view_usuario->assign('error_password', FALSE);
		$view_usuario->assign('nick', '');

		// Verificamos si se han enviado los datos.
		if (Request::method() == 'POST')
		{
			// Verificamos los datos enviados.

			// Verificamos estén ambos datos.
			if ( ! isset($_POST['nick']) || empty($_POST['nick']) || ! isset($_POST['password']) || empty($_POST['password']))
			{
				// Verifico ajax.
				if (Request::is_ajax())
				{
					echo json_encode(array(
						'response' => 'ERROR',
						'body' => array(
							'error' => __('Debe introducir el E-Mail o Usuario y la contraseña para poder acceder.', FALSE),
							'error_nick' => ! (isset($_POST['nick']) && ! empty($_POST['nick'])),
							'error_password' => ! (isset($_POST['password']) && ! empty($_POST['password']))
						),
					));
					return;
				}

				// Verifico campos.
				$view_usuario->assign('error', __('Debe introducir el E-Mail o Usuario y la contraseña para poder acceder.', FALSE));
				$view_usuario->assign('error_nick', ! (isset($_POST['nick']) && ! empty($_POST['nick'])));
				$view_usuario->assign('error_password', ! (isset($_POST['password']) && ! empty($_POST['password'])));
				$view_usuario->assign('nick', isset($_POST['nick']) ? $_POST['nick'] : '');
			}
			else
			{
				// Obtenemos los datos.
				$nick = $_POST['nick'];
				$password = $_POST['password'];

				// Realizamos el login.
				$model_usuario = new Model_Usuario;

				$rst = $model_usuario->login($nick, $password);

				switch ($rst)
				{
					case -1: // Datos inválidos.
						if (Request::is_ajax())
						{
							echo json_encode(array(
								'response' => 'ERROR',
								'body' => array(
									'error' => __('Los datos introducidos son inválidos.', FALSE),
									'error_nick' => TRUE,
									'error_password' => TRUE
								),
							));
							return;
						}

						$view_usuario->assign('error', __('Los datos introducidos son inválidos.', FALSE));
						$view_usuario->assign('error_nick', TRUE);
						$view_usuario->assign('error_password', TRUE);
						break;
					case Model_Usuario::ESTADO_ACTIVA: // Cuenta activa.
						// Actualizo los puntos.
						if ($model_usuario->lastlogin === NULL || $model_usuario->lastlogin->getTimestamp() < mktime(0, 0, 0))
						{
							$model_usuario->actualizar_campo('puntos', $model_usuario->puntos + $model_usuario->rango()->puntos);
						}

						// Verifico si tiene advertencias si visualizar.
						if ($model_usuario->cantidad_avisos(Model_Usuario_Aviso::ESTADO_NUEVO) > 0)
						{
							add_flash_message(FLASH_INFO, __(strintf('Tienes advertencias nuevas. Puedes verlas desde <a href="%s/cuenta/avisos/">aquí</a>.', SITE_URL), FALSE));
						}

						// Envío mensaje de bienvenida.
						add_flash_message(FLASH_SUCCESS, __('Bienvenido.', FALSE));

						// Informo que fue correcto.
						if (Request::is_ajax())
						{
							echo json_encode(array('response' => 'OK', 'redirect' => SITE_URL.'/'));
							return;
						}

						// Lo envío a la portada.
						Request::redirect('/', FALSE, TRUE);
						break;
					case Model_Usuario::ESTADO_PENDIENTE:  // Cuenta por activar.
						if (Request::is_ajax())
						{
							echo json_encode(array(
								'response' => 'ERROR',
								'body' => array(
									'error' => sprintf(__('La cuenta no ha sido validada aún. Si no recibiste el correo de activación haz click <a href="%s/usuario/pedir_activacion/">aquí</a>', FALSE), SITE_URL),
									'error_nick' => FALSE,
									'error_password' => FALSE
								),
							));
							return;
						}

						$view_usuario->assign('error', sprintf(__('La cuenta no ha sido validada aún. Si no recibiste el correo de activación haz click <a href="%s/usuario/pedir_activacion/">aquí</a>', FALSE), SITE_URL));
						break;
					case Model_Usuario::ESTADO_SUSPENDIDA: // Cuenta suspendida.
						// Obtenemos la suspensión.
						$suspension = $model_usuario->suspension();

						// Obtengo información para formar mensaje.
						$motivo = Decoda::procesar($suspension->motivo);
						$moderador = $suspension->moderador()->as_array();
						$seconds = $suspension->restante();

						// Tiempo restante
						$restante = sprintf("%d:%02d:%02d", floor($seconds / 3600), floor($seconds % 3600 / 60), $seconds % 60);
						unset($seconds);

						if (Request::is_ajax())
						{
							echo json_encode(array(
								'response' => 'ERROR',
								'body' => array(
									'error' => sprintf(__('%s te ha suspendido por %s debido a:<br /> %s', FALSE), $moderador['nick'], $restante, $motivo),
									'error_nick' => FALSE,
									'error_password' => FALSE
								),
							));
							return;
						}

						$view_usuario->assign('error', sprintf(__('%s te ha suspendido por %s debido a:<br /> %s', FALSE), $moderador['nick'], $restante, $motivo));
						break;
					case Model_Usuario::ESTADO_BANEADA:    // Cuenta bloqueada.
						$baneo = $model_usuario->baneo();

						if (Request::is_ajax())
						{
							echo json_encode(array(
								'response' => 'ERROR',
								'body' => array(
									'error' => sprintf(__('%s ha bloqueado esta cuenta el %s debido a: <br /> %s', FALSE), $baneo->moderador()->nick, $baneo->fecha->format('d/m/Y H:i:s'), Decoda::procesar($baneo->razon)),
									'error_nick' => FALSE,
									'error_password' => FALSE
								),
							));
							return;
						}
						
						$view_usuario->assign('error', sprintf(__('%s ha bloqueado esta cuenta el %s debido a: <br /> %s', FALSE), $baneo->moderador()->nick, $baneo->fecha->format('d/m/Y H:i:s'), Decoda::procesar($baneo->razon)));
				}

				$view_usuario->assign('nick', $nick);
			}
		}
		else
		{
			$view_usuario->assign('nick', '');
		}

		if (Request::is_ajax() && Request::method() == 'GET')
		{
			$this->template = NULL;
			$view_usuario->show();
		}
		else
		{
			// Agregamos el la vista a la plantilla.
			$this->template->assign('contenido', $view_usuario->parse());
		}
	}

	/**
	 * Crearse una nueva cuenta.
	 */
	public function action_register()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			if (Request::is_ajax())
			{
				Request::http_response_code(303);
				echo SITE_URL;
				return;
			}
			else
			{
				// Lo enviamos al perfil.
				Request::redirect('/', FALSE, TRUE);
			}
		}

		// Cargo configuraciones.
		Model_Configuracion::get_instance()->load_list(array('registro', 'usuarios_bloqueados', 'rango_defecto', 'activacion_usuario'));

		// Verifico si está abierto el registro.
		if ( ! (bool) Model_Configuracion::get_instance()->get('registro', TRUE))
		{
			if (Request::is_ajax())
			{
				// Informo que la petición es inválida.
				Request::http_response_code(409);
				__('El registro se encuentra cerrado, no se pueden crear nuevas cuentas.');
				return;
			}
			else
			{
				// Informo que el registro no se encuentra disponible.
				add_flash_message(FLASH_ERROR, __('El registro se encuentra cerrado, no se pueden crear nuevas cuentas.', FALSE));
				Request::redirect('/usuario/login/');
			}
		}

		// Cargamos modelo del usuario.
		$model_usuario = new Model_Usuario;

		// Asignamos el título.
		$this->template->assign('title', __('Registro', FALSE));

		// Cargamos la vista del usuario.
		if (Request::is_ajax())
		{
			$view_usuario = View::factory('usuario/register_ajax');
		}
		else
		{
			$view_usuario = View::factory('usuario/register');
		}

		// Pasamos toda la información a la vista.
		foreach (array('nick', 'email', 'password', 'c_password', 'captcha') as $field)
		{
			$view_usuario->assign($field, in_array($field, array('nick', 'email')) ? (isset($_POST[$field]) ? $_POST[$field] : ''): '');
			$view_usuario->assign('error_'.$field, FALSE);
		}

		// Verificamos si se han enviado los datos.
		if (Request::method() == 'POST')
		{
			// Verificamos los datos enviados.
			$error = FALSE;
			foreach (array('nick', 'email', 'password', 'c_password', 'captcha') as $field)
			{
				if ( ! isset($_POST[$field]) || empty($_POST[$field]))
				{
					$view_usuario->assign('error_'.$field, TRUE);
					$error = TRUE;
				}
			}

			if ($error)
			{
				$view_usuario->assign('error', __('Debe introducir todos los datos', FALSE));
			}
			else
			{
				// Pasamos toda la información a la vista.
				foreach (array('nick', 'email', 'captcha') as $field)
				{
					$view_usuario->assign($field, $_POST[$field]);
				}

				// Realizamos verificaciones.
				$error = FALSE;

				// Verificamos el nick.
				if ( ! preg_match('/^[a-zA-Z0-9]{4,16}$/D', $_POST['nick']))
				{
					$view_usuario->assign('error_nombre', TRUE);
					$error = TRUE;
				}
				else // Verifico existencia.
				{
					// Proceso nick.
					$nick = trim(preg_replace('/\s+/', ' ', $_POST['nick']));

					// Listado de nombres de usaurios no permitidos.
					$nicks_bloqueados = unserialize(Model_Configuracion::get_instance()->get_default('usuarios_bloqueados', 'a:0:{}'));

					if (in_array($nick, $nicks_bloqueados))
					{
						$view_usuario->assign('error_nombre', __('Ya existe un usuario con ese nick.', FALSE));
						$error = TRUE;
					}
				}

				// Verificamos e-mail.
				if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $_POST['email']))
				{
					$view_usuario->assign('error_email', TRUE);
					$error = TRUE;
				}

				// Verificamos contraseña.
				if ( ! preg_match('/^[a-zA-Z0-9\-_@\*\+\/#$%]{6,20}$/D', $_POST['password']))
				{
					$view_usuario->assign('error_password', TRUE);
					$error = TRUE;
				}
				else
				{
					// Verificamos que concuerden.
					if ($_POST['password'] != $_POST['c_password'])
					{
						$view_usuario->assign('error_c_password', TRUE);
						$error = TRUE;
					}
				}

				// Verifico CAPTCHA.
				include_once(VENDOR_PATH.'securimage'.DS.'securimage.php');
				$securimage = new securimage;
				if ($securimage->check($_POST['captcha']) === FALSE)
				{
					$view_usuario->assign('error_captcha', TRUE);
					$error = TRUE;
				}

				if ($error)
				{
					$view_usuario->assign('error', __('Los datos introducidos no son válidos', FALSE));
				}
				else
				{
					// Formateamos las entradas.
					$nick = trim(preg_replace('/\s+/', ' ', $_POST['nick']));
					$email = trim($_POST['email']);
					$password = trim($_POST['password']);

					// Realizamos el registro.
					try {
						$id = $model_usuario->register($nick, $email, $password, (int) Model_Configuracion::get_instance()->get('rango_defecto', 1));
					}
					catch (Exception $e)
					{
						$view_usuario->assign('error', $e->getMessage());
						$this->template->assign('contenido', $view_usuario->parse());
						return;
					}

					if ($id)
					{
						// Verifico tipo de activación del usuario.
						$t_act = (int) Model_Configuracion::get_instance()->get('activacion_usuario', 1);

						if ($t_act == 1)
						{
							// Genero el token de activación.
							$model_activar = new Model_Usuario_Recuperacion;
							$token = $model_activar->crear($id, $email, Model_Usuario_Recuperacion::TIPO_ACTIVACION);

							// Configuraciones del sitio.
							$model_config = Model_Configuracion::get_instance();

							// Creo el mensaje de correo.
							$message = Email::get_message();
							$message->setSubject(sprintf(__('Activación cuenta de %s', FALSE), $model_config->get('nombre', 'Marifa')));
							$message->setTo($email, $nick);

							// Cargo la vista.
							$message_view = View::factory('emails/register');
							$message_view->assign('codigo', $token);
							$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
							$message->setBody($message_view->parse());
							unset($message_view);

							// Envío el email.
							$mailer = Email::get_mailer();
							$mailer->send($message);
						}
						elseif ($t_act == 2)
						{
							$model_usuario->load_by_nick($nick);
							$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
						}

						// Registro completo.
						switch ($t_act)
						{
							case 0: // Activación manual.
								if (Request::is_ajax())
								{
									echo json_encode(array('response' => 'OK', 'body' => __('El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe esperar que un administrador active su cuenta, cuando eso suceda serás notificado por correo.', FALSE)));
									return;
								}
								else
								{
									add_flash_message(FLASH_SUCCESS, __('El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe esperar que un administrador active su cuenta, cuando eso suceda serás notificado por correo.', FALSE));
								}
								break;
							case 1: // Activación por e-mail.
								if (Request::is_ajax())
								{
									echo json_encode(array('response' => 'OK', 'body' => __('El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe seguir las instrucciones que fueron enviadas a su casilla de <strong>E-Mail</strong>.', FALSE)));
									return;
								}
								else
								{
									add_flash_message(FLASH_SUCCESS, __('El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe seguir las instrucciones que fueron enviadas a su casilla de <strong>E-Mail</strong>.', FALSE));
								}
								break;
							case 2: // Activación automática.
								if (Request::is_ajax())
								{
									echo json_encode(array('response' => 'OK', 'body' => sprintf(__('El registro se ha realizado <strong>correctamente</strong>. Ya puedes acceder a tu cuenta iniciando sesión <a href="%s/usuario/login/">aquí</a>.', FALSE), SITE_URL)));
									return;
								}
								else
								{
									add_flash_message(FLASH_SUCCESS, sprintf(__('El registro se ha realizado <strong>correctamente</strong>. Ya puedes acceder a tu cuenta iniciando sesión <a href="%s/usuario/login/">aquí</a>.', FALSE), SITE_URL));
								}
								break;
						}
						Request::redirect('/login');
					}
					else
					{
						// Error al registrar.
						$view_usuario->assign('error', __('No se pudo crear la cuenta, por favor reintente.', FALSE));
					}
				}

			}
		}

		// Verifico vista ajax.
		if (Request::is_ajax())
		{
			$view_usuario->show();
			$this->template = NULL;
		}
		else
		{
			// Agregamos el la vista a la plantilla.
			$this->template->assign('contenido', $view_usuario->parse());
			unset($view_usuario);
		}
	}

	/**
	 * Obtenemos el formulario de registro.
	 * Se usa para peticiones AJAX.
	 */
	public function action_register_form()
	{
		// Solo peticiones ajax. El resto a register.
		if ( ! Request::is_ajax())
		{
			Request::redirect('/usuario/register', FALSE, TRUE);
		}

		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			Request::http_response_code(303);
			echo SITE_URL;
			return;
		}

		// Verifico si está abierto el registro.
		if ( ! (bool) Utils::configuracion()->get('registro', TRUE))
		{
			// Informo que la petición es inválida.
			Request::http_response_code(409);
			__('El registro se encuentra cerrado, no se pueden crear nuevas cuentas.');
			return;
		}

		// Cargo y muestro la vista.
		View::factory('usuario/register_modal_ajax')->show();

		// Evito salida de la plantilla base.
		$this->template = NULL;
	}

	/**
	 * Verifico si el e-mail es válido y además que no esté en uso.
	 */
	public function action_validar_email()
	{
		// Obtengo el e-mail.
		$email = arr_get($_POST, 'email', '');

		// Verifico el formato.
		if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $email))
		{
			$model_usuario = new Model_Usuario();
			if ($model_usuario->exists_email($email))
			{
				echo json_encode(array('response' => 'ERROR', 'body' => __('Ya existe una cuenta con ese e-mail.', FALSE)));
			}
			else
			{
				echo json_encode(array('response' => 'OK', 'body' => ''));
			}
		}
		else
		{
			// Informo que es inválido.
			echo json_encode(array('response' => 'ERROR', 'body' => __('El formato no es correcto.', FALSE)));
		}
	}

	/**
	 * Verifico si el nick es válido y además que no esté en uso.
	 */
	public function action_validar_nick()
	{
		// Obtengo el nick.
		$nick = arr_get($_POST, 'nick', '');

		// Verifico el formato.
		if (preg_match('/^[a-zA-Z0-9]{4,16}$/D', $nick))
		{
			// Cargo usuarios bloqueados.
			$nicks_bloqueados = unserialize(Utils::configuracion()->get_default('usuarios_bloqueados', 'a:0:{}'));

			$model_usuario = new Model_Usuario();
			if (in_array($nick, $nicks_bloqueados) || $model_usuario->exists_nick($nick))
			{
				echo json_encode(array('response' => 'ERROR', 'body' => __('Ya existe una cuenta con ese nick.', FALSE)));
			}
			else
			{
				echo json_encode(array('response' => 'OK', 'body' => ''));
			}
		}
		else
		{
			// Informo que es inválido.
			echo json_encode(array('response' => 'ERROR', 'body' => __('El formato no es correcto.', FALSE)));
		}
	}

	/**
	 * Tratamos de activa una cuenta de usuario.
	 * @param string $token Token para utilizar en validación.
	 */
	public function action_activar($token)
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos a la portada.
			add_flash_message(FLASH_ERROR, __('No puedes registrarte si ya has iniciado sesión.', FALSE));
			Request::redirect('/');
		}

		// Verifico formato del token.
		if ( ! preg_match('/^[a-zA-Z0-9]{32}$/D', $token))
		{
			add_flash_message(FLASH_ERROR, __('La clave de activación no es correcta.', FALSE));
			Request::redirect('/');
		}

		// Verifico existencia del token.
		$model_recuperacion = new Model_Usuario_Recuperacion;
		if ( ! $model_recuperacion->es_valido($token, Model_Usuario_Recuperacion::TIPO_ACTIVACION))
		{
			add_flash_message(FLASH_ERROR, __('La clave de activación ha caducado.', FALSE));
			Request::redirect('/');
		}

		// Cargo el token.
		$model_recuperacion->load_by_hash($token);

		// Activo la cuenta del usuario.
		$model_usuario = $model_recuperacion->usuario();

		// Actualizamos el estado.
		if ($model_usuario->estado === Model_Usuario::ESTADO_PENDIENTE)
		{
			$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
		}

		// Borramos el token.
		$model_recuperacion->borrar();

		add_flash_message(FLASH_SUCCESS, __('La cuenta se ha activado correctamente.', FALSE));
		Request::redirect('/usuario/login');
	}

	/**
	 * Pido el envío de una nueva clave de activación.
	 */
	public function action_pedir_activacion()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos a la portada.
			add_flash_message(FLASH_ERROR, __('No puedes registrarte si ya has iniciado sesión.', FALSE));
			Request::redirect('/');
		}

		// Configuraciones del sitio.
		$model_config = Model_Configuracion::get_instance();

		// Verifico el tipo de activación.
		if ( (int) $model_config->get('activacion_usuario', 1) !== 1)
		{
			add_flash_message(FLASH_ERROR, __('No se pueden pedir correos de activación, este método no es correcto.', FALSE));
			Request::redirect('/usuario/login/');
		}

		// Asignamos el título.
		$this->template->assign('title', __('Activar cuenta', FALSE));

		// Cargamos la vista del usuario.
		$view_usuario = View::factory('usuario/activar');

		// Cargo datos.
		$view_usuario->assign('email', '');
		$view_usuario->assign('error_email', '');

		// Verificamos si se han enviado los datos.
		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Verificamos los datos enviados.
			$email = isset($_POST['email']) ? trim($_POST['email']) : '';

			if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $email))
			{
				$view_usuario->assign('error_email', __('La casilla de correo ingresada no es válida.', FALSE));
				$error = TRUE;
			}

			if ( ! $error)
			{
				// Verifico existencia del correo.
				$model_usuario = new Model_Usuario;
				if ( ! $model_usuario->exists_email($email))
				{
					$view_usuario->assign('error_email', __('La casilla de correo ingresada no se ha encontrado en nuestra base de datos.', FALSE));
					$error = TRUE;
				}
				else
				{
					$error = FALSE;
				}
			}

			if ( ! $error)
			{
				// Verifico estado.
				$model_usuario->load_by_email($email);
				if ($model_usuario->estado !== Model_Usuario::ESTADO_PENDIENTE)
				{
					$view_usuario->assign('error_email', __('La casilla de correo ingresada no se ha encontrado en nuestra base de datos.', FALSE));
					$error = TRUE;
				}
				else
				{
					$error = FALSE;
				}
			}

			if ( ! $error)
			{
				// Elimino posibles tokens del usuario.
				$model_recuperacion = new Model_Usuario_Recuperacion;
				$model_recuperacion->borrar_por_usuario($model_usuario->id);

				// Genero un nuevo token.
				$token = $model_recuperacion->crear($model_usuario->id, $email, Model_Usuario_Recuperacion::TIPO_ACTIVACION);

				// Configuraciones del sitio.
				$model_config = Model_Configuracion::get_instance();

				// Creo el mensaje de correo.
				$message = Email::get_message();
				$message->setSubject(sprintf(__('Activación cuenta de %s', FALSE), $model_config->get('nombre', 'Marifa')));
				$message->setTo($model_usuario->email, $model_usuario->nick);

				// Cargo la vista.
				$message_view = View::factory('emails/register');
				$message_view->assign('codigo', $token);
				$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
				$message->setBody($message_view->parse());
				unset($message_view);

				// Envío el email.
				$mailer = Email::get_mailer();
				$mailer->send($message);

				// Registro completo.
				add_flash_message(FLASH_SUCCESS, __('Se ha enviado un correo a tu cuenta de con los pasos de la activación de la cuenta. Recuerda que el enlace caduca en 24hs.', FALSE));
				Request::redirect('/login');
			}
		}

		// Agregamos el la vista a la plantilla.
		$this->template->assign('contenido', $view_usuario->parse());
	}

	/**
	 * Enviamos un correo para recuperar la contraseña.
	 */
	public function action_recuperar()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos a la portada.
			Request::redirect('/');
		}

		// Asignamos el título.
		$this->template->assign('title', __('Recuperar clave'), FALSE);

		// Cargamos la vista del usuario.
		$view_usuario = View::factory('usuario/recuperar');

		// Cargo datos.
		$view_usuario->assign('email', '');
		$view_usuario->assign('error_email', '');

		// Verificamos si se han enviado los datos.
		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Verificamos los datos enviados.
			$email = isset($_POST['email']) ? trim($_POST['email']) : '';

			if ( ! $error)
			{
				// Verifico existencia del correo.
				$model_usuario = new Model_Usuario;
				if ( ! $model_usuario->exists_email($email))
				{
					// Verifico existencia de nick.
					if ( ! $model_usuario->exists_nick($email))
					{
						$view_usuario->assign('error_email', __('El nick o correo ingresado no existe.', FALSE));
						$error = TRUE;
					}
					else
					{
						$model_usuario->load_by_nick($email);
						$error = FALSE;
					}
				}
				else
				{
					$model_usuario->load_by_email($email);
					$error = FALSE;
				}
			}

			if ( ! $error)
			{
				// Elimino posibles tokens del usuario.
				$model_recuperacion = new Model_Usuario_Recuperacion;
				$model_recuperacion->borrar_por_usuario($model_usuario->id);

				// Genero un nuevo token.
				$token = $model_recuperacion->crear($model_usuario->id, $model_usuario->email, Model_Usuario_Recuperacion::TIPO_RECUPERACION);

				// Configuraciones del sitio.
				$model_config = Model_Configuracion::get_instance();

				// Creo el mensaje de correo.
				$message = Email::get_message();
				$message->setSubject(sprintf(__('Restaurar contraseña de %s', FALSE), $model_config->get('nombre', 'Marifa')));
				$message->setTo($model_usuario->email, $model_usuario->nick);

				// Cargo la vista.
				$message_view = View::factory('emails/recuperar');
				$message_view->assign('codigo', $token);
				$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
				$message->setBody($message_view->parse());
				unset($message_view);

				// Envio el email.
				$mailer = Email::get_mailer();
				$mailer->send($message);

				// Registro completo.
				add_flash_message(FLASH_SUCCESS, __('Se ha enviado un correo a tu cuenta de con los pasos para restaurar tu clave de acceso. Recuerda que el enlace caduca en 24hs.'), FALSE);
				Request::redirect('/login');
			}
		}

		// Agregamos el la vista a la plantilla.
		$this->template->assign('contenido', $view_usuario->parse());
	}

	/**
	 * Tratamos de activa una cuenta de usuario.
	 * @param string $token Token para utilizar en validación.
	 */
	public function action_restaurar($token)
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos a la portada.
			Request::redirect('/');
		}

		// Verifico formato del token.
		if ( ! preg_match('/^[a-zA-Z0-9]{32}$/D', $token))
		{
			add_flash_message(FLASH_ERROR, __('La clave de restauración no es correcta.'), FALSE);
			Request::redirect('/usuario/recuperar/');
		}

		// Verifico existencia del token.
		$model_recuperacion = new Model_Usuario_Recuperacion;
		if ( ! $model_recuperacion->es_valido($token, Model_Usuario_Recuperacion::TIPO_RECUPERACION))
		{
			add_flash_message(FLASH_ERROR, __('La clave de restauración ha caducado.'), FALSE);
			Request::redirect('/usuario/recuperar/');
		}

		// Cargo el token.
		$model_recuperacion->load_by_hash($token);

		// Cargo la vista.
		$view = View::factory('/usuario/restaurar/');

		// Seteo valores por defecto.
		$view->assign('error_password', FALSE);
		$view->assign('error_cpassword', FALSE);

		if (Request::method() === 'POST')
		{
			// Obtengo lo datos.
			$password = isset($_POST['password']) ? trim($_POST['password']) : '';
			$cpassword = isset($_POST['cpassword']) ? trim($_POST['cpassword']) : '';

			$error = FALSE;

			// Verificamos contraseña.
			if ( ! preg_match('/^[a-zA-Z0-9\-_@\*\+\/#$%]{6,20}$/D', $password))
			{
				$view->assign('error_password', TRUE);
				$error = TRUE;
			}
			else
			{
				// Verificamos que concuerden.
				if ($password != $cpassword)
				{
					$view->assign('error_cpassword', TRUE);
					$error = TRUE;
				}
			}

			if ( ! $error)
			{
				// Cargo el usuario.
				$model_usuario = $model_recuperacion->usuario();

				// Actualizo la contraseña.
				$model_usuario->actualizar_contrasena($password);

				// Borro el token.
				$model_recuperacion->borrar();

				// Notifico y envío al inicio de sesión.
				add_flash_message(FLASH_SUCCESS, __('La contraseña se ha restaurado correctamente.'), FALSE);
				Request::redirect('/usuario/login/');
			}
		}

		// Agregamos el la vista a la plantilla.
		$this->template->assign('contenido', $view->parse());

		// Título.
		$this->template->assign('title', __('Restaurar contraseña'), FALSE);
	}

	/**
	 * Cerramos la sesión del usuario.
	 */
	public function action_logout()
	{
		Usuario::logout();
		add_flash_message(FLASH_SUCCESS, __('Gracias por su visita.'), FALSE);
		Request::redirect('/');
	}
}
