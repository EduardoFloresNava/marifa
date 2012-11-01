<?php
/**
 * migration.php is part of Marifa.
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
 * Controlador encargado de realizar las migraciones.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Controller_Migration extends Shell_Controller {

	/**
	 * Descripcion corta del comando.
	 * @var string
	 */
	public $descripcion = "Migraciones de la base de datos.";

	/**
	 * Listado de variantes del comando.
	 * @var array
	 */
	public $lines = array(
		'status',
		'apply');

	/**
	 * Descripción detallada del comando.
	 * @var string
	 */
	public $help = "Realizamos acciones relacionadas con las migraciones de la base de datos.
    status Verificamos el estado de las migraciones.
    apply  Aplicamos todas las migraciones.";

	/**
	 * Nombre de la clase para solventar problemas de la versión de PHP.
	 * @var string
	 */
	protected $class = __CLASS__;

	/**
	 * Función de inicio del controlador.
	 */
	public function start()
	{
		parent::start();

		$action = isset($this->params[1]) ? $this->params[1] : NULL;

		// Seleccionamos la acción a tomar.
		switch ($action)
		{
			case 'apply':
				// Verificamos conección y verificamos exista la tabla.
				self::check_db_status();

				// Obtenemos las versiones instaladas.
				try {
					$lista = Database::get_instance()->query('SELECT numero, fecha FROM migraciones')->get_pairs();
				}
				catch (Database_Exception $e)
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('Error ', 'red').$e->getCode().': '.$e->getMessage());
				}

				// Obtenemos las faltantes.
				$migraciones = Shell_Migraciones::migraciones();
				$faltantes = array_diff($migraciones, array_keys($lista));

				// Aplicamos las faltantes.
				foreach ($faltantes as $f)
				{
					try {
						Shell_Cli::write('Aplicando migracion '.$f.'... ');
						if (Shell_Migraciones::migrar($f))
						{
							Shell_Cli::write_line(Shell_Cli::get_colored_string('OK', 'green'));
						}
					}
					catch (Exception $e)
					{
						Shell_Cli::write_line(Shell_Cli::get_colored_string('ERROR ', 'red').$e->getCode().': '.$e->getMessage());
						die();
					}
				}

				// Informamos que todo fue correcto.
				Shell_Cli::write_line(Shell_Cli::get_colored_string('Migraciones aplicadas correctamente.', 'green'));
				break;
			case 'status':
			default:
				// Verificamos conección y verificamos exista la tabla.
				self::check_db_status();

				// Obtenemos las versiones instaladas.
				try {
					$lista = Database::get_instance()->query('SELECT numero, fecha FROM migraciones')->get_pairs();
				}
				catch (Database_Exception $e)
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('Error ', 'red').$e->getCode().': '.$e->getMessage());
				}

				// Obtenemos las pentientes.
				$migraciones = Shell_Migraciones::migraciones();
				$faltantes = array_diff($migraciones, array_keys($lista));

				// Mostramos la salida.
				if (count($migraciones) == 0)
				{
					Shell_Cli::write_line('No hay migraciones definidas aún.');
				}
				else
				{
					if (count($faltantes) == 0)
					{
						Shell_Cli::write_line('No hay migraciones pendientes.');
					}
					else
					{
						if (count($faltantes) == 1)
						{
							Shell_Cli::write_line('Falta aplicar la migración '.array_shift($faltantes).'.');
						}
						else
						{
							Shell_Cli::write_line('Faltan aplicar las siguiente migraciones:');
							foreach ($faltantes as $f)
							{
								Shell_Cli::write_line('  - '.$f);
							}
						}
					}
				}
		}
	}

	/**
	 * Verificamos el estado de configuración de la base de datos.
	 */
	protected static function check_db_status()
	{
		// Verificamos coneccion a la base de datos.
		try {
			$db = Database::get_instance();
		}
		catch (Database_Exception $e)
		{
			Shell_Cli::write_line(Shell_Cli::get_colored_string('Error ', 'red').$e->getCode().': '.$e->getMessage());
		}
		Shell_Cli::write_line('Conección DB: '.Shell_Cli::get_colored_string('OK', 'green'));

		// Intentamos crear la tabla de migraciones.
		try {
			$db->insert('CREATE TABLE IF NOT EXISTS `migraciones` ( `numero` INTEGER NOT NULL, `fecha` DATETIME NOT NULL, PRIMARY KEY (`numero`) );');
		}
		catch (Database_Exception $e)
		{
			Shell_Cli::write_line(Shell_Cli::get_colored_string('Error ', 'red').$e->getCode().': '.$e->getMessage());
		}
	}

}
