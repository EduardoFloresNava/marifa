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
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo para manejar el usuario actual.
 * Realiza el manejo de la sessión, sus datos y permisos.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Usuario {

	/**
	 * Cache para ver si está logueado o no.
	 * @var bool
	 */
	private static $is_login;

	/**
	 * Modelo del usuario logueado o FALSE si no hay.
	 * @var Model_Usuario|FALSE
	 */
	private static $usuario;

	/**
	 * Id del usuario que se encuentra logueado.
	 * @var int
	 */
	public static $usuario_id;

	/**
	 * Listado de permisos que tiene el usuario asignados.
	 * @var array
	 */
	private static $permisos;

	/**
	 * Cache de usuarios que permite referir el usuario.
	 * @var array
	 */
	private static $usuarios_permitidos;

	/**
	 * Obtenemos si hay un usuario logueado o no.
	 * @return bool
	 */
	public static function is_login()
	{
		// Verifico si ya tengo un valor.
		if ( ! isset(self::$is_login))
		{
			// Verifico cookie + session.
			if ( ! isset($_SESSION['usuario_id']))
			{
				if (Cookie::cookie_exists('usuario_id'))
				{
					$_SESSION['usuario_id'] = Cookie::get_cookie_value($cookiename);
				}
			}

			// Verifico si existe el valor en la session.
			if (isset($_SESSION['usuario_id']))
			{
				// Verifico si está en la base de datos.
				$model_session = new Model_Session(session_id());

				// Verifico el tiempo.
				if ($model_session->expira !== NULL)
				{
					if ($model_session->expira->getTimestamp() < time())
					{
						// Termino la sessión.
						$model_session->borrar();
						self::$is_login = FALSE;
					}
					else
					{
						// Actualizo la session.
						$model_session->actualizar_expira(session_cache_expire() * 60);
						self::$is_login = TRUE;
					}
				}
				else
				{
					self::$is_login = FALSE;
				}
				unset($model_session);
			}
			else
			{
				self::$is_login = FALSE;
			}
		}
		return self::$is_login;
	}

	/**
	 * Modelo del usuario logueado o FALSE si no hay.
	 * @return Model_Usuario|FALSE
	 */
	public static function usuario()
	{
		if ( ! isset(self::$usuario))
		{
			self::$usuario = self::is_login() ? ( new Model_Usuario(self::$usuario_id)) : FALSE;
		}
		return self::$usuario;
	}

	/**
	 * Verificamos si el usuario tiene el permiso asociado.
	 * @param int|array $permiso Permiso o listado de permisos a comprobar. Si se pasa un
	 * listado, con la existencia de 1 es TRUE.
	 * @return bool
	 */
	public static function permiso($permiso)
	{
		if (self::is_login())
		{
			// Verifico la lista de permisos.
			if ( ! isset(self::$permisos))
			{
				// Cargo el modelo de permisos.
				self::$permisos = self::usuario()->rango()->permisos();
			}
			// Verifico si existe.
			if (is_array($permiso))
			{
				foreach ($permiso as $p)
				{
					if (in_array($p, self::$permisos))
					{
						return TRUE;
					}
				}
				return FALSE;
			}
			else
			{
				return in_array($permiso, self::$permisos);
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Verifico si puedo hacer una referencia al usuario (@usaurio) o publicar en su perfil.
	 * Debo seguirlo o el me debe seguir pero no puede estar bloqueandome.
	 * @param int $usuario_id ID del usuario a verificar.
	 * @return bool
	 */
	public static function puedo_referirlo($usuario_id)
	{
		if ( ! is_array(self::$usuarios_permitidos))
		{
			// Cargo la cache.
			self::$usuarios_permitidos = Database::get_instance()->query(
				'SELECT `id` FROM ( ( SELECT `seguidor_id` AS `id` FROM `usuario_seguidor` WHERE `usuario_id` = ?) UNION (SELECT `usuario_id` AS `id` FROM `usuario_seguidor` WHERE `seguidor_id` = ?)) AS ul WHERE `id` NOT IN (SELECT `usuario_id` FROM `usuario_bloqueo` WHERE `bloqueado_id` = ? AND `usuario_id` IN (SELECT `id` FROM ((SELECT `seguidor_id` AS `id` FROM `usuario_seguidor` WHERE `usuario_id` = ?) UNION (SELECT `usuario_id` AS `id` FROM `usuario_seguidor` WHERE `seguidor_id` = ?)) AS ul))',
				array(Usuario::$usuario_id, Usuario::$usuario_id, Usuario::$usuario_id, Usuario::$usuario_id, Usuario::$usuario_id)
			)->get_pairs(Database_Query::FIELD_INT);
		}

		// Verifico si está disponible.
		return in_array($usuario_id, self::$usuarios_permitidos);
	}

	/**
	 * Indicamos que el usuario ha iniciado sessión para realizar los registros
	 * de sessiones pertinentes.
	 * @param Model_Usuario $usuario Modelo del usuario que inicio sessión.
	 * @param int $ip Desde donde ha iniciado sessión.
	 */
	public static function login($usuario, $ip)
	{
		$model_session = new Model_Session;
		$model_session->crear(session_id(), $usuario->id, $ip, date('Y/m/d H:i:s', time() + session_cache_expire() * 60));
		$_SESSION['usuario_id'] = $usuario->id;
		self::$usuario_id = $usuario->id;
		self::$usuario = $usuario;
		self::$is_login = TRUE;
	}

	/**
	 * Terminamos la sessión del usuario actual.
	 */
	public static function logout()
	{
		if (isset($_SESSION['usuario_id']))
		{
			$model_session = new Model_Session(session_id());
			$model_session->borrar();
			unset($_SESSION['usuario_id']);
			session_destroy();
		}
	}

	/**
	 * Inicio el manejo de usuarios.
	 * Esto permite sincronizar la session según lo que dice la base de datos.
	 */
	public static function start()
	{
		// Inicio y verifico las sessiones.
		self::start_session();

		if ( ! self::is_login() && isset($_SESSION['usuario_id']))
		{
			unset($_SESSION['usuario_id']);
		}
		self::$usuario_id = arr_get($_SESSION, 'usuario_id', NULL);
	}

	/**
	 * Inicio la sessión de forma segura.
	 */
	private static function start_session()
	{
		// Fuerzo inicio de la sessión
		if ( ! isset($_SESSION))
		{
			session_start();
		}

		// Verifico sessión iniciada validamente.
		if ( ! isset($_SESSION['initiated']))
		{
			session_regenerate_id();
			$_SESSION['initiated'] = TRUE;
		}

		// Verifico perdida de sessión.
		if (isset($_SESSION['HTTP_USER_AGENT']))
		{
			if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
			{
				// Borro la session.
				if (isset($_SESSION['usuario_id']))
				{
					unset($_SESSION['usuario_id']);
				}
				session_destroy();

				// Mando a la portada.
				Request::redirect('/');
			}
		}
		else
		{
			$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		}
	}
}
