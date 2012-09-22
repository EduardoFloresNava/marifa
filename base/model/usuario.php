<?php
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
 * @package		Marifa\Base
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo del usuario.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      0.1
 * @package    Marifa\Base
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
	 * Cargamos un usuario con su nick actual.
	 * @param string $nick Nick del usuario a cargar.
	 * @return bool
	 */
	public function load_by_nick($nick)
	{
		if ($this->load(array('nick' => $nick)))
		{
			$this->primary_key['id'] = $this->get('id');
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Actualizamos el estado del usuario.
	 * @param int $estado Estado a setear.
	 */
	public function actualizar_estado($estado)
	{
		$this->db->update('UPDATE usuario SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
		$this->update_value('estado', $estado);
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
		if ($rst->num_rows() > 0)
		{
			// Obtenemos la información.
			$data = $rst->get_record(Database_Query::FETCH_ASSOC, array('id' => Database_Query::FIELD_INT, 'estado' => Database_Query::FIELD_INT));

			// Verificamos la contraseña.
			$enc = new Phpass(8, FALSE);
			if ( ! $enc->check_password($password, $data['password']) == TRUE)
			{
				return -1;
			}

			// Verificamos el estado.
			switch ($data['estado'])
			{
				case self::ESTADO_SUSPENDIDA: // Cuenta suspendida.
					$this->primary_key['id'] = $data['id'];
					$suspension = $this->suspension();

					if ($suspension === NULL)
					{
						$this->actualizar_estado(self::ESTADO_ACTIVA);
						$data['estado'] = self::ESTADO_ACTIVA;
					}
					else
					{
						// Verificamos si terminó.
						if ($suspension->restante() <= 0)
						{
							$suspension->anular();
							$this->actualizar_estado(self::ESTADO_ACTIVA);
							$data['estado'] = self::ESTADO_ACTIVA;
						}
						else
						{
							break;
						}
					}
				case self::ESTADO_BANEADA:    // Cuenta baneada.
					// Verificamos por paso de suspendida.
					if ($data['estado'] == self::ESTADO_BANEADA)
					{
						$this->primary_key['id'] = $data['id'];
						if ($this->baneo() === NULL)
						{
							$this->actualizar_estado(self::ESTADO_ACTIVA);
							$data['estado'] = self::ESTADO_ACTIVA;
						}
						break;
					}
				case self::ESTADO_ACTIVA: // Cuenta activa.
					// Iniciamos la sessión.
					Session::set('usuario_id', $data['id']);

					// Seteamos el usuario actual.
					$this->primary_key['id'] = $data['id'];

					// Actualizamos el inicio de session.
					$this->db->update('UPDATE usuario SET lastlogin = ?, lastactive = ?, lastip = ? WHERE id = ?', array(date('Y/m/d H:i:s'), date('Y/m/d H:i:s'), ip2long(IP::get_ip_addr()), $this->primary_key['id']));
					break;
				case self::ESTADO_PENDIENTE:  // Cuenta por activar.
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
		$enc_password = $enc->hash_password($password);
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
		$enc_password = $enc->hash_password($password);
		unset($enc);

		// Creamos arreglo con los datos.
		//TODO: Agregar rango.
		$info = array($nick, $enc_password, $email, 0, 10, 10, date('Y/m/d H:i:s'), 0);

		// Creamos la cuenta.
		list ($id, $cant) = $this->db->insert('INSERT INTO usuario (nick, password, email, rango, puntos, puntos_disponibles, registro, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', array_values($info));

		return ($cant > 0) ? $id : FALSE;
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
		if ( ! isset($this->_perfil))
		{
			$this->_perfil = new Model_Usuario_Perfil($this->primary_key['id']);
		}
		return $this->_perfil;
	}

	/**
	 * Cantidad de seguidores que tiene el usuario.
	 * @return int
	 */
	public function cantidad_seguidores()
	{
		// Clave de la cache.
		$cache_key = 'usuario_'.$this->primary_key['id'].'_seguidores';

		// Obtenemos el objeto de cache.
		$cantidad = Cache::get_instance()->get($cache_key);

		if ( ! $cantidad)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM usuario_seguidor WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Guardo cache.
			Cache::get_instance()->save($cache_key, $cantidad);
		}

		return $cantidad;
	}

	/**
	 * Obtenemos los seguidores del usuario.
	 * @return array
	 */
	public function seguidores()
	{
		$lista = $this->db->query('SELECT seguidor_id FROM usuario_seguidor WHERE usuario_id = ?', $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($lista as $l)
		{
			$lst[] = new Model_Usuario($l);
		}
		return $lst;
	}

	/**
	 * Obtenemos la lista de usuarios que sigue.
	 * @return array
	 */
	public function sigue()
	{
		$lista = $this->db->query('SELECT usuario_id FROM usuario_seguidor WHERE seguidor_id = ?', $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($lista as $l)
		{
			$lst[] = new Model_Usuario($l);
		}
		return $lst;
	}

	/**
	 * Cantidad de posts que realizó el usuario.
	 * @return int
	 */
	public function cantidad_posts()
	{
		// Clave de la cache.
		$cache_key = 'usuario_'.$this->primary_key['id'].'_posts';

		// Obtenemos el objeto de cache.
		$cantidad = Cache::get_instance()->get($cache_key);

		if ($cantidad === FALSE)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM post WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Guardo cache.
			Cache::get_instance()->save($cache_key, $cantidad);
		}

		return $cantidad;
	}

	/**
	 * Cantidad de fotos que tiene el usuario.
	 * @return int
	 */
	public function cantidad_fotos()
	{
		// Clave de la cache.
		$cache_key = 'usuario_'.$this->primary_key['id'].'_fotos';

		// Obtenemos el objeto de cache.
		$cantidad = Cache::get_instance()->get($cache_key);

		if ($cantidad === FALSE)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM foto WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Guardo cache.
			Cache::get_instance()->save($cache_key, $cantidad);
		}

		return $cantidad;
	}

	/**
	 * Cantidad de comentarios que realizó el usuario.
	 * @return int
	 */
	public function cantidad_comentarios()
	{
		// Clave de la cache.
		$cache_key = 'usuario_'.$this->primary_key['id'].'_comentarios';

		// Obtenemos el objeto de cache.
		$cantidad = Cache::get_instance()->get($cache_key);

		if ($cantidad === FALSE)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM post_comentario WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
			$cantidad += $this->db->query('SELECT COUNT(*) FROM foto_comentario WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
			$cantidad += $this->db->query('SELECT COUNT(*) FROM comunidad_comentario WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Guardo cache.
			Cache::get_instance()->save($cache_key, $cantidad);
		}

		return $cantidad;
	}

	/**
	 * Cantidad de puntos que tiene el usuario.
	 * @return int
	 */
	public function cantidad_puntos()
	{
		// Clave de la cache.
		$cache_key = 'usuario_'.$this->primary_key['id'].'_puntos';

		// Obtenemos el objeto de cache.
		$cantidad = Cache::get_instance()->get($cache_key);

		if ($cantidad === FALSE)
		{
			$cantidad = (int) $this->db->query('SELECT SUM(cantidad) FROM post_punto, post WHERE post.id = post_punto.post_id AND post.usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Guardo cache.
			Cache::get_instance()->save($cache_key, $cantidad);
		}

		return $cantidad;
	}

	/**
	 * Obtenemos el listado de posts del usuario ordenados por fecha.
	 * @param int $cantidad Cantidad de posts a devolver por página.
	 * @param int $pagina Número de página empezando por 0.
	 * @return array
	 */
	public function posts_perfil_by_fecha($cantidad, $pagina = 0)
	{
		$posts = $this->db->query('SELECT id FROM post WHERE usuario_id = ? ORDER BY fecha DESC LIMIT '.$pagina*$cantidad.','.$cantidad, $this->primary_key['id'])->get_pairs();

		$lst = array();
		foreach ($posts as $p)
		{
			$lst[] = new Model_Post($p);
		}
		return $lst;
	}

	/**
	 * Obtenemos el listado de los usuarios más puntuados.
	 * @param int $pagina Número de página empezando en 1.
	 * @param int $cantidad Cantidad de post por página.
	 * @return array
	 */
	public function obtener_tops($pagina = 1, $cantidad = 10)
	{
		// Primer elemento a devolver.
		$inicio = $cantidad * ($pagina - 1);

		// Obtenemos el listado.
		$rst = $this->db->query('SELECT usuario.id, SUM(post_punto.cantidad) as puntos FROM usuario LEFT JOIN post ON post.usuario_id = usuario.id LEFT JOIN post_punto ON post.id = post_punto.post_id GROUP BY usuario.id ORDER BY puntos DESC LIMIT '.$inicio.', '.$cantidad)->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));

		// Armamos la lista.
		$lst = array();
		foreach ($rst as $k => $v)
		{
			$lst[] = new Model_Usuario($k);
		}

		return $lst;
	}

	/**
	 * Cantidad total de usuarios.
	 * @return int
	 */
	public function cantidad()
	{
		if ($this->primary_key['id'] !== NULL)
		{
			$params = $this->primary_key['id'];
			$q = ' WHERE id != ?';
		}
		else
		{
			$params = NULL;
			$q = '';
		}

		$key = 'usuario_total';

		$rst = Cache::get_instance()->get($key);
		if ( ! $rst)
		{
			$rst = $this->db->query('SELECT COUNT(*) FROM usuario'.$q, $params)->get_var(Database_Query::FIELD_INT);

			Cache::get_instance()->save($key, $rst);
		}

		return $rst;
	}

	/**
	 * Cantidad de usuarios activos en el último minuto..
	 * @return type
	 */
	public function cantidad_activos()
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario WHERE UNIX_TIMESTAMP(lastactive) > ?', (time() - 60))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Listado de usuarios existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de noticias por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10)
	{
		if ($this->primary_key['id'] !== NULL)
		{
			$params = $this->primary_key['id'];
			$q = ' WHERE id != ?';
		}
		else
		{
			$params = NULL;
			$q = '';
		}

		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM usuario'.$q.' ORDER BY registro DESC LIMIT '.$start.','.$cantidad, $params)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Usuario($v);
		}
		return $lst;
	}

	/**
	 * Obtengo el modelo de suspensión si hay una en proceso.
	 * @return Model_Usuario_Suspension|NULL
	 */
	public function suspension()
	{
		$id = $this->db->query('SELECT id FROM usuario_suspension WHERE usuario_id = ? LIMIT 1', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

		if ($id !== NULL)
		{
			return new Model_Usuario_Suspension($id);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Obtengo el modelo del baneo si hay uno en proceso.
	 * @return Model_Usuario_Baneo|NULL
	 */
	public function baneo()
	{
		$id = $this->db->query('SELECT id FROM usuario_baneo WHERE usuario_id = ? LIMIT 1', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

		if ($id !== NULL)
		{
			return new Model_Usuario_Baneo($id);
		}
		else
		{
			return NULL;
		}
	}
}
