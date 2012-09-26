<?php
/**
 * suspension.php is part of Marifa.
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
 * Suspensiones a los distintos usuarios.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Usuario_Suspension extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'usuario_suspension';

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
		'motivo' => Database_Query::FIELD_STRING,
		'inicio' => Database_Query::FIELD_DATETIME,
		'fin' => Database_Query::FIELD_DATETIME
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
	 * Moderador que creo la suspensión.
	 * @return Model_Usuario
	 */
	public function moderador()
	{
		return new Model_Usuario($this->get('moderador_id'));
	}

	/**
	 * Usuario que ha sido suspendido
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Segundos restantes para terminar.
	 * @return int
	 */
	public function restante()
	{
		return $this->get('fin')->getTimestamp() - time();
	}

	/**
	 *
	 * @param int $usuario ID del usuario a suspender.
	 * @param int $moderador ID del moderador que realiza la suspensión.
	 * @param string $asunto Motivo de la suspensión.
	 * @param int $fin Cuando finaliza la suspensión.
	 * @return int ID de la suspensión.
	 */
	public function nueva($usuario, $moderador, $asunto, $fin)
	{
		// Creo la suspensión.
		list($id,) = $this->db->insert('INSERT INTO usuario_suspension (usuario_id, moderador_id, motivo, inicio, fin) VALUES (?, ?, ?, ?, ?)', array($usuario, $moderador, $asunto, date('Y/m/d H:i:s'), date('Y/m/d H:i:s', $fin)));

		// Actualizo el estado del usuario.
		$this->db->update('UPDATE usuario SET estado = ? WHERE id = ?', array(Model_Usuario::ESTADO_SUSPENDIDA, $usuario));

		return $id;
	}

	/**
	 * Anulamos la suspensión.
	 * @return mixed
	 */
	public function anular()
	{
		return $this->db->query('DELETE FROM usuario_suspension WHERE id = ?', $this->primary_key['id']);
	}
}
