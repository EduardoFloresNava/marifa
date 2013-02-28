<?php

/**
 * icono.php is part of Marifa.
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
 * @since		Versión 0.3
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para el manejo de íconos.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Icono {

	/**
	 * Listado de iconos.
	 * @var array
	 */
	protected $iconos;

	/**
	 * Constructor de la clase.
	 * @param string $directorio Directorio donde se encuentran los íconos.
	 */
	public function __construct($directorio)
	{
		// Asigno el listado de iconos.
		$this->iconos = self::cargar_iconos($directorio);
	}

	/**
	 * Cargamos iconos de un directorio.
	 * @param  string $directorio Directorio donde se encuentran los iconos.
	 * @return array Arreglo con los iconos cargados.
	 */
	protected static function cargar_iconos($directorio)
	{
		// Verifico exista el archivo.
		if ( ! file_exists($directorio.DS.'iconos.json'))
		{
			throw new Exception('El directorio no tiene una colección de íconos válida');
		}

		// Cargo el archivo json.
		$data = @file_get_contents($directorio.DS.'iconos.json');

		// Verifico carga.
		if ( ! $data)
		{
			throw new Exception('El identificador de la colección de íconos no es válido');
		}

		// Proceso json.
		$json = json_decode($data);

		// Verifico elemento.
		if ( ! is_object($json))
		{
			throw new Exception('El identificador de la colección de íconos no posee un formato válido');
		}

		// Devuelvo el listado.
		return (array) $json;
	}

	/**
	 * Obtengo un elemento a partir del directorio, la clave y el tamaño deseado.
	 * @param  string $directorio Directorio donde se encuentran los iconos.
	 * @param  string $clave      Clave del icono a obtener.
	 * @param  string|int $tamano Tamaño de la imagen a obtener.
	 * @return string             ruta de la imagen.
	 */
	public static function elemento($directorio, $clave, $tamano)
	{
		// Obtengo los iconos.
		$iconos = self::cargar_iconos($directorio);

		// Obtengo el elemento.
		return self::_obtener_elemento($iconos, $clave, $tamano);
	}

	/**
	 * Devuelvo el listado de elementos.
	 * @param string|int $tamaño Tamaño de los objeto que se necesitan. NULL para todos.
	 * @return array Arreglo con 'elemento' => 'path_requerido'
	 */
	public function listado_elementos($tamano)
	{
		// Variable donde poner lo procesado.
		$lst = array();

		// Proceso elementos.
		foreach ($this->iconos as $k => $v)
		{
			// Obtengo tamaño.
			if (isset($v->$tamano))
			{
				$lst[$k] = $v->$tamano;
			}
			else
			{
				$claves = array_keys( (array) $v);
				$lst[$k] = $v->$claves[0];
			}
		}

		// Devuelvo el listado de elementos.
		return $lst;
	}

	/**
	 * Obtenemos la imagen de una lista de iconos.
	 * @param  array $iconos      Listado de iconos.
	 * @param  string $elemento   Nombre del elemento a obtener.
	 * @param  string|int $tamaño Tamaño de la imagen a obtener.
	 * @return string             Ruta de la imagen.
	 */
	protected static function _obtener_elemento($iconos, $elemento, $tamano)
	{
		// Verifico existencia del elemento.
		if ( ! isset($iconos[$elemento]))
		{
			throw new Exception('El icono que buscas no se encuentra en la colección.');
		}

		// Verifico existencia del tamaño.
		if (isset($iconos[$elemento]->$tamano))
		{
			// Devuelvo el tamaño.
			return $iconos[$elemento]->$tamano;
		}
		else
		{
			// Obtengo disponibles.
			$claves = array_keys( (array) $iconos[$elemento]);

			// Devuelvo la primera.
			return $iconos[$elemento]->$claves[0];
		}
	}

	/**
	 * Obtenemos la imagen de un elemento.
	 * @param  string $elemento   Nombre del elemento a obtener.
	 * @param  string|int $tamano Tamaño de la imagen a obtener.
	 * @return string             Ruta de la imagen.
	 */
	public function obtener_elemento($elemento, $tamano)
	{
		return self::_obtener_elemento($this->iconos, $elemento, $tamano);
	}
}