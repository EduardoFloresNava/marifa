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
 * @subpackage  Database/Driver/Mysqli
 */

/**
 * Clase que representa el resultado de una consulta de selección.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa/Base
 * @subpackage Database/Driver/Mysqli
 */
class Base_Database_Driver_Mysqli_Query extends Database_Query {

	/**
	 * Objeto mysqli_result con información de la consulta.
	 * @var mysqli_result
	 */
	protected $query;

	/**
	 * Posición del elemento actual, usada para que sea iterable.
	 * @var int
	 */
	protected $position = 0;

	/**
	 * Contructor de la clase.
	 *
	 * @param mysqli_result $query
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function __construct($query)
	{
		$this->query = $query;
	}

	/**
	 * Cuando destruimos el objeto limpiamos la consulta.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function __destruct()
	{
		unset($this->query);
		unset($this);
	}

	/**
	 * Convertimos un modo de devolver un registro al correspondiente de PDO.
	 * @param int $type Tipo de dato a convertir.
	 * @return int Tipo de dato convertido.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	protected function fetch_mode_pdo($type)
	{
		switch ($type)
		{
			case Database_Query::FETCH_NUM:
				return PDO::FETCH_NUM;
			case Database_Query::FETCH_OBJ:
				return PDO::FETCH_OBJ;
			case Database_Query::FETCH_ASSOC:
			default:
				return PDO::FETCH_ASSOC;
		}
	}

	/**
	 * Devolvemos la cantidad de filas afectadas por la consulta
	 *
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 * @return int
	 */
	public function num_rows()
	{
		return $this->query->num_rows;
	}

	/**
	 * Obtenemos un elemento del resultado.
	 * @param $type Tipo de retorno de los valores.
	 * @return mixed
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function get_record($type = Database_Query::FETCH_ASSOC)
	{
		$this->next();
		switch ($type)
		{
			case Database_Query::FETCH_NUM:
				return $this->query->fetch_array(MYSQLI_NUM);
			case Database_Query::FETCH_OBJ:
				return $this->query->fetch_object();
			case Database_Query::FETCH_ASSOC:
			default:
				return $this->query->fetch_array(MYSQLI_ASSOC);
		}
	}

	/**
	 * Obtenemos un arreglo de elementos.
	 * @param $type Tipo de retorno de los valores.
	 * @return array
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function get_records($type = self::FETCH_ASSOC)
	{
		$this->rewind();
		$rst = array();
		while($this->valid())
		{
			$rst[] = $this->current($type);
			$this->next();
		}
		return $rst;
	}

	/**
	 * Obtenemos el elemento actual, el tipo de valor devuelto depende del formato especificado.
	 * Su implementación es para utilizar la interface iterator.
	 * @return mixed
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function current()
	{
		$this->query->field_seek($this->position);
		return $this->get_record($this->fetch_type);
	}

	/**
	 * Obtenemos la clave del elemento actual.
	 * Su implementación es para utilizar la interface iterator.
	 * @return int
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Movemos el puntero al siguiente elemento.
	 * Su implementación es para utilizar la interface iterator.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function next()
	{
		++$this->position;
	}

	/**
	 * Ponemos el puntero en el primer elemento.
	 * Su implementación es para utilizar la interface iterator.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Verificamos si el elemento es válido.
	 * Su implementación es para utilizar la interface iterator.
	 * @return bool
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function valid()
	{
		return $this->position < $this->num_rows();
	}
}
