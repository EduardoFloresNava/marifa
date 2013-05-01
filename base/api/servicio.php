<?php
/**
 * servicio.php is part of Marifa.
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
 * Clase para realizar peticiones sobre el estado del servicio.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Api_Servicio extends Api_Request {

	/**
	 * Obtengo el estado del servicio.
	 * @return array|bool FALSE si se produjo un error o un arreglo($code, $message) si es correcto.
	 */
	public static function check()
	{
		// Realizo petición.
		list ($response, $code) = self::do_get_request(Api::get_server().'/servicio/status');

		// Verifico respuesta.
		if ($code !== 200)
		{
			return FALSE;
		}
		else
		{
			return @json_decode($response, TRUE);
		}
	}
}
