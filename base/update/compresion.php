<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * compresion.php is part of Marifa.
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
 * @since		Versión 0.3
 * @filesource
 * @subpackage  Update
 * @package		Marifa/Base
 */

/**
 * Clase para comprimir, se basa en el uso de drivers.
 * Nos permite consultar información sobre versiones y/o plugins.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @subpackage Update
 * @package    Marifa/Base
 */
class Base_Update_Compresion {

	/**
	 * Listado de compresores conocidos.
	 * @var array
	 */
    protected static $compresors = array();

	/**
	 * Listado de compresores disponibles para su uso.
	 * @var array
	 */
    protected static $available = array();

	/**
	 * Listado de instancias de los compresores.
	 * @var array
	 */
    protected static $instances = array();

    /**
     * Constructor de la clase, cargamos los drivers.
     */
    private static function load()
    {
        if (count(self::$compresors) <= 0)
        {
            // Agregamos los distintos drivers.
            self::_addSupport('zip', 'Update_Compresion_Zip', 'ZipArchive');
            self::_addSupport('tar', 'Update_Compresion_Tar');
            self::_addSupport('gz', 'Update_Compresion_Gz', NULL, 'gzopen');
            self::_addSupport('bz2', 'Update_Compresion_Bz2', NULL, 'bzopen');
            //self::_addSupport('rar', 'Compresion_Rar', 'RarArchive');
            //self::_addSupport('lzf', 'Compresion_Lzf', NULL, 'lzf_compress');
            //self::_addSupport('phar', 'Compresion_Phar', 'Phar');

            // Comprobamos el soporte.
            self::_testSupport();
        }
    }

    /**
     * Patrón singleton para obtener los distintos compresores si están
     * disponibles.
	 * @param string $type Tipo de compresor a usar.
	 * @return Api_Compresion_Compresion
     */
    public static function getInstance($type)
    {
        // Cargamos la información.
        self::load();

        if ( ! isset(self::$instances[$type]))
        {
            if (isset(self::$available[$type]))
            {
                $c_n = self::$available[$type];
                self::$instances[$type] = new $c_n;
            }
            else
            {
                throw new Exception('Método de compresión no soportado');
            }
        }
        return self::$instances[$type];
    }

	/**
	 * Agregamos un compresor a la lista.
	 * @param string $name Nombre del compresor.
	 * @param string $compresor Clase usada para el compresor.
	 * @param array|string $testClass Clases que deben estar disponibles para su uso.
	 * @param array|string $testFunctions Funciones que deben estar disponibles para su uso.
	 */
    private static function _addSupport($name, $compresor, $testClass = NULL, $testFunctions = NULL)
    {
        $class = array();
        $class['compresor'] = $compresor;
        if ($testClass !== NULL)
        {
            if (is_array($testClass))
            {
                $class['support']['class'] = $testClass;
            }
            else
            {
                $class['support']['class'] = array($testClass);
            }
        }
        if ($testFunctions !== NULL)
        {
            if (is_array($testFunctions))
            {
                $class['support']['functions'] = $testFunctions;
            }
            else
            {
                $class['support']['functions'] = array($testFunctions);
            }
        }
        self::$compresors[$name] = $class;
    }

    /**
     * Comprobamos la disponibilidad de compresores.
     */
    private static function _testSupport()
    {
        self::$available = array();
        foreach (self::$compresors as $name => $info)
        {
            if ( ! class_exists($info['compresor']))
            {
                continue;
            }

            if (isset($info['support']['class']))
            {
				$c = TRUE;
                foreach ($info['support']['class'] as $class)
                {
                    if ( ! class_exists($class))
                    {
                        $c = FALSE;
						break;
                    }
                }
				if ( ! $c)
				{
					continue;
				}
            }
            if (isset($info['support']['functions']))
            {
				$c = TRUE;
                foreach ($info['support']['functions'] as $function)
                {
					if ( ! function_exists($function))
					{
	                    $c = FALSE;
						break;
	                }
	            }
				if ( ! $c)
				{
					continue;
				}
	        }
			self::$available[$name] = $info['compresor'];
        }
    }

    /**
     * Obtenemos la lista de compresores disponibles.
	 * @param bool $all Obtener los soportados o los disponibles.
	 * @return array
     */
    public static function get_list($all = FALSE)
    {
        self::load();
        return array_keys($all ? self::$compresors : self::$available);
    }
}
