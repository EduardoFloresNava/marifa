<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * post.php is part of Marifa.
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
 * Modelo de los posts.
 *
 * @since      0.1
 * @package    Marifa/Base
 * @subpackage Model
 */
class Base_Model_Post extends Model {

	/**
	 * ID del post.
	 * @var int
	 */
	protected $id;

	/**
	 * Datos del post.
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor del post.
	 * @param int $id ID del post a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->id = $id;
	}

	/**
	 * Obtenemos el valor de un campo del post.
	 * @param string $field Nombre del campo a obtener.
	 * @return mixed
	 */
	public function get($field)
	{
		if ($this->data === NULL)
		{
			// Obtenemos los campos.
			$rst = $this->db->query('SELECT * FROM post WHERE id = ? LIMIT 1', $this->id)
				->get_record(Database_Query::FETCH_ASSOC,
					array(
						'id' => Database_Query::FIELD_INT,
						'usuario_id' => Database_Query::FIELD_INT,
						'post_categoria_id' => Database_Query::FIELD_INT,
						'comunidad_id' => Database_Query::FIELD_INT,
						'fecha' => Database_Query::FIELD_DATETIME,
						'vistas' => Database_Query::FIELD_INT,
						'privado' => Database_Query::FIELD_BOOL,
						'sponsored' => Database_Query::FIELD_BOOL,
						'sticky' => Database_Query::FIELD_BOOL,
						'estado' => Database_Query::FIELD_INT
					)
				);

			if (is_array($rst))
			{
				$this->data = $rst;
			}
		}

		return isset($this->data[$field]) ? $this->data[$field] : NULL;
	}

	/**
	 * Obtenemos una propiedad del post.
	 * @param string $field Nombre del campo.
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Usuario creador del post.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Agregamos una nueva vista al post.
	 */
	public function agregar_vista()
	{
		$this->db->update('UPDATE post SET vistas = vistas + 1 WHERE id = ?', $this->id);
	}

	/**
	 * Compartimos el post.
	 * @param int $usuario_id ID del usuario que va a compartirlo.
	 */
	public function compartir($usuario_id)
	{
		$this->db->insert('INSERT INTO post_compartido (post_id, usuario_id) VALUES (?, ?)', array($this->id, $usuario_id));
	}

	/**
	 * Verificamos si el usuario compartio el post.
	 * @param int $usuario_id ID del usuario a verificar si lo compartio.
	 * @return bool
	 */
	public function fue_compartido($usuario_id)
	{
		return $this->db->query('SELECT post_id FROM post_compartido WHERE post_id = ? AND usuario_id = ? LIMIT 1', array($this->id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos la cantidad de veces que fue compartido.
	 * @return int
	 */
	public function veces_compartido()
	{
		return $this->db->query('SELECT COUNT(*) FROM post_compartido WHERE post_id = ?', $this->id)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Seguimos el post.
	 * @param int $usuario_id ID del usuario que va a serguir el post.
	 */
	public function seguir($usuario_id)
	{
		$this->db->insert('INSERT INTO post_seguidor (post_id, usuario_id) VALUES (?, ?)', array($this->id, $usuario_id));
	}

	/**
	 * Verificamos si el usuario sigue el post.
	 * @param int $usuario_id ID del usuario a verificar si sigue el post.
	 * @return bool
	 */
	public function es_seguidor($usuario_id)
	{
		return $this->db->query('SELECT post_id FROM post_seguidor WHERE post_id = ? AND usuario_id = ? LIMIT 1', array($this->id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos la cantidad de seguidores.
	 * @return int
	 */
	public function cantidad_seguidores()
	{
		return $this->db->query('SELECT COUNT(*) FROM post_seguidor WHERE post_id = ?', $this->id)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Hacemos el post favorito.
	 * @param int $usuario_id ID del usuario que va a convertir el post a favorito.
	 */
	public function favorito($usuario_id)
	{
		$this->db->insert('INSERT INTO post_favorito (post_id, usuario_id) VALUES (?, ?)', array($this->id, $usuario_id));
	}

	/**
	 * Verificamos si el usuario tiene el post como favorito.
	 * @param int $usuario_id ID del usuario a verificar si tiene el post como favorito.
	 * @return bool
	 */
	public function es_favorito($usuario_id)
	{
		return $this->db->query('SELECT post_id FROM post_favorito WHERE post_id = ? AND usuario_id = ? LIMIT 1', array($this->id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos la cantidad de usuario que tienen el post como favorito.
	 * @return int
	 */
	public function cantidad_favoritos()
	{
		return $this->db->query('SELECT COUNT(*) FROM post_favorito WHERE post_id = ?', $this->id)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtenemos el listado de etiquetas del post.
	 * @return array
	 */
	public function etiquetas()
	{
		return $this->db->query('SELECT nombre FROM post_tag WHERE post_id = ?', $this->id)->get_pairs();
	}

	/**
	 * Agregamos 1 o más etiquetas al post.
	 * @param string|array $etiqueta Etiqueta o arreglo de etiquetas.
	 */
	public function agregar_etiqueta($etiqueta)
	{
		// Armamos arreglo de etiquetas.
		if ( ! is_array($etiqueta))
		{
			$etiqueta = array($etiqueta);
		}

		// Agregamos las etiquetas.
		foreach($etiqueta as $e)
		{
			$this->db->insert('INSERT INTO post_tag (post_id, nombre) VALUES (?, ?)', array($this->id, $e));
		}
	}

	/**
	 * Borramos 1 o varias etiquetas.
	 * @param string|array $etiqueta Etiqueta o arreglo con las etiquetas a borrar.
	 */
	public function borrar_etiqueta($etiqueta)
	{
		if (is_array($etiqueta))
		{
			//FIXME: Depurar SQL y reemplazo enh los motores soportados.
			$this->db->delete('DELETE FROM post_tag WHERE post_id = ? AND nombre IN (?)', array($this->id, $etiqueta));
		}
		else
		{
			$this->db->delete('DELETE FROM post_tag WHERE post_id = ? AND nombre = ?', array($this->id, $etiqueta));
		}
	}

	/**
	 * Cantidad de puntos del post.
	 * @return int
	 */
	public function puntos()
	{
		return $this->db->query('SELECT SUM(puntos) FROM post_punto WHERE post_id = ?', $this->id)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si el usuario dio puntos al post.
	 * @param int $usuario_id ID del usuario a verificar si dio puntos.
	 * @return int
	 */
	public function dio_puntos($usuario_id)
	{
		return $this->db->query('SELECT usuario_id FROM post_punto WHERE post_id = ? AND usuario_id = ?', array($this->id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * El usuario da puntos a post
	 * @param int $usuario_id ID del usuario que da los puntos.
	 * @param int $cantidad Cantidad de puntos a dar.
	 */
	public function dar_puntos($usuario_id, $cantidad)
	{
		$this->db->insert('INSERT INTO post_punto (post_id, usuario_id, cantidad) VALUES (?, ?, ?)', array($this->id, $usuario_id, $cantidad));
	}

	/**
	 * Obtenemos la comunidad a la que pertenece el post.
	 * @return Model_Comunidad|null
	 */
	public function comunidad()
	{
		$c = $this->get('comunidad_id');
		if ($c !== NULL)
		{
			return new Model_Comunidad($c);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Obtenemos la categoria del post.
	 * @return Model_Post_Categoria
	 */
	public function categoria()
	{
		return new Model_Post_Categoria($this->get('post_categoria_id'));
	}

	/**
	 * Obtenemos el listado de comentarios del post.
	 * @param int|array $estado Estado o arreglo de estados de los comentarios a obtener.
	 * @return array Arreglo de modelos de comentarios.
	 */
	public function comentarios($estado = 0)
	{
		//TODO: UTILIZAR ESTADO DE LOS COMENTARIOS.
		//TODO: IMPLEMENTAR UTILIZACION DIRECTA DE MODELOS EN LOS RESULTADOS.
		//TODO: DIFERENCIAR 1 estado de un arreglo. Mejora rendimiento SQL.
		$rst = $this->db->query('SELECT id FROM post_comentario WHERE post_id = ? AND estado IN (?)', array($this->id, $estado));
		$rst->set_cast_type(array('id' => Database_Query::FIELD_INT));

		$lst = array();
		foreach($rst as $v)
		{
			$lst[] = new Model_Post_Comentario($v['id']);
		}
		return $lst;
	}

	/**
	 * Agregamos un comentario al post.
	 * @param int $usuario_id Quien realiza el comentario.
	 * @param string $contenido Contenido del comentario.
	 */
	public function comentar($usuario_id, $contenido)
	{
		$this->db->insert('INSERT INTO post_comentario (post_id, usuario_id, fecha, contenido, estado) VALUES (?, ?, ?, ?, ?)',
			array(
				$this->id,
				$usuario_id,
				date('Y/m/d H:i:s'),
				$contenido,
				0, //TODO: Ver los estados.
			));
	}

	/**
	 * Denunciamos el post.
	 * @param int $usuario_id Quien denuncia.
	 * @param int $motivo El motivo de la denuncia.
	 * @param string $comentario Descripción de la denuncia.
	 */
	public function denunciar($usuario_id, $motivo, $comentario)
	{
		$this->db->insert('INSERT INTO post_denuncia (post_id, usuario_id, motivo, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)',
			array(
				$this->id,
				$usuario_id,
				$motivo,
				$comentario,
				date('Y/m/d H:i:s'),
				0 //TODO: VER ESTADOS.
			));
	}

	/**
	 * Verificamos si existen denuncias del usuario-
	 * @param int $usuario_id
	 * @return bool
	 */
	public function existe_denuncia($usuario_id)
	{
		//TODO: Ver estado necesario para no poder enviar.
		return $this->db->query('SELECT id FROM post_denuncia WHERE post_id = ? AND usuario_id = ?', array($this->id, $usuario_id))->num_rows() > 0;
	}

	/**
	 * Seteamos un nuevo estado al post.
	 * @param int $estado Estado a setear.
	 */
	public function actualizar_estado($estado)
	{
		$this->db->update('UPDATE post SET estado = ? WHERE id = ?', array($estado, $this->id));
	}

	/**
	 * Seteamos o quitamos el parámetro sticky de un post.
	 * @param int $sticky Sticky o no.
	 */
	public function setear_sticky($sticky)
	{
		$this->db->update('UPDATE post SET sticky = ? WHERE id = ?', array($sticky, $this->id));
	}

	/**
	 * Seteamos o quitamos la categoria de esponsoreado de un post.
	 * @param int $sponsored Si se debe poner sponsoreado.
	 */
	public function setear_sponsored($sponsored)
	{
		$this->db->update('UPDATE post SET sponsored = ? WHERE id = ?', array($sponsored, $this->id));
	}

	/**
	 * Seteamos como privado o publico un estado.
	 * @param bool $privado Si se setea como privado o no.
	 */
	public function setear_privado($privado)
	{
		$this->db->update('UPDATE post SET privado = ? WHERE id = ?', array($privado, $this->id));
	}

	/**
	 * Actualizamos el contenido de un post.
	 * @param string $contenido Contenido nuevo del post.
	 */
	public function modificar_contenido($contenido)
	{
		$this->db->update('UPDATE post SET contenido = ? WHERE id = ?', array($contenido, $this->id));
	}

	/**
	 * Cambiamos la categoria del post.
	 * @param int $categoria_id Categoria nueva para el post.
	 */
	public function cambiar_categoria($categoria_id)
	{
		//FIXME: Verificar si es lógico cambiar de categoria al post.
		$this->db->update('UPDATE post SET post_catergoria_id = ? WHERE id = ?', array($categoria_id, $this->id));
	}

}