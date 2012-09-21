<?php

/**
 * Controlador encargado de realizar las migraciones.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */
class Shell_Controller_Migration extends Shell_Controller {

	/**
	 * @var string Descripcion corta del comando.
	 */
	public $descripcion = "Migraciones de la base de datos.";

	/**
	 * @var array Listado de variantes del comando.
	 */
	public $lines = array(
		'status',
		'apply');

	/**
	 * @var string Descripción detallada del comando.
	 */
	public $help = "Realizamos acciones relacionadas con las migraciones de la base de datos.
    status Verificamos el estado de las migraciones.
    apply  Aplicamos todas las migraciones.";

	/**
	 * @var string Nombre de la clase para solventar problemas de la versión de PHP.
	 */
	protected $class = __CLASS__;

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
					Shell_Cli::write_line(Shell_Cli::getColoredString('Error ', 'red').$e->getCode().': '.$e->getMessage());
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
							Shell_Cli::write_line(Shell_Cli::getColoredString('OK', 'green'));
						}
					}
					catch (Exception $e)
					{
						Shell_Cli::write_line(Shell_Cli::getColoredString('ERROR ', 'red').$e->getCode().': '.$e->getMessage());
						die();
					}
				}

				// Informamos que todo fue correcto.
				Shell_Cli::write_line(Shell_Cli::getColoredString('Migraciones aplicadas correctamente.', 'green'));
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
					Shell_Cli::write_line(Shell_Cli::getColoredString('Error ', 'red').$e->getCode().': '.$e->getMessage());
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

	protected static function check_db_status()
	{
		// Verificamos coneccion a la base de datos.
		try {
			$db = Database::get_instance();
		}
		catch (Database_Exception $e)
		{
			Shell_Cli::write_line(Shell_Cli::getColoredString('Error ', 'red').$e->getCode().': '.$e->getMessage());
		}
		Shell_Cli::write_line('Conección DB: '.Shell_Cli::getColoredString('OK', 'green'));

		// Intentamos crear la tabla de migraciones.
		try {
			$db->insert('CREATE TABLE IF NOT EXISTS `migraciones` ( `numero` INTEGER NOT NULL, `fecha` DATETIME NOT NULL, PRIMARY KEY (`numero`) );');
		}
		catch (Database_Exception $e)
		{
			Shell_Cli::write_line(Shell_Cli::getColoredString('Error ', 'red').$e->getCode().': '.$e->getMessage());
		}
	}

}
