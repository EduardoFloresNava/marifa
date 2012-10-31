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
 * Manejador de las migraciones.
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Migraciones {

	/**
	 * Obtenemos el listado de migraciones.
	 */
	public static function migraciones()
	{
		$lst = array();

		$files = scandir(SHELL_PATH.DS.'migraciones'.DS);

		foreach ($files as $f)
		{
			if (preg_match('/^([0-9]+)_migracion\.php$/D', $f))
			{
				$lst[] = (int) array_shift(explode('_', $f));
			}
		}

		return $lst;
	}

	/**
	 * Aplicamos una migracion.
	 * @param int $numero Número de migración a aplicar.
	 */
	public static function migrar($numero)
	{
		$f_migracion = SHELL_PATH.DS.'migraciones'.DS.$numero.'_migracion.php';

		// Verificamos exista la migración.
		if ( ! file_exists($f_migracion))
		{
			throw new Exception('No existe la migración '.$numero, 202);
		}

		// Intentamos aplicarla.
		$rst = include($f_migracion);

		if ($rst)
		{
			// Guardamos la migracion.
			list(,$c) = Database::get_instance()->insert('INSERT INTO migraciones (numero, fecha) VALUES (?, ?)', array($numero, date('Y/m/d H:i:s')));

			if ($c < 0)
			{
				throw new Exception('No se pudo guardar el estado de la migracion en la base de datos.', 203);
			}

			return TRUE;
		}
		else
		{
			throw new Exception('El resultado de la migracion es inesperado.', 204);
		}
	}
}
