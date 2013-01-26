<?php
/**
 * suceso.php is part of Marifa.
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
 * Modelo para manejo de sucesos.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read int $id ID del suceso.
 * @property-read int $objeto_id ID del objeto al que apunta el suceso.
 * @property-read int $objeto_id1 ID del objeto al que apunta el suceso.
 * @property-read int $objeto_id2 ID del objeto al que apunta el suceso.
 * @property-read string $tipo Tipo de suceso.
 * @property-read bool $notificar Si se muestra en la barra de sucesos o no.
 * @property-read bool $visto Si fue visto o no.
 * @property-read bool $desplegado Si se ha visto el suceso en la lista.
 * @property-read fechahora $fecha Cuando se producio el suceso.
 */
class Base_Model_Suceso extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'suceso';

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
		'objeto_id' => Database_Query::FIELD_INT,
		'objeto_id1' => Database_Query::FIELD_INT,
		'objeto_id2' => Database_Query::FIELD_INT,
		'tipo' => Database_Query::FIELD_STRING,
		'notificar' => Database_Query::FIELD_BOOL,
		'visto' => Database_Query::FIELD_BOOL,
		'desplegado' => Database_Query::FIELD_BOOL,
		'fecha' => Database_Query::FIELD_DATETIME
	);

	/**
	 * Constructor del suceso.
	 * @param int $id ID del suceso a cargar.
	 * @param array $data Listado de información para carga automática.
	 */
	public function __construct($id = NULL, $data = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
		if (is_array($data))
		{
			$this->data = $data;
		}
	}

	/**
	 * Agregamos un nuevo suceso para un usuario.
	 * @param int|array $usuario_id ID del usuario dueño del suceso o arreglo
	 * de dueños. De esta forma todos los usuarios involucrados tiene el mismo
	 * suceso.
	 * @param string $tipo Tipo de suceso.
	 * @param bool $notificar Si el suceso va a la barra de notificaciones o no.
	 * @param int $objeto_id ID del objeto del suceso.
	 * @param int $objeto_id2 ID secundario del objeto del suceso.
	 * @param int $objeto_id3 ID terciario del objeto del suceso.
	 * @param int $fecha Timestamp con la fecha del suceso. NULL para la hora actual.
	 */
	public function crear($usuario_id, $tipo, $notificar, $objeto_id, $objeto_id2 = NULL, $objeto_id3 = NULL, $fecha = NULL)
	{
		// Creo la fecha.
		if ($fecha == NULL)
		{
			$fecha = time();
		}

		if (is_array($usuario_id))
		{
			// Eliminamos repetidos.
			$usuario_id = array_unique($usuario_id, SORT_NUMERIC);

			// Ejecutamos las consultas.
			$rst = array();
			foreach ($usuario_id as $id)
			{
				$rst[] = $this->crear($id, $tipo, $notificar, $objeto_id, $objeto_id2 = NULL, $objeto_id3 = NULL, $fecha = NULL);
			}
			
			return $rst;
		}
		else
		{
			return $this->db->insert('INSERT INTO suceso (usuario_id, objeto_id, objeto_id1, objeto_id2, tipo, fecha, notificar) VALUES (?, ?, ?, ?, ?, ?, ?)',
				array($usuario_id, $objeto_id, $objeto_id2, $objeto_id3, $tipo, date('Y/m/d H:i:s'), $notificar));
		}
	}

	/**
	 * Seteamos las notificaciones del usuario como vistas.
	 * @param int $usuario ID del usuario del que se setean como vistas.
	 */
	public function vistas($usuario)
	{
		return $this->db->query('UPDATE suceso SET visto = 1, desplegado = 1 WHERE usuario_id = ?', $usuario);
	}

	/**
	 * Seteamos la notificación como desplegada
	 * @param int $id ID de la notificación a setear como desplegada.
	 */
	public function desplegado($id)
	{
		return $this->db->query('UPDATE suceso SET desplegado = 1 WHERE id = ?', $id);
	}

	/**
	 * Obtenemos un listado de sucesos del usuario
	 * @param int $usuario_id ID del usuario dueño del suceso.
	 * @param int $pagina Número de paginas a obtener. La primera es 1.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function obtener_by_usuario($usuario_id, $pagina = 1, $cantidad = 20)
	{
		// Calculamos primer elemento.
		$primero = $cantidad * ($pagina - 1);

		// Obtenemos la lista de sucesos.
		$sucesos = $this->db->query('SELECT * FROM suceso WHERE usuario_id = ? ORDER BY fecha DESC LIMIT '.$primero.','.$cantidad, $usuario_id);

		$sucesos->set_cast_type($this->fields);
		$sucesos->set_fetch_type(Database_Query::FETCH_ASSOC);

		$listado = array();
		foreach ($sucesos as $s)
		{
			$listado[] = new Model_Suceso($s['id'], $s);
		}

		return $listado;
	}

	/**
	 * Procesamos el suceso obteniendo toda la información asociada.
	 * Se basa en la utilización de la clase Suceso.
	 * @return array
	 */
	public function get_data()
	{
		return Suceso::procesar($this->as_array());
	}

	/**
	 * Obtenemos la cantidad de sucesos segun su dueño y tipo.
	 * @param int $usuario ID del usuario a dueño de los sucesos. NULL para todos.
	 * @param array $tipo Arreglo de tipos, NULL para todos.
	 * @param bool $notificar Si hay mostramos con un tipo de notificar o ninguno.
	 * @param bool $visto Si hay mostramos con un estado visto o ninguno.
	 * @param bool $desplegado Si hay mostramos con un estado desplagado o ninguno.
	 * @return int
	 */
	public function cantidad($usuario = NULL, $tipo = NULL, $notificar = NULL, $visto = NULL, $desplegado = NULL)
	{
		$params = array();
		$q = array();

		// Arreglo de parametros.
		if (is_array($usuario))
		{
			$params = $usuario;

			$qa = 'usuario_id IN(';
			for ($i = 0; $i < count($params); $i++)
			{
				$qa .= '?, ';
			}
			$q = array(substr($qa, 0, -2).')');
			unset($qa);
		}
		else
		{
			$params = array($usuario);
			$q = array('usuario_id = ?');
		}

		// Agrego limitacion tipo.
		if ($tipo !== NULL)
		{
			$params = array_merge($params, $tipo);

			// Armo la lista de estados.
			$kk = array();
			$c = count($tipo);
			for ($i = 0; $i < $c; $i++)
			{
				$kk[] = '?';
			}
			$q[] = 'tipo IN ('.implode(', ', $kk).')';
		}

		// Agrego limitacion notificar.
		if ($notificar !== NULL)
		{
			$params[] = $notificar;
			$q[] = 'notificar = ?';
		}

		// Agrego limitacion visto.
		if ($visto !== NULL)
		{
			$params[] = $visto;
			$q[] = 'visto = ?';
		}

		// Agrego limitacion desplegado.
		if ($desplegado !== NULL)
		{
			$params[] = $desplegado;
			$q[] = 'desplegado = ?';
		}

		// Ejecuto la consulta.
		if (count($params) == 0)
		{
			return $this->db->query('SELECT COUNT(*) FROM suceso')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return $this->db->query('SELECT COUNT(*) FROM suceso WHERE '.implode(' AND ', $q), $params)->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Listado de sucesos.
	 * @param int $usuario ID del usuario dueño de los sucesos.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de posts por página.
	 * @param array $tipo Listado de tipos de sucesos a mostrar.
	 * @param bool $notificar Si hay mostramos con un tipo de notificar o ninguno.
	 * @param bool $visto Si hay mostramos con un estado visto o ninguno.
	 * @param bool $desplegado Si hay mostramos con un estado desplagado o ninguno.
	 * @return array
	 */
	public function listado($usuario, $pagina, $cantidad = 10, $tipo = NULL, $notificar = NULL, $visto = NULL, $desplegado = NULL)
	{
		// Elemento de inicio para paginacion.
		$start = ($pagina - 1) * $cantidad;

		// Arreglo de parametros.
		if (is_array($usuario))
		{
			$params = $usuario;

			$qa = 'usuario_id IN(';
			for ($i = 0; $i < count($params); $i++)
			{
				$qa .= '?, ';
			}
			$q = array(substr($qa, 0, -2).')');
			unset($qa);
		}
		else
		{
			$params = array($usuario);
			$q = array('usuario_id = ?');
		}


		// Agrego limitacion tipo.
		if ($tipo !== NULL)
		{
			$params = array_merge($params, $tipo);

			// Armo la lista de estados.
			$kk = array();
			$c = count($tipo);
			for ($i = 0; $i < $c; $i++)
			{
				$kk[] = '?';
			}
			$q[] = 'tipo IN ('.implode(', ', $kk).')';
		}

		// Agrego limitacion notificar.
		if ($notificar !== NULL)
		{
			$params[] = $notificar;
			$q[] = 'notificar = ?';
		}

		// Agrego limitacion visto.
		if ($visto !== NULL)
		{
			$params[] = $visto;
			$q[] = 'visto = ?';
		}

		// Agrego limitacion desplegado.
		if ($desplegado !== NULL)
		{
			$params[] = $desplegado;
			$q[] = 'desplegado = ?';
		}

		// Ejecuto la consulta.
		$rst = $this->db->query('SELECT id FROM suceso WHERE '.implode(' AND ', $q).' ORDER BY fecha DESC LIMIT '.$start.','.$cantidad, $params)->get_pairs(Database_Query::FIELD_INT);

		// Armamos el listado.
		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Suceso($v);
		}
		return $lst;
	}

}
