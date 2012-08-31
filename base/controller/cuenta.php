<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * cuenta.php is part of Marifa.
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
 * Controlador para las opciones del perfil del usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa/Base
 * @subpackage Controller
 */
class Base_Controller_Cuenta extends Controller {

	/**
	 * Constructor de la clase.
	 */
	public function __construct()
	{
		// Verificamos permisos.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/usuario/login');
		}

		// Llamamos al constructor padre.
		parent::__construct();
	}

	/**
	 * Listado de pestañas del perfil.
	 * @param int $action Pestaña seleccionada.
	 */
	protected function submenu($activo)
	{
		return array(
			'index' => array('link' => '/cuenta', 'caption' => 'Cuenta', 'active' => $activo == 'index'),
			'perfil' => array('link' => '/cuenta/perfil', 'caption' => 'Perfil', 'active' => $activo == 'perfil'),
			'bloqueados' => array('link' => '/cuenta/bloqueados', 'caption' => 'Bloqueos', 'active' =>  $activo == 'bloqueados'),
			'password' => array('link' => '/cuenta/password', 'caption' => 'Contrase&ntilde;a', 'active' =>  $activo == 'password'),
			'nick' => array('link' => '/cuenta/nick', 'caption' => 'Nicks', 'active' =>  $activo == 'nick'),
		);
	}

	public function action_index()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta');

		// Cargamos la vista.
		$view = View::factory('cuenta/index');

		// Cargamos el usuario.
		$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));

		// Seteamos los datos actuales.
		$view->assign('error', array());
		$view->assign('email', $model_usuario->email);
		$view->assign('estado_email', 0);
		$view->assign('origen', Utils::prop($model_usuario->perfil(), 'origen'));
		$view->assign('estado_origen', 0);


		$view->assign('pais', Utils::prop($model_usuario->perfil(), 'pais'));
		$view->assign('estado_pais', 0);
		$view->assign('estado', Utils::prop($model_usuario->perfil(), 'estado'));
		$view->assign('estado_estado', 0);


		$view->assign('sexo', Utils::prop($model_usuario->perfil(), 'sexo'));
		$view->assign('estado_sexo', 0);
		$view->assign('nacimiento', explode('-', Utils::prop($model_usuario->perfil(), 'nacimiento')));
		$view->assign('estado_nacimiento', 0);

		// Listado de paises.
		$lista_pais = Configuraciones::obtener(CONFIG_PATH.DS.'geonames.'.FILE_EXT);
		$view->assign('paices', $lista_pais);

		if (Request::method() == 'POST')
		{
			$errors = array();

			// Verificamos el e-mail.
			if (isset($_POST['email']) && ! empty($_POST['email']))
			{
				$view->assign('email', trim($_POST['email']));

				// Verificamos el formato de e-mail.
				if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST['email']))
				{
					$errors[] = 'La direcci&oacute;n de email es inv&oacute;lida.';
					$view->assign('estado_email', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['email']) != $model_usuario->email)
					{
						$m = trim($_POST['email']);

						// Verifico no exista un usuario con ese email.
						if ($model_usuario->exists_email($m))
						{
							$errors[] = 'Ya existe un usuario con ese E-Mail.';
							$view->assign('estado_email', -1);
						}
						else
						{
							// Actualizo la casilla de correo.
							//TODO: pedir validación de la misma.
							$model_usuario->cambiar_email($m);

							$view->assign('success', 'Datos actualizados correctamente.');
							$view->assign('estado_email', 1);
						}
						unset($m);
					}
				}
			}

			// Verificamos el sexo.
			if (isset($_POST['sexo']) && ! empty($_POST['sexo']))
			{
				$view->assign('sexo', trim($_POST['sexo']));

				// Verificamos el valor.
				if ($_POST['sexo'] != 'f' && $_POST['sexo'] != 'm')
				{
					$errors[] = 'El sexo seleccionado no es correcto.';
					$view->assign('estado_sexo', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['sexo']) != Utils::prop($model_usuario->perfil(), 'sexo', NULL))
					{
						// Actualizo sexo.
						$model_usuario->perfil()->sexo = trim($_POST['sexo']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_sexo', 1);
					}
				}
			}

			// Verificamos la fecha de nacimiento.
			if ((isset($_POST['dia']) && ! empty($_POST['dia']) ) && ( isset($_POST['mes']) && ! empty($_POST['mes']) ) && ( isset($_POST['ano']) && ! empty($_POST['ano']) ))
			{
				// Obtenemos los parámetros.
				$ano = (int) $_POST['ano'];
				$mes = (int) $_POST['mes'];
				$dia = (int) $_POST['dia'];

				$error = FALSE;

				// Verificamos los rangos.
				if ($dia < 1 || $dia > 31)
				{
					$errors[] = 'El día de nacimiento es incorrecto.';
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ($mes < 1 || $mes > 12)
				{
					$errors[] = 'El mes de nacimiento es incorrecto.';
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ($ano < 1900 || $dia > date('Y'))
				{
					$errors[] = 'El año de nacimiento es incorrecto.';
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ( ! $error)
				{
					// Validamos la fecha.
					if ( ! checkdate($mes, $dia, $ano))
					{
						$errors[] = 'La fecha de nacimiento es incorrecta';
						$view->assign('estado_nacimiento', -1);
					}
					else
					{
						// Creamos la fecha.
						$fecha = $_POST['ano'].'-'.$_POST['mes'].'-'.$_POST['dia'];

						// Verificamos con la actual.
						if (Utils::prop($model_usuario->perfil(), 'nacimiento', NULL) != $fecha)
						{
							$model_usuario->perfil()->nacimiento = $fecha;
							$view->assign('estado_nacimiento', 1);
							$view->assign('success', 'Datos actualizados correctamente.');
						}
					}
				}
			}

			// Verificamos el pais.
			if (isset($_POST['origen']) && ! empty($_POST['origen']))
			{
				// Obtenemos el pais y el estado.
				list($pais, $estado) = explode('.', trim(strtoupper($_POST['origen'])));

				if ( ! isset($lista_pais[$pais]))
				{
					$errors[] = 'El lugar de origen es incorrecto.';
					$view->assign('estado_origen', -1);
				}
				else
				{
					if ( ! isset($lista_pais[$pais][1][$estado]))
					{
						$errors[] = 'El lugar de origen es incorrecto.';
						$view->assign('estado_origen', -1);
					}
					else
					{
						// Verificamos sea distinto al actual.
						if (Utils::prop($model_usuario->perfil(), 'origen', NULL) != $pais.'.'.$estado)
						{
							$model_usuario->perfil()->origen = $pais.'.'.$estado;
							$view->assign('estado_origen', 1);
							$view->assign('success', 'Datos actualizados correctamente.');
						}
					}
				}
			}


			$view->assign('error', $errors);
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('index'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_perfil()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Perfil');

		// Cargamos la vista.
		$view = View::factory('cuenta/perfil');

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('perfil'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_bloqueados()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Bloqueos');

		// Cargamos la vista.
		$view = View::factory('cuenta/bloqueos');

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('bloqueados'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_password()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Contrase&ntilde;a');

		// Cargamos la vista.
		$view = View::factory('cuenta/password');

		// Valores por defecto.
		$view->assign('error', NULL);
		$view->assign('error_actual', NULL);
		$view->assign('error_password', NULL);
		$view->assign('error_c_password', NULL);

		if (Request::method() == 'POST')
		{
			// Verificamos que estén los datos.
			if (
				( ! isset($_POST['current']) || empty($_POST['current'])) ||
				( ! isset($_POST['password']) || empty($_POST['password'])) ||
				( ! isset($_POST['cpassword']) || empty($_POST['cpassword']))
			   )
			{
				if ( ! isset($_POST['current']) || empty($_POST['current']))
				{
					$view->assign('error', 'Debe rellenar todos los datos.');
					$view->assign('error_current', TRUE);
				}

				if ( ! isset($_POST['password']) || empty($_POST['password']))
				{
					$view->assign('error', 'Debe rellenar todos los datos.');
					$view->assign('error_password', TRUE);
				}

				if ( ! isset($_POST['cpassword']) || empty($_POST['cpassword']))
				{
					$view->assign('error', 'Debe rellenar todos los datos.');
					$view->assign('error_cpassword', TRUE);
				}
			}
			else
			{
				// Comprobamos el formato
				if ( ! preg_match('/^[a-zA-Z0-9]{6,20}$/', $_POST['password']) || $_POST['password'] != $_POST['cpassword'])
				{
					if ($_POST['password'] != $_POST['cpassword'])
					{
						$view->assign('error', 'Las contrase&ntilde;as ingresadas no coinciden.');
						$view->assign('error_password', TRUE);
					}
					else
					{
						$view->assign('error', 'La contrase&ntilde;a debe tener entre 6 y 20 caracteres alphanumericos.');
						$view->assign('error_password', TRUE);
					}
				}
				else
				{
					// Cargamos el usuario actual.
					$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));

					// Verificamos la contraseña.
					$enc = new Phpass(8, FALSE);

					if ( ! $enc->CheckPassword($_POST['current'], $model_usuario->password))
					{
						$view->assign('error', 'La contrase&ntilde;a es incorrecta.');
						$view->assign('error_current', TRUE);
					}
					else
					{
						// Actualizo la caontraseña.
						$model_usuario->actualizar_contrasena(trim($_POST['password']));
						$view->assign('success', 'La contrase&ntilde;a se ha actualizado correctamente.');
					}
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('password'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_nick()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Nick');

		// Cargamos la vista.
		$view = View::factory('cuenta/nick');

		// Cargamos el usuario actual.
		$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));

		// Valores por defecto.
		$view->assign('nick_actual', $model_usuario->nick);
		$view->assign('nick', '');
		$view->assign('error_nick', NULL);
		$view->assign('error_password', NULL);

		if (Request::method() == 'POST')
		{
			if ( ( ! isset($_POST['nick']) || empty($_POST['nick']) ) || ( ! isset($_POST['password']) || empty($_POST['password'])))
			{
				// Verificamos los datos
				if ( ! isset($_POST['nick']) || empty($_POST['nick']))
				{
					$view->assign('error_nick', 'Debe ingresar un nuevo nick.');
				}
				else
				{
					$view->assign('nick', $_POST['nick']);
				}

				if ( ! isset($_POST['password']) || empty($_POST['password']))
				{
					$view->assign('error_password', 'Debe ingresar su contrase&ntilde;a para validar el cambio.');
				}
			}
			else
			{
				$nick = $_POST['nick'];
				$password = $_POST['password'];

				$view->assign('nick', $nick);

				// Verifico longitud Nick.
				if ( ! preg_match('/^[a-zA-Z0-9]{4,20}$/', $nick))
				{
					$view->assign('error_nick', 'El nick debe tener entre 4 y 20 caracteres alphanum&eacute;ricos.');
				}
				else
				{
					// Verifico la contraseña.
					$enc = new Phpass(8, FALSE);

					if ( ! $enc->CheckPassword($password, $model_usuario->password))
					{
						$view->assign('error_password', 'La contrase&ntilde;a es incorrecta.');
					}
					else
					{
						// Verifico que no exista el nick.
						if ($model_usuario->exists_nick($nick))
						{
							$view->assign('error_nick', 'El nick no est&aacute; disponible.');
						}
						else
						{
							// Actualizamos.
							$model_usuario->cambiar_nick($nick);

							$view->assign('success', 'El nick se ha actualizado correctamente.');
							$view->assign('nick', '');
							$view->assign('nick_actual', $nick);
						}
					}
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('nick'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

}
