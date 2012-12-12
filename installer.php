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
 * Defino versión de marifa.
 */
define('VERSION', '0.2RC3');

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

// Suprimimos advertencias de DateTime. Si lo deseas puedes poner una TZ estatica.
date_default_timezone_set('UTC');

// Defino producción para simplificar.
define('PRODUCTION', ! DEBUG);

// Información de rendimiento para depuración.
define('START_MEMORY', memory_get_peak_usage());

/**
 * Separador de directorios
 */
define('DS', '/');

/**
 * Directorio base de la aplicación.
 */
define('APP_BASE', dirname(__FILE__));

/**
 * Directorio de configuraciones.
 */
define('CONFIG_PATH', APP_BASE.DS.'config');

/**
 * Extensión de los archivos a incluir.
 */
define('FILE_EXT', 'php');

/**
 * Directorio de las vistas.
 */
define('VIEW_PATH', 'installer'.DS);

/**
 * Directorio de la cache.
 */
define('CACHE_PATH', APP_BASE.DS.'cache');

/**
 * Directorio de clases de 3ros.
 */
define('VENDOR_PATH', APP_BASE.DS.'vendor'.DS);

// Cargamos funciones varias.
require_once (APP_BASE.DS.'function.php');

// Iniciamos el proceso de carga automatica de librerias.
spl_autoload_register('loader_load');

/**
 * Defino la URL del sitio.
 */
define('SITE_URL', get_site_url());

// Cargo el tema actual.
define('THEME', 'theme');

define('THEME_URL', SITE_URL.DS.VIEW_PATH.THEME);

// Inicio logs.
Log::setup(APP_BASE.DS.'log', '%d-%m-%Y.log', PRODUCTION ? Log::INFO : Log::DEBUG);

// Iniciamos el manejo de errores.
Error::get_instance()->start(DEBUG);

// Database profiler.
PRODUCTION || Profiler_Profiler::get_instance()->set_query_explain_callback('Database::explain_profiler');

PRODUCTION || Profiler_Profiler::get_instance()->log_memory('Framework memory');

// Cargamos el despachador y damos el control al controlador correspondiente.
Installer_Dispatcher::dispatch();

PRODUCTION || Profiler_Profiler::get_instance()->display();