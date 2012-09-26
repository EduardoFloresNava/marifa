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
 * @subpackage  Database
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Interface para los objetos devueltos por las consultas de selección.
 *
 * @version    0.1
 * @package    Marifa\Base
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
	 * Obtenemos los elementos como un objeto.
	 */
	const FETCH_OBJ = 2;

	/**
	 * Campo de tipo ENTERO.
	 */
	const FIELD_INT = 0;

	/**
	 * Campo de tipo REAL.
	 */
	const FIELD_FLOAT = 1;

	/**
	 * Campo de tipo STRING.
	 */
	const FIELD_STRING = 2;

	/**
	 * Campo de tipo FECHA.
	 */
	const FIELD_DATE = 3;

	/**
	 * Campo de tipo FECHA Y HORA.
	 */
	const FIELD_DATETIME = 4;

	/**
	 * Campo del tipo booleano.
	 */
	const FIELD_BOOL = 5;

	/**
	 * Tipo de valor devuelto en las iteraciones.
	 * @var int
	 */
	protected $fetch_type = self::FETCH_NUM;

	/**
	 * Tipo de cast a aplicar a los valores devueltos en las iteraciones.
	 * @var int|array
	 */
	protected $cast = NULL;

	/**
	 * Asignamos el tipo por defecto usado cuando se devuelven datos.
	 * @param int $type Tipo a usar.
	 * @throws Database_Exception
	 */
	public function set_fetch_type($type)
	{
		// Validamos el tipo.
		if ( ! in_array($type, array(self::FETCH_NUM, self::FETCH_ASSOC, self::FETCH_OBJ)))
		{
			throw new Database_Exception('Invalid fetch type');
		}
		$this->fetch_type = $type;
	}

	/**
	 * Asignamos el cast por defecto usado cuando se devuelven datos.
	 * @param int|array $cast Cast a realizar
	 */
	public function set_cast_type($cast)
	{
		$this->cast = $cast;
	}

	/**
	 * Realizamos el cast de un campo según los tipos provistos para las base de
	 * datos.
	 * @param mixed $field Campo a convertir.
	 * @param int $cast Tipo de dato deseado.
	 * @return mixed
	 */
	protected function cast_field($field, $cast)
	{
		if ($cast === self::FIELD_INT)
		{
			return ($field == NULL) ? NULL : ( (int) $field);
		}
		elseif ($cast === self::FIELD_FLOAT)
		{
			return ($field == NULL) ? NULL : ( (float) $field);
		}
		elseif ($cast === self::FIELD_STRING)
		{
			return ($field == NULL) ? NULL : ( (string) $field);
		}
		elseif ($cast === self::FIELD_DATE || $cast === self::FIELD_DATETIME)
		{
			return ($field == NULL) ? NULL : new Fechahora($field);
		}
		elseif ($cast === self::FIELD_BOOL)
		{
			return ($field == NULL) ? NULL : ( (bool) $field);
		}
		else
		{
			return $field;
		}
	}

	/**
	 * Devolvemos la cantidad de filas afectadas por la consulta
	 * @return int
	 */
	abstract public function num_rows();

	/**
	 * Obtenemos un valor simple de una consulta.
	 * @param int $cast Cast a aplicar al resultado.
	 * @return string
	 */
	public function get_var($cast = NULL)
	{
		$dt = $this->get_record(self::FETCH_NUM);

		$v = (is_array($dt) && isset($dt[0])) ? $dt[0] : NULL;

		if ($cast !== NULL)
		{
			return $this->cast_field($v, $cast);
		}
		else
		{
			return $v;
		}
	}

	/**
	 * Expandimos la lista de CAST a la cantidad indicada.
	 * @param array $list Arreglo de entrada.
	 * @param int|array $cant Cantidad de campos que debe tener el arreglo de salida.
	 * O arreglo con las claves de arreglo de salida.
	 * @return array
	 */
	protected function expand_cast_list($list, $cant)
	{
		// Armamos arreglo inicial.
		if ( ! is_array($list))
		{
			$list = ($list === NULL) ? array() : array($list);
		}

		if (is_array($cant))
		{
			foreach ($cant as $k)
			{
				if ( ! isset($list[$k]))
				{
					$list[$k] = NULL;
				}
			}
		}
		else
		{
			// Expandimos hasta completar.
			for ($i = 0; $i < $cant; $i++)
			{
				if ( ! isset($list[$i]))
				{
					$list[$i] = NULL;
				}
			}
		}

		return $list;
	}

	/**
	 * Obtenemos un arreglo asociativo usando el primer parámetro como clave.
	 * En caso de solo tener 1 elemento, se lo usa de valor y las claves son enteros.
	 * @param int|array $cast Cast a aplicar a los elementos. El 1ro para las
	 * claves y el 2do para los valores.
	 * @return array
	 */
	public function get_pairs($cast = NULL)
	{
		// Expandimos la lista de cast.
		$cast = $this->expand_cast_list($cast, 2);

		$dt = $this->get_records(self::FETCH_NUM);
		$rst = array();
		foreach ($dt as $data)
		{
			// Verificamos si hay cast o no. Se valida a fines de rendimiento.
			if ($cast !== NULL)
			{
				// Verificamos si tenemos 1 o 2.
				if (count($data) == 1)
				{
					$rst[] = $this->cast_field($data[0], $cast[0]);
				}
				else
				{
					$rst[$this->cast_field($data[0], $cast[0])] = $this->cast_field($data[1], $cast[1]);
				}
			}
			else
			{
				// Verificamos si tenemos 1 o 2.
				if (count($data) == 1)
				{
					$rst[] = $data[0];
				}
				else
				{
					$rst[$data[0]] = $data[1];
				}
			}
		}
		return $rst;
	}

	/**
	 * Obtenemos un elemento del resultado.
	 * @param int $type Tipo de retorno de los valores.
	 * @param int|array $cast Cast a aplicar a los elementos.
	 * @return mixed
	 */
	abstract public function get_record($type = self::FETCH_ASSOC, $cast = NULL);

	/**
	 * Obtenemos un arreglo de elementos.
	 * @param int $type Tipo de retorno de los valores.
	 * @param int|array $cast Cast a aplicar a los elementos.
	 * @return array
	 */
	abstract public function get_records($type = self::FETCH_ASSOC, $cast = NULL);

}
