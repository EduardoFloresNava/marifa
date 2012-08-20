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
		// Creamos la consulta.
		try {
			$sth = $this->dbh->prepare($query);
		}
		catch(PDOException $e)
		{
			throw new Exception_Database("Error generando la consulta: '{$e->getMessage()}'", $e->getCode(), $e);
		}

		// Verificamos sea arreglo.
		if ( ! is_array($params) && $params !== NULL)
		{
			$params = array($params);
		}

		// Ejecutamos la consulta.
		if ($sth->execute($params))
		{
			// Generamos un objeto para dar compatibilidad al resto de motores.
			return new Database_Driver_Pdo_Query($sth);
		}
		else
		{
			// Error ejecutando la consulta.
			$err_data = $sth->errorInfo();
			throw new Exception_Database("Error ejecutando la consulta: '{$err_data[2]}'", $err_data[0]);
			return FALSE;
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
		// Creamos la consulta.
		try {
			$sth = $this->dbh->prepare($query);
		}
		catch(PDOException $e)
		{
			throw new Exception_Database("Error generando la consulta: '{$e->getMessage()}'", $e->getCode(), $e);
		}

		// Verificamos sea arreglo.
		if ( ! is_array($params) && $params !== NULL)
		{
			$params = array($params);
		}

		// Realizamos la consulta
		$rst = $sth->execute($params);

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
			throw new Exception_Database("Error ejecutando la consulta: '{$err_data[2]}'", $err_data[0]);
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
