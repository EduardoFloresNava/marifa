<?php
/**
 * function.php is part of Marifa.
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
 * @author		Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @copyright	Copyright (c) 2012 Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Conjunto de funciones para tareas que se usan intensivamente y su uso
 * fuera de clases mejora el rendimiento.
 */


/**
 * Funcion para la carga de una clase.
 * Usada por spl_autoload_register.
 * @param string $class Nombre de la clase a cargar.
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */
function loader_load($class)
{
	// Tranformamos el nombre de la clase a un path equivalente.
	$class = strtolower(preg_replace('/\/+/', DS, preg_replace('/\_/', DS, $class)));

	if (file_exists(APP_BASE.DS.'marifa'.DS.$class.'.'.FILE_EXT))
	{
		// Incluimos el archivo.
		require(APP_BASE.DS.'marifa'.DS.$class.'.'.FILE_EXT);
	}
	else
	{
		// Comprobamos que exista el archivo de la clase.
		if (file_exists(APP_BASE.DS.$class.'.'.FILE_EXT))
		{
			// Incluimos el archivo.
			require(APP_BASE.DS.$class.'.'.FILE_EXT);
		}
	}
}

/**
 * Devolvemos el nombre de la clase con o sin prefijo base.
 * @param string $class Nombre de la clase a agregar o sacar el prefijo.
 * En caso de tenerlo no lo vuelve a agregar y en caso de no tenerlo no
 * lo vuelve a suprimir.
 * @param bool $prefix Si se agrega o se saca el prefijo.
 * @return string
 */
function loader_prefix_class($class, $prefix = FALSE)
{
	// Verificamos si tiene el prefijo.
	if (substr($class, 0, 5) == 'Base_')
	{
		if ( ! $prefix)
		{
			$class = substr($class, 5);
		}
	}
	else
	{
		if ($prefix)
		{
			$class = 'Base_'.$class;
		}
	}
	return $class;
}