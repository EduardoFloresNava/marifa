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
 * @license	http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.3
 * @filesource
 * @package	Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para manejo de las cuentas de MarifaNetwork V1.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Api_Cuenta extends Api_Request {

	/**
	 * Verifico el estado del token.
	 * @param string $token Token a verificar.
	 */
	public static function check($token)
	{
		// Verifico el token.
		if ( ! preg_match('/tk_[0-9a-z]{14}\.[0-9a-z]{8}/', $token))
		{
			throw new InvalidArgumentException('El token es incorrecto.');
		}

		// Realizo la petición.
		list($response, $code) = self::do_get_request(Api::get_server().'/cuenta/check', array('token' => $token));

		// Verifico código de respuesta.
		if ($code == 400)
		{
			throw new Api_Exception('Token inválido', 331);
		}
		elseif ($code == 200)
		{
			return json_decode($response);
		}
		else
		{
			throw new Api_Exception('Error al verificar el token: ['.$code.'] '.$response, 333);
		}
	}

	/**
	 * Registramos el sitio y obtenemos un TOKEN.
	 * @return string Token del registro.
	 */
	public static function register()
	{
		// Realizo la petición.
		list($response, $code) = self::do_post_request(Api::get_server().'/cuenta/register', array('dominio' => SITE_URL));

		// Verifico código de respuesta.
		if ($code == 400)
		{
			throw new Api_Exception('Petición inválida (\''.$response.'\')', 334);
		}
		elseif ($code == 201 && preg_match('/tk_[0-9a-z]{14}\.[0-9a-z]{8}/', $response))
		{
			return $response;
		}
		else
		{
			throw new Api_Exception('Error al registrar el dominio: ['.$code.'] '.$response, 333);
		}
	}

}
