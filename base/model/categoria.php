<?php
/**
 * categoria.php is part of Marifa.
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
 * Modelo de categorias de los posts, fotos y comunidades.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read int $id ID de la categoria.
 * @property-read string $nombre Nombre completo de la categoria.
 * @property-read string $seo Texto seo de la categoria.
 * @property-read string $imagen Nombre de la imagen a utilizar.
 */
class Base_Model_Categoria extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'categoria';

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
		'seo' => Database_Query::FIELD_STRING,
		'imagen' => Database_Query::FIELD_STRING,
	);

	/**
	 * Cargamos una categoria.
	 * @param int $id Id de la categoria.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->primary_key['id'] = $id;
	}

	/**
	 * Cargamos los datos de una categoria por su SEO.
	 * @param int $seo SEO a cargar.
	 */
	public function load_by_seo($seo)
	{
		$rst = parent::load(array('seo' => $seo));
		$this->primary_key['id'] = $this->get('id');
		return $rst;
	}

	/**
	 * Cargamos los datos de una categoria por su nombre.
	 * @param string $nombre Nombre a cargar.
	 */
	public function load_by_nombre($nombre)
	{
		parent::load(array('nombre' => $nombre));
		$this->primary_key['id'] = $this->get('id');
	}

	/**
	 * Dejamos solo letras y números y convertimos los espacios en -.
	 * @param string $nombre Nombre a convertir.
	 * @return string
	 */
	public function make_seo($nombre)
	{
		return self::make_seo_s($nombre);
	}

	/**
	 * Dejamos solo letras y números y convertimos los espacios en -.
	 * @param string $nombre Nombre a convertir.
	 * @return string
	 */
	public static function make_seo_s($nombre)
	{
		return preg_replace('/\s+/u', '-', preg_replace('/[^a-z0-9\s]/u', '', str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('a', 'e', 'i', 'o', 'u', 'n'), trim(strtolower($nombre)))));
	}

	/**
	 * Cambiamos el nombre de una categoria.
	 * Automáticamente se actualiza el link SEO.
	 * @param string $nombre
	 */
	public function cambiar_nombre($nombre)
	{
		// Obtenemos seo.
		$seo = $this->make_seo($nombre);

		// Verificamos que no exista.
		if ($this->existe_seo($seo, TRUE))
		{
			throw new Exception('Existe un campo con ese nombre');
		}

		// Actualizamos los datos.
		$this->db->update('UPDATE categoria SET nombre = ?, seo = ? WHERE id = ?', array($nombre, $seo, $this->primary_key['id']));
	}

	/**
	 * Creo una nueva categoria.
	 * @param string $nombre Nombre de la categoria.
	 * @param string $imagen Imagen de la categoria.
	 * @return int ID de la categoria.
	 */
	public function nueva($nombre, $imagen)
	{
		// Obtenemos seo.
		$seo = $this->make_seo($nombre);

		// Creamos la categoria.
		list($id,) = $this->db->insert('INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array($nombre, $seo, $imagen));

		return $id;
	}

	/**
	 * Verificamos si existe un campo son el mismo SEO.
	 * @param string $seo SEO a buscar.
	 * @param bool $not_current Si omitimos el actual.
	 */
	public function existe_seo($seo, $not_current = FALSE)
	{
		if ($not_current)
		{
			return $this->db->query('SELECT id FROM categoria WHERE seo = ? AND id != ? LIMIT 1', array($seo, $this->primary_key['id']))->num_rows() > 0;
		}
		else
		{
			return $this->db->query('SELECT id FROM categoria WHERE seo = ? LIMIT 1', array($seo))->num_rows() > 0;
		}
	}

	/**
	 * Actualizamos la imagen de la categoria.
	 * @param string $imagen Imagen a colocar.
	 */
	public function cambiar_imagen($imagen)
	{
		$this->db->update('UPDATE categoria SET imagen = ? WHERE id = ?', array($imagen, $this->primary_key['id']));
	}

	/**
	 * Borramos la categoria. Si tiene posts asociados se lanza una excepcion.
	 * @throws Exception
	 */
	public function borrar()
	{
		// Verificamos que no tenga posts.
		if ($this->tiene_posts() || $this->tiene_fotos())
		{
			throw new Exception('No se puede borrar la categoria porque tiene posts y/o fotos asociados.');
		}

		// Borramos.
		$this->db->delete('DELETE FROM categoria WHERE id = ?', $this->primary_key['id']);
	}

	/**
	 * Obtenemos listado de posts de esta categoria.
	 * @return array
	 */
	public function posts()
	{
		// Obtenemos la lista.
		$rst = $this->db->query('SELECT id FROM post WHERE categoria_id = ?', $this->primary_key['id']);
		$rst->set_cast_type(array('id' => Database_Query::FIELD_INT));

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post($v['id']);
		}

		return $lst;
	}

	/**
	 * Obtenemos la cantidad de post que tiene asignados la categoria.
	 * @return int
	 */
	public function cantidad_posts()
	{
		return $this->db->query('SELECT COUNT(*) FROM post WHERE categoria_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si posee posts.
	 * @return bool
	 */
	public function tiene_posts()
	{
		return $this->db->query('SELECT categoria_id FROM post WHERE categoria_id = ? LIMIT 1', $this->primary_key['id'])->num_rows() > 0;
	}

	/**
	 * Obtenemos listado de fotos de esta categoria.
	 * @return array
	 */
	public function fotos()
	{
		// Obtenemos la lista.
		$rst = $this->db->query('SELECT id FROM foto WHERE categoria_id = ?', $this->primary_key['id']);
		$rst->set_cast_type(array('id' => Database_Query::FIELD_INT));

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Foto($v['id']);
		}

		return $lst;
	}

	/**
	 * Cantidad de categorias que existen.
	 * @return int
	 */
	public function cantidad()
	{
		return $this->db->query('SELECT COUNT(*) FROM categoria')->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtenemos la cantidad de post que tiene asignados la categoria.
	 * @return int
	 */
	public function cantidad_fotos()
	{
		return $this->db->query('SELECT COUNT(*) FROM foto WHERE categoria_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si posee fotos.
	 * @return bool
	 */
	public function tiene_fotos()
	{
		return $this->db->query('SELECT categoria_id FROM foto WHERE categoria_id = ? LIMIT 1', $this->primary_key['id'])->num_rows() > 0;
	}

	/**
	 * Listado de categorias disponibles.
	 * @return array
	 */
	public function lista()
	{
		return $this->db->query('SELECT id, seo, nombre, imagen FROM categoria ORDER BY nombre ASC')
				->get_records(
						Database_Query::FETCH_ASSOC,
						array(
							'id'     => Database_Query::FIELD_INT,
							'seo'    => Database_Query::FIELD_STRING,
							'nombre' => Database_Query::FIELD_STRING,
							'imagen' => Database_Query::FIELD_STRING
						)
				);
	}
}
