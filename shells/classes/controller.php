<?php
/**
 * controller.php is part of Marifa.
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
 * Controlador base.
 * Realiza algunas acciones automáticamente como generar la ayuda.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Controller {

	/**
	 * Descripción del controlador.
	 * @var string
	 */
	public $descripcion = "";

	/**
	 * Listado de lineas de llamadas.
	 * @var array
	 */
	public $lines = array();

	/**
	 * Texto de ayuda.
	 * @var string
	 */
	public $help = "";

	/**
	 * Clase para solucionar BUG llamadas estaticas para PHP<5.3
	 * @var string
	 */
	protected $class = __CLASS__;

	/**
	 * Listado de parámetros.
	 * @var array
	 */
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
		$command = strtolower(array_pop(explode('_', $this->class)));
		$script = $_SERVER['PHP_SELF'];

		Shell_Cli::write_line($this->descripcion);
		if (is_array($this->lines) && count($this->lines) > 0)
		{
			Shell_Cli::write_line('Usage: ');
			foreach ($this->lines as $line)
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
