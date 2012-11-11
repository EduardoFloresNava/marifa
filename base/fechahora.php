<?php
/**
 * fechahora.php is part of Marifa.
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
 * Clase para el manejo de fechas y horas. Agrega soporte para tiempos incrementales
 * a DateTime de PHP.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Fechahora extends DateTime {

	/**
	 * Obtenemos una fecha en formato amigable.
	 * @return string
	 */
	public function fuzzy()
	{
		// Obtenemos diferencia de tiempo.
		if (method_exists($this, 'diff'))
		{
			$diff = $this->diff(new DateTime("now"));
		}
		else
		{
			$diff = date_diff(new DateTime('now'));
		}

		if ($diff->invert)
		{
			$key = __('en %s', FALSE);
		}
		else
		{
			$key = __('hace %s', FALSE);
		}

		if ($diff->y != 0)
		{
			return sprintf($key, $this->pluralize($diff->y, __('a&ntilde;o', FALSE)));
		}

		if ($diff->m != 0)
		{
			return sprintf($key, $this->pluralize($diff->m, __('mes', FALSE)));
		}

		if ($diff->d != 0)
		{
			return sprintf($key, $this->pluralize($diff->d, __('dia', FALSE)));
		}

		if ($diff->h != 0)
		{
			return sprintf($key, $this->pluralize($diff->h, __('hora', FALSE)));
		}

		if ($diff->i != 0)
		{
			return sprintf($key, $this->pluralize($diff->i, __('minuto', FALSE)));
		}

		return sprintf($key, __('instantes', FALSE));
	}
	
	/**
	 * Fallback de getTimestamp para PHP<5.3
	 */
	public function getTimestamp()
	{
		return (int) $this->format('U');
	}

	/**
	 * Obtenemos cadena en plural si es necesario.
	 * @param int $count Cantidad. >1 se vuelve plural.
	 * @param string $text Cadena en sigular.
	 * @return string
	 */
	private function pluralize($count, $text)
	{
		return $count.(($count == 1) ? (" $text") : (" ${text}s"));
	}

}