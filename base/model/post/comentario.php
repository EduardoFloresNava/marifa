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
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo de comentario de un post.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read int $id ID del comentario.
 * @property-read int $post_id ID del post donde se encuentra el comentario.
 * @property-read int $usuario_id ID del usuario que creó el comentario.
 * @property-read Fechahora $fech Cuando se creó el comentario. En caso de edición se debe actualizar esta fecha.
 * @property-read string $contenido Contenido del comentario.
 * @property-read int $estado Estado del comentario.
 */
class Base_Model_Post_Comentario extends Model_Dataset {

	/**
	 * Comentario visible para todos.
	 */
	const ESTADO_VISIBLE = 0;

	/**
	 * Comentario oculto por acción de moderación o del usuario.
	 */
	const ESTADO_OCULTO = 1;

	/**
	 * Comentario eliminado. Existe por cuestión de dependencias.
	 */
	const ESTADO_BORRADO = 2;

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
		'estado' => Database_Query::FIELD_INT
	);

	/**
	 * Cargamos un comentario.
	 * @param int $id ID del comentario.
	 */
	public function __construct($id = NULL)
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
	 * Tipo de voto realizado por el usuario.
	 * @param int $usuario_id ID del usuario del que obtener el tipo de voto.
	 * @return bool|NULL Positivo (true), Negativo (false) o Sin voto (NULL)
	 */
	public function tipo_voto($usuario_id)
	{
		$t = $this->db->query('SELECT cantidad FROM post_comentario_voto WHERE usuario_id = ? AND post_comentario_id = ? LIMIT 1', array($usuario_id, $this->primary_key['id']))->get_var(Database_Query::FIELD_INT);

		return ($t === 1) ? TRUE : (($t === -1) ? FALSE : NULL);
	}

	/**
	 * Agregamos el voto del usuario.
	 * @param int $usuario_id ID del usuario.
	 * @param bool $positivo TRUE para positivo, FALSE para negativo.
	 */
	public function votar($usuario_id, $positivo = TRUE)
	{
		$this->db->insert('INSERT INTO post_comentario_voto (post_comentario_id, usuario_id, cantidad) VALUES (?, ?, ?)', array($this->primary_key['id'], $usuario_id, $positivo ? 1 : -1));
	}

	/**
	 * Obtenemos la cantidad de votos del comentario.
	 * @return int
	 */
	public function cantidad_votos()
	{
		return $this->db->query('SELECT SUM(cantidad) FROM post_comentario_voto WHERE post_comentario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtengo cantidad de posts por estado.
	 * @return array
	 */
	public static function cantidad_agrupados()
	{
		// Arreglo de estados.
		$categorias = array(
			0 => 'visible',
			1 => 'oculto',
			2 => 'borrado'
		);

		// Obtengo grupos.
		$rst = Database::get_instance()->query('SELECT estado, COUNT(*) AS total FROM post_comentario GROUP BY estado')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));

		// Armo arreglo resultado.
		$lst = array();
		foreach ($categorias as $k => $v)
		{
			$lst[$v] = isset($rst[$k]) ? $rst[$k] : 0;
		}

		// Calculo total.
		$lst['total'] = array_sum($lst);

		return $lst;
	}

	/**
	 * Obtenemos el listado de los últimos comentarios.
	 * @param int $pagina Número de página empezando en 1.
	 * @param int $cantidad Cantidad de post por página.
	 * @return array
	 */
	public static function obtener_ultimos($pagina = 1, $cantidad = 10)
	{
		// Primer elemento a devolver.
		$inicio = $cantidad * ($pagina - 1);

		// Obtenemos el listado.
		$rst = Database::get_instance()->query('SELECT post_comentario.id FROM post_comentario INNER JOIN post ON post_comentario.post_id = post.id WHERE post.estado = 0 ORDER BY post_comentario.fecha DESC LIMIT '.$inicio.', '.$cantidad)->get_pairs(Database_Query::FIELD_INT);

		// Armamos la lista.
		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post_Comentario($v);
		}

		return $lst;
	}

	/**
	 * Cantidad de comentarios.
	 * @param int $estado Si contamos algun estado en particular o NULL para todos.
	 * @return int
	 */
	public static function cantidad($estado = NULL)
	{
		if ($estado === NULL)
		{
			return Database::get_instance()->query('SELECT COUNT(*) FROM post_comentario')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return Database::get_instance()->query('SELECT COUNT(*) FROM post_comentario WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Listado de comentarios existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de comentarios por página.
	 * @param int $estado Estado a obtener. NULL para cualquiera.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10, $estado = NULL)
	{
		$start = ($pagina - 1) * $cantidad;

		if ($estado === NULL)
		{
			$rst = $this->db->query('SELECT id FROM post_comentario ORDER BY fecha LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM post_comentario WHERE estado = ? ORDER BY fecha LIMIT '.$start.','.$cantidad, $estado)->get_pairs(Database_Query::FIELD_INT);
		}

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post_Comentario($v);
		}
		return $lst;
	}
}
