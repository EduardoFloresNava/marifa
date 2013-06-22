<?php
/**
 * noticia.php is part of Marifa.
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
 * Clase para obtener noticias por parte del API.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Api_Noticia extends Api_Request {

	/**
	 * URL del servidor del API.
	 * @var string
	 */
	protected $server;

	/**
	 * Token para acceder al servidor.
	 * @var string
	 */
	protected $token;

	/**
	 * Constructor de la clase.
	 * @param string $server URL del servidor.
	 * @param string $token Token para realizar peticiones.
	 */
	public function __construct($server, $token)
	{
		$this->server = $server;
		$this->token = $token;
	}

	/**
	 * Obtenemos un listado de noticias.
	 * @param int $cantidad Cantidad de notificas a obtener. Debe estar entre 1 y 50.
	 * @param string $version Versión a las que deben pertenecer las noticias. NULL para todas.
	 * @param string $fecha Fecha en un formato para strtotime. NULL para cualquier fecha.
	 */
	public function noticias($cantidad = 10, $version = NULL, $fecha = NULL)
	{
		// Verifico la cantidad.
		if ($cantidad < 1 || $cantidad > 50)
		{
			throw new InvalidArgumentException('La cantidad debe estar entre 1 y 50');
		}

		// Listado de campos.
		$fields = array(
			'token' => $this->token,
			'cantidad' => $cantidad
		);

		// Versión si es necesaria.
		if ($version !== NULL)
		{
			$fields['version'] = $version;
		}

		// Versión si es necesaria.
		if ($fecha !== NULL)
		{
			$fields['fecha'] = $fecha;
		}

		// Realizo la petición.
		list($response, $code) = self::do_get_request($this->server.'/noticias', $fields);

		// Verifico código de respuesta.
		if ($code == 400 || $code == 403)
		{
			throw new Api_Exception('Token inválido', 331);
		}
		elseif ($code == 200)
		{
			return json_decode($response, TRUE);
		}
		else
		{
			throw new Api_Exception('Error al obtener las noticias: ['.$code.'] '.$response, 333);
		}
	}

	/**
	 * Obtenemos una noticia en particular.
	 * @param int $id ID de la noticia a obtener.
	 */
	public function detalle($id)
	{
		// Realizo la petición.
		list($response, $code) = self::do_get_request($this->server.'/noticias/detalles', array('token' => $this->token, 'id' => $id));

		// Verifico código de respuesta.
		if ($code == 400 || $code == 403)
		{
			throw new Api_Exception('Token inválido', 331);
		}
		elseif ($code == 404)
		{
			throw new Api_Exception('Noticia inválida', 332);
		}
		elseif ($code == 200)
		{
			return json_decode($response);
		}
		else
		{
			throw new Api_Exception('Error al obtener la noticia: ['.$code.'] '.$response, 333);
		}
	}

}
