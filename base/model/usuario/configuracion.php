<?php defined('APP_BASE') or die('No direct access allowed.');
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
 * @package		Marifa/Base
 * @subpackage  Model
 */

/**
 * Modelo de configuraciones del usuario.
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Configuracion extends Model {

	/**
	 * ID del usuario dueño de las propiedades.
	 * @var int
	 */
	protected $usuario_id;

	/**
	 * Instanciamos las configuraciones del usuario.
	 * @param int $usuario_id ID del usuario.
	 */
	public function __construct($usuario_id)
	{
		parent::__construct();

		$this->usuario_id = $usuario_id;
	}

	/**
	 * Obtenemos el listado de campos de configuración que tiene el usaurio.
	 * @return array
	 */
	public function lista()
	{
		return $this->db->query('SELECT clave, valor FROM usuario_configuracion WHERE usuario_id = ?', $this->usuario_id)->get_pairs();
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
			return $this->db->query('SELECT valor FROM usuario_configuracion WHERE clave = ? AND usuario_id = ?', array($name, $this->usuario_id))->get_var();
		}
		else
		{
			throw new Exception('No existe la clave.');
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
			$this->db->update('UPDATE usuario_configuracion SET valor = ? WHERE clave = ? AND usuario_id = ?', array($value, $name, $this->usuario_id));
		}
		else
		{
			$this->db->insert('INSERT INTO usuario_configuracion (usuario_id, clave, valor) VALUES (?, ?, ?)', array($this->usuario_id, $name, $value));
		}
	}

	/**
	 * Verificamos la existencia de la clave.
	 * @param string $name Nombre de la clave a buscar.
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->db->query('SELECT clave FROM usuario_configuracion WHERE clave = ? AND usuario_id LIMIT 1', array($name, $this->usuario_id))->num_rows() > 0;
	}

	/**
	 * Eliminamos una clave.
	 * @param string $name Clave a eliminar.
	 */
	public function __unset($name)
	{
		$this->db->delete('DELETE FROM usuario_configuracion WHERE clave = ? AND usuario_id', array($name, $this->usuario_id));
	}
}
