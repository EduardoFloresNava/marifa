<?php
/**
 * migraciones.php is part of Marifa.
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
 * @package		Marifa\Shell
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para la carga a automatica de archivos.
 * Se basa en los nombre de las clases.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Loader {

	/**
	 * Funcion para la carga de una clase.
	 * Usada por spl_autoload_register.
	 * @param string $class Nombre de la clase a cargar.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public static function load($class)
	{
		if (substr($class, 0, 6) == 'Shell_')
		{
			$class = substr($class, 6);
		}
		else
		{
			return FALSE;
		}

		// Tranformamos el nombre de la clase a un path equivalente.
		$class = self::inflector($class);

		// Comprobamos que exista el archivo de la clase.
		if (file_exists(SHELL_PATH.DS.'classes'.DS.$class.'.'.FILE_EXT))
		{
			// Incluimos el archivo.
			include_once (SHELL_PATH.DS.'classes'.DS.$class.'.'.FILE_EXT);
		}
	}

	/**
	 * Transforma una cadena del tipo Foo_Bar_Sub a foo/bar/sub
	 * @param string $class Nombre del la clase a transformar.
	 * @return string Path correspondiente a la clase.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@hotmail.com>
	 */
	protected static function inflector($class)
	{
		return strtolower(preg_replace('/\/+/', '/', preg_replace('/\_/', '/', $class)));
	}

}
