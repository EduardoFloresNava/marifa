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
	 * Obtenemos si hay un usuario logueado o no.
	 * @return bool
	 */
	public static function is_login()
	{
		if ( ! isset(self::$is_login))
		{
			if (Session::is_set('usuario_id'))
			{
				$model_session = new Model_Session(Session::$id);
				self::$is_login = $model_session->existe();
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
			self::$usuario = self::is_login() ? new Model_Usuario( (int) Session::is_set('usuario_id')) : FALSE;
		}
		return self::$usuario;
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
		$model_session->crear(Session::$id, $usuario->id, $ip, date('Y/m/d H:i:s'));
		Session::set('usuario_id', $usuario->id);
		self::$usuario = $usuario;
		self::$is_login = TRUE;
	}

	/**
	 * Terminamos la sessión del usuario actual.
	 */
	public static function logout()
	{
		if (Session::is_set('usuario_id'))
		{
			$model_session = new Model_Session(Session::$id);
			$model_session->borrar();
			Session::un_set('usuario_id');
		}
	}

	/**
	 * Inicio el manejo de usuarios.
	 * Esto permite sincronizar la session según lo que dice la base de datos.
	 */
	public static function start()
	{
		if ( ! self::is_login() && Session::is_set('usuario_id'))
		{
			Session::un_set('usuario_id');
		}
	}
}
