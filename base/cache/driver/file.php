<?php
/**
 * file.php is part of Marifa.
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
 * @subpackage  Cache\Driver
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Driver de cache para archivos en disco.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Cache\Driver
 */
class Base_Cache_Driver_File implements Cache_Driver {

	/**
	 * Directorio donde se aloja la cache.
	 * @var string
	 */
	private $_cache_path;

	/**
	 * Creamos una instancia de la cache en disco.
	 * @param string $path Directorio donde poner la cache.
	 */
	public function __construct($path)
	{
		$this->_cache_path = $path;
	}

	/**
	 * Obtenemos un elemento de la cache.
	 * @param string $id Clave del elemento abtener.
	 * @return mixed Información si fue correcto o FALSE en caso de error.
	 */
	public function get($id)
	{
		if ( ! file_exists($this->_cache_path.$id))
		{
			return FALSE;
		}

		$data = file_get_contents($this->_cache_path.$id);
		$data = unserialize($data);

		if (time() >  $data['time'] + $data['ttl'])
		{
			unlink($this->_cache_path.$id);
			return FALSE;
		}

		return $data['data'];
	}

	/**
	 * Guardamos un elemento en la cache.
	 * @param string $id Clave del elemento.
	 * @param mixed $data Información a guardar.
	 * @param int $ttl Tiempo en segundo a mantener la información.
	 * @return bool Resultado de la operación.
	 */
	public function save($id, $data, $ttl = 60)
	{
		// Generamos el contenido.
		$contents = array('time' => time(), 'ttl' => $ttl, 'data' => $data);

		// Abrimos el archivo.
		if ( ! $fp = @fopen($this->_cache_path.$id, FOPEN_WRITE_CREATE_DESTRUCTIVE))
		{
			return FALSE;
		}

		// Guardamos la información.
		flock($fp, LOCK_EX);
		fwrite($fp, serialize($contents));
		flock($fp, LOCK_UN);

		// Cerramos el archivo.
		fclose($fp);

		// Seteamos los permisos.
		@chmod($this->_cache_path.$id, 0777);

		// Salimos.
		return TRUE;
	}

	/**
	 * Borramos un elemento de la cache.
	 * @param string $id Clave del elemento.
	 * @return bool Resultado de la operación.
	 */
	public function delete($id)
	{
		return unlink($this->_cache_path.$id);
	}

	/**
	 * Borrado recursivo de directorios y archivos.
	 * @param string $path Path a eliminar.
	 * @param bool $del_dir Si borramos el directorio.
	 * @param int $level
	 * @return mixed
	 */
	private function delete_files($path, $del_dir = FALSE, $level = 0)
	{
		// Trim the trailing slash
		$path = preg_replace('|^(.+?)/*$|', '$1', $path);

		if ( ! $current_dir = @opendir($path))
		{
			return;
		}

		while (($filename = @readdir($current_dir)) !== FALSE)
		{
			if ($filename != "." && $filename != "..")
			{
				if (is_dir($path.'/'.$filename))
				{
					// Ignore empty folders
					if (substr($filename, 0, 1) != '.')
					{
						$this->delete_files($path.'/'.$filename, $del_dir, $level + 1);
					}
				}
				else
				{
					unlink($path.'/'.$filename);
				}
			}
		}
		@closedir($current_dir);

		if ($del_dir == TRUE && $level > 0)
		{
			@rmdir($path);
		}
	}

	/**
	 * Limpiamos la cache.
	 * @return bool Resultado de la operación.
	 */
	public function clean()
	{
		return $this->delete_files($this->_cache_path);
	}

	/**
	 * Verificamos si realmente se puede escribir en ese path.
	 * @param string $file Directorio a verificar.
	 * @return bool
	 */
	private function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' && @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}

	/**
	 * Verificamos si el driver es soportado por el sistema.
	 * @return bool
	 */
	public function is_supported()
	{
		return $this->is_really_writable($this->_cache_path);
	}
}
