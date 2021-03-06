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
	 * Agregamos una llamada al stack.
	 * @param string $method Método llamado.
	 * @param string $controller Controllador llamado.
	 * @param string $action Acción llamada.
	 * @param array $params Parametros de la llamada.
	 * @param string $plugin Plugin si corresponde.
	 */
	public static function add_stack($method, $controller, $action, $params, $plugin)
	{
		if ( ! is_array(self::$request))
		{
			self::$request = array();
		}

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
	 * @param bool $object Si hay que devolverlo como un objeto en lugar de como arreglo.
	 * @return string|NULL Petición o NULL si no hay disponible.
	 */
	public static function current($object = FALSE)
	{
		// Obtenemos la petición actual.
		if (is_array(self::$request) && count(self::$request) > 0)
		{
			// Obtenemos la actual.
			if ($object)
			{
				return (object) self::$request[count(self::$request) - 1];
			}
			else
			{
				return self::$request[count(self::$request) - 1];
			}
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Convertimos un arreglo de una petición a una URL válida.
	 * @param array $peticion Petición.
	 * @return string URL
	 */
	public static function peticion_to_url($peticion)
	{
		if ($peticion['plugin'] !== NULL)
		{
			// Quito prefijo y convierto a URL.
			$controller = strtolower(str_replace('_', '/', str_replace('Controller_', '', $peticion['controller'])));

			return '/'.$controller.'/'.$peticion['action'].'/'.implode('/', $peticion['args']);
		}
		else
		{
			// Quito prefijo y convierto a URL.
			$controller = strtolower(str_replace('_', '/', substr($peticion['controller'], 11)));

			// Devolvemos la URL completa.
			return '/'.$controller.'/'.$peticion['action'].'/'.implode('/', $peticion['args']);
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
		return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
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
	 * @param bool $save_current Si guardamos la URL para que el usuario pueda regresar.
	 * @param bool $go_saved Si tratamos de ir a una ruta guardada.
	 */
	public static function redirect($url, $save_current = FALSE, $go_saved = FALSE)
	{
		// Verifico ruta guardada.
		if ($go_saved && Cookie::cookie_exists('r_u'))
		{
			$url = $_COOKIE['r_u'];
			Cookie::delete_cookie('r_u');
		}

		// Si tenemos los segmentos generamos la URL.
		if (is_array($url))
		{
			$url = '/'.implode('/', $url);
		}

		if ($url{0} == '/')
		{
			$url = substr($url, 1);
		}

		if(substr($url, 0, strlen(SITE_URL)) != SITE_URL)
		{
			$url = SITE_URL.'/'.$url;
		}

		// Verifico si tengo que guardar la URL.
		if ($save_current)
		{
			Cookie::set_classic_cookie('r_u', Request::peticion_to_url(Request::current()), 0, '/');
		}

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

	/**
	 * Obtiene el URL de la petición actual.
	 * @return string Url actual
	 */
	public static function current_url()
	{
		if ( ! isset($_SERVER['REQUEST_URI']) || ! isset($_SERVER['SCRIPT_NAME']))
		{
			return '';
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
		{
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
		{
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}

		// This section ensures that even on servers that require the URI to be in the
		// query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strncmp($uri, '?/', 2) === 0)
		{
			$uri = substr($uri, 2);
		}
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1]))
		{
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}
		else
		{
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri == '/' || empty($uri))
		{
			return '/';
		}

		$uri = parse_url($uri, PHP_URL_PATH);

		// Do some final cleaning of the URI and return it
		$uri = str_replace(array(
				'//',
				'../'
		), '/', trim($uri, '/'));

		return $uri;
	}

	/**
	 * Obtengo o asigno el código HTTP de la respuesta.
	 * @param  int $code Código de respuesta a asignar. NULL para obtener el actual.
	 * @return int
	 */
	public static function http_response_code($code = NULL)
	{
		// Verifico existencia de la función del sistema.
		if (function_exists('http_response_code'))
		{
			return http_response_code($code);
		}

		if ($code !== NULL)
		{
			// Obtengo texto de código.
			switch ($code)
			{
				case 100:
					$text = 'Continue';
					break;
				case 101:
					$text = 'Switching Protocols';
					break;
				case 200:
					$text = 'OK';
					break;
				case 201:
					$text = 'Created';
					break;
				case 202:
					$text = 'Accepted';
					break;
				case 203:
					$text = 'Non-Authoritative Information';
					break;
				case 204:
					$text = 'No Content';
					break;
				case 205:
					$text = 'Reset Content';
					break;
				case 206:
					$text = 'Partial Content';
					break;
				case 300:
					$text = 'Multiple Choices';
					break;
				case 301:
					$text = 'Moved Permanently';
					break;
				case 302:
					$text = 'Moved Temporarily';
					break;
				case 303:
					$text = 'See Other';
					break;
				case 304:
					$text = 'Not Modified';
					break;
				case 305:
					$text = 'Use Proxy';
					break;
				case 400:
					$text = 'Bad Request';
					break;
				case 401:
					$text = 'Unauthorized';
					break;
				case 402:
					$text = 'Payment Required';
					break;
				case 403:
					$text = 'Forbidden';
					break;
				case 404:
					$text = 'Not Found';
					break;
				case 405:
					$text = 'Method Not Allowed';
					break;
				case 406:
					$text = 'Not Acceptable';
					break;
				case 407:
					$text = 'Proxy Authentication Required';
					break;
				case 408:
					$text = 'Request Time-out';
					break;
				case 409:
					$text = 'Conflict';
					break;
				case 410:
					$text = 'Gone';
					break;
				case 411:
					$text = 'Length Required';
					break;
				case 412:
					$text = 'Precondition Failed';
					break;
				case 413:
					$text = 'Request Entity Too Large';
					break;
				case 414:
					$text = 'Request-URI Too Large';
					break;
				case 415:
					$text = 'Unsupported Media Type';
					break;
				case 500:
					$text = 'Internal Server Error';
					break;
				case 501:
					$text = 'Not Implemented';
					break;
				case 502:
					$text = 'Bad Gateway';
					break;
				case 503:
					$text = 'Service Unavailable';
					break;
				case 504:
					$text = 'Gateway Time-out';
					break;
				case 505:
					$text = 'HTTP Version not supported';
					break;
				default:
					throw new Exception('Unknown http status code "'.htmlentities($code).'"');
					break;
			}

			// Obtengo protocolo.
			$protocol = arr_get($_SERVER, 'SERVER_PROTOCOL', 'HTTP/1.0');

			// Envío la cabecera.
			header($protocol . ' ' . $code . ' ' . $text);

			// Asigno variable global.
			$GLOBALS['http_response_code'] = $code;
		}
		else
		{
			$code = arr_get($GLOBALS, 'http_response_code', 200);
		}

		return $code;
	}

}
