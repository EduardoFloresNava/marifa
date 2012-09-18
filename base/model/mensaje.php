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
class Base_Model_Mensaje extends Model_Dataset {

	/**
	 * El mensaje es nuevo y no se ha visto.
	 */
	const ESTADO_NUEVO = 0;

	/**
	 * El mensaje se ha leido.
	 */
	const ESTADO_LEIDO = 1;

	/**
	 * En envio una respuesta al mensaje.
	 */
	const ESTADO_RESPONDIDO = 2;

	/**
	 * Se ha reenviado el mensaje.
	 */
	const ESTADO_REENVIADO = 3;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'mensaje';

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
		'emisor_id' => Database_Query::FIELD_INT,
		'receptor_id' => Database_Query::FIELD_INT,
		'estado' => Database_Query::FIELD_INT,
		'asunto' => Database_Query::FIELD_STRING,
		'contenido' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME,
		'padre_id' => Database_Query::FIELD_INT
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
	 * Quien creó el mensaje.
	 * @return Model_Usuario
	 */
	public function emisor()
	{
		return new Model_Usuario($this->get('emisor_id'));
	}

	/**
	 * A quien se encontraba dirigido el mensaje.
	 * @return Model_Usuario
	 */
	public function receptor()
	{
		return new Model_Usuario($this->get('receptor_id'));
	}

	/**
	 * Mensaje padre. En caso de no tener se devuelve NULL.
	 * @return Model_Mensaje
	 */
	public function padre()
	{
		if ($this->get('padre_id') != NULL)
		{
			return new Model_Mensaje($this->get('padre_id'));
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Listado de mensajes recibidos por el usuario.
	 * @param int $usuario_id ID del usuario del cual buscar los mensajes.
	 * @param int $pagina Página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function recibidos($usuario_id, $pagina = 1, $cantidad = 20)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM mensaje WHERE receptor_id = ? LIMIT '.$start.','.$cantidad, $usuario_id)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Mensaje($v);
		}
		return $lst;
	}

	/**
	 * Cantidad de mensaje recibidos por el usuario.
	 * @param int $usuario_id ID del usuario del cual contar los mensajes.
	 * @return int
	 */
	public function total_recibidos($usuario_id)
	{
		return $this->db->query('SELECT COUNT(*) FROM mensaje WHERE receptor_id = ?', $usuario_id)->get_pairs(Database_Query::FIELD_INT);
	}

	/**
	 * Listado de mensajes enviados por el usuario.
	 * @param int $usuario_id ID del usuario del cual buscar los mensajes.
	 * @param int $pagina Página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function enviados($usuario_id, $pagina = 1, $cantidad = 20)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM mensaje WHERE emisor_id = ? LIMIT '.$start.','.$cantidad, $usuario_id)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Mensaje($v);
		}
		return $lst;
	}

	/**
	 * Cantidad de mensaje enviados por el usuario.
	 * @param int $usuario_id ID del usuario del cual contar los mensajes.
	 * @return int
	 */
	public function total_enviados($usuario_id)
	{
		return $this->db->query('SELECT COUNT(*) FROM mensaje WHERE emisor_id = ?', $usuario_id)->get_pairs(Database_Query::FIELD_INT);
	}

	/**
	 * Enviamos un nuevo mensaje.
	 * @param int $emisor_id Emisor del mensaje.
	 * @param int $receptor_id Receptor del mensaje.
	 * @param string $asunto Asunto del mensaje.
	 * @param string $mensaje Contenido del mensaje.
	 * @param int $padre_id ID del mensaje padre.
	 * @return int
	 */
	public function enviar($emisor_id, $receptor_id, $asunto, $mensaje, $padre_id = NULL)
	{
		list($id,) = $this->db->insert('INSERT INTO mensaje (emisor_id, receptor_id, estado, asunto, contenido, fecha, padre_id) VALUES (?, ?, ?, ?, ?, ?, ?)',
				array($emisor_id, $receptor_id, 0, $asunto, $mensaje, date('Y/m/d H:i:s'), $padre_id));

		return $id;
	}

	/**
	 * Actualizamos el estado de un mensaje.
	 * @param int $estado Estado a setear.
	 * @return mixed
	 */
	public function actualizar_estado($estado)
	{
		$this->update_value('estado', $estado);
		return $this->db->update('UPDATE mensaje SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
	}

}
