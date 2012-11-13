<?php
/**
 * view.php is part of Marifa.
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
 * @since		Version 0.1
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase encargada del manejo de las vistas.
 * Es una abstracción de RainTPL que permite olvidarse del path de las vistas.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_View {

	/**
	 * Propiedad que indica si esta o no configurado RainTPL.
	 */
	private static $is_configured = FALSE;

	/**
	 * Configuramos RainTPL.
	 */
	private static function configure()
	{
		// Defino constantes para URL's relativas.
		if ( ! defined('THEME_URL'))
		{
			define('THEME_URL', SITE_URL.'theme/'.THEME);
		}

		// No usarmos las URL's de RainTPL.
		RainTPL::configure('base_url', '');
		RainTPL::configure('path_replace', FALSE);

		// Configuramos directorio de los template's. Seteamos base para que nuestra
		// extensión se encarge.
		RainTPL::configure('tpl_dir', APP_BASE.DS);

		// Directorio de cache de raintpl ( se usa subdirectorio por la cache de otros
		// elementos).
		RainTPL::configure('cache_dir', CACHE_PATH.DS.'raintpl'.DS.THEME.DS);

		// Extension de los templates iguales que los archivos generales. Evitamos su
		// descarga.
		RainTPL::configure('tpl_ext', FILE_EXT);

		// Los templates por razones de seguridad no pueden usar variables globales.
		RainTPL::configure('black_list', array(
				'\$this',
				'raintpl::',
				'self::',
				'eval',
				'exec',
				'unlink',
				'rmdir',
		));

		RainTPL::configure('check_template_update', TRUE);

		// Por defecto no permitimos etiquetas PHP.
		// Es por seguridad y para mantener el patrón MVC.
		RainTPL::configure('php_enabled', FALSE);

		RainTPL::configure('debug', FALSE);
	}

	/**
	 * Creamos una instancia de RainTPL configurada y lista para usarse.
	 * @param string $view Path a la vista deseada.
	 * @return RainTPL instancia del template.
	 */
	public static function factory($view = NULL)
	{
		// Comprobamos si esta configurado.
		if ( ! self::$is_configured)
		{
			// Configuramos RainTPL.
			self::configure();
			self::$is_configured = TRUE;
		}

		// Devolvemos un objeto de RainTPL.
		return new RainTPL($view);
	}

}
