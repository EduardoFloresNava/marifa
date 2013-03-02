<?php
/**
 * driver.php is part of Marifa.
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
 * Interface base para los drivers de carga de imágenes.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Upload\Imagen
 */
interface Base_Upload_Imagen_Driver {

	/**
	 * Constructor del driver. Se le pasa un arreglo con las configuraciones
	 * para limitar su funcionamiento interno.
	 * @param array|null $configuration Arreglo de configuraciones. NULL si no es necesario.
	 */
	public function __construct($configuration);

	/**
	 * Guardamos la imagen obteniendo la URL para ser mostrada.
	 * @param string $path Path donde esta alojada la imagen a enviar.
	 * @return string URL donde encontrar la imagen.
	 */
	public function save($path);

}
