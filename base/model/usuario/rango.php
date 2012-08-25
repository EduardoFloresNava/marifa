<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * rango.php is part of Marifa.
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
 * Modelo de los rangos que puede tener un usuario.
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Usuario_Rango extends Model {

	/**
	 * ID del rango
	 * @var int
	 */
	protected $id;

	/**
	 * Datos del rango.
	 * @var int
	 */
	protected $data;


	/**
	 * Constructor de la clase.
	 * @param int $id Id del rango.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->id = $id;
	}

	/**
	 * Obtenemos el valor de un campo del rango.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		if ($this->data === NULL)
		{
			// Obtenemos los campos.
			$rst = $this->db->query('SELECT id, nombre, color, imagen FROM usuario_rango WHERE id = ? LIMIT 1', $this->id)
				->get_record(Database_Query::FETCH_ASSOC, array('id' => Database_Query::FIELD_INT, 'color' => Database_Query::FIELD_INT));

			if (is_array($rst))
			{
				$this->data = $rst;
			}
		}

		return isset($this->data[$field]) ? $this->data[$field] : NULL;
	}

	/**
	 * Obtenemos una propiedad del rango del usuario.
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Listado de permisos del rango.
	 * @return array
	 */
	public function permisos()
	{
		return $this->db->query('SELECT permiso FROM usuario_rango_permiso WHERE rango_id = ?', $this->id)->get_pairs();
	}

	/**
	 * Agregamos un permiso a un rango.
	 * @param string $permiso Permiso a agregar.
	 */
	public function agregar_permiso($permiso)
	{
		if ( ! $this->tiene_permiso($permiso))
		{
			$this->db->insert('INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (?, ?)', $this->id, $permiso);
		}
	}

	/**
	 * Quitamos un permiso a un rango.
	 * @param string $permiso Permiso a borrar.
	 */
	public function borrar_permiso($permiso)
	{
		$this->db->delete('DELETE FROM usuario_rango_permiso WHERE rango_id = ? AND permiso = ?', array($this->id, $permiso));
	}

	/**
	 * Verificamos si el rango tiene el permiso deseado.
	 * @param string $permiso Permiso a buscar.
	 * @return bool
	 */
	public function tiene_permiso($permiso)
	{
		return $this->db->query('SELECT permiso FROM usuario_rango_permiso WHERE rango_id = ? AND permiso = ? LIMIT 1', array($this->id, $permiso))->num_rows() > 0;
	}

	/**
	 * Borramos un rango. Para poder borrarlo no debe tener usuarios asociados.
	 * @return bool
	 * @throws Exception Posee usuarios asignados.
	 */
	public function borrar_rango()
	{
		// Verificamos que no tenga usuario.
		if ($this->tiene_usuarios())
		{
			throw new Exception('El rango no puede ser borrado por tener usuarios asignados.');
		}

		// Borramos la lista de permisos.
		$this->db->delete('DELETE FROM usuario_rango_permiso WHERE rango_id = ?', $this->id);

		// Borramos el rango.
		$this->db->delete('DELETE FROM usuario_rango WHERE id = ?', $this->id);

		return TRUE;
	}

	/**
	 * Obtenemos un arreglo con los IDs de los usuarios que tienen este rango.
	 * @return array
	 */
	public function usuarios()
	{
		return $this->db->query('SELECT id FROM usuario WHERE rango = ?', $this->id)->get_pairs(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si tiene usuarios asignados el rango.
	 * @return bool
	 */
	public function tiene_usuarios()
	{
		return $this->db->query('SELECT id FROM usuario WHERE rango = ? LIMIT 1', $this->id)->num_rows() > 0;
	}

	/**
	 * Creamos un nuevo rango.
	 * @param string $nombre Nombre del rango.
	 * @param int|string $color Color del rango.
	 * @param string $imagen Imagen del rango.
	 * @param array $permisos Listado de permisos a dar.
	 * @return bool Resultado de la inserción.
	 * @throws Exception Ya existe el rango.
	 */
	public function nuevo_rango($nombre, $color, $imagen, $permisos = array())
	{
		// Verificamos no exista un rango con ese nombre.
		if ($this->db->query('SELECT COUNT(*) FROM usuario_rango WHERE nombre = ?', $nombre)->get_var(Database_Query::FIELD_INT) > 0)
		{
			throw new Exception('Ya existe un rango con ese nombre.');
		}

		// Fomateamos el color.
		if ( ! is_int($color))
		{
			// Lo convertimos a entero.
			$color = hexdec($color);
		}

		// Insertamos el rango.
		list($id, $cant) = $this->db->insert('INSERT INTO usuario_rango (nombre, color, imagen) VALUES (?, ?, ?)', array($nombre, $color, $imagen));

		if ($cant > 0)
		{
			// Seteamos el ID del actual.
			$this->id = $id;

			// Agregamos los permisos.
			if (count($permisos) > 0)
			{
				foreach($permisos as $permiso)
				{
					$this->agregar_permiso($permiso);
				}

				return TRUE; // Inserción correcta.
			}
			else
			{
				return TRUE; // Inserción correcta.
			}
		}
		else
		{
			return FALSE; // No se pudo insertar.
		}
	}

	/**
	 * Cambiamos el nombre de un grupo.
	 * @param string $nombre Nombre a utilizar.
	 * @return bool Si se actualizó correctamente o no.
	 * @throws Exception Si no hay un ID de rango definido.
	 */
	public function renombrar($nombre)
	{
		if ($this->id !== NULL)
		{
			return $this->db->update('UPDATE usuario_rango SET nombre = ? WHERE id = ?', array($nombre, $this->id)) > 0;
		}
		else
		{
			throw new Exception('No ha definido un rango a actualizar.');
		}
	}

	/**
	 * Cambiamos el color de un rango.
	 * @param string|int $color Cadena hexadecimal o entero.
	 * @return bool Si se actualizó correctamente o no.
	 * @throws Exception Si no hay un ID de rango definido.
	 */
	public function cambiar_color($color)
	{
		if ( ! is_int($color))
		{
			// Lo convertimos a entero.
			$color = hexdec($color);
		}

		if ($this->id !== NULL)
		{
			return $this->db->update('UPDATE usuario_rango SET color = ? WHERE id = ?', array($color, $this->id)) > 0;
		}
		else
		{
			throw new Exception('No ha definido un rango a actualizar.');
		}
	}

	/**
	 * Cambiamos al imagen de un rango.
	 * @param string $imagen Nombre de la imagen a utilizar.
	 * @return bool Si se actualizó correctamente o no.
	 * @throws Exception Si no hay un ID de rango definido.
	 */
	public function cambiar_imagen($imagen)
	{
		if ($this->id !== NULL)
		{
			return $this->db->update('UPDATE usuario_rango SET imagen = ? WHERE id = ?', array($imagen, $this->id)) > 0;
		}
		else
		{
			throw new Exception('No ha definido un rango a actualizar.');
		}
	}
}
