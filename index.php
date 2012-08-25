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
 * @package		Marifa
 */

/**
 * Cargamos el modo de depuración desde una variable de entorno.
 * Se puede setear esta variable desde htaccess.
 */
if (isset($_SERVER['MARIFA_DEBUG']))
{
	define('DEBUG', (bool) $_SERVER['MARIFA_DEBUG']);
}
else
{
	// Lo seteamos manualmente.
	define('DEBUG', TRUE);
	// PARA PRODUCCION DEBE SER FALSE.
}

if (DEBUG)
{
	// Información de rendimiento para depuración.
	$timestart = microtime(TRUE);
}

/**
 * Separador de directorios
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Directorio base de la aplicación.
 */
define('APP_BASE', __DIR__);

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
 * Directorio de las vistas.
 */
define('VIEW_PATH', 'view');

/**
 * Directorio de la cache.
 */
define('CACHE_PATH', APP_BASE.DS.'cache');

// Cargamos la libreria de carga de clases.
require_once (APP_BASE.DS.'base'.DS.'loader.php');
require_once (APP_BASE.DS.'marifa'.DS.'loader.php');

// Iniciamos el proceso de carga automatica de librerias.
spl_autoload_register('Loader::load');

// Iniciamos el manejo de errores.
Error::getInstance()->start(DEBUG);

// Verificamos bloqueos.
$lock = new Mantenimiento();
if ($lock->is_locked())
{
	if ($lock->is_locked_for(IP::getIpAddr()))
	{
		die("Modo mantenimiento activado.");
	}
}

// Definimos el directorio temporal. Puede definir uno manualmente.
define('TMP_PATH', Update_Utils::sys_get_temp_dir().DS);

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
Cache::getInstance();

// Cargamos las configuraciones del gestor de actualizaciones.
if ( file_exists(CONFIG_PATH.DS.'update.php'))
{
	Configuraciones::load(CONFIG_PATH.DS.'update.php', TRUE);
}

// Comprobamos que existe la lista de plugins.
if ( ! file_exists(APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php'))
{
	// Generamos la lista de plugins.
	Plugin_Manager::getInstance()->regenerar_lista();
}

// Iniciamos la session.
Session::start('random_value');

// Cargamos el despachador y damos el control al controlador correspondiente.
Dispatcher::dispatch();

if (DEBUG)
{
	// Mostramos rendimiento.
	echo(Update_Utils::formatBytes(memory_get_peak_usage(), 1).' - '.round(microtime(true) - $timestart, 1).'s');
}