<?php

/**
 * Description of migraciones
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
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
