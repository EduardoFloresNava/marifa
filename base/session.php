<?php
/**
 * session.php is part of Marifa.
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
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Clase para el manejo de las sessiones.
 *
 * @author     Cody Roodaka <roodakazo@hotmail.com>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Session {

	/**
	 * Clave para encriptar la sesión
	 * @var string
	 */
	private static $key = '';

	/**
	 * Seteamos la clave e iniciamos la sesión
	 * @param string $key Clave de la sesión
	 */
	public static function start($key)
	{
		if ( ! isset($_SESSION))
		{
			session_start();
		}
		self::$key = md5($key);
	}

	/**
	 * Chequeamos si está seteada la variable de sesión.
	 * @param string $var Variable de la sesión
	 */
	public static function is_set($var)
	{
		return isset($_SESSION[md5($var.self::$key)]);
	}

	/**
	 * Seteamos una nueva variable de sesión
	 * @param string $var Variable de la sesión
	 * @param mixed $value Valor de la variable
	 */
	public static function set($var, $value)
	{
		$_SESSION[md5($var.self::$key)] = base64_encode($value);
	}

	/**
	 * Obtenemos el valor de una variable de sesión
	 * @param string $var Variable de la sesión
	 */
	public static function get($var)
	{
		return self::is_set($var) ? base64_decode($_SESSION[md5($var.self::$key)]) : NULL;
	}

	/**
	 * Destruimos una variable de sesión
	 * @param string $var Variable de la sesión
	 */
	public static function un_set($var = NULL)
	{
		if ($var === NULL)
		{
			unset($_SESSION);
			session_destroy();
		}
		else
		{
			unset($_SESSION[md5($var.self::$key)]);
		}
	}

	/**
	 * Obtenemos el campo y lo borramos. Si no existe devolvemos $default.
	 * @param string $var Elemento buscado.
	 * @param mixed $default Valor por defecto, devuelto si no existe el elemento.
	 * @return mixed
	 */
	public static function get_flash($var, $default = NULL)
	{
		if (self::is_set($var))
		{
			$v = self::get($var);
			self::un_set($var);
			return $v;
		}
		else
		{
			return $default;
		}
	}
}
