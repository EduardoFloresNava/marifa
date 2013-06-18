<?php
/**
 * pagina.php is part of Marifa.
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
 * Modelo de páginas estáticas del sitio.
 *
 * @since		0.3
 * @package	Marifa\Base
 * @subpackage	Model
 *
 * @property-read int $id ID de la página.
 * @property-read string $titulo Título de la página.
 * @property-read string $contenido Contenido de la página.
 * @property-read int $menu Menú donde colocar la página.
 * @property-read int $estado Estado de la página.
 * @property-read Fecharho $creacion Fecha y hora en la que se creo la página.
 * @property-read Fechahora $modificacion Fecha y hora de la última modificación.
 */
class Base_Model_Pagina extends Model_Dataset {

	/**
	 * La página está oculta.
	 */
	const ESTADO_OCULTO = 0;

	/**
	 * La página es visible.
	 */
	const ESTADO_VISIBLE = 1;

	/**
	 * Se coloca en el menú superior.
	 */
	const MENU_SUPERIOR = 0;

	/**
	 * Se coloca en el pie de página.
	 */
	const MENU_PIE = 1;

	/**
	 * Se coloca en el menú superior y en el pie de página.
	 */
	const MENU_AMBOS = 2;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'pagina';

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
		'titulo' => Database_Query::FIELD_STRING,
		'contenido' => Database_Query::FIELD_STRING,
		'menu' => Database_Query::FIELD_INT,
		'estado' => Database_Query::FIELD_INT,
		'creacion' => Database_Query::FIELD_DATETIME,
		'modificacion' => Database_Query::FIELD_DATETIME
	);

	/**
	 * Constructor del mensaje.
	 * @param int $id ID del mensaje a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
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
			return Database::get_instance()->query('SELECT COUNT(*) AS c FROM pagina')->get_var(Database_Query::FIELD_INT);
		}
		else
		{
			return Database::get_instance()->query('SELECT COUNT(*) AS c FROM pagina WHERE estado = ?', array($estado))->get_var(Database_Query::FIELD_INT);
		}
	}

	/**
	 * Creamos una nueva página.
	 * @param string $titulo Título de la página.
	 * @param string $contenido Contenido de la página.
	 * @param int $menu Donde colocar el menú.
	 * @param int $estado Estado de la página.
	 * @return int ID de la página creada.
	 */
	public function nueva($titulo, $contenido, $menu, $estado = self::ESTADO_OCULTO)
	{
		// Creo el objeto.
		list ($id, ) = $this->db->insert('INSERT INTO '.$this->table.' (titulo, contenido, menu, estado, creacion, modificacion) VALUES (?, ?, ?, ?, ?, ?)', array($titulo, $contenido, $menu, $estado, date('Y/m/d H:i:s'), NULL));

		// Lo asigno para cargar si corresponde.
		if ($this->primary_key['id'] == NULL)
		{
			$this->primary_key['id'] = $id;
		}

		// Devuelvo el ID de la censura.
		return $id;
	}

	/**
	 * Obtenemos el listado de páginas.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos a mostrar por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 20)
	{
		$start = ($pagina - 1) * $cantidad;

		$rst = $this->db->query('SELECT * FROM '.$this->table.' ORDER BY modificacion LIMIT '.$start.','.$cantidad)
				->set_fetch_type(Database_Query::FETCH_ASSOC)
				->set_cast_type($this->fields);

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Pagina($v['id'], $v);
		}
		return $lst;
	}

	/**
	 * Obtenemos datos para el menu superior.
	 * @return array
	 */
	public function menu_superior()
	{
		return $this->db->query('SELECT id, titulo FROM '.$this->table.' WHERE estado = ? AND (menu = ? OR menu = ?)', array(self::ESTADO_VISIBLE, self::MENU_SUPERIOR, self::MENU_AMBOS))
				->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_STRING));
	}

	/**
	 * Obtenemos datos para el menu superior.
	 * @return array
	 */
	public function menu_pie()
	{
		return $this->db->query('SELECT id, titulo FROM '.$this->table.' WHERE estado = ? AND (menu = ? OR menu = ?)', array(self::ESTADO_VISIBLE, self::MENU_PIE, self::MENU_AMBOS))
				->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_STRING));
	}
}
