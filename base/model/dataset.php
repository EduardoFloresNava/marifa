<?php
/**
 * dataset.php is part of Marifa.
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
 * Modelo que tiene métodos para un simple mapeo de propiedades.
 * Permite acceder a los campos de una fila del modelo de forma fácil.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Dataset extends Model {

	/**
	 * Nombre de la tabla
	 * @var string
	 */
	protected $table = NULL;

	/**
	 * Clave primaria para la carga de datos.
	 * En caso de ponerlo NULL no hay objeto a cargar.
	 * Sino hace falta un arreglo con las claves y sus valores.
	 * @var array
	 */
	protected $primary_key = NULL;

	/**
	 * Listado de campos a obtener y su cast correspondiente.
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Listado de campos cargados.
	 * @var array|null
	 */
	protected $data;

	/**
	 * Obtenemos el valor de un campo del usuario.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		$this->load($this->primary_key);
		return isset($this->data[$field]) ? $this->data[$field] : NULL;
	}

	/**
	 * Cargamos la información de un campo.
	 * @param array $primary_key Arreglo asociativo con los campos que componen
	 * la clave primaria.
	 */
	protected function load($primary_key)
	{
		if ($this->data === NULL)
		{
			// Armamos el listado de campos.
			$f_list = implode(', ', array_keys($this->fields));

			// Listado de claves.
			$k_list = array();
			foreach ($primary_key as $k => $v)
			{
				$k_list[] = "$k = ?";
			}
			$k_list = implode('AND ', $k_list);

			// Obtenemos los campos.
			$rst = $this->db->query("SELECT $f_list FROM $this->table WHERE $k_list LIMIT 1", array_values($primary_key))
				->get_record(Database_Query::FETCH_ASSOC, $this->fields);

			if (is_array($rst))
			{
				$this->data = $rst;
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Obtenemos una propiedad de la tabla
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Actualizamos el valor de un campo. Es una actualización de la cache interna
	 * del objeto.
	 * @param string $field Campo a actualizar.
	 * @param mixed $value Nuevo valor.
	 */
	protected function update_value($field, $value)
	{
		if (isset($this->data) && isset($this->data[$field]))
		{
			$this->data[$field] = $value;
		}
	}

	/**
	 * Listado de campos en un listado asociativo.
	 * @return array
	 */
	public function as_array()
	{
		$this->load($this->primary_key);
		return isset($this->data) ? $this->data : NULL;
	}

	/**
	 * Listado de campos con un stdClass.
	 * @return stdClass
	 */
	public function as_object()
	{
		$this->load($this->primary_key);
		return isset($this->data) ? (object) $this->data : NULL;
	}

	/**
	 * Verificamos la existencia de un elemento.
	 * @param array $primary_key Clave primaria o NULL para la seteada en el constructor.
	 * @return bool
	 */
	public function existe($primary_key = NULL)
	{
		if ($primary_key === NULL)
		{
			$primary_key = $this->primary_key;
		}

		// Listado de claves.
		$k_list = array();
		foreach ($primary_key as $k => $v)
		{
			$k_list[] = "$k = ?";
		}

		return $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE ".implode('AND ', $k_list), $primary_key)->get_var(Database_Query::FIELD_INT) > 0;
	}
}
