<?php
/**
 * actualizacion.php is part of Marifa.
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
 * Clase para manejo de actualizaciones de MarifaNetwork V1.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Api_Actualizacion extends Api_Request {

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
	 * Obtengo el listado de actualizaciones de una versión.
	 * @param string $version Versión actual.
	 * @return array Listado de versiones nuevas.
	 * @throws Api_Exception
	 */
	public function versiones($version)
	{
		// Realizo la petición.
		list($response, $code) = self::do_get_request($this->server.'/actualizacion/versiones', array('token' => $this->token, 'version' => $version));

		// Verifico código de respuesta.
		if ($code == 400 || $code == 403)
		{
			throw new Api_Exception('Token inválido', 331);
		}
		elseif ($code == 200)
		{
			return json_decode($response);
		}
		else
		{
			throw new Api_Exception('Error al obtener actualizaciones: ['.$code.'] '.$response, 333);
		}
	}

	/**
	 * Obtenemos los detalles de una actualización.
	 * @param string $version Versión actual.
	 * @param string $v Versión a la que actualizar.
	 * @return stdClass Datos de la actualización.
	 * @throws Api_Exception
	 */
	public function detalles($version, $v)
	{
		// Realizo la petición.
		list($response, $code) = self::do_get_request($this->server.'/actualizacion/detalles', array('token' => $this->token, 'version' => $version, 'posterior' => $v));

		// Verifico respuesta.
		$rst = @json_decode($response);
		if ( ! is_array($rst))
		{
			if ($response == 'Invalid token' || $response == 'Forbidden')
			{
				throw new Api_Exception('Token inválido');
			}
			else
			{
				throw new Api_Exception('Error al obtener actualizaciones: '.$response);
			}
		}
		else
		{
			return $rst;
		}

		// Verifico código de respuesta.
		if ($code == 400 || $code == 403)
		{
			throw new Api_Exception('Token inválido', 331);
		}
		elseif ($code == 200)
		{
			return json_decode($response);
		}
		else
		{
			throw new Api_Exception('Error al obtener actualizaciones: ['.$code.'] '.$response, 333);
		}
	}

	/**
	 * Descargo una actualización.
	 * @param string $de Versión de la que actualizar.
	 * @param string $a Versión a la que actualizar.
	 * @param string $compresion Tipo de compresión.
	 * @param string $target Donde colocar la descarga.
	 * @return stdClass
	 */
	public function download($de, $a, $compresion, $target)
	{
		return self::download_file($this->server.'/actualizacion/download', $target, 'GET', array('token' => $this->token, 'de' => $de, 'a' => $a, 'compresion' => $compresion));
	}

}