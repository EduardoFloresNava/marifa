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
 * @subpackage  Database
 */
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Driver base para la base de datos.
 *
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Database
 */
abstract class Base_Database_Driver {

	/**
	 * Constructor de la clase.
	 *
	 * Acá se debe presentar toda la lógica de conección a la base de datos
	 * y dejar preparado en entorno para realizar consultas.
	 * @param array $data Arreglo con los datos de conección a la base de datos.
	 */

	abstract public function __construct($data);

	/**
	 * Función para realizar consultas.
	 *
	 * Estás son las consultas que devuelven un objeto con
	 * datos de la base de datos, como puede ser un SELECT.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return Database_Query Objeto resultado de la consulta.
	 */
	abstract public function query($query, $params = array());

	/**
	 * Realiza una inserción en la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, un arreglo con
	 * el id de la inserción y el número de filas afectadas si fue correcto.
	 */
	abstract public function insert($query, $params = array());

	/**
	 * Borramos información de la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, el numero de filas
	 * afectadas si fue correcto.
	 */
	abstract public function delete($query, $params = array());

	/**
	 * Realiza una actualización en la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, el numero de filas
	 * afectadas si fue correcto.
	 */
	abstract public function update($query, $params = array());
}
