<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * query.php is part of Marifa.
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
 * @author		Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @copyright	Copyright (c) 2012 Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa/Base
 * @subpackage  Database
 */

/**
 * Interface para los objetos devueltos por las consultas de selección.
 *
 * @version    0.1
 * @package    Marifa/Base
 * @subpackage Database
 */
abstract class Base_Database_Query implements Iterator {

	/**
	 * Obtenemos los elementos como un arreglo númerico
	 */
	const FETCH_NUM = 0;

	/**
	 * Obtenemos los elementos como un arreglo asociativo.
	 */
	const FETCH_ASSOC = 1;

	/**
	 * Obtenems los elementos como un objeto.
	 */
	const FETCH_OBJ = 2;

	/**
	 * Tipo de valor devuelto en las iteraciones.
	 * @var int
	 */
	protected $fetch_type = self::FETCH_NUM;

	/**
	 * Asignamos el tipo por defecto usado cuando se devuelven datos.
	 * @param int $type Tipo a usar.
	 * @throws Exception_Database
	 */
	public function set_fetch_type($type)
	{
		// Validamos el tipo.
		if ( ! in_array($type, array(self::FETCH_NUM, self::FETCH_ASSOC, self::FETCH_OBJ)))
		{
			throw new Exception_Database('Invalid fetch type');
		}
		$this->fetch_type = $type;
	}

	/**
	 * Devolvemos la cantidad de filas afectadas por la consulta
	 * @return int
	 */
	abstract public function num_rows();

	/**
	 * Obtenemos un valor simple de una consulta.
	 * @return string
	 */
	public function get_var()
	{
		$dt = $this->get_record(self::FETCH_NUM);

		return is_array($dt) && isset($dt[0]) ? $dt[0] : NULL;
	}

	/**
	 * Obtenemos un arreglo asociativo usando el primer parámetro como clave.
	 * @return array
	 */
	public function get_pairs()
	{
		$dt = $this->get_records(self::FETCH_NUM);
		$rst = array();
		foreach($dt as $data)
		{
			$rst[$data[0]] = $data[1];
		}
		return $rst;
	}

	/**
	 * Obtenemos un elemento del resultado.
	 * @param $type Tipo de retorno de los valores.
	 * @return mixed
	 */
	abstract public function get_record($type = self::FETCH_ASSOC);

	/**
	 * Obtenemos un arreglo de elementos.
	 * @param $type Tipo de retorno de los valores.
	 * @return array
	 */
	abstract public function get_records($type = self::FETCH_ASSOC);

}
