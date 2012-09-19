<?php

/**
 * Description of dispatcher
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */
class Shell_Dispatcher {

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
				Shell_Cli::write_line(CLI::getColoredString("Parámetros incorrectos, intente llamando a la ayuda con --help", 'red'));
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
