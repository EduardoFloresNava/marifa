<?php
/**
 * dispatcher.php is part of Marifa.
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
 * @package		Marifa\Shell
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para despachar las peticiones.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Dispatcher {

	/**
	 * Despachamos una petición.
	 */
	public static function dispatch()
	{
		// Obtenemos los parametros.
		$params = Shell_Cli::parseArgs($_SERVER['argv']);

		// Obtenemos el controlador.
		if ( ! isset($params[0]) || $params[0] == 'help')
		{
			// Usamos de ayuda.
			$controller = 'Shell_Controller_Ayuda';
		}
		else
		{
			// Armamos el nombre.
			$c_name = ucfirst(strtolower($params[0]));
			$c_name = preg_replace('/\s/', '_', $c_name);

			if ( ! class_exists('Shell_Controller_'.$c_name))
			{
				Shell_Cli::write_line(Shell_Cli::getColoredString("Parámetros incorrectos, intente llamando a la ayuda con --help", 'red'));
				exit;
			}
			else
			{
				$controller = 'Shell_Controller_'.$c_name;
			}
		}

		$c = new $controller($params);
		$c->start();
	}

}
