<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * usuario.php is part of Marifa.
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
 * @package		Marifa/Base
 * @subpackage  Model
 */

/**
 * Modelo del usuario.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Usuario extends Model {

	/**
	 * Cuenta sin activar por medio de email.
	 */
	const ESTADO_PENDIENTE = 0;

	/**
	 * Cuenta activa y lista para usar.
	 */
	const ESTADO_ACTIVA = 1;

	/**
	 * Cuenta con una suspención temporal.
	 */
	const ESTADO_SUSPENDIDA = 2;

	/**
	 * Cuenta baneada permanentemente.
	 */
	const ESTADO_BANEADA = 3;

	/**
	 * ID del usuario cargado por el modelo.
	 * @var int
	 */
	protected $id;

	/**
	 * Arreglo con la información del usuario.
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor del modelo
	 * @param int $id ID del usuario a cargar.
	 */
	public function __construct($id = NULL)
	{
		// Iniciamos el modelo.
		parent::__construct();

		// Seteamos ID del usuario.
		$this->id = $id;
	}

	/**
	 * Obtenemos el valor de un campo del usuario.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		if ($this->data === NULL)
		{
			// Obtenemos los campos.
			$rst = $this->db->query('SELECT * FROM usuario WHERE id = ? LIMIT 1', $this->id)->get_record();

			if (is_array($rst))
			{
				$this->data = $rst;
			}
		}

		return isset($this->data[$field]) ? $this->data[$field] : NULL;
	}

	/**
	 * Obtenemos una propiedad del usuario.
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Iniciamos sessión con un usuario.
	 * @param string $mail E-Mail
	 * @param string $password Contraseña
	 * @return int Estado de la cuenta. -1 Si los datos son inválidos.
	 */
	public function login($mail, $password)
	{
		$rst = $this->db->query('SELECT id, nick, password, email, estado FROM usuario WHERE email = ? OR nick = ? LIMIT 1', array($mail, $mail));

		// Verificamos que exista el usuario.
		if ( $rst->num_rows() > 0)
		{
			// Obtenemos la información.
			$data = $rst->get_record(Database_Query::FETCH_ASSOC, array('id' => Database_Query::FIELD_INT, 'estado' => Database_Query::FIELD_INT));

			// Verificamos la contraseña.
			$enc = new Phpass(8, FALSE);
			if ( ! $enc->CheckPassword($password, $data['password']) == TRUE)
			{
				return -1;
			}

			// Verificamos el estado.
			switch ($data['estado'])
			{
				case self::ESTADO_ACTIVA: // Cuenta activa.
					// Iniciamos la sessión.
					Session::set('usuario_id', $data['id']);

					// Seteamos el usuario actual.
					$this->id = $data['id'];

					//TODO: Verificar la compatibilidad de fechas del servidor de base de datos y el web.

					// Actualizamos el inicio de session.
					$this->db->update('UPDATE usuario SET lastlogin = NOW(), lastactive = NOW(), lastip = ? WHERE id = ?', array(ip2long(IP::getIpAddr()), $this->id));

					break;
				case self::ESTADO_PENDIENTE:  // Cuenta por activar.
				case self::ESTADO_SUSPENDIDA: // Cuenta suspendida.
					//TODO: Verificar si no ha terminado.
				case self::ESTADO_BANEADA:    // Cuenta baneada.
					break;
				default:
					throw new Exception("El estado del usuario {$data['id']} es {$data['estado']} y no se puede manejar.");
			}

			// Informamos el estado.
			return $data['estado'];
		}
		else
		{
			// No existe el usuario.
			return -1;
		}
	}

	/**
	 * Verificamos si existe un usuario con ese email.
	 * @param string $email E-mail a buscar.
	 * @return bool
	 */
	public function exists_email($email)
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario WHERE email = ?', $email)->get_var(Database_Query::FIELD_INT) > 0;
	}

	/**
	 * Verificamos si ese nick está disponible.
	 * @param string $nick Nick a buscar.
	 * @return bool
	 */
	public function exists_nick($nick)
	{
		// Buscamos en los actuales.
		$exists = $this->db->query('SELECT COUNT(*) FROM usuario WHERE nick = ?', $nick)->get_var(Database_Query::FIELD_INT) > 0;

		if ( ! $exists)
		{
			// Buscamos en los utilizados.
			return $this->db->query('SELECT COUNT(*) FROM usuario_nick WHERE nick = ?', $nick)->get_var(Database_Query::FIELD_INT) > 0;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Creamos una nueva cuenta de usuario.
	 * @param string $nick Nick del usuario
	 * @param string $email E-Mail del usuario.
	 * @param string $password Contraseña del usuario.
	 * @return int|bool FALSE si no se pudo crear. En caso de ser correcto el ID
	 * del usuario insertado o NULL si no es soportado por el motor.
	 * @throws Exception
	 */
	public function register($nick, $email, $password)
	{
		// Verificamos el email.
		if ($this->exists_email($email))
		{
			throw new Exception('Ya existe un usuario con ese e-mail.');
		}

		// Verificamos el nick.
		if ($this->exists_nick($nick))
		{
			throw new Exception('Ya existe un usuario con ese nick.');
		}

		// Codificamos la contraseña.
		$enc = new Phpass(8, FALSE);
		$enc_password = $enc->HashPassword($password);
		unset($enc);

		// Creamos arreglo con los datos.
		//TODO: Agregar rango.
		$info = array($nick, $enc_password, $email, 0, 10, 10, date('Y/m/d H:i:s'), 0);

		// Creamos la cuenta.
		list ($id, $cant) = $this->db->insert('INSERT INTO usuario (nick, password, email, rango, puntos, puntos_disponibles, registro, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', array_values($info));

		return $cant > 0 ? $id : FALSE;
	}

}
