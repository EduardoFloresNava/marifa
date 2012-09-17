<?php
/**
 * mysql.php is part of Marifa.
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
 * Driver base para mysql.
 *
 * @author     Cody Roodaka <roodakazo@hotmail.com>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Database\Driver
 */
class Base_Database_Driver_Mysql extends Database_Driver {

	/**
	 * Objeto de coneccion a la base de datos.
	 */
	protected $conn = NULL;

	/**
	 * IP o Host de la base de datos
	 */
	protected $host;

	/**
	 * Usuario del Servidor.
	 */
	protected $user;

	/**
	 * Contraseña del Servidor.
	 */
	protected $pass;

	/**
	 * Nombre de la Base de datos.
	 */
	protected $db;

	/**
	 * Constructor de la clase.
	 *
	 * @param array $data Arreglo con la información necesaria para conectar.
	 * Debe tener host, user, pass y db.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 */
	public function __construct($data)
	{
		$this->host = $data['host'];
		$this->user = $data['username'];
		$this->pass = $data['password'];
		$this->db = $data['db_name'];

		// Conectamos a la base de datos.
		$this->connect();
	}

	public function explain_query($sql)
	{
		// Realizamos la ejecución.
		if ($this->is_connected())
		{
			// Consulta.
			$rst = new Database_Driver_Mysql_Query($sql, $this->conn);
			$rst->set_fetch_type(Database_Query::FETCH_OBJ);

			// Armamos el resultado.
			$lst = array();
			foreach ($rst as $v)
			{
				// Posibles keys
				if (isset($v->possible_keys) && $v->possible_keys != NULL)
				{
					if ( ! isset($lst['posibles_keys']))
					{
						$lst['posibles_keys'] = array();
					}

					$lst['posibles_keys'][] = $v->possible_keys;
				}

				// Key
				if (isset($v->key) && $v->key != NULL)
				{
					if ( ! isset($lst['key']))
					{
						$lst['key'] = array();
					}
					$ks = $v->key;

					if (isset($v->key_len) && $v->key_len != NULL)
					{
						$ks = $ks.'('.$v->key_len.')';
					}
					$lst['key'][] = $ks;
				}

				// Type
				if (isset($v->type) && $v->type != NULL)
				{
					if ( ! isset($lst['type']))
					{
						$lst['type'] = array();
					}
					$lst['type'][] = $v->type;
				}

				// Rows
				if (isset($v->rows) && $v->rows != NULL)
				{
					if ( ! isset($lst['rows']))
					{
						$lst['rows'] = (int) $v->rows;
					}
					else
					{
						$lst['rows'] += (int) $v->rows;
					}
				}
			}

			// Tranformamos elemento a string.
			foreach ($lst as $k => $v)
			{
				if (is_array($v))
				{
					$lst[$k] = implode(', ', $v);
				}
			}

			// Devolvemos el resultado.
			return $lst;
		}
		else
		{
			return array();
		}
	}

	/**
	 * Destructor de la clase.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 */
	public function __destruct()
	{
		if ($this->conn !== NULL)
		{
			return @mysql_close($this->conn);
		}
		$this->conn = NULL;
	}


	/**
	 * Conectamos a la base de datos.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @throws Database_Exception
	 */
	protected function connect()
	{
		if ($this->is_connected() === FALSE)
		{
			$this->conn = @mysql_connect($this->host, $this->user, $this->pass);
			if ($this->conn === FALSE)
			{
				$e = new Database_Exception(mysql_error(), mysql_errno());
				throw new Database_Exception('No se ha podido conectar al servidor de base de datos.', 100, $e);
			}

			if ( ! @mysql_select_db($this->db, $this->conn))
			{
				$this->conn = NULL;
				$e = new Database_Exception(mysql_error(), mysql_errno());
				throw new Database_Exception('No se ha podido seleccionar la base de datos.', 101, $e);
			}
		}
	}

	/**
	 * Verificamos el estado de la conección a la base de datos.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @return bool
	 */
	private function is_connected()
	{
		if ($this->conn === NULL || ! $this->conn)
		{
			return FALSE;
		}
		else
		{
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
	 * Realizamos una consulta de selección.
	 *
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|Database_Driver_Mysql_Query Falso si hubo un error
	 * o un objeto para obtener la información del resultado obtenido.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @throws Database_Exception
	 */
	public function query($query, $params = array())
	{
		if ($this->is_connected())
		{
			$query = $this->parse_query($query, $params);
			return new Database_Driver_Mysql_Query($query, $this->conn);
		}
		else
		{
			throw new Database_Exception('No hay una conección a la base de datos extablecida.', 102);
			return FALSE;
		}
	}

	/**
	 * Función encargada de limpiar los objetos generados internamente por la
	 * clase.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @param mixed $query Objeto a limpiar.
	 */
	protected function free($query)
	{
		return mysql_free_result($query);
	}

	/**
	 * Realiza una inserción en la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, un arreglo con
	 * el id de la inserción y el número de filas afectadas si fue correcto.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @throws Database_Exception
	 */
	public function insert($query, $params = array())
	{
		if ($this->is_connected())
		{
			$query = $this->parse_query($query, $params);
			Profiler_Profiler::get_instance()->log_query($query);
			$rst = mysql_query($query, $this->conn);
			Profiler_Profiler::get_instance()->log_query($query);

			if ($rst === TRUE)
			{
				// Si fue correcto devolvemos el ID y las filas afectadas.
				return array(
						mysql_insert_id(),
						mysql_affected_rows()
				);
			}
			else
			{
				// Generamos una excepción.
				throw new Database_Exception("Error al ejecutar la consulta: '".mysql_error()."'", mysql_errno());

				// Devolvemos falso para indicar que no fue correcto.
				return FALSE;
			}
		}
		else
		{
			throw new Database_Exception('No hay una conección a la base de datos extablecida.', 102);
			return FALSE;
		}
	}

	/**
	 * Borramos información de la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, el numero de filas
	 * afectadas si fue correcto.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @throws Database_Exception
	 */
	public function delete($query, $params = array())
	{
		if ($this->is_connected())
		{
			$query = $this->parse_query($query, $params);
			Profiler_Profiler::get_instance()->log_query($query);
			$rst = mysql_query($query, $this->conn);
			Profiler_Profiler::get_instance()->log_query($query);

			if ($rst === TRUE)
			{
				// Si fue correcto devolvemos las filas afectadas.
				return mysql_affected_rows();
			}
			else
			{
				// Generamos una excepción.
				throw new Database_Exception("Error al ejecutar la consulta: '".mysql_error()."'", mysql_errno());

				// Devolvemos falso para indicar que no fue correcto.
				return FALSE;
			}
		}
		else
		{
			throw new Database_Exception('No hay una conección a la base de datos extablecida.', 102);
			return FALSE;
		}
	}

	/**
	 * Realiza una actualización en la base de datos.
	 * @param string $query Consulta SQL.
	 * @param array $params Arreglo con los parametros a reemplazar.
	 * @return bool|int False cuando se produce un error, el numero de filas
	 * afectadas si fue correcto.
	 * @author Cody Roodaka <roodakazo@hotmail.com>
	 * @throws Database_Exception
	 */
	public function update($query, $params = array())
	{
		if ($this->is_connected())
		{
			$query = $this->parse_query($query, $params);
			Profiler_Profiler::get_instance()->log_query($query);
			$rst = mysql_query($query, $this->conn);
			Profiler_Profiler::get_instance()->log_query($query);

			if ($rst === TRUE)
			{
				// Si fue correcto devolvemos las filas afectadas.
				return mysql_affected_rows();
			}
			else
			{
				// Generamos una excepción.
				throw new Database_Exception("Error al ejecutar la consulta: '".mysql_error()."'", mysql_errno());

				// Devolvemos falso para indicar que no fue correcto.
				return FALSE;
			}
		}
		else
		{
			throw new Database_Exception('No hay una conección a la base de datos establecida.', 102);
			return FALSE;
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
		return mysql_real_escape_string($data, $this->conn);
	}
}
