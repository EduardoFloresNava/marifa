<?php
/**
 * recuperacion.php is part of Marifa.
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
 * @property-read int $id ID del elemento.
 * @property-read int $usuario_id ID del usuario dueño de la clave de activación/recuperación.
 * @property-read string $email E-Mail donde se envió el correo.
 * @property-read string $hash Hash de verificación de la acción.
 * @property-read Fechahora $fecha Fecha en la que se creo la clave.
 * @property-read int $tipo Tipo de clave.
 */
class Base_Model_Usuario_Recuperacion extends Model_Dataset {

	/**
	 * La clave enviada es para una activación de cuenta.
	 */
	const TIPO_ACTIVACION = 0;

	/**
	 * La clave enviada es para recuperar una cuenta.
	 */
	const TIPO_RECUPERACION = 1;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'usuario_recuperacion';

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
		'email' => Database_Query::FIELD_STRING,
		'hash' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME,
		'tipo' => Database_Query::FIELD_INT
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
	 * Cargamos según el hash.
	 * @param string $token Token a cargar.
	 */
	public function load_by_hash($token)
	{
		$this->load(array('hash' => $token));
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
	 * Creamos una clave de activación/recuperacion.
	 * @param int $usuario ID del usuario dueño de la clave.
	 * @param string $email E-Mail donde se envia el correo.
	 * @param int $tipo Tipo de token.
	 * @return string Token creado.
	 */
	public function crear($usuario, $email, $tipo)
	{
		// Genero el token.
		$token = md5(time());

		// Inserto la clave.
		$this->db->insert('INSERT INTO usuario_recuperacion (usuario_id, email, hash, fecha, tipo) VALUES (?, ?, ?, ?, ?)', array($usuario, $email, $token, date('Y/m/d H:i:s'), $tipo));

		return $token;
	}

	/**
	 * Limpiamos las claves viejas. Se consideran caducas cuando pasaron 24hs.
	 */
	public function limpiar()
	{
		$this->db->delete('DELETE FROM usuario_recuperacion WHERE fecha < ?', date('Y/m/d H:i:s', time() - 86400));
	}

	/**
	 * Verifico si un hash es válido.
	 * @param string $hash Hash a buscar.
	 * @param int $tipo Tipo de token.
	 * @return bool
	 */
	public function es_valido($hash, $tipo)
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_recuperacion WHERE fecha >= ? AND hash = ? AND tipo = ?', array(date('Y/m/d H:i:s', time() - 86400), $hash, $tipo))->get_var() > 0;
	}

	/**
	 * Borramos el token actual.
	 */
	public function borrar()
	{
		$this->db->query('DELETE FROM usuario_recuperacion WHERE id = ?', $this->id);
	}

	/**
	 * Eliminamos un token por su hash.
	 * @param int $hash Hash a eliminar.
	 */
	public function borrar_por_hash($hash = NULL)
	{
		$this->db->query('DELETE FROM usuario_recuperacion WHERE hash = ?', $hash);
	}

	/**
	 * Eliminamos los tokens de un usuario.
	 * @param int $usuario ID del usuario.
	 */
	public function borrar_por_usuario($usuario = NULL)
	{
		$this->db->query('DELETE FROM usuario_recuperacion WHERE usuario_id = ?', $usuario);
	}

}
