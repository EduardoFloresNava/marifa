<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * pdo.php is part of Marifa.
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
 * @subpackage  Database/Driver
 */

/**
 * Driver base para PDO.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa/Base
 * @subpackage Database/Driver
 */
class Base_Database_Driver_Pdo extends Database_Driver {

	/**
	 * Constructor de la clase.
	 *
	 * Acá se debe presentar toda la lógica de conección a la base de datos
	 * y dejar preparado en entorno para realizar consultas.
	 * @param array $data Arreglo con los datos de conección a la base de datos.
	 */
	public function __construct($data)
	{
		// Obtenemos los parametros de conección.
		foreach(array('dsn', 'username', 'password', 'options') as $t)
		{
			$this->$t = isset($data[$t]) ? $data[$t] : NULL;
		}

		// Realizamos la conección.
		$this->connect();
	}

	/**
	 * Destructor de la clase, nos aseguramos de desconectar la base de datos.
	 */
	public function __destruct()
	{
		// Destruimos el objeto.
		$this->db = NULL;
		unset($this);
	}

	/**
	 * Realizamos la conección a la base de datos.
	 * @throws Database_Exception
	 */
	private function connect()
	{
		// Verificamos el estado de la conección.
		if (isset($this->dbh))
		{
			// Ya esta conectado.
			return TRUE;
		}

		// Realizamos la coneccion.
		try
		{
			$this->dbh = new PDO($this->dsn, $this->username, $this->password, $this->options);
			// Coneccion satisfactoria.
			return TRUE;
		}
		catch (PDOException $e)
		{
			// Generamos la excepcion de base de datos.
			throw new Database_Exception("No se pudo conectar a la base de datos: '{$e->getMessage()}'", $e->getCode(), $e);
			// Mostramos que no nos pudimos conectar.
			$this->dbh = NULL;
			// Hubo un problema en la coneccion.
			return FALSE;
		}
	}

	/**
	 * Función para realizar consultas.
	 *
	 * Estás son las consultas que devuelven un objeto con
	 * datos de la base de datos, como puede ser un SELECT.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return mixed Objeto resultado de la consulta.
	 * @throws Database_Exception
	 */
	public function query($query, $params = array())
	{
		// Cargamos la consulta.
		$sth = $this->make_query($query, $params);

		Profiler_Profiler::getInstance()->logQuery($sth->queryString);

		// Ejecutamos la consulta.
		if ($sth->execute())
		{
			// Generamos un objeto para dar compatibilidad al resto de motores.
			return new Database_Driver_Pdo_Query($sth);
		}
		else
		{
			// Error ejecutando la consulta.
			$err_data = $sth->errorInfo();
			throw new Database_Exception("Error ejecutando la consulta: '{$err_data[2]}'", $err_data[0]);
			return FALSE;
		}
	}

	/**
	 * Creamos una consulta y asignamos los campos correspondientes.
	 * @param string $query Consulta.
	 * @param mixed $params Parámetros
	 * @return PDOStatement
	 * @throws Database_Exception
	 */
	protected function make_query($query, $params)
	{
		// Generamos la consulta y sus campos.
		list($query, $fields) = $this->get_query_data($query, $params);

		// Creamos la consulta.
		try {
			$sth = $this->dbh->prepare($query);
		}
		catch(PDOException $e)
		{
			throw new Database_Exception("Error generando la consulta: '{$e->getMessage()}'", $e->getCode(), $e);
		}

		// Asignamos todos los campos.
		foreach($fields as $f)
		{
			$sth->bindValue(is_int($f[0]) ? $f[0] + 1 : $f[0], $f[1], $f[2]);
		}

		return $sth;
	}

	/**
	 * Expandimos un campo de la consulta.
	 * Si $field es numerico, pasamos de tener 1 ? a tener $cantidad ?.
	 * En caso de ser string, pasamos de :$field a n :$field_n.
	 * @param string $query Consulta a expandir.
	 * @param int|string $field Campo a expandir.
	 * @param int $cantidad Cantidad de campos necesarios.
	 */
	protected function expand_field($query, $field, $cantidad)
	{
		if (is_int($field)) // Procesamos como ENTERO.
		{
			// Generamos arreglo de datos.
			$expand = array();
			for($i = 0; $i < $cantidad; $i++)
			{
				$expand[] = '?';
			}

			// Reemplazamos.
			$offset = 0;
			for($i = 0; $i < $field; $i++)
			{
				$offset = strpos($query, '?', $offset) + 1;
			}

			$query = substr_replace($query, implode(', ', $expand), $offset - 1, 1);
		}
		else
		{
			// Generamos arreglo de datos.
			$expand = array();
			for($i = 0; $i < $cantidad; $i++)
			{
				$expand[] = $field.'_'.($i + 1);
			}

			// Reemplazamos.
			$query = preg_replace('/'.preg_quote($field).'/', implode(', ', $expand), $query, 1);
		}

		return $query;
	}

	/**
	 * Procesamos los campos y la consulta generando una nueva consulta y
	 * la lista de asiganciones de campos para PDO.
	 * @param string $query
	 * @param mixed|array $params
	 * @return array
	 */
	protected function get_query_data($query, $params)
	{
		// Convertimos en arreglo.
		if ( ! is_array($params))
		{
			$params = array($params);
		}

		// Procesamos la consulta y los campos.
		$param_rst = array();

		$add = 0; // Desplazamiento por arreglos.
		foreach($params as $k => $v)
		{
			// Calculamos claves aplicando desplazamiento.
			$ku = is_int($k) ? $k + $add : $k;

			// Obtenemos los datos.
			list($query, $pa, $p) = $this->get_query_field($query, $ku, $v);

			// Vemos de insertar los valores generados.
			if ($p > 0 || $p == -1)
			{
				foreach($pa as $vv)
				{
					$param_rst[] = $vv;
				}

				if ($p == -1)
				{
					$p = 0;
				}
			}
			else
			{
				$param_rst[] = $pa;
			}

			// Actualizamos el desplazamiento.
			$add += $p;
		}

		return array($query, $param_rst);
	}

	protected function get_query_field($query, $field, $object)
	{
		// Convertimos el arreglo a parametros.
		if (is_object($object))
		{
			// Es un objeto, lo transformamos a una cadena.
			return array($query, array($field, ( string ) $object, PDO::PARAM_STR), 0);
		}
		elseif (is_numeric($object))
		{
			// Es un número, lo convertimos.

			// Verificamos si es un entero.
			if (is_int($object))
			{
				return array($query, array($field, $object, PDO::PARAM_INT), 0);
			}

			// Verificamos si puede tratarse como un entero.
			if ((( int ) $object) == $object)
			{
				return array($query, array($field, (int) $object, PDO::PARAM_INT), 0);
			}

			// Lo tratamos como un real.
			return array($query, array($field, (float) $object, PDO::PARAM_STR), 0);
		}
		elseif (is_array($object))
		{
			// Es un arreglo, lo procesamos.

			if (is_int($field))
			{
				// Implementamos desplazamiento.

				// Cantidad de campos.
				$c = count($object);

				// Expandimos la consulta.
				$query = $this->expand_field($query, $field + 1, $c);

				// Generamos lista de campos.
				$fs = array();
				for($i = 0; $i < $c; $i++)
				{
					$aux = $this->get_query_field($query, $field + $i, $object[$i]);
					$fs[] = $aux[1];
				}
				return array($query, $fs, $c - 1);
			}
			else
			{
				// No hace falta desplazar.

				// Cantidad de campos.
				$c = count($object);

				// Expandimos la consulta.
				$query = $this->expand_field($query, $field, $c);

				// Generamos lista de campos.
				$fs = array();
				for($i = 0; $i < $c; $i++)
				{
					$aux = $this->get_query_field($query, $field.'_'.($i+1), $object[$i]);
					$fs[] = $aux[1];
				}
				return array($query, $fs, -1);
			}
		}
		elseif ($object === NULL)
		{
			// Dato NULO
			return array($query, array($field, $object, PDO::PARAM_NULL), 0);
		}
		else
		{
			//Suponemos una cadena.
			return array($query, array($field, $object, PDO::PARAM_STR), 0);
		}
	}

	/**
	 * Consulta que realiza una modificación en la base de datos.
	 *
	 * Son consultas que no devuelven un conjunto de datos, simplemente las
	 * filas afectadas y si es una inserción, el id del posible campo automático.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, un arreglo con
	 * el id de la inserción y el número de filas afectadas si fue correcto.
	 * @throws Database_Exception
	 */
	private function write_query($query, $params = array())
	{
		// Cargamos la consulta.
		$sth = $this->make_query($query, $params);

		Profiler_Profiler::getInstance()->logQuery($sth->queryString);

		// Realizamos la consulta
		$rst = $sth->execute();

		if ($rst)
		{
			// Obtenemos el ID.
			try {
				$id = (int) $this->dbh->lastInsertId();
			}
			catch (PDOException $e)
			{
				// No esta soportado o no es correcto aplicarlo.
				$id = NULL;
			}

			// Devolvemos las filas afectadas.
			$cols = $sth->rowCount();

			return array($id, $cols);
		}
		else
		{
			// Hubo un problema, generamos la excepción.
			$err_data = $sth->errorInfo();
			throw new Database_Exception("Error ejecutando la consulta: '{$err_data[2]}'", $err_data[0]);
			return FALSE;
		}
	}

	/**
	 * Realiza una inserción en la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, un arreglo con
	 * el id de la inserción y el número de filas afectadas si fue correcto.
	 * @throws Database_Exception
	 */
	public function insert($query, $params = array())
	{
		// Llamamos la consulta de modificación.
		return $this->write_query($query, $params);
	}

	/**
	 * Borramos información de la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, el numero de filas
	 * afectadas si fue correcto.
	 * @throws Database_Exception
	 */
	public function delete($query, $params = array())
	{
		// Llamamos la consulta de modificación.
		$rst = $this->write_query($query, $params);

		// Sacamos el ID de inserción obtenido.
		if (is_array($rst))
		{
			return $rst[1];
		}
		else
		{
			return $rst;
		}
	}

	/**
	 * Realiza una actualización en la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, el numero de filas
	 * afectadas si fue correcto.
	 * @throws Database_Exception
	 */
	public function update($query, $params = array())
	{
		// Llamamos la consulta de modificación.
		$rst = $this->write_query($query, $params);

		// Sacamos el ID de inserción obtenido.
		if (is_array($rst))
		{
			return $rst[1];
		}
		else
		{
			return $rst;
		}
	}
}
