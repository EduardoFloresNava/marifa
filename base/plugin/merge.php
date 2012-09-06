<?php
/**
 * merge.php is part of Marifa.
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
 * @subpackage  Plugin
 */
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Clase encargada de unir las clases de los plugins con las del nucleo.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Plugin
 */
class Base_Plugin_Merge {

	/**
	 * Nombre de la clase origen.
	 * @var string
	 */
	private $from;

	/**
	 * Nombre de la clase destino.
	 * @var string
	 */
	private $to;

	/**
	 * Creamos la instancia de union de clases.
	 * @param string $from Nombre de la clase de donde se sacan los valores a agregar.
	 * @param string $to Nombre de la clase donde se van a anexar los otros datos.
	 */
	public function __construct($from, $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * Obtenemos la cabecera del archivo a generar.
	 * @return string
	 */
	private function get_header()
	{
		return "<?php defined('APP_BASE') or die('No direct access allowed.');\n\n";
	}

	/**
	 * Revertimos la unión.
	 */
	public function revert()
	{
		// Cargamos objetos de las clases.
		$ref_to = new ReflectionClass($this->to);

		// Obtenemos los path's.
		$file_to = $ref_to->getFileName();

		// Cargamos los archivos.
		//TODO: puede gastar mucha memoria dependiendo del tamaño de los archivos
		//puede ser una buena idea usar otro método.
		$data_to = file($file_to);

		// Iniciamos la cadena de salida.
		$resultado = $this->get_header();

		// Agregamos la documentación de la clase.
		$resultado .= $ref_to->getDocComment()."\n";

		// Agregamos cabecera.
		$sl = $ref_to->getStartLine() - 1;
		while(TRUE)
		{
			$aux = $data_to[$sl];

			$pos = strpos($aux, '{');

			if ($pos === FALSE)
			{
				$resultado .= $aux;
				$sl++;
			}
			else
			{
				$resultado .= substr($aux, 0, $pos + 1)."\n\n";
				break;
			}
		}

		// Obtenemos las constantes de que han de quedar.
		$const_list = array_diff($this->get_class_constants($this->to), $this->get_class_constants($this->from));

		// Agregamos las constantes
		foreach($const_list as $p)
		{
			$resultado .= "\t".$this->get_constant($ref_to, $p)."\n";
		}

		unset($const_list);

		// Obtenemos el listado de propiedades que han de quedar.
		$prop_list = array_diff($this->get_class_properties($this->to), $this->get_class_properties($this->from));

		// Agregamos las propiedades
		foreach($prop_list as $p)
		{
			$resultado .= "\t".$this->get_property($ref_to, $p)."\n";
		}

		unset($prop_list);

		// Obtenemos el listado de métodos que han de quedar.
		$meth_list = array_diff($this->get_class_methods($this->to), $this->get_class_methods($this->from));

		// Agregamos las métodos
		foreach($meth_list as $p)
		{
			$resultado .= "\t".$this->get_method($ref_to, $p, $data_to)."\n\n";
		}

		unset($meth_list);

		// Agregamos pie.
		$resultado = trim($resultado);
		$resultado .= "\n}\n";

		// Guardamos el resultado.
		file_put_contents($file_to, $resultado);
	}

	/**
	 * Realizamos la unión.
	 */
	public function merge()
	{
		// Cargamos objetos de las clases.
		$ref_from = new ReflectionClass($this->from);
		$ref_to = new ReflectionClass($this->to);

		// Obtenemos los path's.
		$file_from = $ref_from->getFileName();
		$file_to = $ref_to->getFileName();

		// Cargamos los archivos.
		//TODO: puede gastar mucha memoria dependiendo del tamaño de los archivos
		//puede ser una buena idea usar otro método.
		$data_from = file($file_from);
		$data_to = file($file_to);

		// Iniciamos la cadena de salida.
		$resultado = $this->get_header();

		// Agregamos la documentación de la clase.
		$resultado .= $ref_to->getDocComment()."\n";

		// Agregamos cabecera.
		$sl = $ref_to->getStartLine() - 1;
		while(TRUE)
		{
			$aux = $data_to[$sl];

			$pos = strpos($aux, '{');

			if ($pos === FALSE)
			{
				$resultado .= $aux;
				$sl++;
			}
			else
			{
				$resultado .= substr($aux, 0, $pos + 1)."\n\n";
				break;
			}
		}

		// Agregamos las constantes de from.
		foreach($this->get_class_constants($this->from) as $p)
		{
			$resultado .= "\t".$this->get_constant($ref_from, $p)."\n";
		}

		// Agregamos las constantes de to.
		foreach($this->get_class_constants($this->to) as $p)
		{
			$resultado .= "\t".$this->get_constant($ref_to, $p)."\n";
		}

		// Agregamos las propiedades de from.
		foreach($this->get_class_properties($this->from) as $p)
		{
			$resultado .= "\t".$this->get_property($ref_from, $p)."\n";
		}

		// Agregamos las propiedades de to.
		foreach($this->get_class_properties($this->to) as $p)
		{
			$resultado .= "\t".$this->get_property($ref_to, $p)."\n";
		}

		// Agregamos los métodos de from.
		foreach($this->get_class_methods($this->from) as $p)
		{
			$resultado .= "\t".$this->get_method($ref_from, $p, $data_from)."\n\n";
		}

		// Agregamos los métodos de to.
		foreach($this->get_class_methods($this->to) as $p)
		{
			$resultado .= "\t".$this->get_method($ref_to, $p, $data_to)."\n\n";
		}

		// Agregamos pie.
		$resultado = trim($resultado);
		$resultado .= "\n}\n";

		// Guardamos el resultado.
		file_put_contents($file_to, $resultado);
	}

	/**
	 * Obtenemos la representación PHP de una constante.
	 * @param ReflectionClass $ref Objeto de reflección de la clase.
	 * @param string $prop Nombre de la constante.
	 * @return string
	 */
	private function get_constant(&$ref, $const)
	{
		return 'const '.$const.' = '.$this->value_to_php($ref->getConstant($const)).";\n";
	}

	/**
	 * Obtenemos la representación PHP de una propiedad.
	 * @param ReflectionClass $ref Objeto de reflección de la clase.
	 * @param string $prop Nombre del método.
	 * @return string
	 */
	private function get_property(&$ref, $prop)
	{
		// Obtenemos la refleccion de la propiedad.
		$ref_prop = $ref->getProperty($prop);

		// Obtenemos el bloque de documentación.
		$rst = $ref_prop->getDocComment();

		$def = '';
		if ($ref_prop->isPrivate())
		{
			$def .= 'private ';
		}
		elseif ($ref_prop->isProtected())
		{
			$def .= 'protected ';
		}
		elseif ($ref_prop->isPublic())
		{
			$def .= 'public ';
		}

		if ($ref_prop->isStatic())
		{
			$def .= 'static ';
		}

		$def .= '$'.$ref_prop->name;

		if ($ref_prop->isDefault())
		{
			// Obtenemos la lista.
			$aux = $ref->getDefaultProperties();
			$default = $aux[$ref_prop->name];
			unset($aux);

			if ($default !== NULL)
			{
				//FIXME: las constantes son tomadas con su valor original.
				$def .= ' = '.$this->value_to_php($default);
			}
		}

		return "$rst\n\t$def;\n";
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
			return "'$value'";
		}
		elseif (is_array($value))
		{
			$rst = array();
			foreach($value as $k => $v)
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
	 * Obtenemos la representación PHP de un método.
	 * @param ReflectionClass $ref Objeto de reflección de la clase.
	 * @param string $name Nombre del método.
	 * @param array $file Arreglo con las lineas del archivo de origen.
	 * @return string
	 */
	private function get_method(&$ref, $name, &$file)
	{
		// Obtenemos la refleccion del método.
		$ref_meth = $ref->getMethod($name);

		// Obtenemos el bloque de documentación.
		$rst = $ref_meth->getDocComment()."\n";

		// Obtenemos la primera y última linea del método.
		$start = $ref_meth->getStartLine();
		$end = $ref_meth->getEndLine();

		// Agregamos el conjunto de lineas.
		for ($i = $start - 1; $i < $end; $i++)
		{
			$rst .= $file[$i];
		}

		return trim($rst);
	}

	/**
	 * Verificamos si son compatibles para insertarse.
	 * @return bool
	 */
	public function is_compatible()
	{
		// Obtenemos los métodos de ambas clases.
		$m_f = $this->get_class_methods($this->from);
		$m_t = $this->get_class_methods($this->to);

		// Verificamos si hay repetidos.
		if (count(array_intersect($m_f, $m_t)) > 0)
		{
			return FALSE;
		}

		// Liberamos memoria.
		unset($m_f, $m_t);

		// Obtenemos las propiedades de ambas clases.
		$p_f = $this->get_class_properties($this->from);
		$p_t = $this->get_class_properties($this->to);

		// Verificamos si hay repetidos.
		if (count(array_intersect($p_f, $p_t)) > 0)
		{
			return FALSE;
		}

		// Liberamos memoria.
		unset($p_f, $p_t);

		// Obtenemos las constantes de ambas clases.
		$c_f = $this->get_class_constants($this->from);
		$c_t = $this->get_class_constants($this->to);

		// Verificamos si hay repetidos.
		//TODO: verificamos los valores, para constantes repetidas.
		if (count(array_intersect($c_f, $c_t)) > 0)
		{
			return FALSE;
		}

		// Liberamos memoria.
		unset($c_f, $c_t);

		//TODO: validar los parámetros y tipos de sobreescritura.

		return TRUE;
	}

	/**
	 * Obtenemos el listado de métodos de una clase.
	 * @param string $class Nombre de la clase.
	 * @param bool $extends También los métodos de las clases padre.
	 * @return array
	 */
	private function get_class_methods($class, $extends = FALSE)
	{
		$reflection = new ReflectionClass($class);

		$rst = array();
		$lst = $reflection->getMethods();
		foreach($lst as $m)
		{
			// Verificamos si para todas las clases.
			if ($extends)
			{
				$rst[] = $m->name;
			}
			else
			{
				if ($m->class == $class)
				{
					$rst[] = $m->name;
				}
			}
		}
		return $rst;
	}

	/**
	 * Obtenemos el listado de propiedades de la clase.
	 * @param string $class Nombre de la clase.
	 * @param bool $extends Si se devuelven las padre también.
	 * @return array
	 */
	private function get_class_properties($class, $extends = FALSE)
	{
		$reflection = new ReflectionClass($class);

		$rst = array();
		$lst = $reflection->getProperties();
		foreach($lst as $m)
		{
			// Verificamos si para todas las clases.
			if ($extends)
			{
				$rst[] = $m->name;
			}
			else
			{
				if ($m->class == $class)
				{
					$rst[] = $m->name;
				}
			}
		}
		return $rst;
	}

	/**
	 * Obtenemos la lista de constantes de la clase.
	 * @param string $class Nombre de la clase.
	 * @return array
	 */
	private function get_class_constants($class)
	{
		$reflection = new ReflectionClass($class);
		$lst = $reflection->getConstants();
		return array_keys($lst);
	}

}
