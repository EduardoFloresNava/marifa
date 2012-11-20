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
 * @property-read int $id ID del rango.
 * @property-read int $orden Orden que ocupa el rango.
 * @property-read string $nombre Nombre del rango.
 * @property-read int $color Color que representa al rango.
 * @property-read string $imagen Imagen del rango.
 * @property-read int $puntos Puntos que se otorgan por día.
 */
class Base_Model_Usuario_Rango extends Model_Dataset {

	/**
	 * Ver las denuncias de usuarios y actuar sobre ellas.
	 */
	const PERMISO_USUARIO_VER_DENUNCIAS = 0;

	/**
	 * Ver suspensiones de usuarios y modificarlas.
	 */
	const PERMISO_USUARIO_SUSPENDER = 1;

	/**
	 * Ver baneos a usuarios y modificarlos.
	 */
	const PERMISO_USUARIO_BANEAR = 2;

	/**
	 * Enviar advertencias a usuarios.
	 */
	const PERMISO_USUARIO_ADVERTIR = 3;

	/**
	 * Revisar posts y fotos agregadas por el usuario.
	 * Es decir, el contenido creado por el usuario va a revisión antes de postearse.
	 */
	const PERMISO_USUARIO_REVISAR_CONTENIDO = 4;

	/**
	 * Permite realizar tareas de administración de usuarios.
	 * Entre ellas está la asignación de rangos, su creación, etc.
	 */
	const PERMISO_USUARIO_ADMINISTRAR = 5;

	/**
	 * Puede crear un post.
	 */
	const PERMISO_POST_CREAR = 20;

	/**
	 * Puede dar puntos a un post.
	 */
	const PERMISO_POST_PUNTUAR = 21;

	/**
	 * Eliminar posts de TODOS los usuarios.
	 */
	const PERMISO_POST_ELIMINAR = 22;

	/**
	 * Oculta/muestra posts de TODOS los usuarios.
	 */
	const PERMISO_POST_OCULTAR = 23;

	/**
	 * Ver las denuncias de posts y actuar sobre ellas.
	 */
	const PERMISO_POST_VER_DENUNCIAS = 24;

	/**
	 * Ver los posts que no se encuentran aprobados.
	 */
	const PERMISO_POST_VER_DESAPROBADO = 25;

	/**
	 * Modificar el parámetro sticky y sponsored de los posts.
	 */
	const PERMISO_POST_FIJAR_PROMOVER = 26;

	/**
	 * Editar posts de TODOS los usuarios.
	 */
	const PERMISO_POST_EDITAR = 27;

	/**
	 * Ver los posts que se encuentran en la papelera de TODOS los usuarios.
	 */
	const PERMISO_POST_VER_PAPELERA = 28;

	/**
	 * Puede agregar fotos.
	 */
	const PERMISO_FOTO_CREAR = 40;

	/**
	 * Puede votar las fotos.
	 */
	const PERMISO_FOTO_VOTAR = 41;

	/**
	 * Eliminar fotos de TODOS los usuarios.
	 */
	const PERMISO_FOTO_ELIMINAR = 42;

	/**
	 * Oculta/muestra fotos de TODOS los usuarios.
	 */
	const PERMISO_FOTO_OCULTAR = 43;

	/**
	 * Ver las denuncias y actuar sobre ellas.
	 */
	const PERMISO_FOTO_VER_DENUNCIAS = 44;

	/**
	 * Ver el contenido que no se encuentra aprobado.
	 */
	const PERMISO_FOTO_VER_DESAPROBADO = 45;

	/**
	 * Editar fotos de TODOS los usuarios.
	 */
	const PERMISO_FOTO_EDITAR = 46;

	/**
	 * Ver la papelera de TODOS los usuarios.
	 */
	const PERMISO_FOTO_VER_PAPELERA = 47;

	/**
	 * Crear comentarios.
	 */
	const PERMISO_COMENTARIO_COMENTAR = 60;

	/**
	 * Comentar aún cuando están cerrados.
	 */
	const PERMISO_COMENTARIO_COMENTAR_CERRADO = 61;

	/**
	 * Puede votar comentarios.
	 */
	const PERMISO_COMENTARIO_VOTAR = 62;

	/**
	 * Puede eliminar comentarios de TODOS los usuarios.
	 */
	const PERMISO_COMENTARIO_ELIMINAR = 63;

	/**
	 * Ocultar y mostrar comentarios de TODOS los usuarios.
	 */
	const PERMISO_COMENTARIO_OCULTAR = 64;

	/**
	 * Editar comentarios de TODOS los usuarios.
	 */
	const PERMISO_COMENTARIO_EDITAR = 65;

	/**
	 * Ver los comentarios que se encuentran desaprobados y tomar acciones sobre ellos.
	 */
	const PERMISO_COMENTARIO_VER_DESAPROBADO = 66;

	/**
	 * Puede ingresar aún con el sitio en mantenimiento.
	 */
	const PERMISO_SITIO_ACCESO_MANTENIMIENTO = 80;

	/**
	 * Permisos para modificar configuraciones globales, acciones sobre temas y
	 * plugins. Modificar la publicidades y todo lo relacionado a configuracion
	 * general.
	 */
	const PERMISO_SITIO_CONFIGURAR = 81;

	/**
	 * Acceso a la administración de contenido del panel de administración.
	 */
	const PERMISO_SITIO_ADMINISTRAR_CONTENIDO = 82;

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
	 * Elementos para el arreglo de rangos.
	 * @var array
	 */
	protected $list = array('key' => 'id', 'value' => 'nombre');

	/**
	 * Listado de campos y sus tipos.
	 * @var array
	 */
	protected $fields = array(
		'id' => Database_Query::FIELD_INT,
		'orden' => Database_Query::FIELD_INT,
		'nombre' => Database_Query::FIELD_STRING,
		'color' => Database_Query::FIELD_INT,
		'imagen' => Database_Query::FIELD_STRING,
		'puntos' => Database_Query::FIELD_INT
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
	 * Obtenemos la cantidad de rangos.
	 * @return int
	 */
	public function cantidad()
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_rango_permiso')->get_var(Database_Query::FIELD_INT);
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
		// Obtengo la lista de rangos y su posición.
		$lista = $this->db->query('SELECT orden, id FROM usuario_rango ORDER BY orden ASC')->get_pairs(array('id' => Database_Query::FIELD_INT, 'orden' => Database_Query::FIELD_INT));

		// Posición del actual.
		$actual = $this->get('orden');

		// Verificamos posición actual.
		if ($posicion != $actual)
		{
			// Guardo ID en una variable auxiliar.
			$aux = $lista[$actual];
			if ($actual < $posicion)
			{
				// Muevo los elementos.
				for($i = $actual; $i < $posicion; $i++)
				{
					$this->db->update('UPDATE usuario_rango SET orden = ? WHERE id = ?', array($i, $lista[$i + 1]));
					//$lista[$i] = $lista[$i + 1];
				}
			}
			else
			{
				// Muevo los elementos.
				for($i = $actual; $i > $posicion; $i--)
				{
					$this->db->update('UPDATE usuario_rango SET orden = ? WHERE id = ?', array($i, $lista[$i - 1]));
					//$lista[$i] = $lista[$i - 1];
				}
			}
			// Posiciono en su lugar final.
			$this->db->update('UPDATE usuario_rango SET orden = ? WHERE id = ?', array($posicion, $aux));
			//$lista[$posicion] = $aux;
			unset($aux, $lista);

			$this->update_value('orden', $posicion);
		}
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
		foreach ($lst as $k => $v)
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
	 * @param int $puntos Cantidad de puntos por día.
	 * @param array $permisos Listado de permisos a dar.
	 * @return bool Resultado de la inserción.
	 * @throws Exception Ya existe el rango.
	 */
	public function nuevo_rango($nombre, $color, $imagen, $puntos, $permisos = array())
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
		list($id, $cant) = $this->db->insert('INSERT INTO usuario_rango (nombre, color, imagen, orden, puntos) VALUES (?, ?, ?, ?, ?)', array($nombre, $color, $imagen, $maximo, $puntos));

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
	 * @param int $orden Orden máximo a mostrar. Si no se especifica devolvemos todos.
	 * @return array
	 */
	public function listado($orden = NULL)
	{
		if ($orden === NULL)
		{
			$rst = $this->db->query('SELECT id FROM usuario_rango ORDER BY orden ASC')->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM usuario_rango WHERE orden >= ? ORDER BY orden ASC', $orden)->get_pairs(Database_Query::FIELD_INT);
		}

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Usuario_Rango($v);
		}
		return $lst;
	}
}
