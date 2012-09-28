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
 * Modelo para el manejo de sessiones de usuario en la base de datos.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 * @property-read string $id ID de la sessión.
 * @property-read int $usuario_id ID del usuario dueño de la sessión.
 * @property-read int $ip IP de la sessión.
 * @property-read Fechahora $expira Fecha en la cual expira la sessión.
 */
class Base_Model_Session extends Model_Dataset {

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'session';

	/**
	 * Clave primaria.
	 * @var array
	 */
	protected $primary_key = array('id' => NULL);

	/**
	 * Listado de campos y sus tipos.
	 */
	protected $fields = array(
		'id' => Database_Query::FIELD_STRING,
		'usuario_id' => Database_Query::FIELD_INT,
		'ip' => Database_Query::FIELD_INT,
		'expira' => Database_Query::FIELD_DATETIME
	);

	/**
	 * Constructor del suceso.
	 * @param string $id ID del la session a cargar.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$this->primary_key['id'] = $id;
	}

	/**
	 * ID del usuario dueño de la sessión.
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Actualizamos la fecha en la cual expira la sessión.
	 * @param type $tiempo
	 */
	public function actualizar_expira($tiempo)
	{
		$this->db->update('UPDATE session SET expira = ? WHERE id = ?', array(date('Y/m/d H:i:s', time() + $tiempo), $this->primary_key['id']));
	}

	/**
	 * Borramos las sessiones antiguas.
	 */
	public function limpiar()
	{
		$this->db->delete('DELETE FROM session WHERE expira < ?', date('Y/m/d H:i:s'));
	}

	/**
	 * Terminamos la sessión del usuario.
	 * @return string
	 */
	public function borrar()
	{
		return $this->db->delete('DELETE FROM session WHERE id = ? OR expira < ?', array($this->primary_key['id'], date('Y/m/d H:i:s')));
	}

	public function crear($id, $usuario, $ip, $expira)
	{
		// Verifico existencia.
		if ($this->existe(array('id' => $id)))
		{
			$this->update_value('ip', $ip);
			$this->update_value('expira', $expira);
			return $this->db->update('UPDATE session SET ip = ?, expira = ? WHERE id = ?', array($ip, $expira, $id));
		}
		else
		{
			return $this->db->insert('INSERT INTO session (id, usuario_id, ip, expira) VALUES (?, ?, ?, ?)', array($id, $usuario, $ip, $expira));
		}
	}

	/**
	 * Listado de sessiones activas.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de noticias por página.
	 * @return array
	 */
	public function listado($pagina, $cantidad = 10)
	{
		if ($this->primary_key['id'] !== NULL)
		{
			$params = $this->primary_key['id'];
			$q = ' WHERE id != ?';
		}
		else
		{
			$params = NULL;
			$q = '';
		}

		$start = ($pagina - 1) * $cantidad;
		$rst = $this->db->query('SELECT id FROM session'.$q.' ORDER BY expira DESC LIMIT '.$start.','.$cantidad, $params)->get_pairs();

		$lst = array();
		foreach ($rst as $v)
		{
			$lst[] = new Model_Session($v);
		}
		return $lst;
	}

	/**
	 * Cantidad total de sessiones.
	 * @return int
	 */
	public function cantidad()
	{
		if ($this->primary_key['id'] !== NULL)
		{
			$params = $this->primary_key['id'];
			$q = ' WHERE id != ?';
		}
		else
		{
			$params = NULL;
			$q = '';
		}
		return $this->db->query('SELECT COUNT(*) FROM session'.$q, $params)->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de sessiones activas existentes.
	 * @return int
	 */
	public function cantidad_activas()
	{
		return (int) $this->db->query('SELECT COUNT(*) FROM session WHERE expira >= ?', date('Y/m/d H:i:s'))->get_var(Database_Query::FIELD_INT);
	}

	/**
	 * Cantidad de sessiones activas sin contar usuarios repetidos.
	 * @return int
	 */
	public function cantidad_usuarios()
	{
		return (int) $this->db->query('SELECT COUNT( DISTINCT usuario_id ) FROM session WHERE expira >= ?', date('Y/m/d H:i:s'))->get_var(Database_Query::FIELD_INT);
	}

}
