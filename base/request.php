<?php
/**
 * request.php is part of Marifa.
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
 * Clase con metodos genericos sobre las peticiones como si es la inicial,
 * si es ajax, el ip, etc etc.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Request {

	/**
	 * Stack de peticiones.
	 * @var array
	 */
	private static $request = array();

	/**
	 * Agregamos una llamada al stack
	 * @param string $call Url llamada.
	 * @param string $method
	 */
	public static function add_stack($method = NULL, $controller, $action, $params, $plugin)
	{
		// Obtenemos metodo.
		$method = ($method === NULL) ? (self::method()) : $method;

		// Agregamos la petición al stack.
		self::$request[] = array(
			'method' => $method,
			'controller' => $controller,
			'action' => $action,
			'args' => $params,
			'plugin' => $plugin
		);
	}

	/**
	 * Damos por terminada la última petición del stack de llamadas.
	 */
	public static function pop_stack()
	{
		if (is_array(self::$request) && count(self::$request) > 0)
		{
			array_pop(self::$request);
		}
	}

	/**
	 * Obtenemos la petición actual.
	 * @return string|NULL Petición o NULL si no hay disponible.
	 */
	public static function current()
	{
		// Obtenemos la petición actual.
		if (is_array(self::$request) && count(self::$request) > 0)
		{
			// Obtenemos la actual.
			return self::$request[count(self::$request) - 1];
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Verificamos si la petición actual es la inicial o es interna.
	 * @return bool Si es la inicial o no.
	 */
	public static function is_initial()
	{
		if (is_array(self::$request))
		{
			return count(self::$request) < 2;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Verificamos si es una linea de comandos o un navegador.
	 * @return bool
	 */
	public static function is_cli()
	{
		return ! isset($_SERVER['HTTP_USER_AGENT']);
	}

	/**
	 * Obtenemos el método de la petición.
	 * @return string
	 */
	public static function method()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Tests if the current request is an AJAX request by checking the
	 * X-Requested-With HTTP request header that most popular JS frameworks
	 * now set for AJAX calls.
	 *
	 * @return  bool
	 */
	public static function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	/**
	 * Redireccionamos a la ruta provista.
	 * @param string|array $url URL o segmentos de la URL a donde redireccionar.
	 */
	public static function redirect($url)
	{
		// Si tenemos los segmentos generamos la URL.
		if (is_array($url))
		{
			$url = '/'.implode('/', $url);
		}

		//TODO: agregamos directorio base.

		// Redireccionamos.
		header("Location: $url");

		die();
	}

	/**
	 * Obtenemos la URL de donde refiere.
	 * @return string
	 */
	public static function referer()
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
	}

}
