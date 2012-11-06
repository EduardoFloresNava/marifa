<?php
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
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @subpackage  Update
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para comprimir, se basa en el uso de drivers.
 * Nos permite consultar información sobre versiones y/o plugins.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update
 * @package    Marifa\Base
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
            self::_add_support('zip', 'Update_Compresion_Zip', 'ZipArchive');
            self::_add_support('tar', 'Update_Compresion_Tar');
            self::_add_support('gz', 'Update_Compresion_Gz', NULL, 'gzopen');
            self::_add_support('bz2', 'Update_Compresion_Bz2', NULL, 'bzopen');
            // self::_add_support('rar', 'Compresion_Rar', 'RarArchive');
            // self::_add_support('lzf', 'Compresion_Lzf', NULL, 'lzf_compress');
            // self::_add_support('phar', 'Compresion_Phar', 'Phar');

            // Comprobamos el soporte.
            self::_test_support();
        }
    }

    /**
     * Patrón singleton para obtener los distintos compresores si están
     * disponibles.
	 * @param string $type Tipo de compresor a usar.
	 * @return Update_Compresion_Compresion
     */
    public static function get_instance($type)
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
	 * @param array|string $test_class Clases que deben estar disponibles para su uso.
	 * @param array|string $test_functions Funciones que deben estar disponibles para su uso.
	 */
    private static function _add_support($name, $compresor, $test_class = NULL, $test_functions = NULL)
    {
        $class = array();
        $class['compresor'] = $compresor;
        if ($test_class !== NULL)
        {
            if (is_array($test_class))
            {
                $class['support']['class'] = $test_class;
            }
            else
            {
                $class['support']['class'] = array($test_class);
            }
        }
        if ($test_functions !== NULL)
        {
            if (is_array($test_functions))
            {
                $class['support']['functions'] = $test_functions;
            }
            else
            {
                $class['support']['functions'] = array($test_functions);
            }
        }
        self::$compresors[$name] = $class;
    }

    /**
     * Comprobamos la disponibilidad de compresores.
     */
    private static function _test_support()
    {
		// Cargo la cache.
		$a = Cache::get_instance()->get('uploaders_cache');
		if (is_array($a))
		{
			self::$available = $a;
			return;
		}

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

		Cache::get_instance()->save('uploaders_cache', self::$available);
    }

    /**
     * Obtenemos la lista de compresores disponibles.
	 * @param bool $all Obtener los soportados o los disponibles.
	 * @return array
     */
    public static function get_list($all = FALSE)
    {
        self::load();
        return array_keys($all ? (self::$compresors) : (self::$available));
    }
}
