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
	 * @param int $objeto_id ID del objeto del suceso.
	 * @param int $objeto_id2 ID secundario del objeto del suceso.
	 * @param int $objeto_id3 ID terciario del objeto del suceso.
	 */
	public function crear($usuario_id, $tipo, $objeto_id, $objeto_id2 = NULL, $objeto_id3 = NULL)
	{
		if (is_array($usuario_id))
		{
			// Eliminamos repetidos.
			$usuario_id = array_unique($usuario_id, SORT_NUMERIC);

			// Ejecutamos las consultas.
			$rst = array();
			foreach ($usuario_id as $id)
			{
				list($rst[],) = $this->db->insert('INSERT INTO suceso (usuario_id, objeto_id, objeto_id1, objeto_id2, tipo, fecha) VALUES (?, ?, ?, ?, ?, ?)',
					array($id, $objeto_id, $objeto_id2, $objeto_id3, $tipo, date('Y/m/d H:i:s')));
			}
			return $rst;
		}
		else
		{
			return $this->db->insert('INSERT INTO suceso (usuario_id, objeto_id, objeto_id1, objeto_id2, tipo, fecha) VALUES (?, ?, ?, ?, ?, ?)',
				array($usuario_id, $objeto_id, $objeto_id2, $objeto_id3, $tipo, date('Y/m/d H:i:s')));
		}
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

}
