<?php
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
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa\Base
 * @subpackage  Database\Driver
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase que representa el resultado de una consulta de selección.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Database\Driver
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
	public function __construct($query, $conn)
	{
		PRODUCTION || Profiler_Profiler::get_instance()->log_query($query);
		$this->query = mysqli_query($conn, $query);
		if ($this->query === FALSE)
		{
			throw new Database_Exception('Error ejecutando la consulta \''.$query.'\': \''.mysqli_error($conn).'\'', mysqli_errno($conn));
		}
		PRODUCTION || Profiler_Profiler::get_instance()->log_query($query);

		// Seteo si es UTF-8.
		$this->use_utf8 = mysqli_character_set_name($conn) == 'utf8';
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
	 * Devolvemos la cantidad de filas afectadas por la consulta
	 *
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 * @return int
	 */
	public function num_rows()
	{
		return mysqli_num_rows($this->query);
	}

	/**
	 * Obtenemos un elemento del resultado.
	 * @param int $type Tipo de retorno de los valores.
	 * @param int|array $cast Cast a aplicar a los elementos.
	 * @return mixed
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function get_record($type = Database_Query::FETCH_ASSOC, $cast = NULL)
	{
		// $this->next();
		switch ($type)
		{
			case Database_Query::FETCH_NUM:
				// Obtenemos el arreglo.
				$resultado = mysqli_fetch_array($this->query, MYSQLI_NUM);

				// Evitamos cast de consultas erroneas o vacias.
				if ( ! is_array($resultado))
				{
					return $resultado;
				}

				// Expandimos listado de cast.
				$cast = $this->expand_cast_list($cast, count($resultado));

				// Realizamos el cast.
				$c = count($resultado);
				for ($i = 0; $i < $c; $i++)
				{
					if (isset($resultado[$i]))
					{
						$resultado[$i] = $this->cast_field($resultado[$i], $cast[$i]);
					}
				}

				return $resultado;
			case Database_Query::FETCH_OBJ:
				// Obtenemos el objeto.
				$object = mysqli_fetch_object($this->query);

				// Evitamos cast de consultas erroneas o vacias.
				if ( ! is_object($object))
				{
					return $object;
				}

				// Expandimos la lista de cast.
				$cast = $this->expand_cast_list($cast, array_keys(get_object_vars($object)));

				// Realizamos el cast.
				foreach ($cast as $k => $v)
				{
					if (isset($object->$k))
					{
						$object->$k = $this->cast_field($object->$k, $v);
					}
				}

				return $object;
			case Database_Query::FETCH_ASSOC:
			default:
				// Obtenemos el arreglo.
				$resultado = mysqli_fetch_array($this->query, MYSQLI_ASSOC);

				// Evitamos cast de consultas erroneas o vacias.
				if ( ! is_array($resultado))
				{
					return $resultado;
				}

				// Expandimos la lista de cast.
				$cast = $this->expand_cast_list($cast, array_keys($resultado));

				// Realizamos el cast.
				foreach ($cast as $k => $v)
				{
					if (isset($resultado[$k]))
					{
						$resultado[$k] = $this->cast_field($resultado[$k], $v);
					}
				}

				return $resultado;
		}
	}

	/**
	 * Obtenemos un arreglo de elementos.
	 * @param int $type Tipo de retorno de los valores.
	 * @param int|array $cast Cast a aplicar a los elementos.
	 * @return array
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function get_records($type = self::FETCH_ASSOC, $cast = NULL)
	{
		$this->rewind();
		$rst = array();
		while ($row = $this->get_record($type, $cast))
		{
			$rst[] = $row;
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
		mysqli_data_seek($this->query, $this->position);
		return $this->get_record($this->fetch_type, $this->cast);
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
