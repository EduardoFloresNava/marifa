<?php
/**
 * error.php is part of Marifa.
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
 * Función para el manejo de errores.
 *
 * Se encarga de manejar los errores mostrando en el modo de desarrollo
 * información util y en el modo de producción una pantalla al usuario
 * de forma de
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Error {

	/**
	 * Instancia para el patrón singleton
	 */
	protected static $instance;

	/**
	 * Modo depuración.
	 */
	protected static $debug = FALSE;

	/**
	 * Clases de log de errores.
	 */
	protected static $loggers;

	/**
	 * Variable que determina si se han mostrado errores o no aun.
	 * @var bool
	 */
	public static $has_error = FALSE;

	/**
	 * Constructor privado para singleton.
	 */
	private function __construct()
	{
		self::$loggers = array();
		self::$debug = FALSE;
	}

	/**
	 * Obtener una instancia para singleton.
	 */
	public static function get_instance()
	{
		if ( ! isset(self::$instance))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * No se puede clonar por singleton.
	 */
	public function __clone()
	{
	}

	/**
	 * No se puede deserializar por singleton.
	 */
	public function __wakeup()
	{
	}

	/**
	 * Inciamos el gestor de errores y excepciones.
	 * @param boolean $debug Habilitada la depuración?
	 */
	public function start($debug = FALSE)
	{
		self::$debug = $debug;

		set_error_handler('Error::error_handler');
		set_exception_handler('Error::exception_handler');

		// Handler PHP 5.2 errors.
		register_shutdown_function('Error::shutdown_handler');
	}

	/**
	 * Agregamos una clase de log.
	 * @param object $logger Instacia de objeto de log.
	 */
	public function attach($logger)
	{
		self::$loggers[] = $logger;
	}

	/**
	 * Manejador de errores de PHP 5.2.
	 */
	public static function shutdown_handler()
	{
		// Obtenemos el último error.
		$error = error_get_last();

		// Verificamos su existencia.
		if ($error !== NULL)
		{
			// Mapeamos la llamada al manejador de errores.
			self::error_handler($error['type'], $error['message'], $error['file'], $error['line']);
		}
	}

	/**
	 * Manejador de los errores.
	 * @param int $errno Numero de error
	 * @param string $errstr Descripción del error
	 * @param string $errfile Archivo donde se produjo el error
	 * @param int $errline Linea donde se produjo el error
	 * @param array $errcontext Contexto del error o backtrace
	 */
	public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext = array())
	{
		// Verifico @.
		if (error_reporting() === 0)
		{
			return;
		}

		// OBTENEMOS EL STACK.
		$ec = (is_array($errcontext)) ? ((count($errcontext) > 0) ? $errcontext : debug_backtrace()) : debug_backtrace();
		// CADENA QUE REPRESENTA EL NUMERO DE ERROR
		$error_type_string = self::get_error_type($errno);
		// CADENA DESCRIPCION DEL ERROR
		$error_string = self::get_error_string($errstr, $errfile, $errline);
		// REPRESENTACION DEL STACK
		$error_backtrace = self::parse_back_trace($ec);

		// Enviamos error a los logs.
		Log::log($error_type_string.$error_string, self::get_error_log_type($errno));

		// MOSTRAMOS ERROR SEGUN ES DEBUG O NO.
		if ( ! self::$debug)
		{
			self::show_error(self::get_user_message($errno), 500);
		}
		else
		{
			self::show_error($error_type_string.$error_string, 500, array('reflection' => self::last_call($ec), 'backtrace' => $error_backtrace, 'file' => $errfile, 'line' => $errline));
		}
		// LOG DEL ERROR PARA TENER SUFICIENTE INFORMACION.
		foreach (self::$loggers as $logger)
		{
			$logger->write($errno, $error_type_string.$error_string.$error_backtrace);
		}

		// Evitamos gestor de errores por defecto.
		return TRUE;
	}

	/**
	 * Obtenemos donde esta la última llamada, diciendo la función o la clase
	 * para obtener el lugar exacto.
	 * @param array $tb Traceback de la llamada
	 */
	private static function last_call($tb = array())
	{
		foreach ($tb as $v)
		{
			$func_name = self::arr_get($v, 'function');
			if ($func_name == 'error_handler' || $func_name == 'shutdown_handler' || $func_name == 'exception_handler')
			{
				continue;
			}

			if ($func_name == "include" || $func_name == "include_once" || $func_name == "require_once" || $func_name == "require")
			{
				continue;
			}

			if ( ! is_array($v))
			{
				return NULL;
			}
			if (isset($v['class']))
			{
				return array($v['class'], $v['type'], $v['function']);
			}
			elseif (isset($v['function']))
			{
				return $v['function'];
			}
			else
			{
				return NULL;
			}

		}
		return NULL;
	}


	/**
	 * Manejador de las excepciones.
	 * @param mixed $exception Excepción no manejada
	 */
	public static function exception_handler($exception)
	{
		// Si es excepcion de RainTPL la ponemos como
		if ($exception instanceof RainTpl_NotFoundException || $exception instanceof RainTpl_SyntaxException)
		{
			return self::parse_as_raintpl($exception);
		}

		$error_type_string = get_class($exception);
		$error_string = self::get_error_string($exception->getMessage(), $exception->getFile(), $exception->getLine());
		$error_backtrace = $exception->getTraceAsString();

		// Envio log del error.
		Log::log($error_type_string.$error_string, Log::ERROR);

		if ( ! self::$debug)
		{
			self::show_error(self::get_user_message('exception'), 500);
		}
		else
		{
			self::show_error($error_type_string.$error_string, 500, array('backtrace' => $error_backtrace, 'file' => $exception->getFile(), 'line' => $exception->getLine()));
		}
		// LOG del error completo.
		foreach (self::$loggers as $logger)
		{
			$logger->write($errno, $error_type_string.$error_string.$error_backtrace);
		}
	}

	/**
	 * Procesamos una excepcion como una de RainTPL.
	 * Permite excepciones mas amigables.
	 * @param RainTPL_Exception $exception Excepcion a procesar.
	 */
	protected static function parse_as_raintpl($exception)
	{
		if ($exception instanceof RainTpl_NotFoundException)
		{
			// Envio log del error.
			Log::log("No se ha podido cargar la vista '{$exception->getTemplateFile()}'.", Log::ERROR);

			if ( ! self::$debug)
			{
				self::show_error(self::get_user_message('exception'), 500);
			}
			else
			{
				//TODO: obtener la llamada al parseo y no el codigo interno.
				self::show_error("No se ha podido cargar la vista '{$exception->getTemplateFile()}'.", 500, array('file' => $exception->getFile(), 'line' => $exception->getLine()));
			}
		}
		elseif ($exception instanceof RainTpl_SyntaxException)
		{
			// Envio log del error.
			Log::log("No se ha podido procesar la vista '{$exception->getTemplateFile()}' por un error en la etiqueta {$exception->getTag()} en la linea {$exception->getTemplateLine()}.", Log::ERROR);

			if ( ! self::$debug)
			{
				self::show_error(self::get_user_message('exception'), 500);
			}
			else
			{
				//TODO: obtener la llamada al parseo y no el codigo interno.
				self::show_error("No se ha podido procesar la vista '{$exception->getTemplateFile()}' por un error en la etiqueta {$exception->getTag()} en la linea {$exception->getTemplateLine()}.", 500, array('file' => $exception->getFile(), 'line' => $exception->getLine()));
			}
		}
	}

	/**
	 * Mostramos la página de error según el tipo de error del que se trate.
	 *
	 * Por defecto se disponen de tan solo 2 errores. 404 y 500.
	 * Estás páginas aplican el modelo de sobreescritura de las vistas y se
	 * ubican en internal/error/$number.php
	 * Donde $number es el numero del error.
	 * Además enviamos una variable $debug para informar si es desarrollo o
	 * producción.
	 *
	 * Los errores internos del PHP y las excepciones se mapean automáticamente
	 * a la correspondiente vista.
	 *
	 * @param mixed $description Descripcion del error.
	 * @param int $number Numero del error, por defecto 500.
	 * @param array $extended Información adicional sobre el error.
	 */
	public static function show_error($description, $number = 500, $extended = NULL)
	{
		// Estamos mostrando un error.
		self::$has_error = TRUE;

		// Cargamos la vista.
		$view = View::factory('internal/error/'.$number);

		// Seteamos entorno
		$view->assign('debug', self::$debug);

		// Seteamos las variables base
		$view->assign('descripcion', NULL);
		$view->assign('backtrace', NULL);
		$view->assign('source', NULL);

		// Comprobamos el marco de trabajo.
		if (self::$debug)
		{
			// Mostramos una pantalla de error para depurar.

			// Agregamos la descripción.
			$view->assign('descripcion', $description);

			// Obtenemos la información util que encontremos en $extended.
			if (is_array($extended))
			{
				// Buscamos bracktrace.
				if (isset($extended['backtrace']))
				{
					$view->assign('backtrace', $extended['backtrace']);
				}

				// Buscamos información para mostra el código fuente.
				if (isset($extended['file']) && isset($extended['line']))
				{
					$view->assign('source', self::show_source_error(7, $extended['line'], $extended['file'], self::arr_get($extended, 'reflection', NULL)));
				}
			}
		}
		else
		{
			// Mostramos una pantalla de error para el usuario en modo produccion.

			// Es producción
			$view->assign('debug', FALSE);

			// Agregamos la descripción del error.
			$view->assign('descripcion', $description);
		}

		// Cabeceras de error.
		if ($number == 404 || $number == 500)
		{
			 header(':', TRUE, $number);
		}

		// Cargo template.
		$template = View::factory('internal/template');

		// Asigno datos.
		try {
			$template->assign('contenido', $view->parse());
		}
		catch (Exception $e)
		{
			die('ERROR '.$e->getCode().': '.$e->getMessage());
		}
		$template->assign('number', $number);

		// Mostramos la pantalla de error.
		try {
			$template->show();
		}
		catch (Exception $e)
		{
			die('ERROR '.$e->getCode().': '.$e->getMessage());
		}

		// Terminamos la ejecución
		exit;
	}

	/**
	 * Devolvemos N lineas a rededor de un error para facilitar la depuración.
	 *
	 * @param int $lines Numero de lineas a mostrar a cada lado. 0 muestra solo
	 * la linea del error
	 * @param int $line Linea del error.
	 * @param string $path Path del archivo donde se produjo el error.
	 * @param array|null $reflection Información de la clase o funcion para aplicar reflection
	 * para mejorar la salida.
	 * @return string
	 */
	private static function show_source_error($lines, $line, $path, $reflection = NULL)
	{
		// Validamos exista el archivo.
		if ( ! file_exists($path))
		{
			return NULL;
		}

		// Abrimos el archivo.
		$source = file($path);

		// Generamos el rango de lineas.
		$start = $line - $lines;
		$end = $line + $lines;

		// Validamos el rango.
		if ($start <= 0)
		{
			$start = 1;
		}
		if ($end > count($source))
		{
			$end = count($source);
		}

		// Verificamos si tenemos reflection
		if ($reflection !== NULL)
		{
			// Si es una clase, movemos los extremos para mejorar el reporte.
			if (is_array($reflection))
			{
				// Cargamos los limites de la función
				$rc = new ReflectionClass($reflection[0]);
				$rm = $rc->getMethod($reflection[2]);
				$start_min = $rm->getStartLine();
				$end_max = $rm->getEndLine();

				// Si empieza antes del inicio de la funcion lo corremos
				if ($start < $start_min)
				{
					$end = $end + $start_min - $start;
					$start = $start_min;

					// Si termina despues luego de correrlo, corregimos.
					if ($end > $end_max)
					{
						$end = $end_max;
					}
				}
				else
				{
					// Si termina despues del final de la función lo corremos
					if ($end > $end_max)
					{
						$start = $start - $end + $end_max;
						$end = $end_max;

						// Si empiza antes de la funcion lo cortamos.
						if ($start < $start_min)
						{
							$start = $start_min;
						}
					}
				}

				// Liberamos memoria.
				unset($start_min);
				unset($end_max);
			}
		}

		// Donde guardar las lineas.
		$l = '';

		// Devolvemos las lineas.
		for ($i = $start - 1; $i < $end; $i++)
		{
			// Buscamos la linea actual para resaltarla.
			if ($i === $line - 1)
			{
				$l .= sprintf("<b>%5d | %s </b>\n", $i, htmlentities(rtrim($source[$i]), ENT_QUOTES, 'UTF-8'));
			}
			else
			{
				$l .= sprintf("%5d | %s \n", $i, htmlentities(rtrim($source[$i]), ENT_QUOTES, 'UTF-8'));
			}
		}
		return $l;
	}

	/**
	 * Obtenemos una cadena de caracteres del número de error.
	 * @param int $error_number Number Número de error a procesar.
	 * @return string Cadena representativa del error.
	 */
	private static function get_error_type($error_number)
	{
		switch ($error_number)
		{
			case E_NOTICE:
			case E_USER_NOTICE:
				$type = 'Notice';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$type = 'Warning';
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$type = 'Fatal Error';
				break;
			default:
				$type = 'Unknown Error';
				break;
		}
		return $type;
	}

	/**
	 * Obtenemos una cadena de caracteres del número de error.
	 * @param int $error_number Number Número de error a procesar.
	 * @return string Cadena representativa del error.
	 */
	private static function get_error_log_type($error_number)
	{
		//TODO: Agregar errores faltantes.
		switch ($error_number)
		{
			case E_NOTICE:
			case E_USER_NOTICE:
				return Log::INFO;
			case E_WARNING:
			case E_USER_WARNING:
				return Log::WARNING;
			case E_ERROR:
			case E_USER_ERROR:
				return Log::ERROR;
			default:
				return Log::ERROR;
		}
	}

	/**
	 * Obtenemos una cadena de caracteres representación del error.
	 * @param string $error_string Descripción del error.
	 * @param string $error_file Archivo del error.
	 * @param int $error_line Linea del error
	 * @return string Cadena representativa del error.
	 */
	private static function get_error_string($error_string, $error_file, $error_line)
	{
		$string = ': "'.$error_string.'" in '.$error_file.' on line '.$error_line.'.';
		return $string;
	}

	/**
	 * Obtiene una representación del stack para ver el contexto del error.
	 * @param array $tb Arreglo con el stack de llamadas.
	 * @return string Cadena representativa del Stack de llamadas.
	 */
	private static function parse_back_trace($tb)
	{
		$backtrace = array();
		foreach ($tb as $k => $v)
		{
			if ( ! is_array($v))
			{
				continue;
			}
			$func_name = self::arr_get($v, 'function');
			if ($func_name == 'error_handler' || $func_name == 'shutdown_handler' || $func_name == 'exception_handler')
			{
				continue;
			}
			if ($func_name == "include" || $func_name == "include_once" || $func_name == "require_once" || $func_name == "require")
			{
				$args = '';
				if (isset($v['args']))
				{
					$args = array();
					foreach ($v['args'] as $v)
					{
						$args[] = gettype($v);
					}
					$args = implode(', ', $args);
				}

				$file = isset($v['file']) ? $v['file'] : '';
				$line = isset($v['line']) ? $v['line'] : '';

				$backtrace[] = "#$k $func_name($args) called at [$file:$line]";
			}
			else
			{
				$args = '';
				if (isset($v['args']))
				{
					$args = array();
					foreach ($v['args'] as $v)
					{
						$args[] = gettype($v);
					}
					$args = implode(', ', $args);
				}

				$file = isset($v['file']) ? $v['file'] : '';
				$line = isset($v['line']) ? $v['line'] : '';

				$backtrace[] = "#$k $func_name($args) called at [$file:$line]";
			}
		}
		return "\n".implode("\n", $backtrace)."\n";
	}

	/**
	 * Helper para no introducir notices en los arreglos.
	 * @param array $array Arreglo donde buscar $value
	 * @param string|int $value Elemento del arreglo deseado.
	 * @param mixed $default Valor si no se encuentra $value en $array.
	 * @return mixed
	 */
	private static function arr_get($array, $value, $default = NULL)
	{
		if ( ! is_array($array)) return $default;
		return isset($array[$value]) ? $array[$value] : $default;
	}

	/**
	 * Error que se le muestra al usuario segun el tipo de error.
	 * @param int $error_number Numero de error.
	 * @return string Cadena representativa para mostrar al usuario.
	 */
	private static function get_user_message($error_number)
	{
		switch ($error_number)
		{
			case E_USER_ERROR:
				return 'ERROR FATAL: Se ha producido un error fatal. Informe al administrador para que pueda ser solucionado cuento antes.';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				return 'ADVERTENCIA: Se ha producido una falla, el sitio puede no estar funcionando de forma correcta. Si el problema continua, por favor contacte al administrador.';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				return 'NOTICE: Se ha producido una pequeña falla al procesar la petición, si el problema persiste contacte al administrador.';
				break;
			default:
				return 'Se ha producido un error al procesar la petición. El administrador del sitio ya ha sido informado del problema.';
				break;
		}
	}

}
