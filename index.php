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

/**
 * Iniciamos buffer.
 */
ob_start();

// Cargamos funciones varias.
require_once (APP_BASE.DS.'function.php');

// Iniciamos el proceso de carga automática de clases y archivos.
spl_autoload_register('loader_load');

/**
 * Defino la URL del sitio. Puede definirla manualmente. No debe terminar en /.
 * Por ejemplo:
 * define('SITE_URL', 'http://demo.marifa.com.ar');
 */
define('SITE_URL', get_site_url());

// Verifico que no exista el instalador.
if (PRODUCTION)
{
	if (file_exists(APP_BASE.DS.'installer') || file_exists(APP_BASE.DS.'installer.php'))
	{
		if ( ! file_exists(CONFIG_PATH.DS.'database.php'))
		{
			Request::redirect('/installer/');
		}

		if (isset($_GET['finish']) && $_GET['finish'] == 1)
		{
			session_start();
			if (isset($_SESSION['step']) && $_SESSION['step'] >= 5)
			{
				// Intento eliminar.
				@unlink(APP_BASE.DS.'installer.php');
				Update_Utils::unlink(APP_BASE.DS.'installer');

				// Redirecciono.
				Request::redirect('/');
			}
		}

		die('Debes eliminar el instalador para poder acceder al sitio. Para intentar de forma automática has click <a href="'.SITE_URL.'/?finish=1">aquí</a>.');
	}
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

// Salida UTF-8.
header('Content-type: text/html; charset=utf-8');

// Cargo el tema actual.
define('THEME', Theme::actual());

// Iniciamos el manejo de errores.
Error::get_instance()->start(DEBUG);

// Iniciamos el usuario.
Usuario::start();

// Verificamos bloqueos.
if (Mantenimiento::is_locked())
{
	if (Mantenimiento::is_locked_for(get_ip_addr()))
	{
		// Cabeceras para SEO.
		header("HTTP/1.1 503 Service Unavailable");
		header("Retry-After: 3600");

		// Cargo la vista.
		$view = View::factory('mantenimiento');
		$view->show();
		exit;
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

// Cargamos las configuraciones del gestor de actualizaciones.
if (file_exists(CONFIG_PATH.DS.'update.php'))
{
	// Configuraciones::load(CONFIG_PATH.DS.'update.php', TRUE);
}

// Comprobamos que existe la lista de plugins.
if ( ! file_exists(APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php'))
{
	// Generamos la lista de plugins.
	Plugin_Manager::get_instance()->regenerar_lista();
}

// Cargamos la lista de eventos.
Event::load_from_plugins();

// Database profiler.
PRODUCTION || Profiler_Profiler::get_instance()->set_query_explain_callback('Database::explain_profiler');

PRODUCTION || Profiler_Profiler::get_instance()->log_memory('Framework memory');

// Cargamos el despachador y damos el control al controlador correspondiente.
Dispatcher::dispatch();

PRODUCTION || Profiler_Profiler::get_instance()->display();