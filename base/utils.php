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
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Funciones de utileria varia.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Utils {

	/**
	 * Obtenemos la propieadad $propiedad del objeto $objeto y su no está seteada
	 * devolvemos $defecto.
	 * @param object $objeto Objeto donde sacar la propiedad
	 * @param string $propiedad Propiedad a objener.
	 * @param mixed $defecto Valor por defecto
	 * @return mixed
	 */
	public static function prop($objeto, $propiedad, $defecto = NULL)
	{
		return isset($objeto->$propiedad) ? $objeto->$propiedad : $defecto;
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @since   Versión 0.1
	 * @package	Base
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
   public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = FALSE, $atts = array())
   {
		$url = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($email)))."?s=$s&d=$d&r=$r&d=mm";
		if ($img)
		{
			$url = "<img src=\"$url\"";
			foreach ($atts as $key => $val)
			{
				$url .= " $key=\"$val\"";
			}
			$url .= ' />';
		}
		return $url;
	}

	/**
	 * Obtenemos el color que contrasta con el argumentado para facilitar la lectura.
	 * @param string $hexcolor Color exadecimal.
	 * @return strign
	 */
	public static function get_contrast_yiq($hexcolor)
	{
		$r = hexdec(substr($hexcolor,0,2));
		$g = hexdec(substr($hexcolor,2,2));
		$b = hexdec(substr($hexcolor,4,2));
		$yiq = (($r*299)+($g*587)+($b*114))/1000;
		return ($yiq >= 128) ? '000000' : 'FFFFFF';
	}

	/**
	 * Cargamos una URL.
	 * @param string $url URL a cargar.
	 * @return mixed
	 */
	public static function remote_call($url)
	{
		if (function_exists('curl_init'))
		{
			$petition = curl_init();
			curl_setopt($petition, CURLOPT_URL, $url);
			curl_setopt($petition, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($petition, CURLOPT_TIMEOUT, 5); // Evitamos mucho tiempo para la respuesta.

			$data = curl_exec($petition);
			if (curl_errno($petition) === 0)
			{
				curl_close($petition);
				return $data;
			}
			else
			{
				curl_close($petition);
				return FALSE;
			}
		}
		else
		{
			return @file_get_contents($url);
		}
	}

	/**
	 * Realizamos la descarga de un archivo.
	 * @param string $url Url del archivo a descargar.
	 * @param string $file Archivo donde guardar la descarga.
	 */
	public static function download_file($url, $file)
	{
		// Verificamos presencia de cURL.
		if (function_exists('curl_init'))
		{
			if (is_string($file))
			{
				// Abrimos el archivo.
				$fp = fopen($file, 'w+');
			}
			else
			{
				$fp =& $file;
			}

			// Iniciamos el objeto.
			$petition = curl_init();

			// Configuramos la peticion.
			curl_setopt($petition, CURLOPT_URL, $url);
			curl_setopt($petition, CURLOPT_TIMEOUT, 50);
			curl_setopt($petition, CURLOPT_FILE, $fp);
			// curl_setopt($petition, CURLOPT_FOLLOWLOCATION, true);

			// Realizamos la peticion.
			curl_exec($petition);

			// Verificamos presencia de errores.
			if (curl_errno($petition) === 0)
			{
				curl_close($petition);
				fclose($fp);
				return TRUE;
			}
			else
			{
				fclose($fp);
				//throw new HttpException(curl_error($petition), curl_errno($petition));
				curl_close($petition);
				return FALSE;
			}
		}
		elseif (ini_get('allow_url_fopen') === TRUE)
		{
			// Intentamos con lectura remota.

			// Tratamos de abrir el fichero.
			$r_fp = @fopen($url, 'r');

			// Verificamos su apertura.
			if ( ! $r_fp)
			{
				return FALSE;
			}

			// Abrimos el local
			if (is_string($file))
			{
				$l_fp = @fopen($file, 'w+');
			}
			else
			{
				$l_fp =& $file;
			}

			// Verificamos su apertura.
			if ( ! $r_fp)
			{
				@fclose($r_fp);
				return FALSE;
			}

			// Comenzamos a mover los bytes.
			while ( ! feof($r_fp))
			{
				fwrite($l_fp, fread($r_fp, 1024));
			}

			// Cerramos los archivos.
			@fclose($r_fp);
			@fclose($l_fp);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Obtenemos una instancia del modelo de configuraciones.
	 * @return Model_Configuracion
	 */
	public static function configuracion()
	{
		return Model_Configuracion::get_instance();
	}

	/**
	 * Verificamos si realmente se puede escribir en esa ruta.
	 * @param string $file Archivo o directorio a verificar.
	 * @return bool
	 */
	public static function is_really_writable($file)
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

			if (($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif (($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}

	/**
	 * Comprimo un archivo con GZIP.
	 * @param string $src Ruta del archivo a comprimir.
	 * @param string $dst Ruta donde colocar el archivo comprimido.
	 */
	public static function compress_gzip($src, $dst)
	{
		// Abro archivo de lectura.
		$fp = fopen($src, 'r');

		// Abro archivo de escritura.
		$zp = gzopen($dst, 'w9');

		// Leo y escribo archivo.
		while ( ! feof($fp))
		{
			gzwrite($zp, fread($fp, 512));
		}

		// Cierro archivos.
		fclose($fp);
		gzclose($zp);
	}
}
