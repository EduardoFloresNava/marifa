<?php

/**
 * Controlador base.
 * Realiza algunas acciones automáticamente como generar la ayuda.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */
class Shell_Controller {

	public $descripcion = "";
	public $lines = array();
	public $help = "";
	protected $class = __CLASS__;

	protected $params;

	/**
	 * Constructor de la clase. Seteamos los parametros a la clase.
	 * @param array $params Parámetros de la petición CLI.
	 */
	public function __construct($params = array())
	{
		$this->params = $params;
	}

	/**
	 * Método llamado automáticamente para generar la ayuda.
	 */
	public function help()
	{
		// Obtenemos el nombre de comando.
		$command = explode('_', $this->class);
		$command = array_pop($command);
		$command = strtolower($command);
		$script = $_SERVER['PHP_SELF'];

		Shell_Cli::write_line($this->descripcion);
		if (is_array($this->lines) && count($this->lines) > 0)
		{
			Shell_Cli::write_line('Usage: ');
			foreach($this->lines as $line)
			{
				Shell_Cli::write_line("  php $script $command $line");
			}
		}
		else
		{
			Shell_Cli::write_line("Usage: php $script $command");
		}
		Shell_Cli::write_line('');
		Shell_Cli::write_line($this->help);
		exit;
	}

	/**
	 * Método para ejecutar acciones genéricas como enviar la ayuda.
	 */
	public function start()
	{
		// En caso de ser una consulta de ayuda, pasamos el control al HELP.
		if (isset($this->params['help']) && $this->params['help'])
		{
			return $this->help();
		}
	}

}
