<?php
/**
 * phpost.php is part of Marifa.
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
 * @package		Marifa\Installer
 * @subpackage  Importador
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase base para los importadores.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.2.RC4
 * @package    Marifa\Installer
 * @subpackage Importador
 */
class Installer_Importador_Phpost extends Installer_Importador {

	/**
	 * Importamos las configuraciones del sitio.
	 */
	protected function import_config()
	{
		// Obtengo elementos.
		$data = $this->importador_db->query('SELECT titulo, slogan, c_reg_active, c_reg_activate FROM w_configuracion LIMIT 1')->get_record(Database_Query::FETCH_OBJ);

		// Modelo de configuraciones.
		$model_config = new Model_Configuracion;

		// Importo nombre del sitio.
		$model_config->nombre = $data->titulo;

		// Descripción del sitio.
		$model_config->descripcion = $data->slogan;

		// Estado del registro.
		$model_config->registro = (bool) $data->c_reg_active;

		// Tipo de activación de las cuentas.
		$model_config->activacion_usuario = $data->c_reg_activate == 0 ? 2 : 1;
		/**
		 * titulo => Nombre del sitio.
		 * slogan => Descripción del sitio.
		 * c_reg_active => Se pueden registrar nuevos usuarios o no.
		 * c_reg_activate => Método de activación de las cuentas.
		 * c_reg_rango => Rango por defecto.
		 */
	}
}
