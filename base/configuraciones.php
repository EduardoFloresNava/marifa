<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * configuraciones.php is part of Marifa.
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
 * @package		Marifa/Base
 */

/**
 * Clase encargada del manejo de configuraciones.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa/Base
 */
class Base_Configuraciones {

	/**
	 * Arreglo con las configuraciones.
	 */
	protected static $data = array();

	/**
	 * Carga un archivo de configuraciones y lo devuelve.
	 * @param string $file Nombre del archivo a cargar.
	 * @param boolean $prepend_name Si agregamos previamente un arreglo con el nombre
	 * del archivo.
	 * @return array
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public static function obtener($file, $prepend_name = FALSE)
	{
		// Comprobamos que exista el archivo.
		if ( ! file_exists($file))
		{
			throw new Exception("No existe el archivo de configuraciones '$file'.", 1);
		}

		// Comprobamos si hay que agregar el nombre del archivo a las llamadas.
		if ($prepend_name)
		{
			// Obtenemos el nombre de archivo, sin extension.
			$fi = pathinfo($file);

			if ( ! isset($fi['filename']))
			{
				$fi['filename'] = substr($fi['basename'], 0, (-1) * strlen($fi['extension']) - 1);
			}

			$name = $fi['filename'];
			unset($fi);

			// Cargamos las configuraciones.
			return array($name => include ($file));
		}
		else
		{
			// Cargamos las configuraciones
			return include ($file);
		}
	}

	/**
	 * Carga un archivo de configuraciones y lo agrega a la lista de configuraciones
	 * global.
	 * @param string $file Nombre del archivo a cargar.
	 * @param boolean $prepend_name Si agregamos previamente un arreglo con el nombre
	 * del archivo.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public static function load($file, $prepend_name = FALSE)
	{
		// Comprobamos que exista el archivo.
		if ( ! file_exists($file))
		{
			throw new Exception("No existe el archivo de configuraciones '$file'.", 1);
		}

		// Comprobamos si hay que agregar el nombre del archivo a las llamadas.
		if ($prepend_name)
		{
			// Obtenemos el nombre de archivo, sin extension.
			$fi = pathinfo($file);

			if ( ! isset($fi['filename']))
			{
				$fi['filename'] = substr($fi['basename'], 0, (-1) * strlen($fi['extension']) - 1);
			}

			$name = $fi['filename'];
			unset($fi);

			// Cargamos las configuraciones.
			$data = array($name => include ($file));
		}
		else
		{
			// Cargamos las configuraciones
			$data = include ($file);
		}

		// Unimos las nuevas configuraciones a las existentes.
		self::$data = array_merge_recursive(self::$data, $data);
	}

	/**
	 * Obtenemos un elemento de las variables de configuracion.
	 * Podemos usar operadores del tipo variable.subvariable.
	 * @param string $variable Nombre del objeto de configuración a obtener.
	 * @param mixed $default Valor a devolver si no se encuenta.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public static function get($variable, $default = NULL)
	{
		// Transformamos la variable en un arreglo a recorrer.
		$segmentos = explode('.', $variable);

		// Si es una variable simple devolvemos. Mejora el rendimiento.
		if (count($segmentos) == 1)
		{
			$segmento = $segmentos[0];
			return isset(self::$data[$segmento]) ? self::$data[$segmento] : $default;
		}

		// Cargamos una copia de las configuraciones para ir reduciendo.
		$data = self::$data;

		// Recorremos recursivamente en busca del valor.
		foreach ($segmentos as $segmento)
		{
			// Comprobamos que exista el segmento en el arreglo base.
			if (is_array($data) && isset($data[$segmento]))
			{
				// Devolvemos el valor.
				$data = $data[$segmento];
			}
			else
			{
				// Usamos el por defecto.
				$data = $default;
				break;
			}
		}

		// Devolvemos el valor.
		return $data;
	}

	/**
	 * Asginamos un valor a un elemento de las variables de configuracion.
	 * Podemos usar operadores del tipo variable.subvariable.
	 * @param string $variable Nombre del objeto de configuración a setear.
	 * @param mixed $valor Valor a poner en ese elemento.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public static function set($variable, $valor)
	{
		// Transformamos la variable en un arreglo a recorrer.
		$segmentos = explode('.', $variable);

		// Si es una variable simple devolvemos. Mejora el rendimiento.
		if (count($segmentos) == 1)
		{
			$data = array($segmentos[0] => $valor);
		}
		else
		{
			$sa = array_reverse($segmentos);

			$data = $valor;
			foreach($sa as $s)
			{
				$data = array($s => $data);
			}
		}

		// Unimos las nuevas configuraciones a las existentes.
		self::$data = array_merge_recursive(self::$data, $data);
	}
}
