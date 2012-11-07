<?php
/**
 * log.php is part of Marifa.
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
 * Manejo de Logs.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.2
 * @package    Marifa\Base
 */
class Base_Log {

	/**
	 * Nivel mas alto, no se deben enviar eventos de este tipo, es solo para
	 * deshabilitar el log.
	 */
	const NONE = 0;

	/**
	 * Error grave.
	 */
	const ERROR = 1;

	/**
	 * Falla no tan grave.
	 */
	const WARNING = 2;

	/**
	 * Evento informativo o falla que no causa problemas (E_NOTICE).
	 */
	const INFO = 3;

	/**
	 * Evento de depuracion.
	 */
	const DEBUG = 4;

	/**
	 * Archivo donde alojar los logs.
	 * @var type
	 */
	protected static $file = '';

	/**
	 * Nivel de logs a guardar. Setea el minimo, los inferiores se omiten,
	 * si setea NONE no se guarda ninguno.
	 * @var int
	 */
	protected static $level = self::INFO;

	/**
	 * Obtenemos el path del archivo a guardar en funcion de los parametros.
	 * Realizamos reemplazos para
	 */
	protected static function get_path($path, $file)
	{
		return $path.DS.strftime($file, time());
	}

	/**
	 * Configuramos los logs del sistema.
	 * @param string $path Directorio donde poner los logs.
	 * @param string $file Nombre del archivo. Puede utilizar modificadores de strftime para que sean dinamicos.
	 * @param int $level Nivel minimos de logs que se van a aplicar.
	 */
	public static function setup($path, $file, $level = self::NONE)
	{
		// Obtenemos el path compilado.
		self::$file = self::get_path($path, $file);

		// Iniciamos el directorio.
		if ( ! file_exists(self::$file))
		{
			// Fuerzo creacion del directorio.
			mkdir(dirname(self::$file), 0777, TRUE);

			// Genero el archivo.
			touch(self::$file);
		}

		// Seteo nivel a utilizar.
		self::$level = $level;
	}

	/**
	 * Guardamos una cadena como un log.
	 * @param string $str Cadena a guardar.
	 * @param int $level Nivel de la cadena.
	 * @return bool Si fue correcto o no.
	 */
	public static function log($str, $level = self::NONE)
	{
		// Verifico el nivel.
		if ($level === self::NONE || $level > self::$level)
		{
			// No se debe loguear ese nivel.
			return TRUE;
		}

		// Obtengo cadena del nivel
		$levels = array(
			1 => ' ERROR ',
			2 => 'WARNING',
			3 => ' INFO  ',
			4 => ' DEBUG '
		);

		// Abro el archivo.
		if ( ! $fp = @fopen(self::$file, 'a'))
		{
			// No se pudo realizar la apertura.
			return FALSE;
		}

		// Armo el mensaje.
		$message = '['.date('d-m-Y H:i:s')."] [{$levels[$level]}] $str ".PHP_EOL;

		// Escribo con bloqueo exclusivo.
		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);

		// Cierro el archivo.
		fclose($fp);

		// Informo que se realizo de forma corecta.
		return TRUE;
	}

	/**
	 * Alias de log pero seteamos $level como DEBUG.
	 * @param string $str Cadena a guardar.
	 * @return bool Si fue correcto o no.
	 */
	public static function debug($str)
	{
		return self::log($str, self::DEBUG);
	}

	/**
	 * Alias de log pero seteamos $level como INFO.
	 * @param string $str Cadena a guardar.
	 * @return bool Si fue correcto o no.
	 */
	public static function info($str)
	{
		return self::log($str, self::INFO);
	}

	/**
	 * Alias de log pero seteamos $level como WARNING.
	 * @param string $str Cadena a guardar.
	 * @return bool Si fue correcto o no.
	 */
	public static function warning($str)
	{
		return self::log($str, self::WARNING);
	}

	/**
	 * Alias de log pero seteamos $level como ERROR.
	 * @param string $str Cadena a guardar.
	 * @return bool Si fue correcto o no.
	 */
	public static function error($str)
	{
		return self::log($str, self::ERROR);
	}
}