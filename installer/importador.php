<?php
/**
 * importador.php is part of Marifa.
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
 * @since		Versión 0.2RC4
 * @filesource
 * @package		Marifa\Installer
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase encargada del manejo de los importadores.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.2.RC4
 * @package    Marifa\Installer
 */
class Installer_Importador {

	/**
	 * Instancia de la base de datos de marifa.
	 * @var Database_Driver
	 */
	protected $marifa_db;

	/**
	 * Instancia de la base de datos de donde se sacan los datos a importar.
	 * @var Database_Driver
	 */
	protected $importador_db;

	/**
	 * Constructor del importador.
	 * @param Database_Driver $marifa_db Conección a la base de datos donde se encuentra marifa.
	 * @param Database_Driver $import_db Conección a la base de datos de donde sacar los datos.
	 */
	public function __construct($marifa_db, $import_db)
	{
		$this->marifa_db = $marifa_db;
		$this->importador_db = $import_db;
	}

	/**
	 * Realizamos el proceso de importación.
	 * Es un proceso general que realiza la llamada de los métodos import_*
	 * En caso de necesitar uno personalizado simplemente sobreescriba este método.
	 */
	public function importar()
	{
		// Obtenemos métodos de la clase.
		$r = new ReflectionClass($this);
		$methods = $r->getMethods();

		// Proceso listado y ejecuto.
		foreach ($methods as $m)
		{
			// Verifico si es del timpo import_.
			$m_name = $m->getName();
			if (substr($m_name, 0, 7) == 'import_')
			{
				$this->$m_name();
			}
		}
	}
}
