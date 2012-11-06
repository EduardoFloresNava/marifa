<?php
/**
 * texto.php is part of Marifa.
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
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase con utilidades de texto.
 *
 * @since      0.1
 * @package    Marifa\Base
 */
class Base_Texto {
	/**
	 * Limits a phrase to a given number of words.
	 * @param string $str phrase to limit words of
	 * @param int $limit number of words to limit to
	 * @param string $end_char end character or entity
	 * @return string
	 */
	public static function limit_words($str, $limit = 100, $end_char = NULL)
	{
		$limit = (int) $limit;
		$end_char = ($end_char === NULL) ? '...' : $end_char;

		if (trim($str) === '')
		{
			return $str;
		}

		if ($limit <= 0)
		{
			return $end_char;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $str, $matches);

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
	}

	/**
	 * Limits a phrase to a given number of characters.
	 * @param string $str phrase to limit characters of
	 * @param int $limit number of characters to limit to
	 * @param string $end_char end character or entity
	 * @param bool $preserve_words enable or disable the preservation of words while limiting
	 * @return string
	 */
	public static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '...' : $end_char;

		$limit = (int) $limit;

		if (trim($str) === '' || strlen($str) <= $limit)
		{
			return $str;
		}

		if ($limit <= 0)
		{
			return $end_char;
		}

		if ($preserve_words === FALSE)
		{
			return rtrim(substr($str, 0, $limit)).$end_char;
		}

		// Don't preserve words. The limit is considered the top limit.
		// No strings with a length longer than $limit should be returned.
		if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
		{
			return $end_char;
		}

		return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
	}
}
