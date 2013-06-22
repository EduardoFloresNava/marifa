<?php
/**
 * configuracion.php is part of Marifa.
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
 * Clase para el manejo de archivo configuración el sistema.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Configuracion implements ArrayAccess {

	/**
	 * Ruta del archivo de configuración al que hace referencia.
	 * @var string
	 */
	protected $file;

	/**
	 * Listado de propiedades del archivo de configuraciones.
	 * @var array
	 */
	protected $datos;

	/**
	 * Constructor de la clase.
	 * @param string $file Ruta al archivo de configuración.
	 * @param array $default Listado de propiedades por defecto. Se agregan a lo existente.
	 */
	public function __construct($file, $default = array())
	{
		// Asigno ruta del archivo.
		$this->file = $file;

		// Cargo las propiedades.
		$this->reload();

		// Uno los datos.
		foreach ($default as $k => $v)
		{
			$this->datos[$k] = $v;
		}
	}

	/**
	 * Obtengo el valor de una propiedad.
	 * @param mixed $key Clave de la propiedad a obtener.
	 * @return mixed Valor de la propiedad, NULL si no existe.
	 */
	public function __get($key)
	{
		return isset($this->datos[$key]) ? $this->datos[$key] : FALSE;
	}

	/**
	 * Actualizo o asigno el valor de una propiedad.
	 * @param mixed $key Clave de la propiedad a actualizar o asignar.
	 * @param mixed $value Valor de la propiedad a actualizar o asignar.
	 */
	public function __set($key, $value)
	{
		$this->datos[$key] = $value;
	}

	/**
	 * Elimino propiedad del listado del archivo de configuración.
	 * @param mixed $key Clave de la propiedad a eliminar.
	 */
	public function __unset($key)
	{
		if (isset($this->datos[$key]))
		{
			unset($this->datos[$key]);
		}
	}

	/**
	 * Verificamos la existencia de una clave de configuración.
	 * @param  mixed $key Clave de la propiedad a verificar la existencia.
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->datos[$key]);
	}

	/**
	 * Borramos todos los elementos del archivo de configuración.
	 */
	public function clean()
	{
		$this->datos = array();
	}

	/**
	 * Obtengo una instancia de un archivo de configuración.
	 * @param string $file Ruta del archivo de configuración.
	 * @param array $default Listado de propiedades por defecto. Se agregan a lo existente.
	 * @return Configuracion
	 */
	public static function factory($file, $default = array())
	{
		return new Configuracion($file, $default);
	}

	/**
	 * Guardamos los datos modificados en el archivo de configuración.
	 * @return bool
	 */
	public function save()
	{
		//TODO: Verificar cambios para no escribir y parsear para nada.
		return file_put_contents($this->file, '<?php defined(\'APP_BASE\') || die(\'No direct access allowed.\');'.PHP_EOL.'return '.$this->value_to_php($this->datos).';');
	}

	/**
	 * Obtenemos la representación PHP de una variable.
	 * @param mixed $value Variable a representar.
	 * @return string
	 */
	private function value_to_php($value)
	{
		if ($value === TRUE || $value === FALSE)
		{
			return $value ? 'TRUE' : 'FALSE';
		}
		elseif (is_int($value) || is_float($value))
		{
			return "$value";
		}
		elseif (is_string($value))
		{
			return "'".str_replace("'", "\\'", $value)."'";
		}
		elseif (is_array($value))
		{
			$rst = array();
			foreach ($value as $k => $v)
			{
				if (is_int($k))
				{
					$rst[] = $this->value_to_php($v);
				}
				else
				{
					$rst[] = "'$k' => ".$this->value_to_php($v);
				}
			}

			return 'array('.implode(', ', $rst).')';
		}
		return 'NULL';
	}

	/**
	 * Recargamos el archivo de configuración.
	 */
	public function reload()
	{
		if (file_exists($this->file))
		{
			$this->datos = configuracion_obtener($this->file);
		}
		else
		{
			$this->datos = array();
		}
	}

	/**
	 * Obtenemos un elemento. Si $key posee '.' es como acceder a un subelemento del arreglo.
	 * Es decir, "foo.bar" es como ['foo']['bar']
	 * @param mixed $key Clave a buscar. El uso de '.' indica acceso a un subelemento del arreglo.
	 * @param mixed $default Valor a devolver si no existe.
	 * @return mixed
	 */
	public function get($key, $default = NULL)
	{
		// Verifico existencia del elemento.
		if (isset($this->datos[$key]))
		{
			return $this->datos[$key];
		}

		// Verifico presencia de '.'.
		$keys = explode('.', $key);

		if (count($keys) > 1)
		{
			$aux = $this->datos;
			foreach ($keys as $v)
			{
				if (isset($aux[$v]))
				{
					$aux = $aux[$v];
				}
				else
				{
					return $default;
				}
			}
			return $aux;
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Seteamos el valor de un elemento. Soporta la utilización de '.' para subelementos.
	 * Si el elemento no existe (los padres) se crea un arreglo.
	 * @param mixed $key Clave a asignar el valor. El uso de '.' indica acceso a un subelemento del arreglo.
	 * @param mixed $value Valor a asignar al elemento.
	 */
	public function set($key, $value)
	{
		// Verifico existencia del elemento.
		if (isset($this->datos[$key]))
		{
			$this->datos[$key] = $value;
		}
		else
		{
			// Verifico presencia de '.'.
			$keys = explode('.', $key);

			if (count($keys) > 1)
			{
				$aux = &$this->datos;
				foreach ($keys as $v)
				{
					if ( ! isset($aux[$v]))
					{
						$aux[$v] = array();

					}
					$aux = &$aux[$v];
				}
				$aux = $value;
			}
			else
			{
				$this->datos[$key] = $value;
			}
		}
	}

	/**
	 * Verifico existencia de una propiedad de configuración.
	 * @param mixed $offset Clave de la propiedad de configuración a verificar.
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->datos[$offset]);
	}

	/**
	 * Obtengo el valor de una propiedad de configuración.
	 * @param mixed $offset Clave de la propiedad a obtener.
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->datos[$offset]) ? $this->datos[$offset] : null;
	}

	/**
	 * Actualizo o asigno el valor de una propiedad.
	 * @param mixed $offset Clave de la propiedad a actualizar o asignar.
	 * @param mixed $value Valor de la propiedad a actualizar o asignar.
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
		{
			$this->datos[] = $value;
		}
		else
		{
			$this->datos[$offset] = $value;
		}
	}

	/**
	 * Borramos una propiedad de configuración.
	 * @param mixed $offset Clave de la propiedad a borrar.
	 */
	public function offsetUnset($offset)
	{
		unset($this->datos[$offset]);
	}

	/**
	 * Obtenemos el arreglo de configuraciones.
	 * @return array
	 */
	public function as_array()
	{
		return $this->datos;
	}

}
