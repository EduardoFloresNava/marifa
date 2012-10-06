<?php
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
 * @package		Marifa\Base
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo de una denuncia de un post
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Post_Denuncia extends Model_Dataset {

	/**
	 * La denuncia se encuentra en espera de ser resuelta.
	 */
	const ESTADO_PENDIENTE = 0;

	/**
	 * La denuncia no era válida.
	 */
	const ESTADO_RECHAZADA = 1;

	/**
	 * La denuncia era válida y se aplicó la sanción de forma correcta.
	 */
	const ESTADO_APLICADA = 2;

	/**
	 * Post identicó a otro.
	 */
	const TIPO_REPOST = 0;

	/**
	 * El post es spam.
	 */
	const TIPO_SPAM = 1;

	/**
	 * El post contiene links muertos.
	 */
	const TIPO_LINKS_MUERTOS = 2;

	/**
	 * El post es racista o irrespetuoso.
	 */
	const TIPO_RACISTA_IRRESPETUOSO = 3;

	/**
	 * El post contiene información personal.
	 */
	const TIPO_INFORMACION_PERSONAL = 4;

	/**
	 * El post tiene su titulo en mayusculas.
	 */
	const TIPO_TITULO_MAYUSCULA = 5;

	/**
	 * El post tiene contenido pedofilo.
	 */
	const TIPO_PEDOFILIA = 6;

	/**
	 * El post es gore o asqueroso.
	 */
	const TIPO_ASQUEROSO = 7;

	/**
	 * La fuente del post es incorrecta.
	 */
	const TIPO_FUENTE = 8;

	/**
	 * El post es pobre o crap.
	 */
	const TIPO_POBRE = 9;

	/**
	 * El sitio no es un foro.
	 */
	const TIPO_FORO = 10;

	/**
	 * El post con cumple con el protocolo.
	 */
	const TIPO_PROTOCOLO = 11;

	/**
	 * Se especifica una razón diferente.
	 */
	const TIPO_PERSONALIZADA = 12;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'post_denuncia';

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
		'motivo' => Database_Query::FIELD_INT,
		'comentario' => Database_Query::FIELD_DATETIME,
		'fecha' => Database_Query::FIELD_DATETIME,
		'estado' => Database_Query::FIELD_INT,
	);

	/**
	 * Cargamos una denuncia.
	 * @param int $id ID de la denuncia.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
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

	/**
	 * Listado de denuncias de posts existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de denuncias por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10, $estado = NULL)
	{
		$start = ($pagina - 1) * $cantidad;

		// Verifico si hace falta el estado o no.
		if ($estado === NULL)
		{
			$rst = $this->db->query('SELECT id FROM post_denuncia ORDER BY fecha LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM post_denuncia WHERE estado = ? ORDER BY fecha LIMIT '.$start.','.$cantidad, $estado)->get_pairs(Database_Query::FIELD_INT);
		}

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post_Denuncia($v);
		}
		return $lst;
	}

	/**
	 * Cantidad total de denuncias.
	 * @param int $estado Estado que deben tener las denuncias para contar.
	 * @return int
	 */
	public static function cantidad($estado = NULL)
	{
		if ($estado === NULL)
		{
			return Database::get_instance()->query('SELECT COUNT(*) FROM post_denuncia')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return Database::get_instance()->query('SELECT COUNT(*) FROM post_denuncia WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Actualizamos el estado de la denuncia.
	 * @param int $estado Estado a aplicar.
	 */
	public function actualizar_estado($estado)
	{
		$this->db->update('UPDATE post_denuncia SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
	}
}
