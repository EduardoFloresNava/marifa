<?php
/**
 * cache.php is part of Marifa.
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
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase base de la cache. Configura y devuelve un driver para su uso.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Cache {

	/**
	 * Instancia del objeto de cache.
	 * @var Cache_Driver
	 */
	private static $instance;

	/**
	 * No se deben generar instancias de Cache.
	 */
	private function __construct()
	{
	}

	/**
	 * Obtenemos un driver
	 * @return Cache_Driver
	 */
	public static function get_instance()
	{
		if ( ! isset(self::$instance))
		{
			// Verificamos si tenemos información de la cache.
			$data = Configuraciones::get('cache');

			if ( ! is_array($data))
			{
				// Tratamos de cargarlo.
				Configuraciones::load(CONFIG_PATH.DS.'cache.php', TRUE);

				// Volvemos a cargar.
				$data = Configuraciones::get('cache');
			}

			// Comprobamos el tipo de cache.
			if ( ! array_key_exists('type', $data))
			{
				throw new Exception('Invalid cache configuration.');
			}

			if ($data['type'] !== NULL)
			{
				// Not dummy.

				// Verificamos el tipo.
				if ( ! in_array($data['type'], array('dummy', 'apc', 'file', 'memcached')))
				{
					throw new Exception("Invalid cache type '{$data['type']}'.");
				}

				// Verificamos los datos segun el tipo.
				switch ($data['type'])
				{
					case 'dummy':
						self::$instance = new Cache_Driver_Dummy;
						break;
					case 'apc':
						self::$instance = new Cache_Driver_Apc;
						break;
					case 'memcached':
						// Verificamos que tengamos todos los datos.
						if ( ! isset($data['hostname']) || ! isset($data['port']) || ! isset($data['weight']))
						{
							throw new Exception('Invalid cache configuration.');
						}

						// Verificamos que el puerto sea válido.
						if ( ! is_int($data['port']) || $data['port'] <= 0)
						{
							throw new Exception('El puerto seleccinado no es válido.');
						}

						// Verificamos que la prioridad sea válida
						if ( ! is_int($data['weight']) || $data['weight'] < 0)
						{
							throw new Exception('La prioridad seleccionada no es correcta.');
						}

						self::$instance = new Cache_Driver_Memcached($data['hostname'], (int) $data['port'], (int) $data['weight']);
						break;
					case 'file':
						// Obtenemos el path.
						$p = isset($data['path']) ? $data['path'] : (APP_BASE.DS.'cache'.DS.'file'.DS);

						// Verificamos que tenga barra final.
						if (substr($p, -1) !== DS)
						{
							$p .= DS;
						}

						// Verificamos existencia.
						if ( ! file_exists($p) || ! is_dir($p))
						{
							throw new Exception('El directorio para la cache no existe.');
						}

						// Verificamos los permisos.
						if ( ! is_writable($p))
						{
							throw new Exception('El directorio para la cache no tiene permisos de escritura.');
						}

						self::$instance = new Cache_Driver_File($p);
						break;
				}
			}
			else
			{
				// Por defecto es Dummy.
				self::$instance = new Cache_Driver_Dummy;
			}
		}
		return self::$instance;
	}

	/**
	 * Patrón singleton, no se permite clonar
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function __clone()
	{
	}

	/**
	 * Patrón singleton, no se permite deserealizar.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function __wakeup()
	{
	}
}
