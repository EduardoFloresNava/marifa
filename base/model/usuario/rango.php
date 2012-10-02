<?php
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
 * @package		Marifa\Base
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo de los rangos que puede tener un usuario.
 *
 * Reglas para permisos:
 *
 *   A cada rango se le asignan los permisos que puede tener, en caso de ser
 *   permisos que tengan trivialidad para la aplicación de la tarea como por
 *   ejemplo banear (un moderador no puede banear a un administrador) se va
 *   a utilizar el orden.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Model_Usuario_Rango extends Model_Dataset {

	/**
	 * Permiso de administrador general del sitio.
	 * Engloba todas las acciones posibles.
	 */
	const PERMISO_ADMINISTRADOR = 0;

	/**
	 * Permiso de moderador del sitio.
	 * Engloba las acciones de manejo de los contenido del sitio.
	 */
	const PERMISO_MODERADOR = 1;

	/**
	 * Puede dar puntos a un post.
	 */
	const PERMISO_PUNTUAR_POST = 2;

	/**
	 * Puede crear posts.
	 */
	const PERMISO_CREAR_POST = 3;

	/**
	 * Puede comentar los posts que se permitan.
	 */
	const PERMISO_COMENTAR_POST = 4;

	/**
	 * Puede votar los comentarios de los post.
	 */
	const PERMISO_VOTAR_COMENTARIO_POST = 5;

	/**
	 * Puede editar sus comentarios.
	 */
	const PERMISO_EDITAR_COMENTARIO_PROPIO = 6;

	/**
	 * Puede eliminar sus comentarios.
	 */
	const PERMISO_ELIMINAR_COMENTARIO_PROPIO = 7;

	/**
	 * Puede cargar fotos.
	 */
	const PERMISO_CREAR_FOTOS = 8;

	/**
	 * Puede comentar fotos.
	 */
	const PERMISO_COMENTAR_FOTOS = 9;

	/**
	 * Los posts del usuario deberán ser revisados antes de ser mostrados.
	 */
	const PERMISO_REVISAR_POST = 10;

	/**
	 * El usuario puede acceder cuando el sitio está en mantenimiento.
	 * Esto expresa que su IP estará en lista de las permitidas.
	 */
	const PERMISO_ACCESO_MANTENIMIENTO = 11;

	/**
	 * Pueden acceder al panel de moderación.
	 */
	const PERMISO_ACCESO_PANEL_MODERACION = 12;

	/**
	 * Ver y cancelar reportes de usuarios.
	 */
	const PERMISO_DENUNCIAS_USUARIOS = 13;

	/**
	 * Ver y cancelar reportes de fotos.
	 */
	const PERMISO_DENUNCIAS_FOTOS = 14;

	/**
	 * Ver y cancelar reportes de posts.
	 */
	const PERMISO_DENUNCIAS_POSTS = 15;

	/**
	 * Ver y cancelar reportes de mensajes.
	 */
	const PERMISO_DENUNCIAS_MENSAJES = 16;

	/**
	 * Ver los usuarios baneados.
	 * Es para ver los elementos de los comentarios que han sido baneados.
	 */
	const PERMISO_VER_USUARIOS_BANEADOS = 17;

	/**
	 * Ver los posts que hay que en la papelera y los eliminados.
	 */
	const PERMISO_VER_PAPELERA_POSTS = 18;

	/**
	 * Ver las fotos que hay en la papelera y eliminados.
	 */
	const PERMISO_VER_PAPELERA_FOTOS = 19;

	/**
	 * Ver posts que están desaprobados.
	 */
	const PERMISO_VER_POSTS_DESAPROBADOS = 20;

	/**
	 * Ver comentarios que están desaprobados y/o ocultos.
	 */
	const PERMISO_VER_COMENTARIOS_DESPROBADOS = 21;

	/**
	 * Pueden fijar o quitar posts.
	 */
	const PERMISO_FIJAR_POSTS = 22;

	/**
	 * Ver listado de usuarios con cuentas suspendidas.
	 */
	const PERMISO_VER_CUENTAS_DESACTIVADAS = 23;

	/**
	 * Ver listado de usuarios con cuentas baneadas.
	 */
	const PERMISO_VER_CUENTAS_BANEADAS = 24;

	/**
	 * Suspender usuarios.
	 */
	const PERMISO_SUSPENDER_USUARIOS = 25;

	/**
	 * Banear usuarios.
	 */
	const PERMISO_BANEAR_USUARIOS = 26;

	/**
	 * Puede eliminar posts de otros usuarios.
	 */
	const PERMISO_ELIMINAR_POSTS = 27;

	/**
	 * Puede editar posts de otros usuarios.
	 */
	const PERMISO_EDITAR_POSTS = 28;

	/**
	 * Puede ocultar posts de otros usuarios.
	 */
	const PERMISO_OCULTAR_POSTS = 29;

	/**
	 * Puede comentar en posts que tienen los comentarios cerrados.
	 */
	const PERMISO_COMENTAR_POST_CERRADO = 30;

	/**
	 * Editar comentarios en los posts.
	 */
	const PERMISO_EDITAR_COMENTARIOS_POSTS = 31;

	/**
	 * Aprobar/desaprobar comentario en posts y revisión de comentarios.
	 */
	const PERMISO_REVISAR_COMENTARIOS = 32;

	/**
	 * Eliminar comentarios de los post.
	 */
	const PERMISO_ELIMINAR_COMENTARIOS_POSTS = 33;

	/**
	 * Eliminar fotos.
	 */
	const PERMISO_ELIMINAR_FOTOS = 34;

	/**
	 * Eliminar comentario de las fotos.
	 */
	const PERMISO_ELIMINAR_COMENTARIOS_FOTOS = 35;

	/**
	 * Editar fotos.
	 */
	const PERMISO_EDITAR_FOTOS = 36;

	/**
	 * Eliminar publicaciones en los muros.
	 */
	const PERMISO_ELIMINAR_PUBLICACIONES_MUROS = 37;

	/**
	 * Eliminar comentarios en los muros.
	 */
	const PERMISO_ELIMINAR_COMENTARIOS_MUROS = 38;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'usuario_rango';

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
		'orden' => Database_Query::FIELD_INT,
		'nombre' => Database_Query::FIELD_STRING,
		'color' => Database_Query::FIELD_INT,
		'imagen' => Database_Query::FIELD_STRING
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
	 * Listado de permisos del rango.
	 * @return array
	 */
	public function permisos()
	{
		return $this->db->query('SELECT permiso FROM usuario_rango_permiso WHERE rango_id = ?', $this->primary_key['id'])->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));
	}

	/**
	 * Agregamos un permiso a un rango.
	 * @param int $permiso Permiso a agregar.
	 */
	public function agregar_permiso($permiso)
	{
		if ( ! $this->tiene_permiso($permiso))
		{
			$this->db->insert('INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (?, ?)', array($this->primary_key['id'], $permiso));
		}
	}

	/**
	 * Quitamos un permiso a un rango.
	 * @param int $permiso Permiso a borrar.
	 */
	public function borrar_permiso($permiso)
	{
		$this->db->delete('DELETE FROM usuario_rango_permiso WHERE rango_id = ? AND permiso = ?', array($this->primary_key['id'], $permiso));
	}

	/**
	 * Verificamos si el rango tiene el permiso deseado.
	 * @param int $permiso Permiso a buscar.
	 * @return bool
	 */
	public function tiene_permiso($permiso)
	{
		return $this->db->query('SELECT permiso FROM usuario_rango_permiso WHERE rango_id = ? AND permiso = ? LIMIT 1', array($this->primary_key['id'], $permiso))->num_rows() > 0;
	}

	/**
	 * Verificamos si tiene un orden superior o no. Sirve para tareas que requieren
	 * mantener una gerarquía.
	 * @param int $usuario_rango ID del rango a comparar.
	 * @return bool Si es de nivel superior o no.
	 */
	public function es_superior($usuario_rango)
	{
		return $this->db->query('SELECT orden FROM usuario_rango WHERE id = ? LIMIT 1', $usuario_rango)->get_var(Database_Query::FIELD_INT) > $this->orden;
	}

	/**
	 * Cambiamos la posición del rango actual.
	 * @param int $posicion Posición 1-based que debe tener.
	 */
	public function posicionar($posicion)
	{
		$actual = $this->get('orden');

		// Verificamos posición actual.
		if ($posicion != $actual)
		{
			// Movemos todos para abajo.
			$this->db->update('UPDATE usuario_rango SET orden = orden + 1 WHERE orden >= ?', $posicion);

			// Me coloco en la posicion.
			$this->db->update('UPDATE usuario_rango SET orden = ? WHERE id = ?', array($posicion, $this->primary_key['id']));

			if ($posicion < $actual)
			{
				// Corrijo los siguientes.
				$this->db->update('UPDATE usuario_rango SET orden = orden - 1 WHERE orden > ?', $actual);
			}
			else
			{
				// Corrijo los siguientes.
				$this->db->update('UPDATE usuario_rango SET orden = orden - 1 WHERE orden > ?', $posicion);
			}
		}
		$this->update_value('orden', $posicion);
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
		$this->db->delete('DELETE FROM usuario_rango_permiso WHERE rango_id = ?', $this->primary_key['id']);

		// Borramos el rango.
		$this->db->delete('DELETE FROM usuario_rango WHERE id = ?', $this->primary_key['id']);

		return TRUE;
	}

	/**
	 * Obtenemos un arreglo con los usuarios que tienen este rango.
	 * @return array
	 */
	public function usuarios()
	{
		$lst = $this->db->query('SELECT id FROM usuario WHERE rango = ?', $this->primary_key['id'])->get_pairs(Database_Query::FIELD_INT);
		foreach($lst as $k => $v)
		{
			$lst[$k] = new Model_Usuario($v);
		}
		return $lst;
	}

	/**
	 * Verificamos si tiene usuarios asignados el rango.
	 * @return bool
	 */
	public function tiene_usuarios()
	{
		return $this->db->query('SELECT id FROM usuario WHERE rango = ? LIMIT 1', $this->primary_key['id'])->num_rows() > 0;
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

		// Obtenemos el máximo orden.
		$maximo = $this->db->query('SELECT MAX(orden) + 1 FROM usuario_rango')->get_var(Database_Query::FIELD_INT);

		if ($maximo < 0 || $maximo === NULL)
		{
			$maximo = 1;
		}

		// Insertamos el rango.
		list($id, $cant) = $this->db->insert('INSERT INTO usuario_rango (nombre, color, imagen, orden) VALUES (?, ?, ?, ?)', array($nombre, $color, $imagen, $maximo));

		if ($cant > 0)
		{
			// Seteamos el ID del actual.
			$this->primary_key['id'] = $id;

			// Agregamos los permisos.
			if (count($permisos) > 0)
			{
				foreach ($permisos as $permiso)
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
		if ($this->primary_key['id'] !== NULL)
		{
			return $this->db->update('UPDATE usuario_rango SET nombre = ? WHERE id = ?', array($nombre, $this->primary_key['id'])) > 0;
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

		if ($this->primary_key['id'] !== NULL)
		{
			return $this->db->update('UPDATE usuario_rango SET color = ? WHERE id = ?', array($color, $this->primary_key['id'])) > 0;
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
		if ($this->primary_key['id'] !== NULL)
		{
			return $this->db->update('UPDATE usuario_rango SET imagen = ? WHERE id = ?', array($imagen, $this->primary_key['id'])) > 0;
		}
		else
		{
			throw new Exception('No ha definido un rango a actualizar.');
		}
	}

	/**
	 * Listado de rangos.
	 * @return array
	 */
	public function listado()
	{
		$rst = $this->db->query('SELECT id FROM usuario_rango ORDER BY orden DESC')->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Usuario_Rango($v);
		}
		return $lst;
	}
}
