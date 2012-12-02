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
 * Permite el inicio de sessión, registro, validar cuentas y recuperar contraseña.
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

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu());
	}

	/**
	 * Inicio de sessión de un usuario.
	 */
	public function action_login()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos al perfil.
			Request::redirect('/', FALSE, TRUE);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Inicio de Sessi&oacute;n');

		// Cargamos la vista del usuario.
		$view_usuario = View::factory('usuario/login');

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
				$view_usuario->assign('error', 'Debe introducir el E-Mail o Usuario y la contrase&ntilde;a para poder acceder.');
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
						$view_usuario->assign('error', 'Los datos introducidos son inv&aacute;lidos.');
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
							add_flash_message(FLASH_INFO, 'Tienes advertencias nuevas. Puedes verlas desde <a href="/cuenta/avisos/">aquí</a>.');
						}

						// Envio mensaje de bienvenida.
						add_flash_message(FLASH_SUCCESS, 'Bienvenido.');

						// Lo envio a la portada.
						Request::redirect('/', FALSE, TRUE);
						break;
					case Model_Usuario::ESTADO_PENDIENTE:  // Cuenta por activar.
						$view_usuario->assign('error', 'La cuenta no ha sido validada a&uacute;n. Si no recibiste el correo de activación haz click <a href="/usuario/pedir_activacion/">aqui</a>');
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

						$view_usuario->assign('error', sprintf(__('%s te ha suspendido por %s debido a:<br /> %s', FALSE), $moderador['nick'], $restante, $motivo));
						break;
					case Model_Usuario::ESTADO_BANEADA:    // Cuenta baneada.
						$baneo = $model_usuario->baneo();
						$view_usuario->assign('error', sprintf(__('%s te ha baneado el %s debido a: <br /> %s', FALSE), $baneo->moderador()->nick, $baneo->fecha->format('d/m/Y H:i:s'), Decoda::procesar($baneo->razon)));
				}

				$view_usuario->assign('nick', $nick);
			}
		}
		else
		{
			$view_usuario->assign('nick', '');
		}

		// Agregamos el la vista a la plantilla.
		$this->template->assign('contenido', $view_usuario->parse());
	}

	/**
	 * Crearse una nueva cuenta.
	 */
	public function action_register()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos a la portada.
			add_flash_message(FLASH_ERROR, 'No puedes registrarte si ya estás logueado.');
			Request::redirect('/');
		}

		// Configuraciones del sitio.
		$model_config = new Model_Configuracion;

		// Verifico si está abierto el registro.
		if ( ! (bool) $model_config->get('registro', TRUE))
		{
			add_flash_message(FLASH_ERROR, 'El registro se encuentra cerrado, no se pueden crear nuevas cuentas.');
			Request::redirect('/usuario/login/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Registrarse');

		// Cargamos la vista del usuario.
		$view_usuario = View::factory('usuario/register');

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
				$view_usuario->assign('error', 'Debe introducir todos los datos');
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
					$view_usuario->assign('error', 'Los datos introducidos no son v&aacute;lidos');
				}
				else
				{
					// Cargamos modelo del usuario.
					$model_usuario = new Model_Usuario;

					// Formateamos las entradas.
					$nick = trim(preg_replace('/\s+/', ' ', $_POST['nick']));
					$email = trim($_POST['email']);
					$password = trim($_POST['password']);

					// Realizamos el registro.
					try {
						$id = $model_usuario->register($nick, $email, $password, (int) $model_config->get('rango_defecto', 1));
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
						$t_act = (int) $model_config->get('activacion_usuario', 1);

						if ($t_act == 1)
						{
							// Genero el token de activacion.
							$model_activar = new Model_Usuario_Recuperacion;
							$token = $model_activar->crear($id, $email, Model_Usuario_Recuperacion::TIPO_ACTIVACION);

							// Configuraciones del sitio.
							$model_config = new Model_Configuracion;

							// Creo el mensaje de correo.
							$message = Email::get_message();
							$message->setSubject('Activación cuenta de '.$model_config->get('nombre', 'Marifa'));
							$message->setTo($email, $nick);

							// Cargo la vista.
							$message_view = View::factory('emails/register');
							$message_view->assign('codigo', $token);
							$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
							$message->setBody($message_view->parse());
							unset($message_view);

							// Envio el email.
							$mailer = Email::get_mailer();
							$mailer->send($message);
						}
						elseif ($t_act == 2)
						{
							$model_usuario->load_by_nick($nick);
							$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
						}

						// Registro completo.
						$view_usuario = View::factory('usuario/register_complete');
						$view_usuario->assign('tipo', $t_act);
					}
					else
					{
						// Error al registrar.
						$view_usuario->assign('error', 'No se pudo crear la cuenta, por favor reintente.');
					}
				}

			}
		}

		// Agregamos el la vista a la plantilla.
		$this->template->assign('contenido', $view_usuario->parse());
		unset($view_usuario);
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
			add_flash_message(FLASH_ERROR, 'No puedes registrarte si ya estás logueado.');
			Request::redirect('/');
		}

		// Verifico formato del token.
		if ( ! preg_match('/^[a-zA-Z0-9]{32}$/D', $token))
		{
			add_flash_message(FLASH_ERROR, 'La clave de activación no es correcta.');
			Request::redirect('/');
		}

		// Verifico existencia del token.
		$model_recuperacion = new Model_Usuario_Recuperacion;
		if ( ! $model_recuperacion->es_valido($token, Model_Usuario_Recuperacion::TIPO_ACTIVACION))
		{
			add_flash_message(FLASH_ERROR, 'La clave de activación ha caducado.');
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

		add_flash_message(FLASH_SUCCESS, 'La cuenta se ha activado correctamente.');
		Request::redirect('/usuario/login');
	}

	/**
	 * Pido el envio de una nueva clave de activación.
	 */
	public function action_pedir_activacion()
	{
		// Verificamos si el usuario está conectado.
		if (Usuario::is_login())
		{
			// Lo enviamos a la portada.
			add_flash_message(FLASH_ERROR, 'No puedes registrarte si ya estás logueado.');
			Request::redirect('/');
		}

		// Configuraciones del sitio.
		$model_config = new Model_Configuracion;

		// Verifico el tipo de activación.
		if ( (int) $model_config->get('activacion_usuario', 1) !== 1)
		{
			add_flash_message(FLASH_ERROR, 'No se pueden pedir correos de activación, este método no es correcto.');
			Request::redirect('/usuario/login/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Activar cuenta');

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
				$view_usuario->assign('error_email', 'La casilla de correo ingresada no es válida.');
				$error = TRUE;
			}

			if ( ! $error)
			{
				// Verfico existencia del correo.
				$model_usuario = new Model_Usuario;
				if ( ! $model_usuario->exists_email($email))
				{
					$view_usuario->assign('error_email', 'La casilla de correo ingresada no se ha encontrado en nuestra base de datos.');
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
					$view_usuario->assign('error_email', 'La casilla de correo ingresada no se ha encontrado en nuestra base de datos.');
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
				$model_config = new Model_Configuracion;

				// Creo el mensaje de correo.
				$message = Email::get_message();
				$message->setSubject('Activación cuenta de '.$model_config->get('nombre', 'Marifa'));
				$message->setTo($email, $model_usuario->nick);

				// Cargo la vista.
				$message_view = View::factory('emails/register');
				$message_view->assign('codigo', $token);
				$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
				$message->setBody($message_view->parse());
				unset($message_view);

				// Envio el email.
				$mailer = Email::get_mailer();
				$mailer->send($message);

				// Registro completo.
				$view_usuario = View::factory('usuario/pedir_activacion_completo');
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
		$this->template->assign('title', 'Recuperar clave.');

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
				// Verfico existencia del correo.
				$model_usuario = new Model_Usuario;
				if ( ! $model_usuario->exists_email($email))
				{
					// Verifico existencia de nick.
					if ( ! $model_usuario->exists_nick($email))
					{
						$view_usuario->assign('error_email', 'El nick o correo ingresado no existe.');
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
				$model_config = new Model_Configuracion;

				// Creo el mensaje de correo.
				$message = Email::get_message();
				$message->setSubject('Restaurar contraseña de '.$model_config->get('nombre', 'Marifa'));
				$message->setTo($email, $model_usuario->nick);

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
				$view_usuario = View::factory('usuario/recuperar_completo');
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
			add_flash_message(FLASH_ERROR, 'La clave de restauración no es correcta.');
			Request::redirect('/usuario/recuperar/');
		}

		// Verifico existencia del token.
		$model_recuperacion = new Model_Usuario_Recuperacion;
		if ( ! $model_recuperacion->es_valido($token, Model_Usuario_Recuperacion::TIPO_RECUPERACION))
		{
			add_flash_message(FLASH_ERROR, 'La clave de restauración ha caducado.');
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
				$view_usuario->assign('error_password', TRUE);
				$error = TRUE;
			}
			else
			{
				// Verificamos que concuerden.
				if ($password != $cpassword)
				{
					$view_usuario->assign('error_cpassword', TRUE);
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

				// Notifico y envio al login.
				add_flash_message(FLASH_SUCCESS, 'La contraseña se ha restaurado correctamente.');
				Request::redirect('/usuario/login/');
			}
		}

		// Agregamos el la vista a la plantilla.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Cerramos la sessión del usuario.
	 */
	public function action_logout()
	{
		Usuario::logout();
		add_flash_message(FLASH_SUCCESS, 'Gracias por su visita.');
		Request::redirect('/');
	}
}
