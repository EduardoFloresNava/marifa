<?php
/**
 * aviso.php is part of Marifa.
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
 * Avisos a los distintos usuarios.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Usuario_Aviso extends Model_Dataset {

	/**
	 * Se envió pero el usuario no lo ha visto.
	 */
	const ESTADO_NUEVO = 0;

	/**
	 * El usuario ya vió el aviso.
	 */
	const ESTADO_VISTO = 1;

	/**
	 * El usuario ha borrado el aviso.
	 */
	const ESTADO_OCULTO = 2;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'usuario_aviso';

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
		'moderador_id' => Database_Query::FIELD_INT,
		'asunto' => Database_Query::FIELD_STRING,
		'contenido' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME,
		'estado' => Database_Query::FIELD_INT
	);

	/**
	 * Constructor de la clase.
	 * @param int $id Id del rango.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
	}

	/**
	 * Moderador que envió el aviso.
	 * @return Model_Usuario
	 */
	public function moderador()
	{
		return new Model_Usuario($this->get('moderador_id'));
	}

	/**
	 * Usuario que ha sido advertido.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Obtenemos el listado de advertencias del usuario.
	 * @param int $usuario_id ID del usuario del que se quieren obtener los avisos.
	 * @return array
	 */
	public function advertencias($usuario_id)
	{
		$rst = $this->db->query('SELECT id FROM usuario_aviso WHERE usuario_id = ?', $usuario_id)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Usuario_Aviso($v);
		}

		return $lst;
	}

	/**
	 * Agregamos un nuevo aviso.
	 * @param int $usuario ID del usuario al que se le envia la advertencia.
	 * @param int $moderador Usuario que envia la advertencia.
	 * @param string $asunto Asunto de la advertencia.
	 * @param string $contenido Contenido de la advertencia.
	 * @return int ID del suceso.
	 */
	public function nueva($usuario, $moderador, $asunto, $contenido)
	{
		list($id,) = $this->db->insert('INSERT INTO usuario_aviso (usuario_id, moderador_id, asunto, contenido, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)', array($usuario, $moderador, $asunto, $contenido, date('Y/m/d H:i:s'), self::ESTADO_NUEVO));
		return $id;
	}

}
