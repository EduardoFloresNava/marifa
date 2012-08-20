<?php defined('APP_BASE') or die('No direct access allowed.');
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
 * @author		Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @copyright	Copyright (c) 2012 Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.3
 * @filesource
 * @subpackage  Update/Compresion
 * @package		Marifa/Base
 */

/**
 * Compresor Tar basado en Pear_Archive
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @subpackage Update/Compresion
 * @package    Marifa/Base
 */
class Base_Update_Compresion_Tar extends Update_Compresion_Compresion {

	/**
	 * Creamos un archivo zip con la lista de archivo enviados.
	 * @param string $file Archivo donde colocar la compresión.
	 * @param string $basePath Path base a utilizar en la compresión zip.
	 * @param array|string $files Arreglo de archivos o directorio donde se
	 * encuentran los archivos a comprimir.
	 * @return bool
	 */
	public function compress($file, $basePath, $files)
	{
		$pt = new Update_Compresion_Pear_Tar($file, NULL);
		return $pt->createModify($files, '', $basePath);
	}

	/**
	 * Descomprimimos el archivo y lo colocamos en el directorio temporal.
	 * @param string $path Path del archivo a descomprimir.
	 * @return bool
	 */
	public function decompress($path)
	{
		$pt = new Update_Compresion_Pear_Tar($path, NULL);
		return $pt->extractModify($this->tempPath, '', FALSE);
	}
}
