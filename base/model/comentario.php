<?php
/**
 * categoria.php is part of Marifa.
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
 * Modelo de representación de comentarios de fotos y post.
 * Permite abstraer el comportamiento de los comentarios para su manejo de una
 * forma conjunta.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Comentario extends Model {

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
	 * Cantidad de comentarios en post y fotos.
	 * @param int $estado
	 * @return int
	 */
	public static function cantidad($estado = NULL)
	{
		if ($estado === NULL)
		{
			$c = Database::get_instance()->query('SELECT COUNT(*) FROM post_comentario')->get_var(Database_Query::FIELD_INT);
			$c += Database::get_instance()->query('SELECT COUNT(*) FROM foto_comentario')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			$c = Database::get_instance()->query('SELECT COUNT(*) FROM post_comentario WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
			$c += Database::get_instance()->query('SELECT COUNT(*) FROM foto_comentario WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
		}
		return $c;
	}

	/**
	 * Listado de comentarios existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @param int $estado Estado de los comentarios a incluir. NULL para todos.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10, $estado = NULL)
	{
		$start = ($pagina - 1) * $cantidad;

		if ($estado === NULL)
		{
			$rst = $this->db->query('(SELECT id, \'foto\' AS tipo, fecha FROM foto_comentario) UNION (SELECT id, \'post\' AS tipo, fecha FROM post_comentario) ORDER BY fecha DESC LIMIT '.$start.','.$cantidad);
		}
		else
		{
			$rst = $this->db->query('(SELECT id, \'foto\' AS tipo, fecha FROM foto_comentario WHERE estado = ?) UNION (SELECT id, \'post\' AS tipo, fecha FROM post_comentario WHERE estado = ?) ORDER BY fecha DESC LIMIT '.$start.','.$cantidad, array($estado, $estado));
		}

		// Seteo modo de obtención.
		$rst->set_fetch_type(Database_Query::FETCH_OBJ);
		$rst->set_cast_type(array('id' => Database_Query::FIELD_INT, 'tipo' => Database_Query::FIELD_STRING, 'fecha' => Database_Query::FIELD_STRING));

		$lst = array();
		foreach ($rst as $v)
		{
			if ($v->tipo == 'post')
			{
				$lst[] = new Model_Post_Comentario($v->id);
			}
			else
			{
				$lst[] = new Model_Foto_Comentario($v->id);
			}
		}
		return $lst;
	}

}
