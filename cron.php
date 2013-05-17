<?php
/**
 * cron.php is part of Marifa.
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
 * Tareas que deben realizarse por cronjobs.
 */

define('DEBUG', TRUE);

/**
 * Defino versión de marifa.
 */
define('VERSION', '0.3.0');

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
 * Directorio de plugins.
 */
define('PLUGINS_PATH', 'plugin');

/**
 * Directorio de las vistas.
 */
define('VIEW_PATH', 'theme'.DS);

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

// Iniciamos el proceso de carga automática de clases y archivos.
spl_autoload_register('loader_load');

/**
 * Defino la URL del sitio. Puede definirla manualmente. No debe terminar en /.
 * Por ejemplo:
 * define('SITE_URL', 'http://demo.marifa.com.ar');
 */
define('SITE_URL', '');

// Verifico que no exista el instalador.
if (file_exists(APP_BASE.DS.'installer') || file_exists(APP_BASE.DS.'installer.php'))
{
	//die('No se puede ejecutar el cronjob sin terminar el instalador.');
}

// Verifico MCrypt.
extension_loaded('mcrypt') || die('Marifa necesita MCrypt para funcionar.');

// Cargo configuraciones Base del sistema.
$default_config = configuracion_obtener(CONFIG_PATH.DS.'marifa.php');

date_default_timezone_set($default_config['default_timezone']);
if (file_exists(APP_BASE.DS.'traducciones'.DS.$default_config['language'].'.php'))
{
	$GLOBALS['lang'] = configuracion_obtener(APP_BASE.DS.'traducciones'.DS.$default_config['language'].'.php');
}
else
{
	$GLOBALS['lang'] = array();
}

// Iniciamos las cookies.
Cookie::start($default_config['cookie_secret']);

// Borro configuraciones del sistema.
unset($default_config);

// Inicio logs.
Log::setup(APP_BASE.DS.'log', '%d-%m-%Y.log', PRODUCTION ? Log::INFO : Log::DEBUG);

// Cargo el tema actual.
define('THEME', Theme::actual());

// Iniciamos el manejo de errores.
Error::get_instance()->start(DEBUG, TRUE);

// Definimos el directorio temporal. Puede definir uno manualmente.
define('TMP_PATH', Update_Utils::sys_get_temp_dir().DS);

// Comprobamos que exista la configuración de la base de datos.
if ( ! file_exists(CONFIG_PATH.DS.'database.php'))
{
	die("Falta configurar la base de datos");
}

// Comprobamos que existe la lista de plugins.
if ( ! file_exists(APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php'))
{
	// Generamos la lista de plugins.
	Plugin_Manager::get_instance()->regenerar_lista();
}

// Cargamos la lista de eventos.
Event::load_from_plugins();

// Procesamos la cola de E-Mail's.
$mail = new Email_Queue;
$mail->procesar();