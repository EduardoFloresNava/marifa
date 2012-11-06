<?php
/**
 * memcached.php is part of Marifa.
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
 * @subpackage  Cache\Driver
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Driver de cache para Memcached.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Cache\Driver
 */
class Base_Cache_Driver_Memcached implements Cache_Driver {

	/**
	 * Instancia del objeto de memcached.
	 * @var Memcached
	 */
	private $_memcached;

	/**
	 * Creamos y configuramos el driver para la cache.
	 * @param string $hostname Nombre del host para conectar al servidor memcached.
	 * @param int $port Puerto para conectar al servidor de memcached.
	 * @param int $weight Importancia del servidor frente al resto.
	 */
	public function __construct($hostname = '127.0.0.1', $port = 11211, $weight = 1)
	{
		// Instanciamos memcached.
		$this->_memcached = new Memcached;

		// Configuramos el servidor.
		$this->_memcached->addServer($hostname, $port, $weight);
	}

	/**
	 * Obtenemos un elemento de la cache.
	 * @param string $id Clave del elemento abtener.
	 * @return mixed Información si fue correcto o FALSE en caso de error.
	 */
	public function get($id)
	{
		$data = $this->_memcached->get($id);
		return (is_array($data)) ? $data[0] : FALSE;
	}

	/**
	 * Guardamos un elemento en la cache.
	 * @param string $id Clave del elemento.
	 * @param mixed $data Información a guardar.
	 * @param int $ttl Tiempo en segundo a mantener la información.
	 * @return bool Resultado de la operación.
	 */
	public function save($id, $data, $ttl = 60)
	{
		return $this->_memcached->add($id, array($data, time(), $ttl), $ttl);
	}

	/**
	 * Borramos un elemento de la cache.
	 * @param string $id Clave del elemento.
	 * @return bool Resultado de la operación.
	 */
	public function delete($id)
	{
		return $this->_memcached->delete($id);
	}

	/**
	 * Limpiamos la cache.
	 * @return bool Resultado de la operación.
	 */
	public function clean()
	{
		return $this->_memcached->flush();
	}

	/**
	 * Verificamos si el driver es soportado por el sistema.
	 * @return bool
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('memcached'))
		{
			return FALSE;
		}
		return TRUE;
	}
}