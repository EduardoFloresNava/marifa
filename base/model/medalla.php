<?php
/**
 * medalla.php is part of Marifa.
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
 * Modelo para manejo de las medallas de los usuarios.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read int $id Id de la medalla.
 * @property-read string $nombre Nombre de la medalla.
 * @property-read string $descripcion Descripción de la medalla.
 * @property-read string $imagen Imagen de la medalla.
 * @property-read int $tipo Elemento que se usa para la condición (post, foto, comentario).
 * @property-read int $condicion Tipo de elemento (puntos, votos, favoritos, etc) que se necesita para ganar la medalla.
 * @property-read int $cantidad Cantidad del elemento de condición para ganar la medalla.
 */
class Base_Model_Medalla extends Model_Dataset {

	/**
	 * Asociado a parámetros del usuario.
	 */
	const TIPO_USUARIO = 0;

	/**
	 * Asociado a parámetros del post.
	 */
	const TIPO_POST = 1;

	/**
	 * Asociado a parámetros de la foto.
	 */
	const TIPO_FOTO = 2;

	/**
	 * El usuario obtiene una cantidad de puntos en sus posts.
	 */
	const CONDICION_USUARIO_PUNTOS = 0;

	/**
	 * El usuario obtiene una cantidad de seguidores en sus posts.
	 */
	const CONDICION_USUARIO_SEGUIDORES = 1;

	/**
	 * El usuario sigue una cantidad de usuarios.
	 */
	const CONDICION_USUARIO_SIGUIENDO = 2;

	/**
	 * El usuario ha realizado una cantidad de comentarios en posts.
	 */
	const CONDICION_USUARIO_COMENTARIOS_EN_POSTS = 3;

	/**
	 * El usuario ha realizado una cantidad de comentarios en fotos.
	 */
	const CONDICION_USUARIO_COMENTARIOS_EN_FOTOS = 4;

	/**
	 * El usuario ha publicado una cantidad de posts.
	 */
	const CONDICION_USUARIO_POSTS = 5;

	/**
	 * El usuario ha publicado una cantidad de fotos.
	 */
	const CONDICION_USUARIO_FOTOS = 6;

	/**
	 * El usuario ha ganado una cantidad de medallas.
	 */
	const CONDICION_USUARIO_MEDALLAS = 7;

	/**
	 * El usuario ha llegado a determinado rango.
	 */
	const CONDICION_USUARIO_RANGO = 8; // Sin implementar.

	/**
	 * El post ha ganado una cantidad de puntos.
	 */
	const CONDICION_POST_PUNTOS = 9;

	/**
	 * El post tiene una cantidad de seguidores.
	 */
	const CONDICION_POST_SEGUIDORES = 10;

	/**
	 * El post tiene una cantidad de comentarios.
	 */
	const CONDICION_POST_COMENTARIOS = 11;

	/**
	 * El post tiene una cantidad de favoritos.
	 */
	const CONDICION_POST_FAVORITOS = 12;

	/**
	 * El post tiene una cantidad de denuncias.
	 */
	const CONDICION_POST_DENUNCIAS = 13;

	/**
	 * El post tiene una cantidad de visitas.
	 */
	const CONDICION_POST_VISITAS = 14;

	/**
	 * El post tiene una cantidad de medallas.
	 */
	const CONDICION_POST_MEDALLAS = 15;

	/**
	 * El post se ha compartido una cantidad de veces.
	 */
	const CONDICION_POST_VECES_COMPARTIDO = 16; // Sin implementar.

	/**
	 * La foto tiene una cantidad de votos negativos.
	 */
	const CONDICION_FOTO_VOTOS_POSITIVOS = 17;

	/**
	 * La foto tiene una cantidad de votos negativos.
	 */
	const CONDICION_FOTO_VOTOS_NEGATIVOS = 18;

	/**
	 * La foto tiene una cantidad neta de votos (negativos + positivos).
	 */
	const CONDICION_FOTO_VOTOS_NETOS = 19;

	/**
	 * La foto tiene una cantidad de comentarios.
	 */
	const CONDICION_FOTO_COMENTARIOS = 20;

	/**
	 * La foto tiene una cantidad de visitas.
	 */
	const CONDICION_FOTO_VISITAS = 21;

	/**
	 * La foto tiene una cantidad de medallas.
	 */
	const CONDICION_FOTO_MEDALLAS = 22;

	/**
	 * La foto tiene una cantidad de favoritos.
	 */
	const CONDICION_FOTO_FAVORITOS = 23;

	/**
	 * La foto tiene una cantidad de denuncias.
	 */
	const CONDICION_FOTO_DENUNCIAS = 24;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'medalla';

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
		'nombre' => Database_Query::FIELD_STRING,
		'descripcion' => Database_Query::FIELD_STRING,
		'imagen' => Database_Query::FIELD_STRING,
		'tipo' => Database_Query::FIELD_INT,
		'condicion' => Database_Query::FIELD_INT,
		'cantidad' => Database_Query::FIELD_INT
	);

	/**
	 * Cargamos una medalla.
	 * @param int $id ID de la medalla.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
	}

	/**
	 * Creamos una medalla.
	 * @param string $nombre Nomde de la medalla a crear.
	 * @param string $descripcion Nomde de la descripción de la medalla a crear.
	 * @param string $imagen Imagen de la medalla a crear.
	 * @param int $tipo Tipo de la medalla a crear.
	 * @param int $condicion Condición de la medalla a crear.
	 * @param int $cantidad Cantidad de la medalla a crear.
	 * @return int ID de la medalla creada.
	 */
	public function crear($nombre, $descripcion, $imagen, $tipo, $condicion, $cantidad)
	{
		list($id, ) = $this->db->insert('INSERT INTO medalla (nombre, descripcion, imagen, tipo, condicion, cantidad) VALUES (?, ?, ?, ?, ?, ?)', array($nombre, $descripcion, $imagen, $tipo, $condicion, $cantidad));
		return $id;
	}

	/**
	 * Listado de medallas existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de medallas por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;

		$rst = $this->db->query('SELECT id FROM medalla LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Medalla($v);
		}
		return $lst;
	}

	/**
	 * Cantidad total de medallas existentes.
	 * @return int
	 */
	public static function cantidad()
	{
		return Database::get_instance()->query('SELECT COUNT(*) FROM medalla')->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de usuarios que tienen la medalla.
	 * @return int
	 */
	public function cantidad_usuarios()
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_medalla WHERE medalla_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Listado de usuarios que tienen una medalla.
	 * @return array
	 */
	public function usuarios($pagina = 1, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;

		$lst = $this->db->query('SELECT usuario_id FROM usuario_medalla WHERE medalla_id = ? LIMIT '.$start.', '.$cantidad, $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = new Model_Usuario($v);
		}
		return $lst;
	}

	/**
	 * Borramos el elemento cargado por la clave primaria.
	 */
	public function borrar()
	{
		// Borramos medallas otorgadas.
		$this->db->delete('DELETE FROM usuario_medalla WHERE medalla_id = ?', $this->primary_key['id']);

		// Borramos la medalla.
		$this->db->delete('DELETE FROM medalla WHERE id = ?', $this->primary_key['id']);
	}
}
