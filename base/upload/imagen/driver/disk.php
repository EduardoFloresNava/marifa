<?php
/**
 * disk.php is part of Marifa.
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
 * @package		Marifa\Base
 * @subpackage  Upload\Imagen
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Driver de almacenamiento de imagenes en disco.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Upload\Imagen
 */
class Base_Upload_Imagen_Driver_Disk implements Upload_Imagen_Driver {

	/**
	 * Configuraciones para el guardado en disco.
	 * @var array
	 */
	protected $config;

	/**
	 * Constructor de la clase.
	 * @param array $configuracion Arreglo con las configuraciones.
	 */
	public function __construct($configuracion)
	{
		$this->config = $configuracion;
	}

	/**
	 * Guardamos un archivo y devolvemos la URL para su visualización.
	 * @param type $path
	 * @return string|bool URL donde encontrar la imagen o FALSE si no se pudo guardar.
	 */
	public function save($path)
	{
		// Armamos path donde guardar.
		if ($this->config['use_hash'])
		{
			// Datos de la URL.
			$p_segs = pathinfo($path);

			$t_name = md5($path).(isset($p_segs['extension']) ? '.'.$p_segs['extension'] : '');
			unset($p_segs);
		}
		else
		{
			$t_name = basename($path);
		}

		// Path donde guardar.
		$target = $this->config['save_path'].$t_name;

		// Verificamos path de destino.
		if ( ! file_exists($this->config['save_path']))
		{
			mkdir($this->config['save_path'], 0777, TRUE);
		}

		// Movemos el archivo.
		if ( @move_uploaded_file($path, $target))
		{
			return $this->config['base_url'].$t_name;
		}
		else
		{
			return FALSE;
		}
	}

}
