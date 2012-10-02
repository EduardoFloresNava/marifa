<?php
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
 * @package		Marifa\Base
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo de los posts.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 *
 * @property-read int $id ID del post.
 * @property-read int $usuario_id ID del usuario dueño del post.
 * @property-read int $categoria_id ID de la categoria a la cual pertenece el post.
 * @property-read string $titulo Titulo del post.
 * @property-read string $contenido Contenido del post. Es BBCode sin procesar.
 * @property-read Fechahora $fecha Fecha de creación del post.
 * @property-read int $vistas Cantidad de visitas que tuvo el post.
 * @property-read bool $privado Si el post es privado o lo pueden ver los usuarios sin registrarse.
 * @property-read bool $sponsored Si el post es patrocinado o no.
 * @property-read bool $sticky Si el post esta fijo a la portada.
 * @property-read int $estado Estado del post.
 */
class Base_Model_Post extends Model_Dataset {

	/**
	 * Post preparado para ser mostrado.
	 */
	const ESTADO_ACTIVO = 0;

	/**
	 * Post guardado como borrador.
	 */
	const ESTADO_BORRADOR = 1;

	/**
	 * Post eliminado.
	 */
	const ESTADO_BORRADO = 2;

	/**
	 * El post esta pendiente de moderación.
	 */
	const ESTADO_PENDIENTE = 3;

	/**
	 * El post está oculto.
	 * Puede ser producto de una acción de moderación o de un moderador.
	 */
	const ESTADO_OCULTO = 4;

	/**
	 * El post fue rechazado.
	 */
	const ESTADO_RECHAZADO = 5;

	/**
	 * El post está en la papelera del usuario.
	 */
	const ESTADO_PAPELERA = 6;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'post';

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
		'usuario_id' => Database_Query::FIELD_INT,
		'categoria_id' => Database_Query::FIELD_INT,
		'titulo' => Database_Query::FIELD_STRING,
		'contenido' => Database_Query::FIELD_STRING,
		'fecha' => Database_Query::FIELD_DATETIME,
		'vistas' => Database_Query::FIELD_INT,
		'privado' => Database_Query::FIELD_BOOL,
		'sponsored' => Database_Query::FIELD_BOOL,
		'sticky' => Database_Query::FIELD_BOOL,
		'estado' => Database_Query::FIELD_INT
	);

	/**
	 * Constructor del post.
	 * @param int $id ID del post a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
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
		$this->db->update('UPDATE post SET vistas = vistas + 1 WHERE id = ?', $this->primary_key['id']);
		$this->update_value('vistas', $this->get('vistas') + 1);
	}

	/**
	 * Actualizamos la fecha de creación al momento actual.
	 */
	public function actualizar_fecha()
	{
		$fecha = new Fechahora();
		$this->db->update('UPDATE post SET fecha = ? WHERE id = ?', array($fecha->format('Y/m/d H:i:s'), $this->primary_key['id']));
		$this->update_value('fecha', $fecha);
	}

	/**
	 * Compartimos el post.
	 * @param int $usuario_id ID del usuario que va a compartirlo.
	 */
	public function compartir($usuario_id)
	{
		// Invalidamos la cache.
		Cache::get_instance()->delete('post_'.$this->primary_key['id'].'_compartido');
		$this->db->insert('INSERT INTO post_compartido (post_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Verificamos si el usuario compartio el post.
	 * @param int $usuario_id ID del usuario a verificar si lo compartio.
	 * @return bool
	 */
	public function fue_compartido($usuario_id)
	{
		return $this->db->query('SELECT post_id FROM post_compartido WHERE post_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos la cantidad de veces que fue compartido.
	 * @return int
	 */
	public function veces_compartido()
	{
		// Obtenemos la cache.
		$cantidad = Cache::get_instance()->get('post_'.$this->primary_key['id'].'_compartido');

		// Validamos estado.
		if ($cantidad === FALSE)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM post_compartido WHERE post_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Actualizamos la cache.
			Cache::get_instance()->save('post_'.$this->primary_key['id'].'_compartido', $cantidad);
		}
		return $cantidad;
	}

	/**
	 * Seguimos el post.
	 * @param int $usuario_id ID del usuario que va a serguir el post.
	 */
	public function seguir($usuario_id)
	{
		// Invalidamos la cache.
		Cache::get_instance()->delete('post_'.$this->primary_key['id'].'_seguido');
		$this->db->insert('INSERT INTO post_seguidor (post_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Verificamos si el usuario sigue el post.
	 * @param int $usuario_id ID del usuario a verificar si sigue el post.
	 * @return bool
	 */
	public function es_seguidor($usuario_id)
	{
		return $this->db->query('SELECT post_id FROM post_seguidor WHERE post_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos la cantidad de seguidores.
	 * @return int
	 */
	public function cantidad_seguidores()
	{
		// Obtenemos la cache.
		$cantidad = Cache::get_instance()->get('post_'.$this->primary_key['id'].'_seguido');

		// Validamos estado.
		if ($cantidad === FALSE)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM post_seguidor WHERE post_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Actualizamos la cache.
			Cache::get_instance()->save('post_'.$this->primary_key['id'].'_seguido', $cantidad);
		}
		return $cantidad;
	}

	/**
	 * Hacemos el post favorito.
	 * @param int $usuario_id ID del usuario que va a convertir el post a favorito.
	 */
	public function favorito($usuario_id)
	{
		// Invalidamos la cache.
		Cache::get_instance()->delete('post_'.$this->primary_key['id'].'_favorito');
		$this->db->insert('INSERT INTO post_favorito (post_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Verificamos si el usuario tiene el post como favorito.
	 * @param int $usuario_id ID del usuario a verificar si tiene el post como favorito.
	 * @return bool
	 */
	public function es_favorito($usuario_id)
	{
		return $this->db->query('SELECT post_id FROM post_favorito WHERE post_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Obtenemos la cantidad de usuario que tienen el post como favorito.
	 * @return int
	 */
	public function cantidad_favoritos()
	{
		// Obtenemos la cache.
		$cantidad = Cache::get_instance()->get('post_'.$this->primary_key['id'].'_favorito');

		// Validamos estado.
		if ($cantidad === FALSE)
		{
			$cantidad = $this->db->query('SELECT COUNT(*) FROM post_favorito WHERE post_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Actualizamos la cache.
			Cache::get_instance()->save('post_'.$this->primary_key['id'].'_favorito', $cantidad);
		}
		return $cantidad;
	}

	/**
	 * Obtenemos el listado de etiquetas del post.
	 * @return array
	 */
	public function etiquetas()
	{
		return $this->db->query('SELECT nombre FROM post_tag WHERE post_id = ?', $this->primary_key['id'])->get_pairs();
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
		foreach ($etiqueta as $e)
		{
			$this->db->insert('INSERT INTO post_tag (post_id, nombre) VALUES (?, ?)', array($this->primary_key['id'], $e));
		}

		// Refrescamos la cache de etiquetas.
		$this->update_tag_list();
	}

	/**
	 * Actualizamos la cache de etiquetas de un post.
	 */
	protected function update_tag_list()
	{
		$keys = implode(', ', $this->db->query('SELECT nombre FROM post_tag WHERE post_id = ?', $this->primary_key['id'])->get_pairs());
		if ($keys !== '')
		{
			$this->db->update('UPDATE post SET tags = ? WHERE id = ?', array($keys, $this->primary_key['id']));
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
			$this->db->delete('DELETE FROM post_tag WHERE post_id = ? AND nombre IN (?)', array($this->primary_key['id'], $etiqueta));
		}
		else
		{
			$this->db->delete('DELETE FROM post_tag WHERE post_id = ? AND nombre = ?', array($this->primary_key['id'], $etiqueta));
		}

		// Refrescamos la cache de etiquetas.
		$this->update_tag_list();
	}

	/**
	 * Cantidad de puntos del post.
	 * @return int
	 */
	public function puntos()
	{
		// Obtenemos la cache.
		$cantidad = Cache::get_instance()->get('post_'.$this->primary_key['id'].'_puntos');

		// Validamos estado.
		if ($cantidad === FALSE)
		{
			$cantidad = (int) $this->db->query('SELECT SUM(cantidad) FROM post_punto WHERE post_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);

			// Actualizamos la cache.
			Cache::get_instance()->save('post_'.$this->primary_key['id'].'_puntos', $cantidad);
		}
		return $cantidad;
	}

	/**
	 * Verificamos si el usuario dio puntos al post.
	 * @param int $usuario_id ID del usuario a verificar si dio puntos.
	 * @return int
	 */
	public function dio_puntos($usuario_id)
	{
		return $this->db->query('SELECT usuario_id FROM post_punto WHERE post_id = ? AND usuario_id = ?', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * El usuario da puntos a post
	 * @param int $usuario_id ID del usuario que da los puntos.
	 * @param int $cantidad Cantidad de puntos a dar.
	 */
	public function dar_puntos($usuario_id, $cantidad)
	{
		// Invalidamos la cache.
		Cache::get_instance()->delete('post_'.$this->primary_key['id'].'_puntos');

		$this->db->insert('INSERT INTO post_punto (post_id, usuario_id, cantidad) VALUES (?, ?, ?)', array($this->primary_key['id'], $usuario_id, $cantidad));
		$this->db->update('UPDATE usuario SET puntos_disponibles = puntos_disponibles - ? WHERE id = ?', array($cantidad, $usuario_id));
	}

	/**
	 * Obtenemos la cantidad de puntos que dio el usuario.
	 * @param int $usuario_id ID del usuario a verificar.
	 * @return int
	 */
	public function puntos_dados($usuario_id)
	{
		return $this->db->query('SELECT cantidad FROM post_punto WHERE post_id = ? AND usuario_id = ?', array($this->primary_key['id'], $usuario_id))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtenemos la categoria del post.
	 * @return Model_Post_Categoria
	 */
	public function categoria()
	{
		return new Model_Categoria($this->get('categoria_id'));
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
		if ($estado === NULL)
		{
			$rst = $this->db->query('SELECT id FROM post_comentario WHERE post_id = ?', $this->primary_key['id']);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM post_comentario WHERE post_id = ? AND estado IN (?)', array($this->primary_key['id'], $estado));
		}
		$rst->set_cast_type(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post_Comentario($v[0]);
		}
		return $lst;
	}

	/**
	 * Agregamos un comentario al post.
	 * @param int $usuario_id Quien realiza el comentario.
	 * @param string $contenido Contenido del comentario.
	 * @return int
	 */
	public function comentar($usuario_id, $contenido)
	{
		list($id, $c) = $this->db->insert('INSERT INTO post_comentario (post_id, usuario_id, fecha, contenido, estado) VALUES (?, ?, ?, ?, ?)',
			array(
				$this->primary_key['id'],
				$usuario_id,
				date('Y/m/d H:i:s'),
				$contenido,
				0, //TODO: Ver los estados.
			));
		return ($c > 0) ? $id : FALSE;
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
				$this->primary_key['id'],
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
		return $this->db->query('SELECT id FROM post_denuncia WHERE post_id = ? AND usuario_id = ?', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Seteamos un nuevo estado al post.
	 * @param int $estado Estado a setear.
	 */
	public function actualizar_estado($estado)
	{
		$this->db->update('UPDATE post SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
	}

	/**
	 * Seteamos o quitamos el parámetro sticky de un post.
	 * @param int $sticky Sticky o no.
	 */
	public function setear_sticky($sticky)
	{
		$this->db->update('UPDATE post SET sticky = ? WHERE id = ?', array($sticky, $this->primary_key['id']));
	}

	/**
	 * Seteamos o quitamos la categoria de esponsoreado de un post.
	 * @param int $sponsored Si se debe poner sponsoreado.
	 */
	public function setear_sponsored($sponsored)
	{
		$this->db->update('UPDATE post SET sponsored = ? WHERE id = ?', array($sponsored, $this->primary_key['id']));
	}

	/**
	 * Seteamos como privado o publico un estado.
	 * @param bool $privado Si se setea como privado o no.
	 */
	public function setear_privado($privado)
	{
		$this->db->update('UPDATE post SET privado = ? WHERE id = ?', array($privado, $this->primary_key['id']));
	}

	/**
	 * Actualizamos el contenido de un post.
	 * @param string $contenido Contenido nuevo del post.
	 */
	public function modificar_contenido($contenido)
	{
		$this->db->update('UPDATE post SET contenido = ? WHERE id = ?', array($contenido, $this->primary_key['id']));
	}

	/**
	 * Cambiamos la categoria del post.
	 * @param int $categoria_id Categoria nueva para el post.
	 */
	public function cambiar_categoria($categoria_id)
	{
		//FIXME: Verificar si es lógico cambiar de categoria al post.
		$this->db->update('UPDATE post SET catergoria_id = ? WHERE id = ?', array($categoria_id, $this->primary_key['id']));
	}

	/**
	 * Creamos un nuevo post.
	 * @param int $usuario_id Usuario que crea el post.
	 * @param string $titulo Título del post.
	 * @param string $contenido Contenido del post.
	 * @param int $categoria_id Categoria del post.
	 * @param bool $privado Solo usuarios registrados.
	 * @param bool $sponsored Post patrocinado.
	 * @param bool $sticky Post fijo en la portada.
	 * @param int $estado Estado con el cual se publica el post.
	 * @return int
	 */
	public function crear($usuario_id, $titulo, $contenido, $categoria_id, $privado, $sponsored, $sticky, $estado = self::ESTADO_ACTIVO)
	{
		list($id,) = $this->db->insert(
			'INSERT INTO post ( usuario_id, categoria_id, titulo, contenido, fecha, vistas, privado, sponsored, sticky, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
			array(
				$usuario_id,
				$categoria_id,
				$titulo,
				$contenido,
				date('Y/m/d H:i:s'),
				0,
				$privado,
				$sponsored,
				$sticky,
				$estado
			)
		);

		return $id;
	}

	/**
	 * Obtenemos el listado de los últimos posts.
	 * @param int $pagina Número de página empezando en 1.
	 * @param int $cantidad Cantidad de post por página.
	 * @return array
	 */
	public function obtener_ultimos($pagina = 1, $cantidad = 10)
	{
		// Primer elemento a devolver.
		$inicio = $cantidad * ($pagina - 1);

		// Obtenemos el listado.
		$rst = $this->db->query('SELECT id FROM post WHERE estado = 0 ORDER BY fecha DESC LIMIT '.$inicio.', '.$cantidad)->get_pairs(Database_Query::FIELD_INT);

		// Armamos la lista.
		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post($v);
		}

		return $lst;
	}

	/**
	 * Obtenemos el listado de los posts más puntuados.
	 * @param int $pagina Número de página empezando en 1.
	 * @param int $cantidad Cantidad de post por página.
	 * @return array
	 */
	public function obtener_tops($pagina = 1, $cantidad = 10)
	{
		// Primer elemento a devolver.
		$inicio = $cantidad * ($pagina - 1);

		// Obtenemos el listado.
		$rst = $this->db->query('SELECT SUM(post_punto.cantidad) as puntos, post.id FROM post LEFT JOIN post_punto ON post.id = post_punto.post_id WHERE post.estado = 0 GROUP BY post.id ORDER BY puntos DESC LIMIT '.$inicio.', '.$cantidad)->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));

		// Armamos la lista.
		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post($v);
		}

		return $lst;
	}

	/**
	 * Cantidad total de posts.
	 * @param int $estado Estado de la categoria a contar. NULL para todas.
	 * @param int $categoria Categoria a contar. NULL para todas.
	 * @return int
	 */
	public function cantidad($estado = NULL, $categoria = NULL)
	{
		$key = 'post_total';

		if ($estado !== NULL)
		{
			$where = ' WHERE estado = ?';
			$condiciones = $estado;
			$key .= 'e_'.$estado;
		}

		if ($categoria !== NULL)
		{
			$where = isset($where) ? ' AND categoria = ?' : ' WHERE categoria = ?';
			$condiciones = isset($condiciones) ? array($condiciones, $categoria) : $categoria;
			$key .= 'c_'.$categoria;
		}

		$rst = Cache::get_instance()->get($key);
		if ( ! $rst)
		{
			$rst = $this->db->query('SELECT COUNT(*) FROM post'.(isset($where) ? $where : ''), (isset($condiciones) ? $condiciones : NULL))->get_var(Database_Query::FIELD_INT);

			Cache::get_instance()->save($key, $rst);
		}

		return $rst;
	}

	/**
	 * Cantidad de post que deben ser corregidos por los usuarios para ser publicados.
	 * @return int
	 */
	public function cantidad_correccion()
	{
		return $this->db->query('SELECT COUNT(*) FROM post_moderado INNER JOIN post ON post_moderado.padre_id = post.id WHERE post.estado = 1')->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Obtenemos listado de categorias que tienen posts y su cantidad.
	 * @return array
	 */
	public function cantidad_categorias()
	{
		return $this->db->query('SELECT categoria_id, SUM(id) AS total FROM post WHERE estado = 0 GROUP BY categoria_id ORDER BY total DESC')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));
	}

	/**
	 * Cantidad de comentarios en posts que hay.
	 * Si hay una clave primaria es la cantidad que hay en un post.
	 * @return int
	 */
	public function cantidad_comentarios()
	{
		if ($this->primary_key['id'] !== NULL)
		{
			$key = 'post_'.$this->primary_key['id'].'_comentarios_total';
		}
		else
		{
			$key = 'post_comentarios_total';
		}

		$rst = Cache::get_instance()->get($key);
		if ( ! $rst)
		{
			if ($this->primary_key['id'] !== NULL)
			{
				$rst = $this->db->query('SELECT COUNT(*) FROM post_comentario WHERE post_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
			}
			else
			{
				$rst = $this->db->query('SELECT COUNT(*) FROM post_comentario')->get_var(Database_Query::FIELD_INT);
			}

			Cache::get_instance()->save($key, $rst);
		}

		return $rst;
	}

	/**
	 * Buscamos posts segun un texto.
	 * @param string $query Palabras a buscar.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elemento por página.
	 * @param int $categoria ID de la categoria en donde buscar. Si no se especifica son todas.
	 * @param int $usuario ID del usuario dueño de los post a buscar.
	 * @return array Primer elemento son un arreglo con los resultados y el segundo la cantidad total de elementos.
	 */
	public function buscar($query, $pagina = 1, $cantidad = 10, $categoria = NULL, $usuario = NULL)
	{
		// Condiciones para categoria y usaurio.
		$where = '';
		$condiciones = array();

		// Agrego consulta a la lista de parametros.
		$condiciones[] = $query;

		// Agrego categoria.
		if ($categoria !== NULL)
		{
			$where .= 'AND post.post_categoria_id = ?';
			$condiciones[] = $categoria;
		}

		// Agrego usuario.
		if ($usuario !== NULL)
		{
			$where .= ' AND post.usuario_id = ?';
			$condiciones[] = $usuario;
		}

		// Cantidad de elementos.
		$total = $this->db->query('SELECT COUNT(*) FROM post WHERE MATCH (`titulo`, `contenido`, `tags`) AGAINST(? IN BOOLEAN MODE) AND estado = 0 '.$where, $condiciones)->get_var(Database_Query::FIELD_INT);

		// Verificamos que existan resultados.
		if ($total == 0)
		{
			return array(array(), 0);
		}

		// Primer elemento.
		$first = ($pagina - 1) * $cantidad;

		// Obtenemos la lista de elementos.
		$rst = $this->db->query('SELECT id FROM post WHERE MATCH (`titulo`, `contenido`, `tags`) AGAINST(? IN BOOLEAN MODE) AND estado = 0 '.$where.' LIMIT '.$first.', '.$cantidad, $condiciones)->get_pairs(Database_Query::FIELD_INT);

		// Listado de elementos.
		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post($v);
		}

		// Generamos la salida.
		return array($lst, $total);
	}

	/**
	 * Buscamos posts relacionados al post actual.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elemento por página.
	 * @return array Primer elemento son un arreglo con los resultados y el segundo la cantidad total de elementos.
	 */
	public function buscar_relacionados($pagina = 1, $cantidad = 10)
	{
		// Cantidad de elementos.
		$total = $this->db->query('SELECT COUNT(*) FROM post INNER JOIN post_tag ON post.id = post_tag.post_id WHERE post.estado = 0 AND post_tag.nombre IN (SELECT nombre FROM post_tag WHERE post_id = ?) AND post.id != ? GROUP BY post.id', array($this->primary_key['id'], $this->primary_key['id']))->get_var(Database_Query::FIELD_INT);

		// Verificamos que existan resultados.
		if ($total == 0)
		{
			return array(array(), 0);
		}

		// Primer elemento.
		$first = ($pagina - 1) * $cantidad;

		// Obtenemos la lista de elementos.
		$rst = $this->db->query('SELECT post.id, COUNT(post.id) AS cantidad FROM post INNER JOIN post_tag ON post.id = post_tag.post_id WHERE post.estado = 0 AND post_tag.nombre IN (SELECT nombre FROM post_tag WHERE post_id = ?) AND post.id != ? GROUP BY post.id ORDER BY cantidad DESC LIMIT '.$first.', '.$cantidad, array($this->primary_key['id'], $this->primary_key['id']))->get_pairs(Database_Query::FIELD_INT);

		// Listado de elementos.
		$lst = array();
		foreach ($rst as $k => $v)
		{
			$lst[] = new Model_Post($k);
		}

		// Generamos la salida.
		return array($lst, $total);
	}

	/**
	 * Listado de posts existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de posts por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10)
	{
		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM post ORDER BY fecha LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Post($v);
		}
		return $lst;
	}

	/**
	 * Agregamos una moderación al post.
	 * @param int $usuario_id ID del usuario que realiza la moderación.
	 * @param int $motivo Tipo de motivo para realizar la moderación.
	 * @param string $razon Razon si corresponde o NULL en caso contrario.
	 * @param bool $borrador Si creamos un borrador o no.
	 * @return bool|int Si crea borrador el ID de ese, sino bool en función del resultado.
	 */
	public function moderar($usuario_id, $motivo, $razon = NULL, $borrador = FALSE)
	{
		// Verifico si es necesario borrador.
		if ($borrador)
		{
			// Creo el post.
			$id = $this->crear($this->get('usuario_id'), $this->get('titulo'), $this->get('contenido'), $this->get('categoria_id'), $this->get('privado'), $this->get('sponsored'), $this->get('sticky'), self::ESTADO_BORRADOR);
		}
		else
		{
			$id = NULL;
		}

		// Creo la moderación.
		$m_p = new Model_Post_Moderado;
		$rst = $m_p->crear($this->primary_key['id'], $usuario_id, $motivo, $id, $razon);

		// Actualizo el estado actual.
		$this->actualizar_estado(self::ESTADO_BORRADO);

		// Envio respuesta.
		if ($id !== NULL)
		{
			return $id;
		}
		else
		{
			return $rst;
		}
	}

	/**
	 * Objeto de moderación o NULL si no posee.
	 * @return Model_Post_Moderado|NULL
	 */
	public function moderacion()
	{
		if ($this->db->query('SELECT COUNT(*) FROM post_moderado WHERE post_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT) > 0)
		{
			return new Model_Post_Moderado($this->primary_key['id']);
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

		return $this->db->query('SELECT SUM(post_punto.cantidad) AS puntos, post.id, post.titulo, categoria.imagen FROM post LEFT JOIN post_punto ON post.id = post_punto.post_id INNER JOIN categoria ON post.categoria_id = categoria.id WHERE post.estado = 0'.$where.' GROUP BY post.id ORDER BY puntos DESC LIMIT 10', $params)
			->get_records(Database_Query::FETCH_ASSOC, array(
				'puntos' => Database_Query::FIELD_INT,
				'id' => Database_Query::FIELD_INT,
				'titulo' => Database_Query::FIELD_STRING,
				'imagen' => Database_Query::FIELD_STRING
		));
	}

	/**
	 * Obtenemos el listado de posts con más favoritos.
	 * @param int $categoria
	 * @param int $intervalo
	 */
	public function top_favoritos($categoria = NULL, $intervalo = 0)
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

		return $this->db->query('SELECT COUNT(post_favorito.post_id) AS favoritos, post.id, post.titulo, categoria.imagen FROM post LEFT JOIN post_favorito ON post.id = post_favorito.post_id INNER JOIN categoria ON post.categoria_id = categoria.id WHERE post.estado = 0'.$where.' GROUP BY post.id ORDER BY favoritos DESC LIMIT 10', $params)
			->get_records(Database_Query::FETCH_ASSOC, array(
				'favoritos' => Database_Query::FIELD_INT,
				'id' => Database_Query::FIELD_INT,
				'titulo' => Database_Query::FIELD_STRING,
				'imagen' => Database_Query::FIELD_STRING
		));
	}

	/**
	 * Obtenemos el listado de posts con más comentarios.
	 * @param int $categoria
	 * @param int $intervalo
	 */
	public function top_comentarios($categoria = NULL, $intervalo = 0)
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

		return $this->db->query('SELECT COUNT(*) AS comentarios, post.id, post.titulo, categoria.imagen FROM post LEFT JOIN post_comentario ON post.id = post_comentario.post_id INNER JOIN categoria ON post.categoria_id = categoria.id WHERE post.estado = 0 AND post_comentario.estado = 0'.$where.' GROUP BY post.id ORDER BY comentarios DESC LIMIT 10', $params)
			->get_records(Database_Query::FETCH_ASSOC, array(
				'comentarios' => Database_Query::FIELD_INT,
				'id' => Database_Query::FIELD_INT,
				'titulo' => Database_Query::FIELD_STRING,
				'imagen' => Database_Query::FIELD_STRING
		));
	}

	/**
	 * Obtenemos el listado de posts con más seguidores.
	 * @param int $categoria
	 * @param int $intervalo
	 */
	public function top_seguidores($categoria = NULL, $intervalo = 0)
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

		return $this->db->query('SELECT COUNT(post_seguidor.post_id) AS seguidores, post.id, post.titulo, categoria.imagen FROM post LEFT JOIN post_seguidor ON post.id = post_seguidor.post_id INNER JOIN categoria ON post.categoria_id = categoria.id WHERE post.estado = 0'.$where.' GROUP BY post.id ORDER BY seguidores DESC LIMIT 10', $params)
			->get_records(Database_Query::FETCH_ASSOC, array(
				'seguidores' => Database_Query::FIELD_INT,
				'id' => Database_Query::FIELD_INT,
				'titulo' => Database_Query::FIELD_STRING,
				'imagen' => Database_Query::FIELD_STRING
		));
	}

}
