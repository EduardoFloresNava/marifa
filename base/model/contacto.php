<?php
/**
 * contacto.php is part of Marifa.
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
 * @license	http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package	Marifa\Base
 * @subpackage	Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Modelo para manejo de censuras de palabras.
 *
 * @since		0.3
 * @package	Marifa\Base
 * @subpackage	Model
 * @property-read int $id ID de la censura.
 * @property-read string $valor Conjunto de caracteres a coincidir.
 * @property-read int $tipo Tipo de conjunto de caracteres a coincidir.
 * @property-read int $estado Estado de la censura.
 */
class Base_Model_Contacto extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'contacto';

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
		'asunto' => Database_Query::FIELD_STRING,
		'contenido' => Database_Query::FIELD_STRING,
		'estado' => Database_Query::FIELD_INT
	);

	/**
	 * Contacto nuevo.
	 */
	const ESTADO_NUEVO = 0;

	/**
	 * Contacto visto.
	 */
	const ESTADO_VISTO = 1;

	/**
	 * Cargamos un contacto.
	 * @param int $id Id del contacto.
	 * @param array $data Arreglo con los datos del contacto.
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
	 * Creo una nueva censura.
	 * @param string $valor Valor a censurar.
	 * @param int $tipo Tipo de censura.
	 * @param string $censura Valor de la censura.
	 * @param int $estado Estado de la censura.
	 * @return int ID de la censura creada.
	 */
	public function nueva($nombre, $asunto, $mensaje, $estado = self::ESTADO_NUEVO)
	{
		// Creo el objeto.
		list ($id, ) = $this->db->insert('INSERT INTO '.$this->table.' (nombre, asunto, contenido, estado) VALUES (?, ?, ?, ?)', array($nombre, $asunto, $mensaje, $estado));

		// Lo asigno para cargar si corresponde.
		if ($this->primary_key['id'] == NULL)
		{
			$this->primary_key['id'] = $id;
		}

		// Devuelvo el ID de la censura.
		return $id;
	}

	/**
	 * Cantidad de contactos presentes.
	 * @param int $estado Estado de los contactos. NULL para cualquier estado.
	 * @return int
	 */
	public static function cantidad($estado = NULL)
	{
		if ($estado === NULL)
		{
			return Database::get_instance()->query('SELECT COUNT(*) AS c FROM contacto')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return Database::get_instance()->query('SELECT COUNT(*) AS c FROM contacto WHERE estado = ?', array($estado))->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Obtenemos un listado de contactos.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public function listado($pagina = 1, $cantidad = 20)
	{
		$start = ($pagina - 1) * $cantidad;

		$rst = $this->db->query('SELECT * FROM '.$this->table.' LIMIT '.$start.','.$cantidad)
				->set_fetch_type(Database_Query::FETCH_ASSOC)
				->set_cast_type($this->fields);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Contacto($v['id'], $v);
		}
		return $lst;
	}
}
