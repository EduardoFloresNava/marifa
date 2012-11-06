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
 * @property-read int $id ID de la suspensión.
 * @property-read int $usuario_id ID del usuario que fue suspendido.
 * @property-read int $moderador_id ID del moderador que ha realizado la suspención.
 * @property-read string $motivo Motivo por el cual se realiza la suspensión.
 * @property-read Fechahora $inicio Fecha en la cual inicia la suspensión.
 * @property-read Fechahora $fin Fecha en la cual termina la suspensión.
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
	 * Borramos las suspensiones terminadas.
	 */
	public static function clean()
	{
		// Borramos suspensiones caducas.
		Database::get_instance()->delete('DELETE FROM usuario_suspension WHERE fin < ?', date('Y/m/d H:i:s'));

		// Cargamos el listado.
		$rst = Database::get_instance()->query('SELECT usuario.id FROM usuario LEFT JOIN usuario_suspension ON usuario.id = usuario_suspension.usuario_id WHERE usuario_suspension.usuario_id IS NULL AND usuario.estado = 2')->get_pairs(Database_Query::FIELD_INT);

		if (is_array($rst) && count($rst) > 0)
		{
			// Actualizamos los estados.
			Database::get_instance()->update('UPDATE usuario SET estado = 1 WHERE id IN ('.implode(', ', array_values($rst)).')');
		}
	}

	/**
	 * Creamos una nueva suspensión para un usuario.
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

	/**
	 * Listado de suspenciones existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de suspensiones por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;

		$rst = $this->db->query('SELECT id FROM usuario_suspension WHERE fin > ? ORDER BY fin LIMIT '.$start.','.$cantidad, date('Y/m/d H:i:s'))->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Usuario_Suspension($v);
		}
		return $lst;
	}

	/**
	 * Cantidad total de suspensiones.
	 * @return int
	 */
	public static function cantidad()
	{
		return Database::get_instance()->query('SELECT COUNT(*) FROM usuario_suspension WHERE fin > ?', date('Y/m/d H:i:s'))->get_var(Database_Query::FIELD_INT);
	}
}
