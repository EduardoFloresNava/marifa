<?php
/**
 * package.php is part of Marifa.
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
 * @since		Versión 0.3
 * @filesource
 * @subpackage  Update
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase encargada de la gestion del paquete de actualización.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @subpackage Update
 * @package    Marifa\Base
 */
class Base_Update_Package {

	/**
	 * Archivo donde se encuentra el paquete.
	 * @var string
	 */
	protected $package;

	/**
	 * Constructor de la clase.
	 * @param string $package Path al paquete a tratar.
	 */
	public function __construct($package)
	{
		// Seteamos el paquete.
		$this->package = $package;
	}

	/**
	 * Descomprimimos el paquete para poder trabajar.
	 * @param string $tmp_path Directorio temporal donde colocar la descompresion.
	 * @return bool
	 */
	protected function descomprimir($tmp_path)
	{
		// Obtenemos el tipo de compresion para el paquete.
		$c_type = Update_Utils::mime2compresor(Update_Utils::get_mime($this->package));

		// Cargamos el compresor.
		$c = Update_Compresion::get_instance($c_type);

		// Seteamos donde descomprimir.
		$c->set_temp_path($tmp_path);

		return $c->decompress($this->package);
	}

	/**
	 * Obtenemos la información del paquete.
	 * @return Update_Package_Info
	 */
	public function get_info()
	{
		return new Update_Package_Info($this->environment->get('temp_path').'/info.json');
	}

	/**
	 * Obtenemos el path del directorio temporal de un paquete.
	 * @return string
	 */
	private function get_tmp_dir()
	{
		// Obtenemos el directorio temporal para el paquete.
		$temp_dir = TMP_PATH.uniqid('pkg_').DS;

		// Creamos el directorio temporal.
		mkdir($temp_dir, 0777, TRUE);

		return $temp_dir;
	}

	/**
	 * Realizamos el proceso de instalación o actualización.
	 * @param bool $update Si es una actualización.
	 * @return bool
	 */
	public function install($update = TRUE)
	{
		// Obtenemos el directorio temporal para el paquete.
		$temp_dir = $this->get_tmp_dir();

		// Descomprimimos el paquete.
		if ( ! $this->descomprimir($temp_dir))
		{
			// Limpiamos el directorio temporal.
			Update_Utils::unlink($temp_dir);

			// No se pudo descomprimir.
			throw new Exception('Imposible descomprimir el paquete.');
		}

		// Obtenemos la información del paquete.
		if ( ! file_exists($temp_dir.'info.json'))
		{
			// Limpiamos el directorio temporal.
			Update_Utils::unlink($temp_dir);

			// No se pudo descomprimir.
			throw new Exception('El paquete es inválido.');
		}
		$data = json_decode(file_get_contents($temp_dir.'info.json'));

		// Obtenemos el nombre del paquete.
		$pkg_name = $data->nombre;

		// Cargamos el archivo para personalizar la actualización.
		if (file_exists($temp_dir.'/install.php'))
		{
			@include($temp_dir.'/install.php');

			if (class_exists('Install'))
			{
				// Cargamos el instalador.
				$install = new Install($temp_dir, $update);
			}
		}

		// Ejecutamos pre_instalacion.
		if (isset($install))
		{
			// Verificamos soporte.
			if (method_exists($install, 'before'))
			{
				$install->before();
			}
		}

		// Movemos los archivos.
		Update_Utils::copyr($temp_dir.DS.'files'.DS, Plugin_Manager::nombre_as_path($pkg_name));

		// Ejecutamos post_instalacion.
		if (isset($install))
		{
			// Verificamos soporte.
			if (method_exists($install, 'after'))
			{
				$install->after();
			}
		}

		// Limpiamos archivos de la instalación y salimos.
		return Update_Utils::unlink($temp_dir);
	}

}
