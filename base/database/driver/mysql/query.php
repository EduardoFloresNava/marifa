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
 * Clase que representa una consulta SQL realizada desde MySQL.
 *
 * Esta clase posee los métodos para obtener los datos de la consulta.
 *
 * @author     Cody Roodaka <roodakazo@hotmail.com>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Database\Driver
 */
class Base_Database_Driver_Mysql_Query extends Database_Query {

	/**
	 * Objeto PDO donde está la información de la consulta.
	 * @var resource
	 */
	protected $query;

	/**
	 * Posición del elemento actual, usada para que sea iterable.
	 * @var int
	 */
	protected $position = 0;

	/**
	 * Cantidad de filas de la consulta. Se usa de cache.
	 * @var int
	 */
	protected $cant = NULL;

	/**
	 * Constuctor de la clase.
	 *
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @param string $query Consulta SQL.
	 * @param mixed $conn Conección al servidor MySQL.
	 */
	public function __construct($query, $conn)
	{
		PRODUCTION OR Profiler_Profiler::get_instance()->log_query($query);
		$this->query = mysql_query($query, $conn);
		if ($this->query === FALSE)
		{
			throw new Database_Exception(mysql_error($conn).' (error '.mysql_errno($conn).')', mysql_errno($conn));
		}
		PRODUCTION OR Profiler_Profiler::get_instance()->log_query($query);
	}

	/**
	 * Cuando destruimos el objeto limpiamos la consulta.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 */
	public function __destruct()
	{
		if (is_resource($this->query))
		{
			mysql_free_result($this->query);
		}
	}

	/**
	 * Devolvemos la cantidad de filas afectadas por la consulta
	 *
	 * @todo Ver resultado en algunos tipos de base de datos que no retornan
	 * el resultado esperado.
	 *
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 * @return int
	 */
	public function num_rows()
	{
		if ($this->cant === NULL)
		{
			$this->cant = mysql_num_rows($this->query);
		}
		return $this->cant;
	}

	/**
	 * Obtenemos un elemento del resultado.
	 * @param int $type Tipo de retorno de los valores.
	 * @param int|array $cast Cast a aplicar a los elementos.
	 * @return mixed
	 */
	public function get_record($type = Database_Query::FETCH_ASSOC, $cast = NULL)
	{
		switch ($type)
		{
			case Database_Query::FETCH_NUM:
				// Obtenemos el arreglo.
				$resultado = mysql_fetch_array($this->query, MYSQL_NUM);

				// Evitamos cast de consultas erroneas o vacias.
				if ( ! is_array($resultado))
				{
					return $resultado;
				}

				// Verificamos si hay que hcaer cast. Sirve a fines de rendimiento.
				if ($cast !== NULL)
				{
					// Expandimos listado de cast.
					$cast = $this->expand_cast_list($cast, count($resultado));

					// Realizamos el cast.
					$c = count($resultado);
					for ($i = 0; $i < $c; $i++)
					{
						$resultado[$i] = $this->cast_field($resultado[$i], $cast[$i]);
					}
				}

				return $resultado;
			case Database_Query::FETCH_OBJ:
				// Obtenemos el objeto.
				$object = mysql_fetch_object($this->query);

				// Evitamos cast de consultas erroneas o vacias.
				if ( ! is_object($object))
				{
					return $object;
				}

				// Verificamos si hay que hcaer cast. Sirve a fines de rendimiento.
				if ($cast !== NULL)
				{
					// Expandimos la lista de cast.
					$cast = $this->expand_cast_list($cast, array_keys(get_object_vars($object)));

					// Realizamos el cast.
					foreach ($cast as $k => $v)
					{
						$object->$k = $this->cast_field($object->$k, $v);
					}
				}

				return $object;
			case Database_Query::FETCH_ASSOC:
			default:
				// Obtenemos el arreglo.
				$resultado = mysql_fetch_array($this->query, MYSQL_ASSOC);

				// Evitamos cast de consultas erroneas o vacias.
				if ( ! is_array($resultado))
				{
					return $resultado;
				}

				// Verificamos si hay que hcaer cast. Sirve a fines de rendimiento.
				if ($cast !== NULL)
				{
					// Expandimos la lista de cast.
					$cast = $this->expand_cast_list($cast, array_keys($resultado));

					// Realizamos el cast.
					foreach ($cast as $k => $v)
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
	 */
	public function get_records($type = self::FETCH_ASSOC, $cast = NULL)
	{
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
	 */
	public function current()
	{
		mysql_data_seek($this->query, $this->position);
		return $this->get_record($this->fetch_type, $this->cast);
	}

	/**
	 * Obtenemos la clave del elemento actual.
	 * Su implementación es para utilizar la interface iterator.
	 * @return int
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Movemos el puntero al siguiente elemento.
	 * Su implementación es para utilizar la interface iterator.
	 */
	public function next()
	{
		++$this->position;
	}

	/**
	 * Ponemos el puntero en el primer elemento.
	 * Su implementación es para utilizar la interface iterator.
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Verificamos si el elemento es válido.
	 * Su implementación es para utilizar la interface iterator.
	 * @return bool
	 */
	public function valid()
	{
		return $this->position < $this->num_rows();
	}
}
