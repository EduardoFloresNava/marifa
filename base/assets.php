<?php
/**
 * cache.php is part of Marifa.
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
 * Clase para el manejo de assets.
 * Pueden ser LESS o CoffeeScript.
 *
 * Se encarga de compilar el código y mandarlo a una cache. En el caso del modo
 * de depuración no son cacheados, simplemente se envian al vuelo.
 *
 * IMPORTANTE: COFFEESCRIPT NO SE ENCUENTRA IMPLEMENTADO.
 * EL MOTIVO ES LA NECECIDAD DE IMPONER PHP 5.3 COMO REQUERIMIENTO.
 * PUEDE REEMPLAZAR compile_coffeescript CON EL CODIGO NECESARIO PARA SU COMPILACION.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Assets {

	/**
	 * Procesar como LESS.
	 */
	const ASSET_LESS = 'LESS';

	/**
	 * Procesar con CoffeeScript.
	 */
	const ASSET_COFFEESCRIPT = 'COFFEE';

	/**
	 * Listado de transformaciones directas.
	 * Su formato es 'extension' => TRANSFORMACION
	 * @var array
	 */
	protected static $translate = array(
		'less'   => self::ASSET_LESS,
		'coffee' => self::ASSET_COFFEESCRIPT
	);

	/**
	 * Realizamos la compilación de un asset.
	 * @param string $file Archivo a compilar.
	 * @param string $cache Archivo donde colocar la cache.
	 * @return mixed
	 */
	public static function reverse_compile($file, $cache = FALSE)
	{
		// Partes de la url.
		$path_parts = pathinfo($file);

		// Verificamos exista filename (PHP > 5.2.0)
		if ( ! isset($path_parts['filename']))
		{
			$path_parts['filename'] = substr($path_parts['basename'], 0, strlen($path_parts['extension']) + 1);
		}

		// Transformamos el path.
		switch ($path_parts['extension'])
		{
			case 'css':
				// Verificamos si existe LESS.
				if (file_exists($path_parts['dirname'].DS.$path_parts['filename'].'.less'))
				{
					return self::compile($path_parts['dirname'].DS.$path_parts['filename'].'.less', $cache);
				}
				elseif (file_exists($path_parts['dirname'].DS.$path_parts['filename'].'.sass'))
				{
					return self::compile($path_parts['dirname'].DS.$path_parts['filename'].'.sass', $cache);
				}
				else
				{
					Error::show_error('File not found', 404);
				}
				break;
			case 'js':
				if (file_exists($path_parts['dirname'].DS.$path_parts['filename'].'.coffee'))
				{
					return self::compile($path_parts['dirname'].DS.$path_parts['filename'].'.coffee', $cache);
				}
				else
				{
					Error::show_error('File not found', 404);
				}
				break;
			default:
				Error::show_error('File not found', 404);
		}
	}

	/**
	 * Compilamos un archivo.
	 * @param type $file Archivo a procesar. Según la extension puede determinase
	 * automáticamente a travez de la tabla de transformaciones el tipo.
	 * @param bool|string $cache Si se debe guardar como cache. En caso de no estar
	 * el tipo en la lista automática (LESS o CS) se debe colocar la URL donde
	 * guardar.
	 * @param string $tipo Tipo de compilación. LESS o CS. Si no se pasa como
	 * argumento se toma a partir de la lista de filtos y extensiones.
	 */
	public static function compile($file, $cache = FALSE, $tipo = NULL)
	{
		// Verificamos el tipo.
		if ($tipo === NULL)
		{
			$path_parts = pathinfo($file);

			$tipo = isset(self::$translate[$path_parts['extension']]) ? (self::$translate[$path_parts['extension']]) : NULL;
			unset($path_parts);
		}

		// Verificamos la cache.
		$cache = $cache ? (self::cache_path($file, $tipo)) : NULL;

		// Compilamos.
		self::do_compile($file, $tipo, $cache);
	}

	/**
	 * Obtenemos el path donde se coloca la cache.
	 * @param string $file Archivo origen.
	 * @param string $tipo Tipo de archivo.
	 * @return string Path resultado.
	 * @throws Exception
	 */
	protected static function cache_path($file, $tipo)
	{
		switch ($tipo)
		{
			case self::ASSET_LESS:
				$path_parts = pathinfo($file);
				return $path_parts['dirname'].DS.substr($path_parts['basename'], 0, (-1)*strlen($path_parts['extension'])).'css';
			case self::ASSET_COFFEESCRIPT:
				return $path_parts['dirname'].DS.substr($path_parts['basename'], 0, (-1)*strlen($path_parts['extension'])).'js';
			default:
				throw new Exception("No se puede manejar el tipo de compilacion de asset '$tipo'");
		}
	}

	/**
	 * Enviamos la compilacion a la clase correspondiente.
	 * @param string $file Archivo a compilar.
	 * @param string $tipo Tipo de compilacion.
	 * @param string $target Donde se guarda la cache. NULL para al vuelo.
	 * @throws Exception
	 */
	protected static function do_compile($file, $tipo, $target = NULL)
	{
		switch ($tipo)
		{
			case self::ASSET_LESS:
				return self::compile_less($file, $target);
			case self::ASSET_COFFEESCRIPT:
				return self::compile_coffeescript($file, $target);
			default:
				throw new Exception("No se puede manejar el tipo de compilacion de asset '$tipo'");
		}
	}

	/**
	 * Compilamos un archivo por medio de LESS.
	 * @param string $file Archivo a compilar.
	 * @param string $target Donde guardar la cache del archivo. NULL para enviar salida al vuelo.
	 */
	protected static function compile_less($file, $target = NULL)
	{
		// Cargo lessc.
		if ( ! class_exists('lessc'))
		{
			include(VENDOR_PATH.'lessc.php');
		}

		// Verifico si se puede escribir.
		if ($target !== NULL && ! is_writable(dirname($target)))
		{
			Log::warning("No se puede generar la cache del asset '$file' en '$target'. Verifique los permisos de escritura.");
			$target = NULL;
		}

		// Realizo la compilación.
		if ($target !== NULL)
		{
			$less = new Lessc;
			$less->compileFile($file, $target);

			header('Content-type: text/css');
			die(file_get_contents($target));
		}
		else
		{
			$less = new Lessc;
			header('Content-type: text/css');
			die($less->compileFile($file));
		}
	}

	/**
	 * Compilamos un archivo por medio de CoffeeScript.
	 * @param string $file Archivo a compilar.
	 * @param string $target Donde guardar la cache del archivo. NULL para enviar salida al vuelo.
	 */
	protected static function compile_coffeescript($file, $target = NULL)
	{
		throw new Exception("CoffeeScript no se encuentra implementado aún. Debe compilar '$file' manualmente".(($target == NULL) ? '.' : " y colocarlo en '$target'."));
	}

}
