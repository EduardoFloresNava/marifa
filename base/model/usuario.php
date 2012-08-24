<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * usuario.php is part of Marifa.
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
 * Modelo del usuario.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Usuario extends Model {

	/**
	 * ID del usuario cargado por el modelo.
	 * @var int
	 */
	protected $id;

	/**
	 * Arreglo con la información del usuario.
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor del modelo
	 * @param int $id ID del usuario a cargar.
	 */
	public function __construct($id = NULL)
	{
		// Iniciamos el modelo.
		parent::__construct();

		// Seteamos ID del usuario.
		$this->id = $id;
	}

	/**
	 * Obtenemos el valor de un campo del usuario.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		if ($this->data === NULL)
		{
			// Obtenemos los campos.
			$rst = $this->db->query('SELECT * FROM usuario WHERE id = ? LIMIT 1', $this->id)->get_record();

			if (is_array($rst))
			{
				$this->data = $rst;
			}
		}

		return isset($this->data[$field]) ? $this->data[$field] : NULL;
	}

	/**
	 * Obtenemos una propiedad del usuario.
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

}
