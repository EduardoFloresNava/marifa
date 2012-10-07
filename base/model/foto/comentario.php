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
 * Modelo de los comentarios de las fotos.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read int $id ID del comentario.
 * @property-read int $foto_id ID de la foto donde se encuentra el comentario.
 * @property-read int $usuario_id ID del usuario que creó el comentario.
 * @property-read Fechahora $fech Cuando se creó el comentario. En caso de edición se debe actualizar esta fecha.
 * @property-read string $contenido Contenido del comentario.
 * @property-read int $estado Estado del comentario.
 */
class Base_Model_Foto_Comentario extends Model_Dataset {

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
	protected $table = 'foto_comentario';

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
		'foto_id' => Database_Query::FIELD_INT,
		'usuario_id' => Database_Query::FIELD_INT,
		'comentario' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME
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
	 * Obtenemos el usuario dueño del comentario.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Obtenemos la foto a la que pertenece el comentario.
	 */
	public function foto()
	{
		return new Model_Foto($this->get('foto_id'));
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
			return Database::get_instance()->query('SELECT COUNT(*) FROM foto_comentario')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return Database::get_instance()->query('SELECT COUNT(*) FROM foto_comentario WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
		}
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
		$rst = Database::get_instance()->query('SELECT estado, COUNT(*) AS total FROM foto_comentario GROUP BY estado')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));

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
			$rst = $this->db->query('SELECT id FROM foto_comentario ORDER BY fecha LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM foto_comentario WHERE estado = ? ORDER BY fecha LIMIT '.$start.','.$cantidad, $estado)->get_pairs(Database_Query::FIELD_INT);
		}

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Foto_Comentario($v);
		}
		return $lst;
	}

}
