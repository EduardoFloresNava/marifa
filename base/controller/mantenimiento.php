<?php
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
 * @package		Marifa\Base
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador para modo mantenimiento por usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Mantenimiento extends Controller {

	/**
	 * Sobrescribo before para evitar carga de elementos inútil.
	 */
	public function before() {}

	/**
	 * Portada del modo mantenimiento por Usuario.
	 */
	public function action_index()
	{
		// Verifico si esta activo.
		if ( ! Mantenimiento::is_locked(FALSE))
		{
			// Lo mando a la portada.
			Request::redirect('/');
		}

		// Verifico acceso.
		if (Usuario::is_login() && ! Mantenimiento::is_locked_for(Usuario::$usuario_id, FALSE))
		{
			// Lo mando a la portada.
			Request::redirect('/');
		}

		// Obtengo vista a mostrar.
		if (Usuario::is_login())
		{
			// Muestro informativo.
			$vista = View::factory('mantenimiento');
		}
		else
		{
			// Mostramos vista para login.
			$vista = View::factory('mantenimiento_login');

			// Informacion general.
			$vista->assign('error', FALSE);
			$vista->assign('nick', '');
			$vista->assign('error_nick', FALSE);
			$vista->assign('error_password', FALSE);


			if (Request::method() == 'POST')
			{
				// Obtenemos los datos.
				$nick = arr_get($_POST, 'nick', '');
				$password = arr_get($_POST, 'password', '');

				// Actualizo valores de la vista.
				$vista->assign('nick', $nick);

				// Realizamos el login.
				$model_usuario = new Model_Usuario;
				$rst = $model_usuario->login($nick, $password);

				switch ($rst)
				{
					case -1: // Datos inválidos.
						$vista->assign('error', 'Los datos introducidos son inválidos.');
						$vista->assign('error_nick', TRUE);
						$vista->assign('error_password', TRUE);
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
							add_flash_message(FLASH_INFO, 'Tienes advertencias nuevas. Puedes verlas desde <a href="'.SITE_URL.'/cuenta/avisos/">aquí</a>.');
						}

						// Envío mensaje de bienvenida.
						add_flash_message(FLASH_SUCCESS, 'Bienvenido.');

						// Lo envío a la portada.
						Request::redirect('/', FALSE, TRUE);
						break;
					case Model_Usuario::ESTADO_PENDIENTE:  // Cuenta por activar.
						$vista->assign('error', 'La cuenta no ha sido validada aún. Si no recibiste el correo de activación haz click <a href="'.SITE_URL.'/usuario/pedir_activacion/">aquí</a>');
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

						$vista->assign('error', sprintf(__('%s te ha suspendido por %s debido a:<br /> %s', FALSE), $moderador['nick'], $restante, $motivo));
						break;
					case Model_Usuario::ESTADO_BANEADA:    // Cuenta baneada.
						$baneo = $model_usuario->baneo();
						$vista->assign('error', sprintf(__('%s te ha baneado el %s debido a: <br /> %s', FALSE), $baneo->moderador()->nick, $baneo->fecha->format('d/m/Y H:i:s'), Decoda::procesar($baneo->razon)));
				}
			}
		}
		// Cargo nombre del sitio.
		$vista->assign('brand', Utils::configuracion()->get('nombre', 'Marifa'));
		$vista->assign('brand_title', Utils::configuracion()->get('nombre', 'Marifa'));

		// Eventos flash.
		foreach (array('flash_success', 'flash_info', 'flash_error') as $k)
		{
			if (isset($_SESSION[$k]))
			{
				$vista->assign($k, get_flash($k));
			}
		}

		// Muestro la vista.
		$vista->show();
		exit;
	}

	/**
	 * Cerramos la sesión del usuario.
	 */
	public function action_salir()
	{
		Usuario::logout();
		add_flash_message(FLASH_SUCCESS, 'Has cerrado sesión correctamente.');
		Request::redirect('/mantenimiento/');
	}
}
