<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * foto.php is part of Marifa.
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
 * Modelo de las fotos.
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Foto extends Model {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'foto';

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
		'usuario_id' => Database_Query::FIELD_INT,
		'creacion' => Database_Query::FIELD_DATETIME,
		'titulo' => Database_Query::FIELD_STRING,
		'descripcion' => Database_Query::FIELD_STRING,
		'url' => Database_Query::FIELD_STRING,
		'estado' => Database_Query::FIELD_INT,
		'ultima_visita' => Database_Query::FIELD_DATETIME,
		'visitas' => Database_Query::FIELD_INT
	);

	/**
	 * Constructor del post.
	 * @param int $id ID del post a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
	}

	/**
	 * Obtenemos el usuario dueño de la foto.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Agregamos una nueva visita.
	 */
	public function agregar_visita()
	{
		$this->db->update('UPDATE foto SET visitas = visitas + 1, ultima_visita = ? WHERE id = ?', array(date('Y/m/d H:i:s'), $this->primary_key['id']));

		// Invalidamos información para su nueva carga.
		if (is_array($this->data))
		{
			$this->data = NULL;
		}
	}

	/**
	 * Cantidad de votos de la foto.
	 * @return int
	 */
	public function votos()
	{
		return $this->db->query('SELECT COUNT(*) FROM foto_voto WHERE foto_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si el usuario ha votado o no.
	 * @param int $usuario_id ID del usuario a verificar.
	 * @return bool
	 */
	public function ya_voto($usuario_id)
	{
		return $this->db->query('SELECT foto_id FROM foto_voto WHERE foto_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Agregamos el voto del usuario a la foto.
	 * @param int $usuario_id ID del usuario que va a votar.
	 */
	public function votar($usuario_id)
	{
		$this->db->insert('INSERT INTO foto_voto (foto_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Cantidad de favoritos que tiene la foto.
	 * @return int
	 */
	public function favoritos()
	{
		return $this->db->query('SELECT COUNT(*) FROM foto_favorito WHERE foto_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si ya tiene la foto como favorita.
	 * @param int $usuario_id ID del usuario a comprobar.
	 * @return bool
	 */
	public function es_favorito($usuario_id)
	{
		return $this->db->query('SELECT foto_id FROM foto_favorito WHERE foto_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Agregamos la foto a los favoritos del usuario
	 * @param int $usuario_id ID del usuario que pone la foto como favorita.
	 */
	public function agregar_favorito($usuario_id)
	{
		$this->db->insert('INSERT INTO foto_favorito (foto_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Obtenemos el listado de comentarios.
	 * @return array
	 */
	public function comentarios()
	{
		//TODO: estado de los comentarios de las fotos. Agregarlo a la base de datos.
		$rst = $this->db->query('SELECT id FROM foto_comentario WHERE foto_id = ?', $this->primary_key['id']);
		$rst->set_cast_type(array('id' => Database_Query::FIELD_INT));

		$lst = array();
		foreach($rst as $r)
		{
			$lst[] = new Model_Foto_Comentario($r['id']);
		}

		return $lst;
	}

	/**
	 * Comentamos en una foto.
	 * @param string $comentario Comentario a insertar.
	 * @param int $usuario_id ID del usuario que comenta la foto.
	 */
	public function comentar($comentario, $usuario_id)
	{
		$this->db->insert('INSERT INTO foto_comentario (foto_id, usuario_id, comentario, fecha) VALUES (?, ?, ?, ?)', array($this->primary_key['id'], $usuario_id, $comentario, date('Y/m/d H:i:s')));
	}

	/**
	 * Actualizamos el estado de una foto.
	 * @param int $estado Estado a colocarle a la foto.
	 */
	public function actualizar_estado($estado)
	{
		//TODO: Constantes de estado.
		$this->db->update('UPDATE foto SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
	}

	/**
	 * Creamos una nueva foto.
	 * @param int $usuario_id ID del usuario que crea la foto.
	 * @param string $titulo Título de la foto.
	 * @param string $descripcion Descripción de la foto.
	 * @param string $url URL de la foto.
	 * @return bool
	 */
	public function crear($usuario_id, $titulo, $descripcion, $url)
	{
		//TODO: ver estado.
		list ($id, $c) = $this->db->insert('INSERT INTO foto (usuario_id, creacion, titulo, descripcion, url, estado, ultima_visita, visitas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', array($usuario_id, date('Y/m/d H:i:s'), $titulo, $descripcion, $url, 0,  NULL, 0));

		if ($c > 0)
		{
			$this->primary_key['id'] = $id;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Borramos la foto del comentario.
	 */
	public function borrar()
	{
		//TODO: borrar votos y favoritos.
		$this->db->delete('DELETE FROM foto_voto WHERE foto_id = ?', $this->primary_key['id']);
		$this->db->delete('DELETE FROM foto_favorito WHERE foto_id = ?', $this->primary_key['id']);
		$this->db->delete('DELETE FROM foto WHERE id = ?', $this->primary_key['id']);
	}
}
