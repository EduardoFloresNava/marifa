<?php
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
 * @package		Marifa\Base
 * @subpackage  Model
 */
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Modelo de comentario de un post.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Post_Comentario extends Model_Dataset {

	//TODO: Agregar estados.

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'post_comentario';

	/**
	 * Clave primaria.
	 * @var array
	 */
	protected $primary_key = array('id' => NULL);

	/**
	 * Listado de campos y sus tipos.
	 */
	protected $fields = array(
		'id' => Database_Query::FIELD_INT,
		'post_id' => Database_Query::FIELD_INT,
		'usuario_id' => Database_Query::FIELD_INT,
		'fecha' => Database_Query::FIELD_DATETIME,
		'contenido' => Database_Query::FIELD_STRING,
		'estado' => Database_Query::FIELD_INT,
	);

	/**
	 * Cargamos un comentario.
	 * @param int $id ID del comentario.
	 */
	public function __construct($id)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
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
		return $this->db->update('UPDATE post_comentario SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
	}

	/**
	 * Verificamos si el usuario ha votado o no.
	 * @param int $usuario_id ID del usuario a verificar.
	 * @return bool
	 */
	public function ya_voto($usuario_id)
	{
		return $this->db->query('SELECT usuario_id FROM post_comentario_voto WHERE usuario_id = ? AND post_comentario_id = ? LIMIT 1', array($usuario_id, $this->primary_key['id']))->num_rows() > 0;
	}

	/**
	 * Agregamos el voto del usuario.
	 * @param int $usuario_id ID del usuario.
	 * @param bool $positivo TRUE para positivo, FALSE para negativo.
	 */
	public function votar($usuario_id, $positivo = TRUE)
	{
		$cantidad = $positivo ? 1 : -1;

		$this->db->insert('INSERT INTO post_comentario_voto (post_comentario_id, usuario_id, cantidad) VALUES (?, ?, ?)', array($this->primary_key['id'], $usuario_id, $cantidad));
	}

	/**
	 * Obtenemos la cantidad de votos del comentario.
	 * @return int
	 */
	public function cantidad_votos()
	{
		return $this->db->query('SELECT SUM(cantidad) FROM post_comentario_voto WHERE post_comentario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}
}
