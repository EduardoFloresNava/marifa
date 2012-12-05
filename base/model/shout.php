<?php
/**
 * shout.php is part of Marifa.
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
 * Modelo para el manejo de shout's.
 *
 * @since      0.2RC3
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read string $id ID del shout.
 * @property-read int $usuario_id ID del usuario que creó el shout.
 * @property-read string $mensaje Mensaje que contiene el shout.
 * @property-read Fechahora $fecha Fecha en la cual se creó el shout.
 */
class Base_Model_Shout extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'shout';

	/**
	 * Clave primaria.
	 * @var array
	 */
	protected $primary_key = array('id' => NULL);

	/**
	 * Listado de campos y sus tipos.
	 */
	protected $fields = array(
		'id' => Database_Query::FIELD_STRING,
		'usuario_id' => Database_Query::FIELD_INT,
		'mensaje' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME
	);

	/**
	 * Constructor del shout.
	 * @param string $id ID del shout a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
	}

	/**
	 * ID del usuario creador del shout.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->usuario_id);
	}

	/**
	 * Creamos un nuevo shout.
	 * @param int $usuario ID del usuario dueño del shout.
	 * @param string $mensaje Mensaje que contiene el shout
	 * @return int ID del shout creado.
	 */
	public function crear($usuario, $mensaje)
	{
		list($id,) = $this->db->insert('INSERT INTO shout (usuario_id, mensaje, fecha) VALUES (?, ?, ?)', array($usuario, $mensaje, date('y/m/d H:i:s')));
		$this->primary_key['id'] = $id;
		return $id;
	}

	/**
	 * Procesamos las etiquetas.
	 * @param string $mensaje Mensaje que será procesado.
	 * @return array Listado de etiquetas encontradas.
	 */
	public function procesar_etiquetas(&$mensaje)
	{
		// Obtengo listado de etiquetas.
		$keys = array();
		preg_match_all('/#([^\s]{1,50})/', $mensaje, $keys);

		// Agrego espacios para simplificar expresión regular.
		$mensaje = " $mensaje ";

		// Las reemplazo por una etiqueta BBCode.
		foreach ($keys[0] as $v)
		{
			$mensaje = preg_replace('/(\s)('.preg_quote($v).')(\s)/', "$1[tag]{$v}[/tag]$3", $mensaje);
		}

		// Borro espacios extra.
		$mensaje = trim($mensaje);

		// Devuelvo las etiquetas.
		return $keys[1];
	}

	/**
	 * Procesamos links de usuarios.
	 * @param string $mensaje Mensaje que será procesado.
	 * @return array Listado de etiquetas encontradas.
	 */
	public function procesar_usuarios(&$mensaje)
	{
		// Obtengo listado de etiquetas.
		$keys = array();
		preg_match_all('/@([a-zA-Z0-9]{4,16})/', $mensaje, $keys);

		// Armo consulta para buscar usuarios existentes.
		$c = count($keys[1]);
		$q = array();
		for ($i = 0; $i < $c; $i++)
		{
			$q[] = '?';
		}
		$u_list = $this->db->query('SELECT nick, id FROM usuario WHERE nick IN ('.implode(', ', $q).')', $keys[1])->get_pairs(Database_Query::FIELD_STRING, Database_Query::FIELD_INT);

		// Proceso la lista de usuarios.
		$users = array();
		foreach ($keys[1] as $v)
		{
			// Verifico sea válido.
			if (isset($u_list[$v]))
			{
				// Verifico si se puede procesar.
				if ($u_list[$v] !== Usuario::$usuario_id && Usuario::puedo_referirlo($u_list[$v]))
				{
					// Lo agrego para ser procesado.
					$users[$v] = $u_list[$v];
				}
			}
		}

		// Agrego espacios para simplificar expresión regular.
		$mensaje = " $mensaje ";

		// Reemplazo etiquetas.
		foreach ($users as $k => $v)
		{
			$mensaje = preg_replace('/(\s)(@'.preg_quote($k).')(\s)/', "$1[user=\"{$v}\"]@{$k}[/user]$3", $mensaje);
			//$mensaje = str_replace('@'.$k, "[user=$v]@$k\[/user]", $mensaje);
		}

		// Borro espacios extra.
		$mensaje = trim($mensaje);

		return $users;
	}

	/**
	 * Comentamos una publicación.
	 * @param int $usuario_id ID del usuario que realiza el comentario.
	 * @param int $mensaje Contenido del comentario.
	 * @return int ID del comentario creado.
	 */
	public function comentar($usuario_id, $mensaje)
	{
		list($id,) = $this->db->insert('INSERT INTO shout_comentario (usuario_id, shout_id, comentario, fecha) VALUES (?, ?, ?, ?)', array($usuario_id, $this->primary_key['id'], $mensaje, date('y/m/d H:i:s')));
		return $id;
	}

	/**
	 * Listado de comentarios de una publicación.
	 * @param int $pagina Número de página a mostrar. -1 Para todos los elementos.
	 * @param int $cantidad Cantidad de posts por página.
	 * @return array
	 */
	public function comentarios($pagina, $cantidad = 10)
	{
		// Verifico si es necesario paginar.
		if ($pagina > 0)
		{
			$rst = $this->db->query('SELECT id FROM shout_comentario WHERE shout_id = ? ORDER BY fecha LIMIT '.$start.','.$cantidad, $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM shout_comentario WHERE shout_id = ? ORDER BY fecha', $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);
		}

		// Genero arreglo de objetos.
		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Shout_Comentario($v);
		}
		return $lst;
	}

	/**
	 * Cantidad de comentarios que tiene la publicación.
	 * @return int
	 */
	public function cantidad_comentarios()
	{
		return $this->db->query('SELECT COUNT(*) FROM shout_comentario WHERE shout_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Agregamos el voto a la publicación.
	 * @param int $usuario_id ID del usuario del voto.
	 */
	public function votar($usuario_id)
	{
		$this->db->insert('INSERT INTO shout_voto (shout_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Quitamos el voto de la publicación.
	 * @param int $usuario_id ID del usuario del voto.
	 */
	public function quitar_voto($usuario_id)
	{
		$this->db->delete('DELETE FROM shout_voto WHERE shout_id = ? AND usuario_id = ?', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Verificamos si ya votó la publicación.
	 * @param int $usuario_id ID del usuario del voto.
	 * @return bool
	 */
	public function ya_voto($usuario_id)
	{
		return self::s_ya_voto($this->primary_key['id'], $usuario_id);
	}

	/**
	 * Verificamos si ya votó la publicación.
	 * @param int $shout_id ID de la publicación.
	 * @param int $usuario_id ID del usuario del voto.
	 * @return bool
	 */
	public static function s_ya_voto($shout_id, $usuario_id)
	{
		return Database::get_instance()->query('SELECT * FROM shout_voto WHERE shout_id = ? AND usuario_id = ? LIMIT 1', array($shout_id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * Cantidad de votos que tiene la publicación.
	 * @return int
	 */
	public function cantidad_votos()
	{
		return $this->db->query('SELECT COUNT(*) FROM shout_voto WHERE shout_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Agregamos la publicación como favorita.
	 * @param int $usuario_id ID del usuario que agrega la publicación a sus favoritos.
	 */
	public function favorito($usuario_id)
	{
		$this->db->insert('INSERT INTO shout_favorito (shout_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Quitamos la publicación de sus favoritos.
	 * @param int $usuario_id ID del usuario que quita la publicación de sus favoritos.
	 */
	public function quitar_favorito($usuario_id)
	{
		$this->db->delete('DELETE FROM shout_favorito WHERE shout_id = ? AND usuario_id = ?', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Verificamos si ya está en sus favoritos la publicación.
	 * @param int $usuario_id ID del usuario que verificamos la publicación a sus favoritos.
	 * @return bool
	 */
	public function es_favorito($usuario_id)
	{
		return self::s_es_favorito($this->primary_key['id'], $usuario_id);
	}

	/**
	 * Verificamos si ya está en sus favoritos la publicación.
	 * @param int $shout_id ID de la publicación.
	 * @param int $usuario_id ID del usuario que verificamos la publicación a sus favoritos.
	 * @return bool
	 */
	public static function s_es_favorito($shout_id, $usuario_id)
	{
		return Database::get_instance()->query('SELECT * FROM shout_favorito WHERE shout_id = ? AND usuario_id = ? LIMIT 1', array($shout_id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * Cantidad de favoritos que tiene la publicación.
	 * @return int
	 */
	public function cantidad_favoritos()
	{
		return $this->db->query('SELECT COUNT(*) FROM shout_favorito WHERE shout_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si el usuario ha compartido la foto.
	 * @param int $usuario_id ID del usuario que queremos saber si fue compartido.
	 * @return bool
	 */
	public function fue_compartido($usuario_id)
	{
		return self::s_fue_compartido($this->primary_key['id'], $usuario_id);
	}

	/**
	 * Verificamos si el shout fue compartido.
	 */
	public static function s_fue_compartido($shout_id, $usuario_id)
	{
		$m_shout = new Model_Shout($shout_id);

		// Es mi publicación.
		if ($usuario_id == $m_shout->usuario_id)
		{
			return TRUE;
		}

		// Existe en las compartidas.
		return Database::get_instance()->query('SELECT COUNT(*) FROM suceso WHERE objeto_id = ? AND tipo = \'usuario_shout_compartir\' AND objeto_id1 = ?', array($shout_id, $usuario_id))->get_var(Database_Query::FIELD_INT) > 0;
	}

	/**
	 * Cantidad de veces que fue compartido.
	 * @return int
	 */
	public function cantidad_compartido()
	{
		return $this->db->query('SELECT COUNT(*) FROM suceso WHERE objeto_id = ? AND tipo = \'usuario_shout_compartir\' AND usuario_id = ?', array($this->primary_key['id'], $this->usuario_id))->get_var(Database_Query::FIELD_INT);
	}

}
