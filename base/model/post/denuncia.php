<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * denuncia.php is part of Marifa.
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
 * Modelo de una denuncia de un post
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Post_Denuncia extends Model {

	/**
	 * ID de la denuncia.
	 * @var int
	 */
	protected $id;

	/**
	 * Arreglo con la información de la denuncia.
	 * @var array
	 */
	protected $data;

	/**
	 * Cargamos una denuncia.
	 * @param int $id ID de la denuncia.
	 */
	public function __construct($id)
	{
		parent::__construct();

		$this->id = $id;
	}

	/**
	 * Obtenemos el valor de un campo de la denuncia.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		if ($this->data === NULL)
		{
			// Obtenemos los campos.
			$rst = $this->db->query('SELECT * FROM post_denuncia WHERE id = ? LIMIT 1', $this->id)
				->get_record(array(
					'id' => Database_Query::FIELD_INT,
					'post_id' => Database_Query::FIELD_INT,
					'usuario_id' => Database_Query::FIELD_INT,
					'motivo' => Database_Query::FIELD_INT,
					'fecha' => Database_Query::FIELD_DATETIME,
					'estado' => Database_Query::FIELD_INT,
				));

			if (is_array($rst))
			{
				$this->data = $rst;
			}
		}

		return isset($this->data[$field]) ? $this->data[$field] : NULL;
	}

	/**
	 * Obtenemos una propiedad de la denuncia.
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Obtenemos el usuario que creó la denuncia.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Obtenemos el post que fue denunciado.
	 * @return Model_Post
	 */
	public function post()
	{
		return new Model_Post($this->get('post_id'));
	}
}
