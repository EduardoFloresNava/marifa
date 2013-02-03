<?php
/**
 * utils.php is part of Marifa.
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
 * @subpackage  Update
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Utileria varia.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update
 * @package    Marifa\Base
 */
class Base_Update_Utils {

	/**
	 * Generamos el ID de un paquete en función de su nombre.
	 * @param string $name Nombre del paquete.
	 * @return string Hash del Paquete.
	 */
	public static function make_hash($name)
	{
		return md5(trim(preg_replace('/\s+/', ' ', strtolower($name))));
	}

	/**
	 * Convertimos una cantidad de Bytes a su multiplo correspondiente.
	 * @param int $bytes Cantidad de bytes a convertir.
	 * @param int $precision Presición de la conversión.
	 * @return string
	 */
	public static function format_bytes($bytes, $precision = 2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		// $bytes /= pow(1024, $pow);
		$bytes /= (1 << (10 * $pow));

		return round($bytes, $precision).' '.$units[$pow];
	}

	/**
     * Fallback de sys_get_temp_dir
	 * @return string Directorio temporal del sistema.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
     */
    public static function sys_get_temp_dir()
    {
		// Verificamos system_path para PHP>5.2.1.
		if (function_exists('sys_get_temp_dir'))
		{
			return sys_get_temp_dir();
		}

		// Verifico variable de entorno para el resto.
		foreach (array('TMP', 'TEMP', 'TMPDIR') as $t)
		{
			$temp = getenv($t);
			if ($temp !== FALSE)
			{
				return $temp;
			}
		}

		// Verifico los permisos de escritura.
		$temp = tempnam(__FILE__, '');
		if (file_exists($temp))
		{
			unlink($temp);
			return dirname($temp);
		}
	}

    /**
     * Normalizamos el path reemplazando // por / y agregando una / al final.
	 * @param string $path Path a normalizar.
	 * @param bool $real Si necesitamos el path real o solo normalizado.
	 * @return string
     */
    public static function normalize_path($path, $real = TRUE)
    {
        // Obtenemos el path verdadero
        if ($real)
        {
            $path = realpath($path);
        }
        else
        {
            $path = preg_replace('/\/+/', '/', $path);
		    $path = preg_replace('//', '/', $path);
        }

        // Agregamo la barra final.
        $path = (substr($path, -1) == '/') ? $path : ($path.'/');
        return $path;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    public static function copyr($source, $dest)
    {
        // Check for symlinks
        if (is_link($source))
		{
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source))
		{
            return copy($source, $dest);
        }

        // Make destination directory
        if ( ! is_dir($dest))
		{
            mkdir($dest);
        }

        // Loop through the folder
        $dir = scandir($source);
		$rst = TRUE;
        foreach ($dir as $entry)
		{
            // Skip pointers
            if ($entry == '.' || $entry == '..')
			{
                continue;
            }

            // Deep copy directories
            $r = self::copyr("$source/$entry", "$dest/$entry");
			if ( ! $r)
			{
				$rst = FALSE;
			}
        }

        // Clean up
        return $rst;
    }

	/**
	 * Borramos el archivo o directorio de forma recursiva.
	 * @param string $path Directorio a borrar.
	 * @return bool
	 */
	public static function unlink($path)
	{
		if (is_dir($path))
		{
			$lst = scandir($path);

			$rst = TRUE;

			foreach ($lst as $file)
			{
				if ($file === '.' || $file === '..')
				{
					continue;
				}

				if ( ! self::unlink($path.'/'.$file))
				{
					$rst = FALSE;
				}
			}

			if ( ! @rmdir($path))
			{
				$rst = FALSE;
			}

			return $rst;
		}
		else
		{
			return @unlink($path);
		}
	}

	/**
	 * Obtenemos el mimetype de un archivo.
	 * @param string $path Path del archivo.
	 * @return string Mime del archivo.
	 */
	public static function get_mime($path)
	{
		if (function_exists('finfo_open'))
		{
			$finfo = new finfo(FILEINFO_MIME);
			$mime = $finfo->file($path);

			if (strpos($mime, ';') !== FALSE)
			{
				list ($mime, ) = explode(';', $mime);
			}
			return $mime;
		}
		elseif (function_exists('mime_content_type'))
		{
			return mime_content_type($path);
		}
		else
		{
			// Obtengo la extensión.
			$ext = pathinfo($path, PATHINFO_EXTENSION);

			// Verifico el tipo.
			//TODO: implementar mime's extra.
			switch ($ext) {
				case 'zip':
					return 'application/zip';
				case 'tar':
					return 'application/x-tar';
				case 'gz':
					return 'application/x-gzip';
				case 'bz2':
					return 'application/x-bzip2';
				default:
					return 'application/octet-stream';
			}
		}
	}

	/**
	 * Obtenemos el tipo de compresor en función del MIME.
	 * @param string $mime MIME al cual buscar el compresor.
	 * @return string Compresor a usar con el MIME
	 */
	public static function mime2compresor($mime)
	{
		switch ($mime)
		{
			case 'application/zip':
				return 'zip';
			case 'application/x-tar':
				return 'tar';
			case 'application/x-gzip':
				return 'gz';
			case 'application/x-bzip2':
				return 'bz2';
			default:
				return FALSE;
		}
	}
}
