<?php defined('APP_BASE') or die('No direct access allowed.');
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
 * @package		Marifa/Base
 * @subpackage  Model
 */

/**
 * Modelo de categorias de los posts.
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Post_Categoria extends Model {

	/**
	 * ID de la categoria actual.
	 * @var int
	 */
	protected $id;

	/**
	 * Campos de la categoria cargada.
	 * @var array
	 */
	protected $data;

	/**
	 * Cargamos una categoria.
	 * @param int $id Id de la categoria.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();
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
			$rst = $this->db->query('SELECT * FROM post_categoria WHERE id = ? LIMIT 1', $this->id)
				->get_record(array('id' => Database_Query::FIELD_INT));

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
	 * Cargamos los datos de una categoria por su SEO.
	 * @param int $seo SEO a cargar.
	 */
	public function load_by_seo($seo)
	{
		$rst = $this->db->query('SELECT * FROM post_categoria WHERE seo = ? LIMIT 1', $seo)
			->get_record(array('id' => Database_Query::FIELD_INT));

		if (is_array($rst))
		{
			$this->id = $rst['id'];
			$this->data = $rst;
		}
	}

	/**
	 * Cargamos los datos de una categoria por su nombre.
	 * @param string $nombre Nombre a cargar.
	 */
	public function load_by_nombre($nombre)
	{
		$rst = $this->db->query('SELECT * FROM post_categoria WHERE nombre = ? LIMIT 1', $nombre)
			->get_record(array('id' => Database_Query::FIELD_INT));

		if (is_array($rst))
		{
			$this->id = $rst['id'];
			$this->data = $rst;
		}
	}

	/**
	 * Dejamos solo letras y números y convertimos los espacios en -.
	 * @param string $nombre Nombre a convertir.
	 * @return string
	 */
	protected function make_seo($nombre)
	{
		return preg_replace('/\s+/', '-', preg_replace('/[^A-Za-z0-9\s]/', '', trim($nombre)));
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
		if ($this->existe_seo($seo))
		{
			throw new Exception('Existe un campo con ese nombre');
		}

		// Actualizamos los datos.
		$this->db->update('UPDATE post_categoria SET nombre = ? AND seo = ? WHERE id = ?', array($nombre, $seo, $this->id));
	}

	/**
	 * Verificamos si existe un campo son el mismo SEO.
	 * @param string $seo SEO a buscar.
	 */
	public function existe_seo($seo)
	{
		return $this->db->query('SELECT id FROM post_categoria WHERE seo = ? LIMIT 1', array($seo))->num_rows() > 0;
	}

	/**
	 * Actualizamos la imagen de la categoria.
	 * @param string $imagen Imagen a colocar.
	 */
	public function cambiar_imagen($imagen)
	{
		$this->db->update('UPDATE post_categoria SET imagen = ? WHERE id = ?', array($imagen, $this->id));
	}

	/**
	 * Borramos la categoria. Si tiene posts asociados se lanza una excepcion.
	 * @throws Exception
	 */
	public function borrar()
	{
		// Verificamos que no tenga posts.
		if ($this->tiene_posts())
		{
			throw new Exception('No se puede borrar la categoria porque tiene posts asociados.');
		}

		// Borramos.
		$this->db->delete('DELETE FROM post_categoria WHERE id = ?', $this->id);
	}

	/**
	 * Obtenemos listado de posts de esta categoria.
	 * @return array
	 */
	public function posts()
	{
		// Obtenemos la lista.
		$rst = $this->db->query('SELECT id FROM post WHERE post_categoria_id = ?', $this->id);
		$rst->set_cast_type(array('id' => Database_Query::FIELD_INT));

		$lst = array();
		foreach($rst as $v)
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
		return $this->db->query('SELECT COUNT(*) FROM post WHERE post_categoria_id = ?', $this->id)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si posee posts.
	 * @return bool
	 */
	public function tiene_posts()
	{
		return $this->db->query('SELECT post_categoria_id FROM post WHERE post_categoria_id = ? LIMIT 1', $this->id)->num_rows() > 0;
	}
}
