<?php
/**
 * baneo.php is part of Marifa.
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
class Base_Model_Usuario_Baneo extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'usuario_baneo';

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
		'tipo' => Database_Query::FIELD_INT,
		'razon' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME
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
	 * Quitamos un baneo.
	 */
	public function borrar()
	{
		$this->db->update('UPDATE usuario SET estado = ? WHERE id = ?', array(Model_Usuario::ESTADO_ACTIVA, $this->get('usuario_id')));
		return $this->db->delete('DELETE FROM usuario_baneo WHERE id = ?', $this->primary_key['id']);
	}

	/**
	 * Baneamos a un usuario.
	 * @param int $usuario ID del usuario a banear.
	 * @param int $moderador ID del moderador a banear.
	 * @param int $tipo Tipo de baneo.
	 * @param string $razon Razon del baneo.
	 * @return int
	 */
	public function nuevo($usuario, $moderador, $tipo, $razon)
	{
		list($id,) = $this->db->insert('INSERT INTO usuario_baneo (usuario_id, moderador_id, tipo, razon, fecha) VALUES (?, ?, ?, ?, ?)', array($usuario, $moderador, $tipo, $razon, date('Y/m/d H:i:s')));
		$this->db->update('UPDATE usuario SET estado = ? WHERE id = ?', array(Model_Usuario::ESTADO_BANEADA, $usuario));
		return $id;
	}

}
