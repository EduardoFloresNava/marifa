<?php
/**
 * info.php is part of Marifa.
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
 * @subpackage  Update\Package
 * @package		Marifa\Base
 */
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Representación de la información de un paquete.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update\Package
 * @package    Marifa\Base
 */
class Base_Update_Package_Info {

	/**
	 * Directorio del archivo de descripcion del paquete.
	 * @var string
	 */
    private $path;

	/**
	 * Cargamos la información de un paquete.
	 * @param string $file Path del archivo de descripción del paquete.
	 */
    public function __construct($file)
    {
		// Cargamos el path.
        $this->path = $file;

		// Cargamos los datos.
        $this->load();
    }

    /**
     * Cargamos la información del paquete.
     */
    protected function load()
    {
		// Obtenemos la información.
        $data = json_decode(file_get_contents($this->path));

		// Verificamos estado del paquete.
        if ( ! is_object($data))
        {
            throw new Exception('Paquete dañado o inválido');
        }
        else
        {
            $this->data = $data;
        }
    }

    /**
     * Obtenemos la información general del paquete.
	 * @return mixed|NULL
     */
    public function get()
    {
        return isset($this->data) ? $this->data : NULL;
    }
}
