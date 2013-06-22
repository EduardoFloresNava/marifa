<?php
/**
 * request.php is part of Marifa.
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
 * Clase con metodos genericos sobre las peticiones como si es la inicial,
 * si es ajax, el ip, etc etc.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Theme {

	/**
	 * Tema actual. Es un cache para evitar leer continuamente el tema.
	 * @var string
	 */
	protected static $theme;

	public static function check_theme()
	{
		// Verifico si necesito regenerar la cache.
		if ( ! file_exists(APP_BASE.DS.VIEW_PATH.'theme.php') || @filesize(APP_BASE.DS.VIEW_PATH.'theme.php') == 0)
		{
			self::generar_cache();
		}
	}

	/**
	 * Obtengo el tema actual. Si no hay cache la genera y lo obtiene.
	 * @param bool $force_global Forzamos la carga de la permanente o utilizamos la session.
	 * @return string
	 */
	public static function actual($force_global = FALSE)
	{
		// Verifico si necesito regenerar la cache.
		self::check_theme();

		// Devolvemos de disco sin cachear.
		if ($force_global)
		{
			$fp = fopen(APP_BASE.DS.VIEW_PATH.'theme.php', 'rb');
			$theme = trim(fgets($fp));
			fclose($fp);
			return $theme;
		}

		// Verifico si necesito leer el tema.
		if ( ! isset(self::$theme))
		{
			// Verifico tema para previsualizacion.
			$p = self::get_preview();
			if ($p !== NULL)
			{
				self::$theme = $p;
			}
			else
			{
				$fp = fopen(APP_BASE.DS.VIEW_PATH.'theme.php', 'rb');
				self::$theme = trim(fgets($fp));
				fclose($fp);
			}
			unset($p);
		}

		// Devuelvo el tema cacheado.
		return self::$theme;
	}

	/**
	 * Obtenemos la lista de temas que hay.
	 * @param bool $regenerar Si debemos regenerar la cache.
	 * @return array
	 */
	public static function lista($regenerar = FALSE)
	{
		// Verifico si hay que regenerar la cache.
		self::check_theme();
		$themes = array();

		// Abro archivo de temas.
		$fp = fopen(APP_BASE.DS.VIEW_PATH.'theme.php', 'rb');

		// Cargo el actual si no hay previsualizacion.
		$p = self::get_preview();
		if ($p !== NULL)
		{
			self::$theme = $p;
		}
		else
		{
			self::$theme = trim(fgets($fp));
		}
		unset($p);

		// Obtengo el listado restante.
		while ( ! feof($fp))
		{
			$themes[] = trim(fgets($fp));
		}
		fclose($fp);

		return $themes;
	}

	/**
	 * Regeneramos la cache.
	 * @param string $actual Tema para setear como actual. Si no se deja utiliza
	 * el actual o el primero que exista.
	 * @return array
	 */
	private static function generar_cache($actual = NULL)
	{
		// Busco temas existentes..
		$themes = array();
		$dir = scandir(APP_BASE.DS.VIEW_PATH);
		foreach ($dir as $d)
		{
			if ($d == '.' || $d == '..')
			{
				continue;
			}

			if (is_dir(APP_BASE.DS.VIEW_PATH.$d) && file_exists(APP_BASE.DS.VIEW_PATH.$d.DS.'theme.php'))
			{
				$themes[] = trim($d);
			}
		}

		// Verifico si existe actual, sino seteo el primero.
		if ($actual === NULL)
		{
			if (file_exists(APP_BASE.DS.VIEW_PATH.'theme.php') && @filesize(APP_BASE.DS.VIEW_PATH.'theme.php') != 0)
			{
				$actual = self::actual(TRUE);
			}
			else
			{
				$actual = trim($themes[0]);
			}
		}
		self::$theme = $actual;

		// Guardo listado de temas.
		file_put_contents(APP_BASE.DS.VIEW_PATH.'theme.php', $actual.PHP_EOL.implode(PHP_EOL, $themes));

		// Devuelvo los temas.
		return $themes;
	}

	/**
	 * Seteo tema como actual.
	 * @param string $nombre Nombre del tema a setear.
	 */
	public static function setear_tema($nombre)
	{
		//TODO: verificar método para gastar menos RAM. Utilizar un reemplazo directo de la linea.
		self::generar_cache($nombre);
	}

	/**
	 * Obtenemos el tema de previsualizacion si existe y esta seteado.
	 * @return string|NULL
	 */
	private static function get_preview()
	{
		// Verifico esté seteado.
		if (isset($_SESSION['preview-theme']))
		{
			// Verifico exista.
			if (file_exists(APP_BASE.DS.VIEW_PATH.$_SESSION['preview-theme']))
			{
				return $_SESSION['preview-theme'];
			}
		}
		return NULL;
	}

}
