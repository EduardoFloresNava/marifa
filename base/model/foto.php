<?php
/**
 * foto.php is part of Marifa.
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
 * Modelo de las fotos.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 *
 * @property-read int $id ID de la foto.
 * @property-read int $usuario_id ID del usuario que creo la foto.
 * @property-read Fechahora $creacion Fecha y hora en la cual se creó la foto.
 * @property-read string $titulo Titulo de la foto.
 * @property-read string $descripcion Descripción de la foto.
 * @property-read string $url URL a cargar de la imagen.
 * @property-read int $estado Estado de la foto.
 * @property-read Fechahora $ultima_visita Fecha de la última visita.
 * @property-read int $visitas Cantidad de visitas. NULL implica que no se computan.
 * @property-read int $categoria_id ID de la categoria a la que pertenece.
 * @property-read bool $comentar Si se puede comentar o no.
 */
class Base_Model_Foto extends Model_Dataset {

	/**
	 * Estado normal de una foto.
	 */
	const ESTADO_ACTIVA = 0;

	/**
	 * Foto oculta que solo se puede ver en el panel de administración.
	 */
	const ESTADO_OCULTA = 1;

	/**
	 * Se ha enviado a la papelera del usuario.
	 */
	const ESTADO_PAPELERA = 2;

	/**
	 * Se ha borrado la foto.
	 */
	const ESTADO_BORRADA = 3;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'foto';

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
		'creacion' => Database_Query::FIELD_DATETIME,
		'titulo' => Database_Query::FIELD_STRING,
		'descripcion' => Database_Query::FIELD_STRING,
		'url' => Database_Query::FIELD_STRING,
		'estado' => Database_Query::FIELD_INT,
		'ultima_visita' => Database_Query::FIELD_DATETIME,
		'visitas' => Database_Query::FIELD_INT,
		'categoria_id' => Database_Query::FIELD_INT,
		'comentar' => Database_Query::FIELD_BOOL
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
	 * Obtenemos el usuario dueño de la foto.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Categoria de la foto.
	 * @return Model_Categoria
	 */
	public function categoria()
	{
		return new Model_Categoria($this->get('categoria_id'));
	}

	/**
	 * Agregamos una nueva visita.
	 */
	public function agregar_visita()
	{
		$this->db->update('UPDATE foto SET visitas = visitas + 1, ultima_visita = ? WHERE id = ?', array(date('Y/m/d H:i:s'), $this->primary_key['id']));

		// Invalidamos información para su nueva carga.
		if (is_array($this->data))
		{
			$this->data = NULL;
		}
	}

	/**
	 * Cantidad de votos de la foto.
	 * @param int $votos NULL para todos, 1 para positivos y -1 para negativos.
	 * @return int
	 */
	public function votos($votos = NULL)
	{
		if ($votos === NULL)
		{
			return $this->db->query('SELECT SUM(cantidad) FROM foto_voto WHERE foto_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		}
		elseif ($votos < 0)
		{
			return $this->db->query('SELECT SUM(cantidad) FROM foto_voto WHERE foto_id = ? AND cantidad < 0', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return $this->db->query('SELECT SUM(cantidad) FROM foto_voto WHERE foto_id = ? AND cantidad > 0', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Tipo de voto dado.
	 * @param int $usuario_id ID del usuario del cual obtener el voto.
	 * @return int
	 */
	public function votos_dados($usuario_id)
	{
		return $this->db->query('SELECT cantidad FROM foto_voto WHERE foto_id = ? AND usuario_id = ?', array($this->primary_key['id'], $usuario_id))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si el usuario ha votado o no.
	 * @param int $usuario_id ID del usuario a verificar.
	 * @return bool
	 */
	public function ya_voto($usuario_id)
	{
		return $this->db->query('SELECT foto_id FROM foto_voto WHERE foto_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Agregamos el voto del usuario a la foto.
	 * @param int $usuario_id ID del usuario que va a votar.
	 * @param bool $positivo Si es un voto positivo o negativo.
	 */
	public function votar($usuario_id, $positivo = TRUE)
	{
		$cantidad = $positivo ? 1 : -1;
		$this->db->insert('INSERT INTO foto_voto (foto_id, usuario_id, cantidad) VALUES (?, ?, ?)', array($this->primary_key['id'], $usuario_id, $cantidad));
	}

	/**
	 * Cantidad de favoritos que tiene la foto.
	 * @return int
	 */
	public function favoritos()
	{
		return $this->db->query('SELECT COUNT(*) FROM foto_favorito WHERE foto_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Verificamos si ya tiene la foto como favorita.
	 * @param int $usuario_id ID del usuario a comprobar.
	 * @return bool
	 */
	public function es_favorito($usuario_id)
	{
		return $this->db->query('SELECT foto_id FROM foto_favorito WHERE foto_id = ? AND usuario_id = ? LIMIT 1', array($this->primary_key['id'], $usuario_id))->num_rows() > 0;
	}

	/**
	 * Agregamos la foto a los favoritos del usuario
	 * @param int $usuario_id ID del usuario que pone la foto como favorita.
	 */
	public function agregar_favorito($usuario_id)
	{
		$this->db->insert('INSERT INTO foto_favorito (foto_id, usuario_id) VALUES (?, ?)', array($this->primary_key['id'], $usuario_id));
	}

	/**
	 * Obtenemos el listado de comentarios.
	 * @return array
	 */
	public function comentarios()
	{
		//TODO: estado de los comentarios de las fotos. Agregarlo a la base de datos.
		$rst = $this->db->query('SELECT id FROM foto_comentario WHERE foto_id = ?', $this->primary_key['id']);
		$rst->set_cast_type(Database_Query::FIELD_INT);

		$lst = array();
		foreach ($rst as $r)
		{
			$lst[] = new Model_Foto_Comentario($r[0]);
		}

		return $lst;
	}

	/**
	 * Verificamos si se puede comentar o no.
	 * @return bool
	 */
	public function soporta_comentarios()
	{
		return $this->db->query('SELECT comentar FROM foto WHERE id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_BOOL) == 1;
	}

	/**
	 * Comentamos en una foto.
	 * @param int $usuario_id ID del usuario que comenta la foto.
	 * @param string $comentario Comentario a insertar.
	 * @return int
	 */
	public function comentar($usuario_id, $comentario)
	{
		list ($id,) = $this->db->insert('INSERT INTO foto_comentario (foto_id, usuario_id, comentario, fecha) VALUES (?, ?, ?, ?)', array($this->primary_key['id'], $usuario_id, $comentario, date('Y/m/d H:i:s')));
		return $id;
	}

	/**
	 * Actualizamos el estado de una foto.
	 * @param int $estado Estado a colocarle a la foto.
	 */
	public function actualizar_estado($estado)
	{
		$this->db->update('UPDATE foto SET estado = ? WHERE id = ?', array($estado, $this->primary_key['id']));
	}

	/**
	 * Creamos una nueva foto.
	 * @param int $usuario_id ID del usuario que crea la foto.
	 * @param string $titulo Título de la foto.
	 * @param string $descripcion Descripción de la foto.
	 * @param string $url URL de la foto.
	 * @param int $categoria ID de la categoria a la cual pertenece la foto.
	 * @param bool $visitas Si mostrar las visitas o no.
	 * @param bool $comentarios Si hay que permitir comentar o no.
	 * @param int $estado Estado inicial de la foto.
	 * @return bool
	 */
	public function crear($usuario_id, $titulo, $descripcion, $url, $categoria, $visitas = TRUE, $comentarios = TRUE, $estado = self::ESTADO_ACTIVA)
	{
		$visitas = $visitas ? 0 : NULL;
		list ($id, $c) = $this->db->insert('INSERT INTO foto (usuario_id, creacion, titulo, descripcion, url, ultima_visita, visitas, categoria_id, comentar, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($usuario_id, date('Y/m/d H:i:s'), $titulo, $descripcion, $url, NULL, $visitas, $categoria, $comentarios, $estado));

		if ($c > 0)
		{
			$this->primary_key['id'] = $id;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Borramos la foto del comentario.
	 */
	public function borrar()
	{
		//TODO: borrar votos y favoritos.
		$this->db->delete('DELETE FROM foto_voto WHERE foto_id = ?', $this->primary_key['id']);
		$this->db->delete('DELETE FROM foto_favorito WHERE foto_id = ?', $this->primary_key['id']);
		$this->db->delete('DELETE FROM foto WHERE id = ?', $this->primary_key['id']);
	}

	/**
	 * Obtenemos un listado de las ultimas fotos.
	 * @param int $pagina Número de paginas a obtener. La primera es 1.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function obtener_ultimas($pagina = 1, $cantidad = 20)
	{
		// Calculamos primer elemento.
		$primero = $cantidad * ($pagina - 1);

		// Obtenemos la lista de sucesos.
		$sucesos = $this->db->query('SELECT id FROM foto WHERE estado = ? ORDER BY creacion DESC LIMIT '.$primero.','.$cantidad, self::ESTADO_ACTIVA)->get_pairs(Database_Query::FIELD_INT);

		$listado = array();
		foreach ($sucesos as $s)
		{
			$listado[] = new Model_Foto($s);
		}

		return $listado;
	}

	/**
	 * Obtenemos un listado de las ultimas fotos de un usuario.
	 * @param int $usuario_id ID del usuario.
	 * @param int $pagina Número de paginas a obtener. La primera es 1.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function obtener_ultimas_usuario($usuario_id, $pagina = 1, $cantidad = 20)
	{
		// Calculamos primer elemento.
		$primero = $cantidad * ($pagina - 1);

		// Obtenemos la lista de sucesos.
		$sucesos = $this->db->query('SELECT id FROM foto WHERE usuario_id = ? AND estado = ? ORDER BY creacion DESC LIMIT '.$primero.','.$cantidad, array($usuario_id, self::ESTADO_ACTIVA))->get_pairs(Database_Query::FIELD_INT);

		$listado = array();
		foreach ($sucesos as $s)
		{
			$listado[] = new Model_Foto($s);
		}

		return $listado;
	}

	/**
	 * Cantidad total de fotos.
	 * @param int $estado Estado de la categoria a contar. NULL para todas.
	 * @param int $usuario ID del usuario dueño de las fotos. NULL para todos.
	 * @return int
	 */
	public static function s_cantidad($estado = NULL, $usuario = NULL)
	{
		if ($estado !== NULL)
		{
			if ($usuario !== NULL)
			{
				return (int) Database::get_instance()->query('SELECT COUNT(*) FROM foto WHERE estado = ? AND WHERE usuario_id = ?', array($estado, $usuario))->get_var(Database_Query::FIELD_INT);
			}
			else
			{
				return (int) Database::get_instance()->query('SELECT COUNT(*) FROM foto WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
			}
		}
		else
		{
			if ($usuario !== NULL)
			{
				return (int) Database::get_instance()->query('SELECT COUNT(*) FROM foto WHERE usuario_id = ?', $usuario)->get_var(Database_Query::FIELD_INT);
			}
			else
			{
				return (int) Database::get_instance()->query('SELECT COUNT(*) FROM foto')->get_var(Database_Query::FIELD_INT);
			}
		}
	}

	/**
	 * Obtengo cantidad de posts por estado.
	 * @return array
	 */
	public static function cantidad_agrupados()
	{
		// Arreglo de estados.
		$categorias = array(
			0 => 'activa',
			1 => 'oculta',
			2 => 'papelera',
			3 => 'borrada'
		);

		// Obtengo grupos.
		$rst = Database::get_instance()->query('SELECT estado, COUNT(*) AS total FROM foto GROUP BY estado')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_INT));

		// Armo arreglo resultado.
		$lst = array();
		foreach ($categorias as $k => $v)
		{
			$lst[$v] = isset($rst[$k]) ? (int) $rst[$k] : 0;
		}

		// Calculo total.
		$lst['total'] = array_sum($lst);

		return $lst;
	}

	/**
	 * Cantidad total de fotos.
	 * @param int $estado Estado de la categoria a contar. NULL para todas.
	 * @return int
	 */
	public function cantidad($estado = NULL)
	{
		return self::s_cantidad($estado);
	}

	/**
	 * Obtenemos listado de categorias que tienen posts y su cantidad.
	 * @return array
	 */
	public static function cantidad_categorias()
	{
		return Database::get_instance()->query('SELECT categoria.nombre, COUNT(foto.id) AS total FROM foto INNER JOIN categoria ON categoria.id = foto.categoria_id GROUP BY foto.categoria_id ORDER BY total DESC')->get_pairs(array(Database_Query::FIELD_STRING, Database_Query::FIELD_INT));
	}

	/**
	 * Cantidad de comentarios en fotos que hay.
	 * @param int $estado Estado de las fotos a contar.
	 * @return int
	 */
	public function cantidad_comentarios($estado = NULL)
	{
		if (isset($this->primary_key['id']) && $this->primary_key['id'] !== NULL)
		{
			if ($estado === NULL)
			{
				return (int) $this->db->query('SELECT COUNT(*) FROM foto_comentario WHERE foto_id = ?', $this->primary_key['id'])->get_var(Database_Query::FIELD_INT);
			}
			else
			{
				return (int) $this->db->query('SELECT COUNT(*) FROM foto_comentario WHERE foto_id = ? AND estado = ?', array($this->primary_key['id'], $estado))->get_var(Database_Query::FIELD_INT);
			}
		}
		else
		{
			if ($estado === NULL)
			{
				return (int) $this->db->query('SELECT COUNT(*) FROM foto_comentario')->get_var(Database_Query::FIELD_INT);
			}
			else
			{
				return (int) $this->db->query('SELECT COUNT(*) FROM foto_comentario WHERE estado = ?', $estado)->get_var(Database_Query::FIELD_INT);
			}
		}
	}

	/**
	 * Listado de fotos existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de fotos por página.
	 * @param int $estado Estado de las fotos a mostrar. NULL para todos los estados.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10, $estado = NULL)
	{
		$start = ($pagina - 1) * $cantidad;

		if ($estado === NULL)
		{
			$rst = $this->db->query('SELECT id FROM foto ORDER BY creacion LIMIT '.$start.','.$cantidad)->get_pairs(Database_Query::FIELD_INT);
		}
		else
		{
			$rst = $this->db->query('SELECT id FROM foto WHERE estado = ? ORDER BY creacion LIMIT '.$start.','.$cantidad, $estado)->get_pairs(Database_Query::FIELD_INT);
		}

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Foto($v);
		}
		return $lst;
	}

	/**
	 * Denunciamos la foto
	 * @param int $usuario_id Quien denuncia.
	 * @param int $motivo El motivo de la denuncia.
	 * @param string $comentario Descripción de la denuncia.
	 * @return int ID de la denuncia.
	 */
	public function denunciar($usuario_id, $motivo, $comentario)
	{
		list($id,) = $this->db->insert('INSERT INTO foto_denuncia (foto_id, usuario_id, motivo, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)',
			array(
				$this->primary_key['id'],
				$usuario_id,
				$motivo,
				$comentario,
				date('Y/m/d H:i:s'),
				Model_Foto_Denuncia::ESTADO_PENDIENTE
			));
		return $id;
	}

	/**
	 * Cantidad de medallas del post.
	 * @return int
	 */
	public function cantidad_medallas()
	{
		return $this->db->query('SELECT COUNT(*) FROM usuario_medalla WHERE tipo = ? AND objeto_id = ?', array(Model_Medalla::TIPO_FOTO, $this->primary_key['id']))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de denuncias que tiene la foto.
	 * @return int
	 */
	public function cantidad_denuncias()
	{
		return $this->db->insert('SELECT COUNT(*) foto_denuncia WHERE foto_id = ?', $this->primary_key['id']);
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
			case Model_Medalla::CONDICION_FOTO_VOTOS_POSITIVOS:
				$cantidad = $this->votos(1);
				break;
			case Model_Medalla::CONDICION_FOTO_VOTOS_NEGATIVOS:
				$cantidad = $this->votos(-1);
				break;
			case Model_Medalla::CONDICION_FOTO_VOTOS_NETOS:
				$cantidad = $this->votos();
				break;
			case Model_Medalla::CONDICION_FOTO_COMENTARIOS:
				$cantidad = $this->cantidad_comentarios(Model_Comentario::ESTADO_VISIBLE);
				break;
			case Model_Medalla::CONDICION_FOTO_VISITAS:
				$cantidad = $this->visitas;
				break;
			case Model_Medalla::CONDICION_FOTO_MEDALLAS:
				$cantidad = $this->cantidad_medallas();
				break;
			case Model_Medalla::CONDICION_FOTO_FAVORITOS:
				$cantidad = $this->favoritos();
				break;
			case Model_Medalla::CONDICION_FOTO_DENUNCIAS:
				$cantidad = $this->cantidad_denuncias();
				break;
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
				$this->db->insert('INSERT INTO usuario_medalla (medalla_id, usuario_id, fecha, objeto_id) VALUES (?, ?, ?, ?);', array($medalla, $this->usuario_id, date('Y/m/d H:i:s'), $this->primary_key['id']));

				// Envio suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear($this->usuario_id, 'usuario_nueva_medalla', TRUE, $medalla);

				return $medalla;
			}
		}
		else
		{
			// No hay medallas.
			return FALSE;
		}
	}
}
