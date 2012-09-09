<?php
/**
 * database.php is part of Marifa.
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
 * Clase encargada cargar el driver correspondiente de la base de datos.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Database {

	/**
	 * Intancia del Driver que maneja la base de datos.
	 */
	private static $instance;

	/**
	 * Por el patrón singleton se evita tener instancias de esta clase.
	 */
	private function __construct()
	{
	}

	/**
	 * No se permite clonar esta clase.
	 */
	public function __clone()
	{
	}

	/**
	 * No se permite deserealizar esta clase.
	 */
	public function __wakeup()
	{
	}

	/**
	 * Obtenemos el driver de la base de datos.
	 * El driver es el que nos permite interactuar con la base de datos.
	 * @return Database_Driver Driver de la base de datos.
	 */
	public static function get_instance()
	{
		if ( ! isset(self::$instance))
		{
			// Cargamos la configuración de la base de datos.
			$config = Configuraciones::get('database', array());

			// Comprobamos que exista un driver asignado.
			if ( ! isset($config['type']))
			{
				Error::show_error('Los parametros de la base de datos son incorrectos. Verifique el driver.');
			}

			// Generamos el nombre de la clase Driver.
			$driver = 'Database_Driver_'.strtolower($config['type']);

			// Comprobamos la existencia de ese Driver para manejar la BD.
			if ( ! class_exists($driver))
			{
				Error::show_error('No se ha encontrado un controlador válido para manejar las base de datos '.$config['type']);
			}
			else
			{
				// Instanciamos el Driver correspondiente.
				self::$instance = new $driver($config);
			}
		}
		return self::$instance;
	}

}
