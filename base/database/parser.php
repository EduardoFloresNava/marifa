<?php
/**
 * parser.php is part of Marifa.
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
 * @subpackage  Database
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para la creación de consultas SQL seguras.
 *
 * Implementa el uso de variables que se inyectan en las consultas una vez
 * validadas para evitar introducir caracteres extraños que produzcan inyecciones.
 *
 * Ejemplo de uso del parseador.
 * <code>
 * // El constructor de DB_Query tiene 2 parametros:
 * //  El primero es la consulta y tiene 2 formas de ponerle variables:
 * //      ? son las variables sin nombre.
 * //      :name son variables con nombre, donde "name" es el nombre.
 * //  El segundo es opcional y son variables sin nombre.
 *
 * // El constructor de consultas tiene un parseador que tiene como objetivo
 * // que no se puedan realizar inyecciones de modo que valida todos los datos
 * // aunque para mayor flexibilidad se le puede indicar que algunas cosas las
 * // queremos sin modificaciones. Además puede manejar una serie de datos de
 * // entrada variada.
 *
 * //FUNCION DEL PARSEADOR DE DATOS, EXTRAIDA PARA MOSTRAR SU USO.
 * function parse_input($object){
 *     if(is_object($object)){ //Es un objeto?
 *         return (string)$object;
 *     }elseif(is_numeric($object)){ //Es un número?
 *         if(is_int($object)) return $object;
 *         if (((int) $object) == $object) return (int) $object;
 *         return (float)$object;
 *     }elseif($object === NULL){ //Es NULL?
 *         return "NULL";
 *     }elseif(is_array($object)){ //Es un arreglo?
 *         $object = array_map('parse_input', $object);
 *         return implode(', ', $object);
 *     }else{ //Suponemos una cadena
 *         //NO USAMOS MYSQL_REAL_ESCAPE_STRING PORQUE ES UN EJEMPLO Y NO CONECTAMOS
 *         //A LA BASE DE DATOS, SINO SE APLICARIA.
 *         //return "\"".mysql_real_escape_string($object)."\"";
 *         return "\"$object\"";
 *     }
 * }
 *
 * //EJEMPLOS DEL PARSEADOR DE DATOS
 *
 * //Una cadena se le aplica la proteccion contra inyecciones y le la pone entre comillas.
 * var_dump(parse_input("cadena"));
 *
 * //Un numero se lo pone como esta, si es una cadena que puede ser numero se la convierte.
 * var_dump(parse_input(5));
 * var_dump(parse_input(5.5));
 * var_dump(parse_input("5"));
 * var_dump(parse_input("5.5"));
 *
 * //Si es null pasa a ser una cadena "NULL" para que aparezca en las consultas.
 * var_dump(parse_input(NULL));
 * var_dump(parse_input("NULL")); //<= ES NO ES NULL, se evalua como una cadena
 *
 * //Un arreglo lo transforma en una lista, procesando cada uno de sus elementos por separado.
 * var_dump(parse_input(array("cadena", 5, 5.5, NULL)));
 *
 * //Tambien podemos tener arreglos de arreglos
 * var_dump(parse_input(array("cadena", 5, NULL, array("cadena2", 10))));
 * //TENER EN CUENTA QUE NORMALMENTE NO ES MUY UTIL TENER UN ARREGLO DE ARREGLO PERO SE PERMITE.
 * //ADEMAS SE APLICA RECURSIVIDAD, TENER CUIDADO CON EL STACK Y EL LIMITE QUE TENGA.
 *
 *
 * //Por ultimo, podemos pasar objetos, los cuales solo funcionan si tienen
 * //el metodo __toString(). En caso de no tenerlo dará error.
 * //El uso de pasar objetos es para insertar SUBCONSULTAS, FUNCIONES SQL
 * //y algunas cadenas "especiales" como nombres de campos o tablas que no deben
 * //ser modificadas.
 * //Los objetos que se van a pasar son del tipo DB_Query. Su uso inicial se sencillo
 * //deja tal cual el contenido, es como pasar un string sin que lo modifique.
 *
 *
 * $q = new DB_Query("unix_timestamp()");
 * var_dump(parse_input($q));
 * //Como vemos no agrega comillas.
 *
 * $q = new DB_Query("SELECT * FROM usuario LIMIT 1");
 * var_dump(parse_input($q));
 * //Aca ponemos una consulta, para evitar que la trate com un string, ya que sino agregaria comillas
 *
 *
 * //AHORA SIGUEN LOS EJEMPLOS DE CONSULTAS, RECUERDEN QUE CUANDO SE PASA UNA VARIABLE
 * //SIGUE LAS REGLAS DISPUESTAS ANTES
 * //EJEMPLOS DE CONSULTAS
 *
 * //CONSULTA SIMPLE.
 * $query = new DB_Query("SELECT * FROM usuario WHERE user_id = ?", 1);
 * var_dump($query->build());
 *
 * //VARIOS PARAMETROS.
 * $query = new DB_Query("SELECT * FROM usuario WHERE username = ? and password = ?", array("areslepra", "pass"));
 * var_dump($query->build());
 *
 * //EQUIVALENTE ANTERIOR.
 * $query = new DB_Query("SELECT * FROM usuario WHERE username = ? and password = ?");
 * $query->bind(0, "areslepra"); //EL PRIMER PARAMETRO ES DONDE SE ASIGNA Y EL SEGUNDO EL CONTENIDO
 * $query->bind(1, "pass");
 * var_dump($query->build());
 *
 * //LA ANTERIOR PERO CON PARAMETROS CON NOMBRE
 * $query = new DB_Query("SELECT * FROM usuario WHERE username = :user and password = :password");
 * $query->bind("user", "areslepra"); //EL PRIMER PARAMETRO ES EL NOMBRE Y EL SEGUNDO EL CONTENIDO
 * $query->bind("password", "pass");
 * //OBSERVAR QUE SI ES UN STRING ES UN PARAMETRO CON NOMBRE, SI ES UN NUMERO ES UNO SIN NOMBRE.
 * var_dump($query->build());
 *
 * //AHORA MEZCLAMOS UN POCO
 * $query = new DB_Query("SELECT * FROM usuario WHERE username = :user LIMIT ?,?", array(1,5));
 * $query->bind("user", "areslepra");
 * var_dump($query->build());
 *
 * //AHORA MAS COMPLICADO, NOMBRE DE TABLA VARIABLE
 * $query = new DB_Query("SELECT * FROM :db WHERE username = :user");
 * $query->bind("db", "usuario", FALSE);
 * $query->bind("user", "areslepra");
 * //CON EL 3RE PARAMETRO LE DECIMOS QUE PONGA EL VALOR SIN MODIFICARLO, UTIL PARA SUBCONSULTAS Y NOMBRE DE CAMPOS.
 * var_dump($query->build());
 *
 * //UNA SUBCONSULTA
 * $sc = new DB_Query("SELECT user_id FROM comentarios WHERE post_id = ?", 10);
 * $query = new DB_Query("SELECT nombre FROM usuario WHERE id IN (:query)");
 * $query->bind("query", $sc);
 * //CON EL 3ER PARAMETROS EVITAMOS QUE MODIFIQUE Y ADEMAS PARSEA LA CONSULTA PORQUE ES UN OBJETO.
 * var_dump($query->build());
 *
 * //OTRA FORMA DE UNA SUBCONSULTA
 * $sc = new DB_Query("SELECT user_id FROM comentarios WHERE post_id = ?", 10);
 * $query = new DB_Query("SELECT nombre FROM usuario WHERE id IN (:query)");
 * $query->bind("query", $sc->build(), FALSE);
 * //CON EL 3ER PARAMETROS EVITAMOS QUE MODIFIQUE Y ADEMAS PARSEA LA CONSULTA PORQUE ES UN OBJETO.
 * var_dump($query->build());
 *
 * //UN EJEMPLO CON IN Y UN ARREGLO
 * $query = new DB_Query("SELECT * FROM usuario WHERE id IN (?)", array(array(1, 2, 5, 7)));
 * var_dump($query->build());
 *
 * //O LO QUE ES LO MISMO
 * $query = new DB_Query("SELECT * FROM usuario WHERE id IN (?)");
 * $query->bind(0, array(1, 2, 5, 7));
 * //COMO PASAMOS UN ARREGLO, LO TRANSFORMA AUTOMATICAMENTE EN UNA LISTA.
 * var_dump($query->build());
 *
 * //COMO EL ANTERIOR PERO CON LISTAS "MIXTAS"
 * $query = new DB_Query("SELECT * FROM usuario WHERE id IN (?)");
 * $query->bind(0, array(1, 2, "5", new DB_Query("( SELECT id FROM usuario WHERE nombre = ? LIMIT 1 )", "areslepra"), new DB_Query("RAND()")));
 * //TRANSFORMA RESPECTANDO "strings", numeros y literales/subconsultas
 * var_dump($query->build());
 *
 * //POR ULTIMO UN INSERT "EXTRAÑO"
 * $data = array("areslepra", 20, new DB_Query("unix_timestamp()"));
 * $query = new DB_Query("INSERT INTO usuario (nombre, edad, fecha_registro) VALUES (?)", array($data));
 * var_dump($query->build());
 * </code>
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Database
 */
class Base_Database_Parser {

	/**
	 * Consulta SQL.
	 * @var string
	 */
	protected $query;

	/**
	 * Parametros sin nombre.
	 * @var array
	 */
	protected $params;

	/**
	 * Arreglo asociativo con los parametros con nombre.
	 * @var array
	 */
	protected $named_params;

	/**
	 * Constructor de la clase.
	 * @param string $query Consulta SQL a parsear.
	 * @param mixed $params Arreglo de parametros sin nombre, es opcional.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function __construct($query, $params = NULL)
	{
		$this->query = $query;

		$this->named_params = array();

		if ($params !== NULL)
		{
			if ( ! is_array($params))
			{
				$this->params = array($params);
			}
			else
			{
				$this->params = $params;
			}
		}
		else
		{
			$this->params = array();
		}
	}

	/**
	 * Asigna una variable para ser usada en una consulta.
	 * @param string|int $name Nombre de la variable,
	 * si es un string se toma para variables con nombre, si es un numero para
	 * sin nombre
	 * @param mixed $value Objeto para poner un valor.
	 * @param boolean $parse Si se debe aplicar el parseo o se usa literal.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function bind($name, $value, $parse = TRUE)
	{
		if ( ! $parse)
		{
			$c = loader_prefix_class(__CLASS__);
			$value = new $c($value);
		}

		if (is_int($name))
		{
			$this->params[$name] = $value;
		}
		else
		{
			$this->named_params[$name] = $value;
		}
	}

	/**
	 * Función que se encarga de determinar el tipo de datos para ver si debe
	 * aplicar la prevención de inyecciones SQL, si debe usar comillas o si es
	 * un literal ( funcion SQL ).
	 * @param mixed $object Objeto a analizar.
	 * @return string Cadena segura.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	protected function parse_input($object)
	{
		if (is_object($object))
		{
			// Es un objeto, lo transformamos a una cadena.
			return ( string ) $object;
		}
		elseif (is_numeric($object))
		{
			// Es un número, lo convertimos.

			// Verificamos si es un entero.
			if (is_int($object))
			{
				return $object;
			}

			// Verificamos si puede tratarse como un entero.
			if (( (int) $object) == $object)
			{
				return (int) $object;
			}

			// Lo tratamos como un real.
			return ( float ) $object;
		}
		elseif ($object === NULL)
		{
			// Es nulo, damos una representación válida.
			return 'NULL';
		}
		elseif (is_array($object))
		{
			// Es un arreglo, lo procesamos.
			$object = array_map(array($this, __METHOD__), $object);
			return implode(', ', $object);
		}
		elseif ($object === TRUE || $object === FALSE)
		{
			return $object ? 1 : 0;
		}
		else
		{
			// Suponemos una cadena, la limpiamos.
			return "\"".Database::get_instance()->escape_string($object)."\"";
		}
	}

	/**
	 * Parsea argumentos con nombre, es decir, reemplaza los :name por el
	 * elemento correspondiente del arreglo de claves=>valor.
	 * @param string $q Consulta SQL donde reemplazar las variables.
	 * @param array $params Arreglo de clave valor con las variables.
	 * @return string Consulta parseada con los argumentos con nombre.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	protected function parse_named_vars($q, $params)
	{
		if ( ! is_array($params))
		{
			throw new InvalidArgumentException("El parametro debe ser un arreglo válido");
			return $q;
		}

		foreach ($params as $key => $value)
		{
			$q = str_replace(":$key", $this->parse_input($value), $q);
		}
		return $q;
	}

	/**
	 * Parsea argumentos sin nombre, es decir, reemplaza los ? por los elementos
	 * del arreglo manteniendo el orden.
	 * @param string $q Consulta donde reemplazar los datos.
	 * @param array $params Arreglo con los parametros.
	 * @return strign Consulta con los parametros sin nombre reemplazados.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	protected function parse_vars($q, $params)
	{
		// Validamos que los parametros sea un arreglo.
		if ( ! is_array($params))
		{
			throw new InvalidArgumentException("El parametro debe ser un arreglo válido");
			return $q;
		}
		// Validamos que tengamos igual numero de parametros que de los necesarios.
		$aux = array(); // Garantiza compatibilidad en algunos casos.
		if (count($params) != preg_match_all('/\?/', $q, $aux))
		{
			throw new InvalidArgumentException("No coinciden la cantidad de parametros necesarios con los provistos");
			return $q;
		}
		foreach ($params as $param)
		{
			// Proceso la entrada.
			$param = $this->parse_input($param);

			// Valido $n y \\n
			$param = preg_replace('/(\$[0-9]+)/', '\\\\$0', $param);
			$param = preg_replace('/(\\\\[0-9]+)/', '\\\\\\\\$0', $param);

			$q = preg_replace('/\?/', $param, $q, 1);
		}
		return $q;
	}

	/**
	 * Parsea la consulta SQL reemplazando todos los parametros.
	 * @return string Consulta SQL parseada.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function build()
	{
		$query = $this->query;
		$query = $this->parse_vars($query, $this->params);
		$query = $this->parse_named_vars($query, $this->named_params);
		return $query;
	}

	/**
	 * Método mágico para convertir el objeto de consulta en un string
	 * @return string Consulta SQL parseada.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function __toString()
	{
		return (string) $this->build();
	}

}
