<?php
/**
 * Clase para la carga a automatica de archivos.
 * Se basa en los nombre de las clases.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa/Base
 * @subpackage Lib
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
