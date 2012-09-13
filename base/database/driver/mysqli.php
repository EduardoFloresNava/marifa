<?php
/**
 * mysqli.php is part of Marifa.
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
class Base_Database_Driver_Mysqli extends Database_Driver {

	/**
	 * Instancia de la base de datos.
	 * @var mysqli
	 */
	protected $dbh;

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
		foreach (array('host', 'username', 'password', 'db_name') as $t)
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
		// Verificamos desconección.
		if ($this->dbh !== NULL)
		{
			@$this->dbh->close();
		}
		// Destruimos el objeto.
		$this->dbh = NULL;
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
		$this->dbh = new mysqli($this->host, $this->username, $this->password, $this->db_name);
		if ($this->dbh->connect_errno)
		{
			$e = new Database_Exception($this->dbh->connect_error, $this->dbh->connect_errno);
			throw new Database_Exception('No se ha podido conectar al servidor de base de datos.', 100, $e);
			$this->dbh = NULL;

			// Hubo un problema en la coneccion.
			return FALSE;
		}
		else
		{
			// Coneccion satisfactoria.
			return TRUE;
		}
	}

	/**
	 * Verifica si es una cadena de caracteres o un objeto a parsear en una
	 * consulta SQL. Realizando el pertinente parseo para obtener el SQL a
	 * ejecutar.
	 * @param mixed $query Objeto a parsear.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return string Consulta SQL
	 */
	private function parse_query($query, $params = array())
	{
		$o = new Base_Database_Parser($query, $params);
		return $o->build();
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
		// Armamos la consulta.
		$query = $this->parse_query($query, $params);

		// Creamos la consulta.
		if ( ! $sth = $this->dbh->prepare($query))
		{
			throw new Database_Exception("Error generando la consulta: '{$this->dbh->error}'", $this->dbh->errno);
		}

		Profiler_Profiler::get_instance()->logQuery($query);

		// Ejecutamos la consulta.
		if ($sth->execute())
		{
			$rst = $sth->get_result();
			Profiler_Profiler::get_instance()->logQuery($query);
			// Generamos un objeto para dar compatibilidad al resto de motores.
			return new Database_Driver_Mysqli_Query($rst);
		}
		else
		{
			Profiler_Profiler::get_instance()->logQuery($query);
			// Error ejecutando la consulta.
			throw new Database_Exception("Error ejecutando la consulta: '{$sth->error}'", $sth->errno);
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
		// Armamos la consulta.
		$query = $this->parse_query($query, $params);

		// Creamos la consulta.
		if ( ! $sth = $this->dbh->prepare($query))
		{
			throw new Database_Exception("Error generando la consulta: '{$this->dbh->error}'", $this->dbh->errno);
		}

		Profiler_Profiler::get_instance()->logQuery($query);

		// Ejecutamos la consulta.
		if ($sth->execute())
		{
			Profiler_Profiler::get_instance()->logQuery($query);
			return array($sth->insert_id, $sth->affected_rows);
		}
		else
		{
			Profiler_Profiler::get_instance()->logQuery($query);
			// Error ejecutando la consulta.
			throw new Database_Exception("Error ejecutando la consulta: '{$sth->error}'", $sth->errno);
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

	/**
	 * Validamos que una cadena no tenga caracteres no permitidos y la convertimos
	 * en una cadena segura.
	 * @param string $data Cadena a limpiar.
	 * @return string Cadena limpia
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function escape_string($data)
	{
		return mysqli_real_escape_string($this->dbh, $data);
	}
}