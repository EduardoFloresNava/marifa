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
 * Carga un archivo de configuraciones y lo devuelve.
 * @param string $file Nombre del archivo a cargar.
 * @param boolean $prepend_name Si agregamos previamente un arreglo con el nombre
 * del archivo.
 * @return array
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */
function configuracion_obtener($file, $prepend_name = FALSE)
{
	// Comprobamos que exista el archivo.
	if ( ! file_exists($file))
	{
		throw new Exception("No existe el archivo de configuraciones '$file'.", 1);
	}

	// Comprobamos si hay que agregar el nombre del archivo a las llamadas.
	if ($prepend_name)
	{
		// Obtenemos el nombre de archivo, sin extension.
		$fi = pathinfo($file);

		if ( ! isset($fi['filename']))
		{
			$fi['filename'] = substr($fi['basename'], 0, (-1) * strlen($fi['extension']) - 1);
		}

		$name = $fi['filename'];
		unset($fi);

		// Cargamos las configuraciones.
		return array($name => include ($file));
	}
	else
	{
		// Cargamos las configuraciones
		return include ($file);
	}
}

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

/**
 * Armo la URL en función de parámetros del tipo.
 * @return string
 */
function get_site_url()
{
	// Obtengo puerto.
	$puerto = (int) $_SERVER['SERVER_PORT'];

	// Verifico si es HTTPS.
	$https = isset($_SERVER['HTTPS']) ? ($_SERVER['HTTPS'] == 'on') : FALSE;

	// URL del servidor.
	$server_name = $_SERVER['SERVER_NAME'];

	return ($https ? 'https' : 'http').'://'.$server_name.((($https && ($puerto == 443)) || ( ! $https && ($puerto == 80))) ? '' : (':'.$puerto)).'/';
}

/**
 * Función provisoria para el manejo de traducciones.
 * @param string $str Cadena a procesar.
 * @param bool $echo Si lo imprimimos o lo devolvemos.
 * @return mixed
 */
function __($str, $echo = TRUE)
{
	if ($echo)
	{
		echo $str;
	}
	else
	{
		return $str;
	}
}

/**
 * Converts a number of bytes to a more readable format
 *
 * @param int   $size      The number of bytes
 * @param string $format_string The format of the return string
 *
 * @return string
 */
function get_readable_file_size($size, $format_string = '')
{
	$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB');

	if ( ! $format_string)
	{
		$format_string = '%01.2f %s';
	}

	$last_size_string = end($sizes);

	foreach ($sizes as $size_string)
	{
		if ($size < 1024)
		{
			break;
		}

		if ($size_string != $last_size_string)
		{
			$size /= 1024;
		}
	}

	if ($size_string == $sizes[0])
	{
		$format_string = '%01d %s';
	}

	return sprintf($format_string, $size, $size_string);
}

/**
 * Obtenemos una clave de un arreglo, si no existe devolvemos $default.
 * @param array $array Arreglo de donde obtener la clave.
 * @param mixed $key Clave a obtener.
 * @param mixed $default Valor devuelto en caso de no encontrar la clave.
 * @return mixed
 */
function arr_get($array, $key, $default = NULL)
{
	return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Obtenemos un mensaje flash. Es decir, un mensaje que se aloja en 1 sessión y
 * solo se muestra 1 vez.
 * @param mixed $key
 * @return mixed
 */
function get_flash($key)
{
	if (isset($_SESSION[$key]))
	{
		$rst = $_SESSION[$key];
		unset($_SESSION[$key]);
		return $rst;
	}
	return NULL;
}

if ( ! function_exists('date_diff'))
{
	/**
	 * Clase fallback para el manejo de intervalos de fechas.
	 * Es para mantener compatibilidad con PHP < 5.3
	 * @since      Versión 0.1
	 * @package    Marifa
	 */
	class DateInterval {

		/**
		 * Años
		 * @var int
		 */
		public $y;

		/**
		 * Meses
		 * @var int
		 */
		public $m;

		/**
		 * Dias
		 * @var int
		 */
		public $d;

		/**
		 * Horas
		 * @var int
		 */
		public $h;

		/**
		 * Minutos
		 * @var int
		 */
		public $i;

		/**
		 * Segundos
		 * @var int
		 */
		public $s;

		/**
		 * Inverso
		 * @var int
		 */
		public $invert;

		/**
		 * Formats the interval
		 * @param string $format Format
		 * @return string
		 */
		public function format($format)
		{
			$format = str_replace('%R%y', ($this->invert ? '-' : '+').$this->y, $format);
			$format = str_replace('%R%m', ($this->invert ? '-' : '+').$this->m, $format);
			$format = str_replace('%R%d', ($this->invert ? '-' : '+').$this->d, $format);
			$format = str_replace('%R%h', ($this->invert ? '-' : '+').$this->h, $format);
			$format = str_replace('%R%i', ($this->invert ? '-' : '+').$this->i, $format);
			$format = str_replace('%R%s', ($this->invert ? '-' : '+').$this->s, $format);

			$format = str_replace('%y', $this->y, $format);
			$format = str_replace('%m', $this->m, $format);
			$format = str_replace('%d', $this->d, $format);
			$format = str_replace('%h', $this->h, $format);
			$format = str_replace('%i', $this->i, $format);
			$format = str_replace('%s', $this->s, $format);

			return $format;
		}
	}

	/**
	 * Fallback de date_diff. Sirve para mantener compatibilidad con PHP < 5.3
	 * @param DateTime $date1
	 * @param DateTime $date2
	 * @return DateInterval
	 */
	function date_diff(DateTime $date1, DateTime $date2)
	{
		$diff = new DateInterval;

		if ($date1 > $date2)
		{
			$tmp = $date1;
			$date1 = $date2;
			$date2 = $tmp;
			$diff->invert = TRUE;
		}

		$diff->y = ( (int) $date2->format('Y')) - ( (int) $date1->format('Y'));
		$diff->m = ( (int) $date2->format('n')) - ( (int) $date1->format('n'));
		if ($diff->m < 0)
		{
			$diff->y -= 1;
			$diff->m = $diff->m + 12;
		}

		$diff->d = ( (int) $date2->format('j')) - ( (int) $date1->format('j'));
		if ($diff->d < 0)
		{
			$diff->m -= 1;
			$diff->d = $diff->d + ( (int) $date1->format('t'));
		}

		$diff->h = ( (int) $date2->format('G')) - ( (int) $date1->format('G'));
		if ($diff->h < 0)
		{
			$diff->d -= 1;
			$diff->h = $diff->h + 24;
		}

		$diff->i = ( (int) $date2->format('i')) - ( (int) $date1->format('i'));
		if ($diff->i < 0)
		{
			$diff->h -= 1;
			$diff->i = $diff->i + 60;
		}

		$diff->s = ( (int) $date2->format('s')) - ( (int) $date1->format('s'));
		if ($diff->s < 0)
		{
			$diff->i -= 1;
			$diff->s = $diff->s + 60;
		}

		return $diff;
	}

}

/**
 * Obtenemos el IP de la petición.
 * @return string
 */
function get_ip_addr()
{
   if ( ! empty($_SERVER['HTTP_CLIENT_IP']))
   {
	   // Check ip from share internet
	   return $_SERVER['HTTP_CLIENT_IP'];
   }
   elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']))
   {
		// To check ip is pass from proxy
	   return $_SERVER['HTTP_X_FORWARDED_FOR'];
   }
   else
   {
	   return $_SERVER['REMOTE_ADDR'];
   }
}