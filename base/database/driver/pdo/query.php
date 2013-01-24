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
class Base_Database_Driver_Pdo_Query extends Database_Query {

	/**
	 * Objeto PDO donde está la información de la consulta.
	 * @var PDOStatement
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
	 * Contructor de la clase.
	 *
	 * @param PDOStatement $query
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
	 * Asignamos el tipo por defecto usado cuando se devuelven datos.
	 * @param int $type Tipo a usar.
	 * @throws Database_Exception
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function set_fetch_type($type)
	{
		parent::set_fetch_type($type);
		$this->query->setFetchMode($this->fetch_mode_pdo($type));

		// Chainable
		return $this;
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
			$this->cant = $this->query->rowCount();
		}
		return $this->cant;
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
		// Si no hay cast, revolvemos rápidamente para no afectar rendimiento.
		if ($cast === NULL)
		{
			return $this->query->fetch($this->fetch_mode_pdo($type));
		}

		switch ($type)
		{
			case Database_Query::FETCH_NUM:
				// Obtenemos el arreglo.
				$resultado = $this->query->fetch($this->fetch_mode_pdo($type));

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
					$resultado[$i] = $this->cast_field($resultado[$i], $cast[$i]);
				}

				return $resultado;
			case Database_Query::FETCH_OBJ:
				// Obtenemos el objeto.
				$object = $this->query->fetch($this->fetch_mode_pdo($type));

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
					$object->$k = $this->cast_field($object->$k, $v);
				}

				return $object;
			case Database_Query::FETCH_ASSOC:
			default:
				// Obtenemos el arreglo.
				$resultado = $this->query->fetch($this->fetch_mode_pdo($type));

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
					$resultado[$k] = $this->cast_field($resultado[$k], $v);
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
		// Si no hay cast, usamos forma corta para no afectar el rendimiento.
		if ($cast === NULL)
		{
			return $this->query->fetchAll($this->fetch_mode_pdo($type));
		}

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
		// Si no hay cast usamos forma corta para minimizar la perdidad de rendimiento.
		if ($this->cast === NULL)
		{
			return $this->query->fetch($this->fetch_mode_pdo($this->fetch_type), PDO::FETCH_ORI_ABS, $this->position);
		}

		switch ($type)
		{
			case Database_Query::FETCH_NUM:
				// Obtenemos el arreglo.
				$resultado = $this->query->fetch($this->fetch_mode_pdo($this->fetch_type), PDO::FETCH_ORI_ABS, $this->position);

				// Expandimos listado de cast.
				$cast = $this->expand_cast_list($this->cast, count($resultado));

				// Realizamos el cast.
				$c = count($resultado);
				for ($i = 0; $i < $c; $i++)
				{
					$resultado[$i] = $this->cast_field($resultado[$i], $cast[$i]);
				}

				return $resultado;
			case Database_Query::FETCH_OBJ:
				// Obtenemos el objeto.
				$object = $this->query->fetch($this->fetch_mode_pdo($this->fetch_type), PDO::FETCH_ORI_ABS, $this->position);

				// Expandimos la lista de cast.
				$cast = $this->expand_cast_list($this->cast, array_keys(get_object_vars($object)));

				// Realizamos el cast.
				foreach ($cast as $k => $v)
				{
					$object->$k = $this->cast_field($object->$k, $v);
				}

				return $object;
			case Database_Query::FETCH_ASSOC:
			default:
				// Obtenemos el arreglo.
				$resultado = $this->query->fetch($this->fetch_mode_pdo($this->fetch_type), PDO::FETCH_ORI_ABS, $this->position);

				// Expandimos la lista de cast.
				$cast = $this->expand_cast_list($this->cast, array_keys($resultado));

				// Realizamos el cast.
				foreach ($cast as $k => $v)
				{
					$resultado[$k] = $this->cast_field($resultado[$k], $v);
				}

				return $resultado;
		}
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
