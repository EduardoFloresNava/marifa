<?php
/**
 * index.php is part of Marifa.
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
 * @package		Shell
 */

/**
 * Version de la shell.
 */
define('VERSION', 0.1);

/**
 * Separador de directorios
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Directorio base de la aplicación.
 */
define('APP_BASE', dirname(__DIR__));

/**
 * Directorio de shells.
 */
define('SHELL_PATH', APP_BASE.DS.'shells');

/**
 * Directorio de configuraciones.
 */
define('CONFIG_PATH', APP_BASE.DS.'config');

/**
 * Extensión de los archivos a incluir.
 */
define('FILE_EXT', 'php');

/**
 * Directorio de plugins.
 */
define('PLUGINS_PATH', 'plugin');

/**
 * Directorio de la cache.
 */
define('CACHE_PATH', APP_BASE.DS.'cache');

// Cargador de CLI.
require_once (SHELL_PATH.DS.'classes'.DS.'loader.php');
spl_autoload_register('Shell_Loader::load');

// Cargamos la libreria de carga de clases del nucleo.
require_once (APP_BASE.DS.'base'.DS.'loader.php');
require_once (APP_BASE.DS.'marifa'.DS.'loader.php');

// Iniciamos el proceso de carga automatica de librerias.
spl_autoload_register('Loader::load');

// Comprobamos que exista la configuración de la base de datos.
if ( ! file_exists(CONFIG_PATH.DS.'database.php'))
{
	//TODO: lo mandamos al instalador.
	die("Falta configurar la base de datos");
}
else
{
	// Cargamos la configuración de la base de datos.
	Configuraciones::load(CONFIG_PATH.DS.'database.php', TRUE);
}

// Forzamos una cache inexistente. Comente esta linea para habilitar la cache.
Configuraciones::set('cache.type', NULL);

// Cargamos la cache.
Cache::get_instance();

// Comprobamos que existe la lista de plugins.
if ( ! file_exists(APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php'))
{
	// Generamos la lista de plugins.
	Plugin_Manager::get_instance()->regenerar_lista();
}

// Iniciamos la session.
Session::start('random_value');

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

// Cargamos el despachador y damos el control al controlador correspondiente.
Shell_Dispatcher::dispatch();