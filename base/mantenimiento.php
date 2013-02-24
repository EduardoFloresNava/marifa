<?php
/**
 * matenimiento.php is part of Marifa.
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
 * @since		Versión 0.3
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para el manejo del modo mantenimiento. Se encarga de administrar
 * cuando se encuentra activo y quienes son los que tienen permitido el acceso.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Mantenimiento {

	/**
	 * Obtenemos el listado de los ID's de los grupos que tiene permitido el acceso (soft-lock).
	 * @return array
	 */
	public static function grupos_permitidos()
	{
		return Database::get_instance()
				->query(
						'SELECT rango_id FROM usuario_rango_permiso WHERE permiso = ?',
						Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO
					)
				->get_pairs(Database_Query::FIELD_INT);
	}

	/**
	 * Listado de ID's de usuarios que pueden acceder en modo matenimiento (soft-lock).
	 * @return array
	 */
	public static function usuarios_permitidos()
	{
		return unserialize(Utils::configuracion()->get('mantenimiento_usuarios', 'a:0:{}'));
	}

	/**
	 * Verificamos si hay un bloqueo activo.
	 * @param bool $hard Si el bloqueo es a nivel usuario o IP.
	 * @return bool
	 */
	public static function is_locked($hard = TRUE)
	{
		if ($hard)
		{
			return file_exists(APP_BASE.DS.'lock.tmp') && is_file(APP_BASE.DS.'lock.tmp');
		}
		else
		{
			return (bool) Utils::configuracion()->get('soft_lock', FALSE);
		}
	}

	/**
	 * Verificamos si el bloqueo aplica al IP provisto.
	 * @param string|int $ip IP si es bloqueo por IP o ID del usuario en caso de ser por usuarios.
	 * @param bool $hard Si el bloqueo es por usuario o por IP's.
	 * @return bool
	 */
	public static function is_locked_for($ip, $hard = TRUE)
	{
		if ( ! self::is_locked($hard))
		{
			return FALSE;
		}

		// Verifico si es por IP o por Usuario.
		if ($hard)
		{
			// Cargamos los rangos.
			$range_list = file(APP_BASE.DS.'lock.tmp');

			// Verificamos.
			foreach ($range_list as $range)
			{
				if ($ip == $range || IP::ip_in_range($ip, $range))
				{
					// Existe en el rango.
					return FALSE;
				}
			}

			// No existe en los rangos.
			return TRUE;
		}
		else
		{
			// Verifico si esta en el listado de usuarios.
			if (in_array($ip, self::usuarios_permitidos()))
			{
				return FALSE;
			}

			// Obtengo rango del usuario.
			$rango = Model::factory('Usuario', $ip)->rango;

			// Verifico rango.
			if (in_array($rango, self::grupos_permitidos()))
			{
				return FALSE;
			}

			return TRUE;
		}
	}

	/**
	 * Realizamos el bloqueo, excluyendo el listado de IP's y/o rangos provisto.
	 * @param bool $hard Si el bloqueo es por IP o por usuario.
	 * @param array $for Arreglo de IP's y/o rangos que tienen permitido el acceso. Es decir, no le dan importancia al bloqueo.
	 * Solo aplicable a bloqueo por IP.
	 * @return bool
	 */
	public static function lock($hard = TRUE, $for = array())
	{
		// Verifico no se encuentre el otro activo.
		if ( ! self::is_locked($hard))
		{
			self::unlock();
		}

		if ($hard)
		{
			// Generamos el listado de rangos.
			$range = implode(PHP_EOL, $for);

			// Guardamos la información
			return file_put_contents(APP_BASE.DS.'lock.tmp', $range);
		}
		else
		{
			Utils::configuracion()->soft_lock = TRUE;
			return TRUE;
		}
	}

	/**
	 * Desbloqueamos.
	 * @return bool
	 */
	public static function unlock()
	{
		if (self::is_locked(TRUE))
		{
			@unlink(APP_BASE.DS.'lock.tmp');
		}

		if (self::is_locked(FALSE))
		{
			Utils::configuracion()->soft_lock = FALSE;
		}
		return TRUE;
	}

}
