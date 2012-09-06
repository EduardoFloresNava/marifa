<?php
/**
 * client.php is part of Marifa.
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
 * @subpackage  Update\Compresion
 * @package		Marifa\Base
 */
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Clase abstracta para uniformar los compresores.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update\Compresion
 * @package    Marifa\Base
 */
abstract class Base_Update_Compresion_Compresion {

	/**
	 * Directorio temporal.
	 * @var string
	 */
	protected $tempPath = 'temp/';

    /**
     * Seteamos el path para descargas temporales automáticamente
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     */
    public function __construct()
    {
        // Seteamos de manera automática el path de archivos temporales.
        if (function_exists('sys_get_temp_dir'))
        {
            $this->setTempPath(realpath(sys_get_temp_dir()));
        }
        else
        {
            $this->setTempPath(realpath($this->sys_get_temp_dir()));
        }
    }

	/**
	 * Seteamos el directorio temporal.
	 * @param string $path
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
    public function setTempPath($path)
    {
        $this->tempPath = $path;
    }

    /**
     * Fallback de sys_get_temp_dir
	 * @return string Directorio temporal del sistema.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     */
    private function sys_get_temp_dir()
    {
		foreach(array('TMP', 'TEMP', 'TMPDIR') as $t)
		{
			$temp = getenv($t);
			if ($temp !== FALSE)
			{
				return $temp;
			}
		}

		$temp = tempnam(__FILE__, '');
		if (file_exists($temp))
		{
			unlink($temp);
			return dirname($temp);
		}
		return NULL;
    }

	/**
	 * Creamos un archivo zip con la lista de archivo enviados.
	 * @param string $file Archivo donde colocar la compresión.
	 * @param string $basePath Path base a utilizar en la compresión zip.
	 * @param array|string $files Arreglo de archivos o directorio donde se
	 * encuentran los archivos a comprimir.
	 * @return bool
	 */
    abstract public function compress($file, $basePath, $files);

	/**
	 * Descomprimimos el archivo y lo colocamos en el directorio temporal.
	 * @param string $path Path del archivo a descomprimir.
	 * @return bool
	 */
    abstract public function decompress($path);

}
