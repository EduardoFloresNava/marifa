<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * comentario.php is part of Marifa.
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
 * Modelo de comentario de un post.
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Post_Comentario extends Model {

	//TODO: Agregar estados.

	/**
	 * ID del comentario
	 * @var int
	 */
	protected $id;

	/**
	 * Información del comentario.
	 * @var array
	 */
	protected $data;

	/**
	 * Cargamos un comentario.
	 * @param int $id ID del comentario.
	 */
	public function __construct($id)
	{
		parent::__construct();

		$this->id = $id;
	}

	/**
	 * Obtenemos el valor de un campo del comentario.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		if ($this->data === NULL)
		{
			// Obtenemos los campos.
			$rst = $this->db->query('SELECT * FROM post_comentario WHERE id = ? LIMIT 1', $this->id)
				->get_record(array(
					'id' => Database_Query::FIELD_INT,
					'post_id' => Database_Query::FIELD_INT,
					'usuario_id' => Database_Query::FIELD_INT,
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
	 * Obtenemos una propiedad del comentario.
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Post donde se realizó el comentario
	 * @return Model_Post
	 */
	public function post()
	{
		return new Model_Post($this->get('post_id'));
	}

	/**
	 * Usuario que realizó el comentario.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Actualizamos el estado del comentario.
	 * @param int $estado Estado a colocar.
	 */
	public function actualizar_estado($estado)
	{
		return $this->db->update('UPDATE post_comentario SET estado = ? WHERE id = ?', array($estado, $this->id));
	}

	/**
	 * Verificamos si el usuario ha votado o no.
	 * @param int $usuario_id ID del usuario a verificar.
	 * @return bool
	 */
	public function ya_voto($usuario_id)
	{
		$this->db->query('SELECT usuario_id FROM post_comentario_voto WHERE usuario_id = ? AND post_comentario_id = ? LIMIT 1', array($usuario_id, $this->id))->num_rows() > 0;
	}

	/**
	 * Agregamos el voto del usuario.
	 * @param int $usuario_id ID del usuario.
	 * @param bool $positivo TRUE para positivo, FALSE para negativo.
	 */
	public function votar($usuario_id, $positivo = TRUE)
	{
		$cantidad = $positivo ? 1 : -1;

		$this->db->insert('INSERT INTO post_comentario_voto (post_comentario_id, usuario_id, cantidad) VALUES (?, ?, ?)', array($this->id, $usuario_id, $cantidad));
	}

	/**
	 * Obtenemos la cantidad de votos del comentario.
	 * @return int
	 */
	public function cantidad_votos()
	{
		return $this->db->query('SELECT SUM(cantidad) FROM post_comentario_voto WHERE post_comentario_id = ?', $this->id)->get_var(Database_Query::FIELD_INT);
	}
}
