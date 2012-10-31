<?php
/**
 * ayuda.php is part of Marifa.
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
 * Controlador encargado del manejo de la ayuda de los comandos.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Controller_Ayuda extends Shell_Controller {

	/**
	 * Descripción del comando.
	 * @var string
	 */
	public $descripcion = "Ayuda de los comandos";

	/**
	 * Método donde inicia el procesamiento del controlador.
	 */
	public function start()
	{
		// Imprimimos la versión del software.
		Shell_Cli::write_line(sprintf('CLI de utilidades de Marifa. Versión: %s.', VERSION));

		// Verificamos si necesitamos la ayuda de un comando.
		if (isset($this->params[0]) && isset($this->params[1]) && $this->params[0] == 'help')
		{
			// Obtenemos la clase.
			$class = 'Shell_Controller_'.ucfirst(strtolower($this->params[1]));

			if ( ! class_exists($class))
			{
				Shell_Cli::write_line('Comando inválido');
				return;
			}

			$c = new $class($this->params);
			$c->help();

			return;
		}

		// Imprimimos resumen.
		Shell_Cli::write_line("Uso: php {$_SERVER['PHP_SELF']} <comando> [<argumentos>] [--help]");
		Shell_Cli::write_line('');
		Shell_Cli::write_line('Listado de comandos:');

		//Cargamos la lista de controladores.
		$c_list = scandir(dirname(__FILE__));

		//Inicializamos la lista de comandos.
		$command_list = array();

		foreach($c_list as $cl)
		{
			if ($cl == '.' || $cl == '..' || $cl == 'ayuda.php' || ! is_file(dirname(__FILE__).'/'.$cl))
			{
				continue;
			}

			// Obtenemos la información del path.
			$pi = pathinfo(dirname(__FILE__).'/'.$cl);

			// Verificamos exista filename (PHP > 5.2.0)
			if ( ! isset($pi['filename']))
			{
				$pi['filename'] = substr($pi['basename'], 0, strlen($pi['extension']) + 1);
			}

			// Generamos el nombre de la clase.
			$class_name = 'Shell_Controller_'.ucfirst($pi['filename']);

			// Verificamos que exista la clase.
			if ( ! class_exists($class_name))
			{
				continue;
			}

			// Hacemos reflection de la clase.
			$r_c = new ReflectionClass($class_name);

			// Obtenemos el nombre de la clase padre.
			$r_p = $r_c->getParentClass();

			// Verificamos el padre sea Controller.
			if ( ! is_object($r_p) || $r_p->getName() !== 'Shell_Controller')
			{
				continue;
			}
			unset($r_p);

			$obj = new stdClass;

			$d_p = $r_c->getDefaultProperties();

			// Obtenemos las propiedades importantes.
			if (isset($d_p['descripcion']))
			{
				// Dejamos solo el primer párrafo.
				$obj->descripcion = $d_p['descripcion'];
			}
			else
			{
				$obj->descripcion = NULL;
			}

			// Seteamos el nombre de llamada.
			$obj->name = strtolower($pi['filename']);

			// Agregamos el comando a la lista de comandos.
			$command_list[] = $obj;
		}

		// Imprimimos la lista de comandos.
		foreach($command_list as $c)
		{
			Shell_Cli::write_line(sprintf('  %-15.13s%s', $c->name, $c->descripcion));
		}

		// Imprimimos pie de página.
		Shell_Cli::write_line("\nVea php {$_SERVER['PHP_SELF']} help <command> o php {$_SERVER['PHP_SELF']} <command> --help para más información sobre el comando.");
	}

}
