<?php
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
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa\Base
 * @subpackage  Database\Driver
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Driver base para PDO.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Database\Driver
 */
class Base_Database_Driver_Pdo extends Database_Driver {

	/**
	 * Instancia de la clase PDO.
	 * @var PDO
	 */
	protected $dbh;

	/**
	 * Si se utiliza UTF-8 o no.
	 * @var bool
	 */
	protected $utf8 = FALSE;

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
		foreach (array('dsn', 'username', 'password', 'options') as $t)
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

			// Seteo si debo trabajar en UTF-8.
			$qry = $this->dbh->query("SHOW VARIABLES LIKE 'character_set_client'");
			$qry->execute();

			if ($qry->fetch(PDO::FETCH_OBJ)->Value == 'utf8')
			{
				$this->utf8 = TRUE;
			}

			return TRUE;
		}
		catch (PDOException $e)
		{
			// Generamos la excepcion de base de datos.
			throw new Database_Exception("No se pudo conectar a la base de datos: '{$e->getMessage()}'", $e->getCode());
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

		PRODUCTION || Profiler_Profiler::get_instance()->log_query($sth->queryString);

		// Ejecutamos la consulta.
		if ($sth->execute())
		{
			PRODUCTION || Profiler_Profiler::get_instance()->log_query($sth->queryString);
			// Generamos un objeto para dar compatibilidad al resto de motores.
			return new Database_Driver_Pdo_Query($sth, $this->utf8);
		}
		else
		{
			PRODUCTION || Profiler_Profiler::get_instance()->log_query($sth->queryString);
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
		// Verifico que sea un arreglo.
		if ( ! is_array($params))
		{
			$params = array($params);
		}
		else
		{
			// Para dar compatibilidad con MySQL.
			$params = array_values($params);
		}

		// Obtengo el listado de campos con su respectivo tipo.
		foreach ($params as $k => $v)
		{
			$params[$k] = $this->get_query_field($k, $v);
		}

		// Creamos la consulta.
		try {
			$sth = $this->dbh->prepare($query);
		}
		catch(PDOException $e)
		{
			throw new Database_Exception("Error generando la consulta: '{$e->getMessage()}'", $e->getCode());
		}

		// Asignamos todos los campos.
		foreach ($params as $v)
		{
			$sth->bindValue(is_int($v[0]) ? ($v[0] + 1) : $v[0], $v[1], $v[2]);
		}

		return $sth;
	}

	/**
	 * Generamos una consulta con el nombre del campo con su CAST y el tipo de dato para PDO.
	 * @param mixed $field Nombre del campo.
	 * @param mixed $object Objeto con el valor del campo.
	 * @return array
	 */
	protected function get_query_field($field, $object)
	{
		// Convertimos el arreglo a parametros.
		if (is_object($object))
		{
			// Es un objeto, lo transformamos a una cadena.
			return array($field, ( string ) $object, PDO::PARAM_STR);
		}
		elseif (is_numeric($object))
		{
			// Es un número, lo convertimos.

			// Verificamos si es un entero.
			if (is_int($object))
			{
				return array($field, $object, PDO::PARAM_INT);
			}

			// Verificamos si puede tratarse como un entero.
			if (( ( int ) $object) == $object)
			{
				return array($field, (int) $object, PDO::PARAM_INT);
			}

			// Lo tratamos como un real.
			return array($field, (float) $object, PDO::PARAM_STR);
		}
		elseif (is_array($object))
		{
			// No se permiten arreglos.
			throw new Exception('El campo ingresado es un arreglo. No se pueden implementar en una consulta.');
		}
		elseif ($object === NULL)
		{
			// Dato NULO
			return array($field, $object, PDO::PARAM_NULL);
		}
		else
		{
			// Suponemos una cadena.
			return array($field, $object, PDO::PARAM_STR);
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

		PRODUCTION || Profiler_Profiler::get_instance()->log_query($sth->queryString);

		// Realizamos la consulta
		$rst = $sth->execute();

		PRODUCTION || Profiler_Profiler::get_instance()->log_query($sth->queryString);

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
