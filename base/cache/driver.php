<?php
/**
 * driver.php is part of Marifa.
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
 * @subpackage  Cache
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Interface base para la cache.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Cache
 */
interface Base_Cache_Driver {

	/**
	 * Obtenemos un elemento de la cache.
	 * @param string $id Clave del elemento abtener.
	 * @return mixed Información si fue correcto o FALSE en caso de error.
	 */
	public function get($id);

	/**
	 * Guardamos un elemento en la cache.
	 * @param string $id Clave del elemento.
	 * @param mixed $data Información a guardar.
	 * @param int $ttl Tiempo en segundo a mantener la información.
	 * @return bool Resultado de la operación.
	 */
	public function save($id, $data, $ttl = 60);

	/**
	 * Borramos un elemento de la cache.
	 * @param string $id Clave del elemento.
	 * @return bool Resultado de la operación.
	 */
	public function delete($id);

	/**
	 * Limpiamos la cache.
	 * @return bool Resultado de la operación.
	 */
	public function clean();

	/**
	 * Verificamos si el driver es soportado por el sistema.
	 * @return bool
	 */
	public static function is_supported();
}
