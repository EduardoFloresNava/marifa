<?php
/**
 * mensaje.php is part of Marifa.
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
 * Modelo de mensajes entre usuarios.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 *
 * @property-read int $id ID del mensaje.
 * @property-read int $emisor_id ID del usuario que envia el mensaje.
 * @property-read int $receptor_id ID del usuario para el cual se dirige.
 * @property-read int $estado Estado del mensaje.
 * @property-read string $asunto Asunto del mensaje.
 * @property-read string $contenido Contenido del mensaje.
 * @property-read Fechahora $fecha Fecha en la cual se envió el mensaje.
 * @property-read int $padre_id ID del mensaje padre.
 */
class Base_Model_Noticia extends Model_Dataset {

	/**
	 * La noticia no es visible por nadie.
	 */
	const ESTADO_OCULTO = 0;

	/**
	 * Todos los usuarios ven la noticia.
	 */
	const ESTADO_VISIBLE = 1;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'noticia';

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
		'contenido' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME,
		'estado' => Database_Query::FIELD_INT
	);

	/**
	 * Constructor del mensaje.
	 * @param int $id ID del mensaje a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
	}

	/**
	 * Obtenemos una noticia a mostrar.
	 */
	public static function get_active()
	{
		$id = Database::get_instance()->query('SELECT id FROM noticia WHERE estado = ? LIMIT 1', self::ESTADO_VISIBLE)->get_var(Database_Query::FIELD_INT);

		if ($id !== NULL)
		{
			return new Model_Noticia($id);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Quien creó el mensaje.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Hacemos visible la noticia.
	 */
	public function activar()
	{
		$this->db->update('UPDATE noticia SET estado = ? WHERE id = ?', array(self::ESTADO_VISIBLE, $this->primary_key['id']));
		parent::update_value('estado', self::ESTADO_VISIBLE);
	}

	/**
	 * Ocultamos la noticia.
	 */
	public function desactivar()
	{
		$this->db->update('UPDATE noticia SET estado = ? WHERE id = ?', array(self::ESTADO_OCULTO, $this->primary_key['id']));
		parent::update_value('estado', self::ESTADO_OCULTO);
	}

	/**
	 * Borramos la noticia
	 * @return bool Si se borro correctamente.
	 */
	public function eliminar()
	{
		return $this->db->delete('DELETE FROM noticia WHERE id = ?', $this->primary_key['id']) > 0;
	}

	/**
	 * Creamos una nueva noticia.
	 * @param int $usuario ID del usuario que publica la noticia.
	 * @param string $contenido Contenido de la noticia.
	 * @param int $estado Estado de la noticia.
	 * @return int
	 */
	public function nuevo($usuario, $contenido, $estado = self::ESTADO_OCULTO)
	{
		list($id,) = $this->db->insert('INSERT INTO noticia (usuario_id, contenido, fecha, estado) VALUES (?, ?, ?, ?)', array($usuario, $contenido, date('Y/m/d H:i:s'), $estado));
		return $id;
	}

	/**
	 * Listado de noticias activas
	 * @param int $pagina Número de página a mostrar
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function activas($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM noticia WHERE estado = ? ORDER BY fecha DESC LIMIT '.$start.','.$cantidad, self::ESTADO_VISIBLE)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Noticia($v);
		}
		return $lst;
	}

	/**
	 * Cantidad de noticias activas.
	 * @return int
	 */
	public function total_activas()
	{
		return $this->db->query('SELECT COUNT(*) FROM noticia WHERE estado = ?', self::ESTADO_VISIBLE)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Listado de noticias existente.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de noticias por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM noticia ORDER BY estado DESC, fecha DESC LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Noticia($v);
		}
		return $lst;
	}

	/**
	 * Cantidad de noticias.
	 * @return int
	 */
	public function total()
	{
		return $this->db->query('SELECT COUNT(*) FROM noticia')->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Actualizamos el contenido de la noticia.
	 * @param string $contenido
	 */
	public function actualizar_contenido($contenido)
	{
		$this->db->update('UPDATE noticia SET contenido = ? WHERE id = ?', array($contenido, $this->primary_key['id']));
		parent::update_value('contenido', $contenido);
	}

	/**
	 * Eliminamos todas las noticias.
	 */
	public function eliminar_todas()
	{
		$this->db->query('DELETE FROM noticia');
	}

	/**
	 * Desactivamos todas las noticias
	 */
	public function desactivar_todas()
	{
		$this->db->query('UPDATE noticia SET estado = ?', self::ESTADO_OCULTO);
	}
}
