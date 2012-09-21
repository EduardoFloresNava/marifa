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
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if ($img)
		{
			$url = '<img src="'.$url.'"';
			foreach ($atts as $key => $val)
			{
				$url .= ' '.$key.'="'.$val.'"';
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
	public static function getContrastYIQ($hexcolor)
	{
		$r = hexdec(substr($hexcolor,0,2));
		$g = hexdec(substr($hexcolor,2,2));
		$b = hexdec(substr($hexcolor,4,2));
		$yiq = (($r*299)+($g*587)+($b*114))/1000;
		return ($yiq >= 128) ? '000000' : 'FFFFFF';
	}

}
