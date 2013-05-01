<?php
/**
 * api.php is part of Marifa.
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
 * @license	http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.3
 * @filesource
 * @package	Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Manejo de las peticiones del sitio de actualizaciones.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Api {

	/**
	 * URL del servidor del API.
	 * @var string
	 */
	protected static $server = 'http://network.marifa.org/api/v1/';

	/**
	 * Token de acceso al API.
	 * @var string
	 */
	protected $token;

	/**
	 * Constructor del API.
	 * @param string $token Token de acceso al API.
	 */
	public function __construct($token = NULL)
	{
		if ($token === NULL && isset(Utils::configuracion()->api_token))
		{
			$this->token = Utils::configuracion()->api_token;
		}
		else
		{
			$this->token = $token;
		}
	}

	/**
	 * Obtenemos el token actual.
	 * @return string
	 */
	public function get_token()
	{
		return $this->token;
	}

	/**
	 * Obtenemos la URL del servidor de actualizaciones.
	 * @return string
	 */
	public static function get_server()
	{
		return self::$server;
	}

	/**
	 * Verificamos el estado del servidor haciendo uso de cache.
	 * @return array|bool FALSE si se produjo un error o un arreglo($code, $message) si es correcto.
	 */
	public static function server_status()
	{
		// Cargo desde la cache.
		$status = Cache::get_instance()->get('network.api.servicio.status');

		if ($status === FALSE)
		{
			// Obtengo el estado.
			$status = Api_Servicio::check();

			// Guardo en la cache.
			Cache::get_instance()->save('network.api.servicio.status', $status, 60);
		}
		return $status;
	}

	/**
	 * Obtenemos objeto de noticias.
	 * @return Api_Noticia
	 */
	public function noticia()
	{
		return new Api_Noticia(self::$server, $this->token);
	}

	/**
	 * Registro el sitio y obtengo un token.
	 * @return string
	 */
	public function registar_sitio()
	{
		// Obtener token.
		$token = Api_Cuenta::register();

		// Verifico exista.
		if ($token === FALSE)
		{
			throw new Api_Exception('Error al registrar el sitio.');
		}

		// Lo guardo.
		Utils::configuracion()->api_token = $token;
	}

	/**
	 * Verificamos el estado de un token.
	 */
	public function verificar_token()
	{
		// Verifico existencia.
		if ( ! isset(Utils::configuracion()->api_token))
		{
			throw new InvalidArgumentException('No hay un token para verificar');
		}

		// Obtengo el token.
		$token = Utils::configuracion()->api_token;

		// Cargo desde la cache.
		$data = Cache::get_instance()->get('network.api.cuenta.check');

		if ($data === FALSE)
		{
			// Obtengo el estado.
			$data = Api_Cuenta::check($token);

			// Guardo en la cache.
			Cache::get_instance()->save('network.api.cuenta.check', $data, 60);
		}

		// Verifico el estado.
		if (is_object($data) && isset($data->estado))
		{
			// Verifico si está activo.
			if ($data->estado == 1 || $data->estado == 2)
			{
				//TODO: Verificar dominio.
				return TRUE;
			}
			elseif ($data->estado == 3)
			{
				return array(3, $data->mensaje);
			}
			elseif ($data->estado == 4)
			{
				return array(4, $data->mensaje);
			}
			else
			{
				// El token no existe.
				return FALSE;
			}
		}
		else
		{
			throw new Api_Exception('Error al obtener es estado del token');
		}
	}

	/**
	 * Obtenemos objeto para interactuar con el API de actualizaciones configurado.
	 * @return Api_Actualizacion
	 */
	public function actualizacion()
	{
		return new Api_Actualizacion(self::$server, $this->token);
	}

}