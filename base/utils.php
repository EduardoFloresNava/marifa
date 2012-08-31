<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * utils.php is part of Marifa.
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
 * @package		Marifa/Base
 */

/**
 * Funciones de utileria varia.
 *
 * @since      Versión 0.1
 * @package    Marifa/Base
 */
class Base_Utils {

	/**
	 * Obtenemos la propieadad $propiedad del objeto $objeto y su no está seteada
	 * devolvemos $defecto.
	 * @param object $objeto Objeto donde sacar la propiedad
	 * @param string $propiedad Propiedad a objener.
	 * @param mixed $defecto Valor por defecto
	 * @return mixed
	 */
	public static function prop($objeto, $propiedad, $defecto = NULL)
	{
		return isset($objeto->$propiedad) ? $objeto->$propiedad : $defecto;
	}

}
