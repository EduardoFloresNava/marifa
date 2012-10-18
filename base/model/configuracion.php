<?php
/**
 * configuracion.php is part of Marifa.
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
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo de configuraciones generales del sistema.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Configuracion extends Model {

	/**
	 * Seteamos el valor por defecto de una configuracion.
	 * Si no existe creamos la clave con el valor actual igual al por defecto.
	 * @param string $name Clave de la configuracion.
	 * @param mixed $value Valor a setear.
	 */
	public function set_default($name, $value)
	{
		if (isset($this->$name))
		{
			$this->db->update('UPDATE configuracion SET default = ? WHERE clave = ?', array($value, $name));
		}
		else
		{
			$this->db->insert('INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array($name, $value, $value));
		}
	}

	/**
	 * Restauramos el valor por defecto de la clave.
	 * @param string $name Clave a restaurar.
	 * @return bool FALSE si no existe.
	 */
	public function restore_default($name)
	{
		if (isset($this->$name))
		{
			$this->db->update('UPDATE configuracion SET valor = defecto WHERE clave = ?', $name);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Obtenemos el valor por defecto de una clave.
	 * @param string $name Nombre de la clave a obtener.
	 * @return mixed
	 */
	public function get_default($name)
	{
		if (isset($this->$name))
		{
			return $this->db->query('SELECT default FROM configuracion WHERE clave = ?', $name)->get_var();
		}
		else
		{
			throw new Exception('No existe la clave.');
		}
	}

	/**
	 * Obtenemos el valor de una clave.
	 * @param string $name Nombre de la clave a obtener.
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->$name))
		{
			return $this->db->query('SELECT valor FROM configuracion WHERE clave = ?', $name)->get_var();
		}
		else
		{
			throw new UnexpectedValueException('No existe la clave.');
		}
	}

	/**
	 * Obtenemos un valor utilizando un resultado por defecto.
	 * @param string $name
	 * @param mixed $default
	 */
	public function get($name, $default = NULL)
	{
		try {
			return $this->$name;
		}
		catch (UnexpectedValueException $e)
		{
			return $default;
		}
	}

	/**
	 * Actualizamos el valor de la clave. Si no existe se crea con un valor
	 * por defecto igual al actual.
	 * @param string $name Nombre de la clave
	 * @param mixed $value Valor a setear
	 */
	public function __set($name, $value)
	{
		if (isset($this->$name))
		{
			$this->db->update('UPDATE configuracion SET valor = ? WHERE clave = ?', array($value, $name));
		}
		else
		{
			$this->db->insert('INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array($name, $value, $value));
		}
	}

	/**
	 * Verificamos la existencia de la clave.
	 * @param string $name Nombre de la clave a buscar.
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->db->query('SELECT clave FROM configuracion WHERE clave = ? LIMIT 1', $name)->num_rows() > 0;
	}

	/**
	 * Eliminamos una clave.
	 * @param string $name Clave a eliminar.
	 */
	public function __unset($name)
	{
		$this->db->delete('DELETE FROM configuracion WHERE clave = ?', $name);
	}

}
