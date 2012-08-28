<?php defined('APP_BASE') or die('No direct access allowed.');
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
 * @package		Marifa/Base
 * @subpackage  Controller
 */

/**
 * Controlador para el manejo de usuarios.
 * Permite el inicio de sessión, registro, validar cuentas y recuperar contraseña.
 *
 * @since      Versión 0.1
 * @package    Marifa/Base
 * @subpackage Controller
 */
class Base_Controller_Usuario extends Controller {

	public function __construct()
	{
		parent::__construct();

		if ( ! Session::is_set('usuario_id'))
		{
			// Seteamos menu offline.
			$this->template->assign('master_bar', parent::base_menu_logout());
		}
	}

	/**
	 * Inicio de sessión de un usuario.
	 */
	public function action_login()
	{
		// Verificamos si el usuario está conectado.
		if (Session::is_set('usuario_id'))
		{
			// Lo enviamos al perfil.
			Request::redirect('/');
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
				$model_usuario = new Model_Usuario();

				$rst = $model_usuario->login($nick, $password);

				switch ($rst)
				{
					case -1: // Datos inválidos.
						$view_usuario->assign('error', 'Los datos introducidos son inv&aacute;lidos.');
						$view_usuario->assign('error_nick', TRUE);
						$view_usuario->assign('error_password', TRUE);
						break;
					case Model_Usuario::ESTADO_ACTIVA: // Cuenta activa.
						Request::redirect('/');
						break;
					case Model_Usuario::ESTADO_PENDIENTE:  // Cuenta por activar.
						$view_usuario->assign('error', 'La cuenta no ha sido validada a&uacute;n.');
						break;
					case Model_Usuario::ESTADO_SUSPENDIDA: // Cuenta suspendida.
						//TODO: Obtener el motivo.
					case Model_Usuario::ESTADO_BANEADA:    // Cuenta baneada.
						//TODO: Obtener el motivo.
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
		if (Session::is_set('usuario_id'))
		{
			// Lo enviamos a la portada.
			Request::redirect('/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Registrarse');

		// Cargamos la vista del usuario.
		$view_usuario = View::factory('usuario/register');

		// Pasamos toda la información a la vista.
		foreach(array('nick', 'email', 'password', 'c_password') as $field)
		{
			$view_usuario->assign($field, in_array($field, array('nick', 'email')) ? (isset($_POST[$field]) ? $_POST[$field] : ''): '');
			$view_usuario->assign('error_'.$field, FALSE);
		}

		// Verificamos si se han enviado los datos.
		if (Request::method() == 'POST')
		{
			// Verificamos los datos enviados.
			$error = FALSE;
			foreach(array('nick', 'email', 'password', 'c_password') as $field)
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
				foreach(array('nick', 'email') as $field)
				{
					$view_usuario->assign($field, $_POST[$field]);
				}

				// Realizamos verificaciones.
				$error = FALSE;

				// Verificamos el nick.
				if ( ! preg_match('/^[a-zA-Z0-9áéíóúAÉÍÓÚÑñ ]{4,16}$/', $_POST['nick']))
				{
					$view_usuario->assign('error_nombre', TRUE);
					$error = TRUE;
				}

				// Verificamos e-mail.
				if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST['email']))
				{
					$view_usuario->assign('error_email', TRUE);
					$error = TRUE;
				}

				// Verificamos contraseña.
				if ( ! preg_match('/^[a-zA-Z0-9\-_@\*\+\/#$%]{6,20}$/', $_POST['password']))
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

				if ($error)
				{
					$view_usuario->assign('error', 'Los datos introducidos no son v&aacute;lidos');
				}
				else
				{
					// Cargamos modelo del usuario.
					$model_usuario = new Model_Usuario();

					// Formateamos las entradas.
					$nick = trim(preg_replace('/\s+/', ' ', $_POST['nick']));
					$email = trim($_POST['email']);
					$password = trim($_POST['password']);

					// Realizamos el registro.
					try {
						$id = $model_usuario->register($nick, $email, $password);
					}
					catch (Exception $e)
					{
						$view_usuario->assign('error', $e->getMessage());
						$this->template->assign('contenido', $view_usuario->parse());
						return;
					}

					if ($id)
					{
						// Registro completo.
						$view_usuario = View::factory('usuario/register_complete');

						/**
						// Cargamos el usuario.
						$model_usuario->load($email);

						// Creamos token de activación.
						$code = $model_usuario->crear_codigo_activacion();

						// Creamos información para el trabajo.
						$data = array(
							'template' => 'usuario/email/activate',
							'template_data' => array(
								'code' => $code,
								'usuario' => $model_usuario->as_array(),
							),
							'email' => $email,
							'nombre' => $nombre.' '.$apellido,
							'uid' => (int) $model_usuario->as_object()->id,
							'asunto' => 'Activación cuenta',
						);

						// Agregamos el trabajo.
						Api_Job::sCreate(Api_Job::TIPO_EMAIL, $data, LittleDB::getInstance());*/
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
	 * Cerramos la sessión del usuario.
	 */
	public function action_logout()
	{
		if (Session::is_set('usuario_id'))
		{
			Session::un_set('usuario_id');
			Session::un_set();
		}
		Request::redirect('/');
	}
}
