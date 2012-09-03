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
class Base_Model_Usuario extends Model_Dataset {

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
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'usuario';

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
		'nick' => Database_Query::FIELD_STRING,
		'password' => Database_Query::FIELD_STRING,
		'email' => Database_Query::FIELD_STRING,
		'rango' => Database_Query::FIELD_INT,
		'puntos' => Database_Query::FIELD_INT,
		'puntos_disponibles' => Database_Query::FIELD_INT,
		'registro' => Database_Query::FIELD_DATETIME,
		'lastlogin' => Database_Query::FIELD_DATETIME,
		'lastactive' => Database_Query::FIELD_DATETIME,
		'lastip' => Database_Query::FIELD_INT,
		'estado' => Database_Query::FIELD_INT
	);

	/**
	 * Constructor del modelo
	 * @param int $id ID del usuario a cargar.
	 */
	public function __construct($id = NULL)
	{
		// Iniciamos el modelo.
		parent::__construct();

		// Seteamos ID del usuario.
		$this->primary_key['id'] = $id;
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
					$this->primary_key['id'] = $data['id'];

					// Actualizamos el inicio de session.
					$this->db->update('UPDATE usuario SET lastlogin = ?, lastactive = ?, lastip = ? WHERE id = ?', array(date('Y/m/d H:i:s'), date('Y/m/d H:i:s'), ip2long(IP::getIpAddr()), $this->primary_key['id']));

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
	 * Actualizamos el nick del usuario reservando su exclusividad.
	 * @param string $nick Nick a utilizar.
	 * @throws Exception El nick está ocupado.
	 */
	public function cambiar_nick($nick)
	{
		// Verificamos que no lo tenga otro usuario.
		if ($this->db->query('SELECT id FROM usuario WHERE nick = ? AND id != ? LIMIT 1', array($nick, $this->primary_key['id']))->num_rows() > 0)
		{
			throw new Exception('El nick está ocupado por otro usuario');
		}

		// Verificamos que no lo tenga ocupado otro usuario.
		if ($this->db->query('SELECT nick FROM usuario_nick WHERE nick = ? AND usuario_id != ? LIMIT 1', array($nick, $this->primary_key['id']))->num_rows() > 0)
		{
			throw new Exception('El nick está reservado por otro usuario');
		}

		// Obtenemos el nick actual.
		$actual = $this->get('nick');

		// Agregamos la actualización al historial.
		$this->db->insert('INSERT INTO usuario_nick (usuario_id, nick, fecha) VALUES (?, ?, ?)', array($this->primary_key['id'], $actual, date('Y/m/d H:i:s')));

		// Actualizamos el nick del usuario.
		$this->db->update('UPDATE usuario SET nick = ? WHERE id = ?', array($nick, $this->primary_key['id']));

		// Seteamos el valor en los datos actuales.
		$this->data['nick'] = $nick;
	}

	/**
	 * Cambiamos la contraseña de acceso del usuario.
	 * @param string $password Nueva contraseña.
	 */
	public function actualizar_contrasena($password)
	{
		// Generamos el hash.
		$enc = new Phpass(8, FALSE);
		$enc_password = $enc->HashPassword($password);
		unset($enc);

		// Actualizamos la base de datos.
		$this->db->update('UPDATE usuario SET password = ? WHERE id = ?', array($enc_password, $this->primary_key['id']));

		// Actualizamos la cargada.
		if (is_array($this->data))
		{
			$this->data['password'] = $enc_password;
		}
	}

	/**
	 * Actualizamos el email de la cuenta del usuario.
	 * @param string $email Nueva casilla de correo del usuario.
	 */
	public function cambiar_email($email)
	{
		$this->db->update('UPDATE usuario SET email = ? WHERE id = ?', array($email, $this->primary_key['id']));
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

	/**
	 * Obtenemos el rango del usuario.
	 * @return Model_Usuario_Rango
	 */
	public function rango()
	{
		return new Model_Usuario_Rango($this->get('rango'));
	}

	/**
	 * Obtenemos el modelo de configuraciones del usuario.
	 * @return Model_Usuario_Configuracion
	 */
	public function configuracion()
	{
		return new Model_Usuario_Configuracion($this->primary_key['id']);
	}

	/**
	 * Obtenemos el modelo del perfil del usuario actual.
	 * @return Model_Usuario_Perfil
	 */
	public function perfil()
	{
		return new Model_Usuario_Perfil($this->primary_key['id']);
	}

	/**
	 * Cantidad de seguidores que tiene el usuario.
	 * @return int
	 */
	public function cantidad_seguidores()
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_seguidor WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de posts que realizó el usuario.
	 * @return int
	 */
	public function cantidad_posts()
	{
		return $this->db->query('SELECT COUNT(*) FROM post WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de comentarios que realizó el usuario.
	 * @return int
	 */
	public function cantidad_comentarios()
	{
		$c = 0;
		$c += $this->db->query('SELECT COUNT(*) FROM post_comentario WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		$c += $this->db->query('SELECT COUNT(*) FROM foto_comentario WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		$c += $this->db->query('SELECT COUNT(*) FROM comunidad_comentario WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		return $c;
	}

	/**
	 * Cantidad de puntos que tiene el usuario.
	 * @return int
	 */
	public function cantidad_puntos()
	{
		return (int) $this->db->query('SELECT SUM(cantidad) FROM post_punto, post WHERE post.id = post_punto.post_id AND post.usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}
}
