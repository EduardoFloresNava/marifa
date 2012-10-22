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
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para el manejo de IP's. Tiene funciones para facilitar
 * el trabajo con IP's y rangos.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Ip {

	/**
	 * The function will return true if the supplied IP is within the range.
	 * Network ranges can be specified as:
	 * 1. Wildcard format:     1.2.3.*
	 * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
	 * 3. Start-End IP format: 1.2.3.0-1.2.3.255
	 *
	 * @param string $ip IP address
	 * @param string $range "range" in several different formats
	 * @return bool
	 * @throws Exception
	 * @author Paul Gregg <pgregg@pgregg.com>
	 * @copyright 2008 Paul Gregg <pgregg@pgregg.com>
	 * @version 1.2
	 */
	public static function ip_in_range($ip, $range)
	{
		if (strpos($range, '/') !== FALSE)
		{
			// $range is in IP/NETMASK format
			list($range, $netmask) = explode('/', $range, 2);
			if (strpos($netmask, '.') !== FALSE)
			{
				// $netmask is a 255.255.0.0 format
				$netmask = str_replace('*', '0', $netmask);
				$netmask_dec = ip2long($netmask);
				return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
			}
			else
			{
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode('.', $range);
				while (count($x) < 4)
				{
					$x[] = '0';
				}
				list($a, $b, $c, $d) = $x;
				$range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b,empty($c) ? '0' : $c,empty($d) ? '0' : $d);
				$range_dec = ip2long($range);
				$ip_dec = ip2long($ip);

				// Create netmask.
				$wildcard_dec = pow(2, (32-$netmask)) - 1;
				$netmask_dec = ~ $wildcard_dec;

				return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
			}
		}
		else
		{
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
			if (strpos($range, '*') !== FALSE)
			{ // a.b.*.* format
				// Just convert to A-B format by setting * to 0 for A and 255 for B
				$lower = str_replace('*', '0', $range);
				$upper = str_replace('*', '255', $range);
				$range = "$lower-$upper";
			}

			if (strpos($range, '-')!== FALSE)
			{ // A-B format
				list($lower, $upper) = explode('-', $range, 2);
				$lower_dec = (float) sprintf("%u", ip2long($lower));
				$upper_dec = (float) sprintf("%u", ip2long($upper));
				$ip_dec = (float) sprintf("%u" ,ip2long($ip));
				return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
			}

			//throw new Exception('Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format');
			return FALSE;
		}
	}

	/**
	 * Obtenemos el IP de la petición.
	 * @return string
	 */
	public static function get_ip_addr()
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

}
