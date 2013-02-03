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
 * @property-read int $id ID del usuario.
 * @property-read string $nick Nick del usuario.
 * @property-read string $password Contraseña del usuario.
 * @property-read string $email E-Mail del usuario.
 * @property-read int $rango ID del rango del usuario.
 * @property-read int $puntos Cantidad de puntos que tiene el usuario.
 * @property-read Fechahora $registro Fecha del registro.
 * @property-read Fechahora $lastlogin Fecha de su último ingreso al sitio.
 * @property-read Fechahora $lastactive Fecha de su última  visita.
 * @property-read int $lastip La última IP utilizada.
 * @property-read int $estado Estado de la cuenta.
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
	 * Cargamos un usuario con su nick actual.
	 * @param string $email E-Mail del usuario a cargar.
	 * @return bool
	 */
	public function load_by_email($email)
	{
		if ($this->load(array('email' => $email)))
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

			// Verifico tipo password.
			if (strlen($data['password']) == 60) // Marifa
			{
				// Verificamos la contraseña.
				$enc = new Phpass(8, FALSE);
				if ( ! $enc->check_password($password, $data['password']) == TRUE)
				{
					return -1;
				}
			}
			elseif (strlen($data['password']) == 40 && sha1(strtolower($data['nick']).$password)) // SMF 1.1.x, SMF 2.0.x
			{
				// Codifico la contraseña.
				$enc = new Phpass(8, FALSE);
				$enc_password = $enc->hash_password($password);
				unset($enc);

				// Actualizo la contraseña.
				$this->db->query('UPDATE usuario SET password = ? WHERE id = ?', array($enc_password, $data['id']));
			}
			elseif (strlen($data['password']) == 32 && ($data['password'] == md5(md5($password).strtolower($data['nick'])) || $data['password'] == md5($password))) // Zinfinal - phpost
			{
				// Codifico la contraseña.
				$enc = new Phpass(8, FALSE);
				$enc_password = $enc->hash_password($password);
				unset($enc);

				// Actualizo la contraseña.
				$this->db->query('UPDATE usuario SET password = ? WHERE id = ?', array($enc_password, $data['id']));
			}
			else
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
					// IP del usuario.
					$ip = ip2long(get_ip_addr());

					// Seteamos el usuario actual.
					$this->primary_key['id'] = $data['id'];

					// Iniciamos la sessión.
					Usuario::login($this, $ip);

					// Actualizamos el inicio de session.
					$this->db->update('UPDATE usuario SET lastlogin = ?, lastactive = ?, lastip = ? WHERE id = ?', array(date('Y/m/d H:i:s'), date('Y/m/d H:i:s'), $ip, $this->primary_key['id']));
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
	 * Obtenemos el listado de nick's del usuario.
	 * @return array
	 */
	public function nicks()
	{
		return $this->db->query('SELECT nick FROM usuario_nick WHERE usuario_id = ?', $this->primary_key['id'])->get_pairs();
	}

	/**
	 * Eliminamos un nick del usuario.
	 * @param string $nick
	 */
	public function eliminar_nick($nick)
	{
		$this->db->delete('DELETE FROM usuario_nick WHERE usuario_id = ? AND nick = ?', array($this->primary_key['id'], $nick));
	}

	/**
	 * Obtenemos la fecha del ultimo cambio de nick.
	 * @return type
	 */
	public function ultimo_cambio_nick()
	{
		return $this->db->query('SELECT MAX(fecha) FROM usuario_nick WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_DATETIME);
	}

	/**
	 * Creamos una nueva cuenta de usuario.
	 * @param string $nick Nick del usuario
	 * @param string $email E-Mail del usuario.
	 * @param string $password Contraseña del usuario.
	 * @param int $rango Rango por defecto.
	 * @return int|bool FALSE si no se pudo crear. En caso de ser correcto el ID
	 * del usuario insertado o NULL si no es soportado por el motor.
	 * @throws Exception
	 */
	public function register($nick, $email, $password, $rango)
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
		$info = array($nick, $enc_password, $email, $rango, 0, date('Y/m/d H:i:s'), 0);

		// Creamos la cuenta.
		list ($id, $cant) = $this->db->insert('INSERT INTO usuario (nick, password, email, rango, puntos, registro, estado) VALUES (?, ?, ?, ?, ?, ?, ?)', $info);

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
		return (int) $this->db->query('SELECT COUNT(*) FROM usuario_seguidor WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de usuario que sigue.
	 * @return int
	 */
	public function cantidad_sigue()
	{
		return (int) $this->db->query('SELECT COUNT(*) FROM usuario_seguidor WHERE seguidor_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtenemos los seguidores del usuario.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function seguidores($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$lista = $this->db->query('SELECT seguidor_id FROM usuario_seguidor WHERE usuario_id = ? LIMIT '.$start.','.$cantidad, $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($lista as $l)
		{
			$lst[] = new Model_Usuario($l);
		}
		return $lst;
	}

	/**
	 * El usuario comiensa a ser seguidor por $usuario.
	 * @param int $usuario Quien empieza a seguir al usuario.
	 */
	public function seguir($usuario)
	{
		$this->db->query('INSERT INTO usuario_seguidor (usuario_id, seguidor_id, fecha) VALUES (?, ?, ?)', array($this->primary_key['id'], $usuario, date('Y/m/d H:i:s')));
	}

	/**
	 * Dejar de seguir.
	 * @param int $usuario Quien se deja de seguir.
	 */
	public function fin_seguir($usuario)
	{
		$this->db->query('DELETE FROM usuario_seguidor WHERE usuario_id = ? AND seguidor_id = ?',  array($this->primary_key['id'], $usuario));
	}

	/**
	 * Obtenemos la lista de usuarios que sigue.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function sigue($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$lista = $this->db->query('SELECT usuario_id FROM usuario_seguidor WHERE seguidor_id = ? LIMIT '.$start.','.$cantidad, $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);

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
		return (int) $this->db->query('SELECT COUNT(*) FROM post WHERE usuario_id = ? AND estado = 0', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de fotos que tiene el usuario.
	 * @return int
	 */
	public function cantidad_fotos()
	{
		return (int) $this->db->query('SELECT COUNT(*) FROM foto WHERE usuario_id = ? AND estado = 0', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de comentarios que realizó el usuario.
	 * @param bool $foto Contar comentarios en fotos.
	 * @param bool $post Contar comentarios en posts.
	 * @return int
	 */
	public function cantidad_comentarios($foto = TRUE, $post = TRUE)
	{
		$cantidad = 0;

		if ($post)
		{
			$cantidad += $this->db->query('SELECT COUNT(*) FROM post_comentario WHERE usuario_id = ? AND estado = 0', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		}

		if ($foto)
		{
			$cantidad += $this->db->query('SELECT COUNT(*) FROM foto_comentario WHERE usuario_id = ? AND estado = 0', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		}

		return $cantidad;
	}

	/**
	 * Cantidad de puntos que tiene el usuario.
	 * @return int
	 */
	public function cantidad_puntos()
	{
		return (int) $this->db->query('SELECT SUM(cantidad) FROM post_punto, post WHERE post.id = post_punto.post_id AND post.usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtenemos el listado de posts del usuario ordenados por fecha.
	 * @param int $pagina Número de página.
	 * @param int $cantidad Cantidad de posts a devolver por página.
	 * @return array
	 */
	public function posts_perfil_by_fecha($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$posts = $this->db->query('SELECT id FROM post WHERE usuario_id = ? ORDER BY fecha DESC LIMIT '.$start.','.$cantidad, $this->primary_key['id'])->get_pairs();

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
	 * Cantidad de posts en favoritos del usuario.
	 * @return type
	 */
	public function cantidad_favoritos_posts()
	{
		return (int) $this->db->query('SELECT COUNT(*) FROM post_favorito INNER JOIN post ON post_favorito.post_id = post.id WHERE post_favorito.usuario_id = ? AND post.estado = ?', array($this->primary_key['id'], Model_Post::ESTADO_ACTIVO))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de fotos en favoritos del usuario.
	 * @return type
	 */
	public function cantidad_favoritos_fotos()
	{
		return (int) $this->db->query('SELECT COUNT(*) FROM foto_favorito INNER JOIN foto ON foto_favorito.foto_id = foto.id WHERE foto_favorito.usuario_id = ? AND foto.estado = ?', array($this->primary_key['id'], Model_Foto::ESTADO_ACTIVA))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad total de usuarios.
	 * @param int $estado Estado a contar. NULL para todos.
	 * @return int
	 */
	public function cantidad($estado = NULL)
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

		if ($estado !== NULL)
		{
			if ( ! empty($q))
			{
				$q .= ' AND estado = ?';
				$params = array($params, $estado);
			}
			else
			{
				$q = ' WHERE id = ?';
				$params = $estado;
			}
		}
		return $this->db->query('SELECT COUNT(*) FROM usuario'.$q, $params)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Listado de avisos del usuario.
	 * @param int|array $estado Estado de los avisos, NULL para cualquier estado.
	 * @return array
	 */
	public function avisos($estado = NULL)
	{
		// Cargo listado.
		if ($estado === NULL)
		{
			$lst = $this->db->query('SELECT id FROM usuario_aviso WHERE usuario_id = ? ORDER BY fecha DESC', $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			if (is_array($estado))
			{
				$estado = implode(', ', $estado);
			}
			$lst = $this->db->query('SELECT id FROM usuario_aviso WHERE usuario_id = ? AND estado IN('.$estado.') ORDER BY fecha DESC', $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);
		}

		// Armo listado de objetos.
		foreach($lst as $k => $v)
		{
			$lst[$k] = new Model_Usuario_Aviso($v);
		}

		// Devuelvo la lista.
		return $lst;
	}

	/**
	 * Cantidad de avisos que tiene el usuario.
	 * @param int $estado Estado de los avisos, NULL para cualquier estado.
	 * @return
	 */
	public function cantidad_avisos($estado = NULL)
	{
		if ($estado === NULL)
		{
			return $this->db->query('SELECT COUNT(*) FROM usuario_aviso WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return $this->db->query('SELECT COUNT(*) FROM usuario_aviso WHERE usuario_id = ? AND estado = ?', array($this->primary_key['id'], $estado))->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Cantidad de usuarios activos en el último minuto.
	 * @return int
	 */
	public function cantidad_activos()
	{
		$m_s = new Model_Session;
		return $m_s->cantidad_usuarios();
		// return $this->db->query('SELECT COUNT(*) FROM usuario WHERE UNIX_TIMESTAMP(lastactive) > ?', (time() - 60))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de mensajes que tiene el usuario en su bandeja de entrada para
	 * ser leidos.
	 * @return int
	 */
	public function cantidad_mensajes_nuevos()
	{
		$m = new Model_Mensaje;
		return $m->total_recibidos($this->primary_key['id'], Model_Mensaje::ESTADO_NUEVO);
	}

	/**
	 * Listado de usuarios existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @param int $estado Estado de los usuario a obtener. NULL para todos los posibles.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10, $estado = NULL)
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

		if ($estado !== NULL)
		{
			if ( ! empty($q))
			{
				$q .= ' AND estado = ?';
				$params = array($params, $estado);
			}
			else
			{
				$q = ' WHERE id = ?';
				$params = $estado;
			}
		}

		$start = ($pagina - 1) * $cantidad;

		if ($pagina >= 0)
		{
			$limit = ' LIMIT '.$start.','.$cantidad;
		}
		else
		{
			$limit = '';
		}

		$rst = $this->db->query('SELECT id FROM usuario'.$q.' ORDER BY registro DESC'.$limit, $params)->get_pairs(Database_Query::FIELD_INT);

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

	/**
	 * Obtenemos el listado de posts con más puntos.
	 * @param int $categoria
	 * @param int $intervalo
	 */
	public function top_puntos($categoria = NULL, $intervalo = 0)
	{
		$params = NULL;

		// Verifico los intervalos.
		if ($intervalo !== 0)
		{

			switch ($intervalo)
			{
				case 1:
					$start = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
					$end = mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));
					break;
				case 2:
					$start = mktime(0, 0, 0);
					break;
				case 3:
					$start = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - ((date('N')-1)*3600*24);
					break;
				case 4:
					$start = mktime(0, 0, 0, date('m'), 1, date('Y'));
					break;
			}

			if (isset($end))
			{
				$where = ' AND post.fecha > ? AND post.fecha < ?';
				$params = array(date('Y/m/d H:i:s', $start), date('Y/m/d H:i:s', $end));
				unset($start, $end);
			}
			else
			{
				$where = ' AND post.fecha > ?';
				$params = array(date('Y/m/d H:i:s', $start));
				unset($start);
			}
		}

		// Verifico las categorias.
		if ($categoria !== NULL)
		{
			if ( ! isset($where))
			{
				$where = '';
			}

			$where .= ' AND post.categoria_id = ?';

			if ( ! is_array($params))
			{
				$params = array($categoria);
			}
			else
			{
				$params[] = $categoria;
			}
		}

		if ( ! isset($where))
		{
			$where = '';
		}

		return $this->db->query('SELECT SUM(puntos) as puntos, nick FROM (SELECT SUM(post_punto.cantidad) AS puntos, usuario.nick FROM usuario INNER JOIN post ON post.usuario_id = usuario.id LEFT JOIN post_punto ON post.id = post_punto.post_id LEFT JOIN categoria ON post.categoria_id = categoria.id WHERE post.estado = 0 AND usuario.estado = 1'.$where.' GROUP BY post.id ORDER BY puntos DESC LIMIT 10) as A GROUP BY nick ORDER BY puntos DESC', $params)
			->get_records(Database_Query::FETCH_ASSOC, array(
				'puntos' => Database_Query::FIELD_INT,
				'nick' => Database_Query::FIELD_STRING
		));
	}

	/**
	 * Obtenemos el listado de usuario con más seguidores.
	 * @param int $intervalo
	 */
	public function top_seguidores($intervalo = 0)
	{
		$params = NULL;

		// Verifico los intervalos.
		if ($intervalo !== 0)
		{

			switch ($intervalo)
			{
				case 1:
					$start = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
					$end = mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));
					break;
				case 2:
					$start = mktime(0, 0, 0);
					break;
				case 3:
					$start = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - ((date('N')-1)*3600*24);
					break;
				case 4:
					$start = mktime(0, 0, 0, date('m'), 1, date('Y'));
					break;
			}

			if (isset($end))
			{
				$where = ' AND usuario_seguidor.fecha > ? AND usuario_seguidor.fecha < ?';
				$params = array(date('Y/m/d H:i:s', $start), date('Y/m/d H:i:s', $end));
				unset($start, $end);
			}
			else
			{
				$where = ' AND usuario_seguidor.fecha > ?';
				$params = array(date('Y/m/d H:i:s', $start));
				unset($start);
			}
		}

		if ( ! isset($where))
		{
			$where = '';
		}

		return $this->db->query('SELECT COUNT(*) AS seguidores, usuario.nick FROM usuario LEFT JOIN usuario_seguidor ON usuario.id = usuario_seguidor.usuario_id WHERE usuario.estado = 1'.$where.' GROUP BY usuario.id ORDER BY seguidores DESC LIMIT 10', $params)
			->get_records(Database_Query::FETCH_ASSOC, array(
				'seguidores' => Database_Query::FIELD_INT,
				'nick' => Database_Query::FIELD_STRING
		));
	}

	/**
	 * Denunciamos al usuario.
	 * @param int $usuario_id Quien denuncia.
	 * @param int $motivo El motivo de la denuncia.
	 * @param string $comentario Descripción de la denuncia.
	 */
	public function denunciar($usuario_id, $motivo, $comentario)
	{
		list($id,) = $this->db->insert('INSERT INTO usuario_denuncia (denunciado_id, usuario_id, motivo, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)',
			array(
				$this->primary_key['id'],
				$usuario_id,
				$motivo,
				$comentario,
				date('Y/m/d H:i:s'),
				Model_Usuario_Denuncia::ESTADO_PENDIENTE
			));
		return $id;
	}

	/**
	 * Listado de posts favoritos del usuario.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de noticias por página.
	 * @return array
	 */
	public function listado_posts_favoritos($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT post.id FROM post INNER JOIN post_favorito ON post.id = post_favorito.post_id WHERE post_favorito.usuario_id = ? AND post.estado = ? LIMIT '.$start.','.$cantidad, array($this->primary_key['id'], Model_Post::ESTADO_ACTIVO))->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post($v);
		}
		return $lst;
	}

	/**
	 * Listado de fotos favoritas del usuario.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de noticias por página.
	 * @return array
	 */
	public function listado_fotos_favoritos($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT foto.id FROM foto INNER JOIN foto_favorito ON foto.id = foto_favorito.foto_id WHERE foto_favorito.usuario_id = ? AND foto.estado = ? LIMIT '.$start.','.$cantidad, array($this->primary_key['id'], Model_Foto::ESTADO_ACTIVA))->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Foto($v);
		}
		return $lst;
	}

	/**
	 * Verifico si el usuario es seguidor.
	 * @param type $usuario_id
	 * @return type
	 */
	public function es_seguidor($usuario_id)
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_seguidor WHERE usuario_id = ? AND seguidor_id = ?', array($this->primary_key['id'], $usuario_id))->get_var(Database_Query::FIELD_INT) > 0;
	}

	/**
	 * Bloqueo el acceso del usuario.
	 * @param int $usuario_id ID del usuario al que bloqueamos.
	 */
	public function bloquear($usuario_id)
	{
		$this->db->query('INSERT INTO usuario_bloqueo (usuario_id, bloqueado_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Desbloqueo el acceso del usuario.
	 * @param int $usuario_id ID del usuario al que desbloqueamos.
	 */
	public function desbloquear($usuario_id)
	{
		$this->db->query('DELETE FROM usuario_bloqueo WHERE usuario_id = ? AND bloqueado_id = ?', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Verificamos si se encuentra bloqueado.
	 * @param int $usuario_id ID del usuario del que verificamos el bloqueo.
	 * @return bool
	 */
	public function esta_bloqueado($usuario_id)
	{
		return $this->db->query('SELECT * FROM usuario_bloqueo WHERE usuario_id = ? AND bloqueado_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos el listado de usuario que ha bloqueado.
	 * @return array
	 */
	public function bloqueos()
	{
		$rst = $this->db->query('SELECT bloqueado_id FROM usuario_bloqueo WHERE usuario_id = ?', $this->primary_key['id']);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Usuario( (int) $v[0]);
		}

		return $lst;
	}

	/**
	 * Verificamos y realizamos el cambio de rango automático según el tipo provisto.
	 * @param int $tipo Tipo de rango a verificar.
	 */
	public function actualizar_rango($tipo)
	{
		// Cuento la cantidad que tiene.
		switch ($tipo)
		{
			case Model_Usuario_Rango::TIPO_PUNTOS:
				$cantidad = $this->cantidad_puntos();
				break;
			case Model_Usuario_Rango::TIPO_POST:
				$cantidad = $this->cantidad_posts();
				break;
			case Model_Usuario_Rango::TIPO_FOTOS:
				$cantidad = $this->cantidad_fotos();
				break;
			case Model_Usuario_Rango::TIPO_COMENTARIOS:
				$cantidad = $this->cantidad_comentarios();
				break;
		}

		// Verifico si puede promover.
		$nuevo_rango = $this->rango()->puede_promover($tipo, $cantidad);

		// Cambio el rango si es posible.
		if ($nuevo_rango !== NULL)
		{
			// Cambio el rango del usuario.
			$this->actualizar_campo('rango', $nuevo_rango);

			// Envio el suceso.
			$suceso = new Model_Suceso;
			$suceso->crear($this->primary_key['id'], 'usuario_cambio_rango', TRUE, $this->primary_key['id'], $nuevo_rango);
		}
	}

	/**
	 * Cantidad de medallas del usuario.
	 * @return int
	 */
	public function cantidad_medallas()
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_medalla WHERE usuario_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Agregamos medallas si cumple con los requisitos impuestos.
	 * @param int $tipo Tipo de acción a controlar.
	 * @return int|bool FALSE si no hay medalla que asignar, el ID de la medalla si se asignó.
	 */
	public function actualizar_medallas($tipo)
	{
		switch ($tipo)
		{
			case Model_Medalla::CONDICION_USUARIO_PUNTOS:
				$cantidad = $this->cantidad_puntos();
				break;
			case Model_Medalla::CONDICION_USUARIO_SEGUIDORES:
				$cantidad = $this->cantidad_seguidores();
				break;
			case Model_Medalla::CONDICION_USUARIO_SIGUIENDO:
				$cantidad = $this->cantidad_sigue();
				break;
			case Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_POSTS:
				$cantidad = $this->cantidad_comentarios(FALSE);
				break;
			case Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_FOTOS:
				$cantidad = $this->cantidad_comentarios(TRUE, FALSE);
				break;
			case Model_Medalla::CONDICION_USUARIO_POSTS:
				$cantidad = $this->cantidad_posts();
				break;
			case Model_Medalla::CONDICION_USUARIO_FOTOS:
				$cantidad = $this->cantidad_fotos();
				break;
			case Model_Medalla::CONDICION_USUARIO_MEDALLAS:
				$cantidad = $this->cantidad_medallas();
				break;
			//case CONDICION_USUARIO_RANGO: // Sin implementar.
		}

		// Busco medalla.
		$rst = $this->db->query('SELECT id FROM medalla WHERE cantidad = ? AND condicion = ?', array($cantidad, $tipo));

		if ($rst->num_rows() > 0)
		{
			// Cargo la medalla.
			$medalla = $rst->get_var(Database_Query::FIELD_INT);

			// Verifico no tener la medalla.
			if ($this->db->query('SELECT COUNT(*) FROM usuario_medalla WHERE medalla_id = ? AND usuario_id = ?', array($medalla, $this->primary_key['id']))->get_var(Database_Query::FIELD_INT) > 0)
			{
				return FALSE;
			}
			else
			{
				// Agrego la medalla.
				$this->db->insert('INSERT INTO usuario_medalla (medalla_id, usuario_id, fecha) VALUES (?, ?, ?);', array($medalla, $this->primary_key['id'], date('Y/m/d H:i:s')));

				// Envio suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear($this->primary_key['id'], 'usuario_nueva_medalla', TRUE, $medalla);

				return $medalla;
			}
		}
		else
		{
			// No hay medallas.
			return FALSE;
		}
	}

	/**
	 * Listado de medallas del usuario.
	 * @return array
	 */
	public function medallas()
	{
		$rst = $this->db->query('SELECT medalla_id, fecha, objeto_id FROM usuario_medalla WHERE usuario_id = ? ORDER BY fecha DESC', $this->primary_key['id']);
		$rst->set_fetch_type(Database_Query::FETCH_ASSOC);
		$rst->set_cast_type(array('medalla_id' => Database_Query::FIELD_INT, 'fecha' => Database_Query::FIELD_DATETIME, 'objeto_id' => Database_Query::FIELD_INT));

		// Genero listado.
		$lst = array();
		foreach ($rst as $k => $v)
		{
			$lst[$k] = $v;
			$lst[$k]['medalla'] = new Model_Medalla($v['medalla_id']);
		}

		return $lst;
	}
}
