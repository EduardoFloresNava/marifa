<?php
/**
 * imagen.php is part of Marifa.
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
 * Clase para el manejo de carga de imagenes por medio de distintas interfaces.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Upload\Imagen
 */
class Base_Upload_Imagen {

	/**
	 * Arreglo con las configuraciones de las cargas.
	 * @var array
	 */
	private $config;

	public function __construct()
	{
		// Cargamos la configuracion.
		$this->config = Configuraciones::obtener(CONFIG_PATH.DS.'upload.'.FILE_EXT);
	}

	/**
	 * Procesamos la carga de un archivo.
	 * El comportamiento depende de las configuraciones de config/upload.php
	 * @param string $clave Elemento donde sacar los datos (clave del arreglo $_FILES)
	 * @return bool Si se realizó existosamente o no. Si es FALSE sin excepcion
	 * no existe la clave.
	 * @throws Exception Error que se produjo al procesar los datos.
	 */
	public function procesar($clave)
	{
		// Verificamos exista.
		if (isset($_FILES[$clave]))
		{
			// Obtenemos el elemento.
			$file = $_FILES[$clave];

			// Verificamos estado.
			if ($file['error'] === UPLOAD_ERR_OK)
			{
				// Verificamos tamaño.
				if ($file['size'] > $this->config['file_type']['max_size'])
				{
					throw new Exception('El tamaño del archivo debe tener '.$this->config['file_type']['max_size'].' bytes como máximo.');
				}

				// Verificamos si debemos usar un HASH para nombrar.
				if ($this->config['file_type']['use_hash'])
				{
					// Datos de la URL.
					$p_segs = pathinfo($file['name']);

					$t_name = md5($file['name']).'.'.$p_segs['extension'];
					unset($p_segs);
				}
				else
				{
					$t_name = basename($file['name']);
				}

				// Path donde guardar.
				$target = $this->config['file_type']['path'].$t_name;

				// Movemos el archivo.
				return @move_uploaded_file($file['tmp_name'], $target);
			}
			else
			{
				throw new Exception('Error cargando el archivo.', $file['error']);
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Procesamos la carga de una imagen.
	 * @param string $clave Elemento donde sacar los datos (clave del arreglo $_FILES)
	 */
	public function procesar_imagen($clave)
	{
		// Verificamos exista.
		if (isset($_FILES[$clave]))
		{
			// Obtenemos el elemento.
			$file = $_FILES[$clave];

			// Verificamos estado.
			if ($file['error'] === UPLOAD_ERR_OK)
			{
				// Verificamos tamaño.
				if ($file['size'] > $this->config['image_data']['max_size'])
				{
					throw new Exception('El tamaño de la imagen debe ser menor a '.$this->config['image_data']['max_size'].' bytes.');
				}

				// Obtenemos datos de la imagen.
				//TODO: verificar alternativas a GD. Verificar en la instalacion.
				$img_data = getimagesize($file['tmp_name']);
				$w = $img_data[0];
				$h = $img_data[1];
				$mime = $img_data['mime'];

				// Verificamos el MIME de la imagen.
				if ( ! in_array($mime, array_values($this->config['image_data']['extension'])))
				{
					throw new Exception('El tipo de imagen no está permitido');
				}

				// Verificamos el tamaño de la imagen.
				if ($w > $this->config['image_data']['resolucion_maxima'][0] || $h > $this->config['image_data']['resolucion_maxima'][1])
				{
					throw new Exception('La imagen es más grande que el permitido');
				}

				// Verificamos el tamaño de la imagen.
				if ($w < $this->config['image_data']['resolucion_minima'][0] || $h < $this->config['image_data']['resolucion_minima'][1])
				{
					throw new Exception('La imagen es más pequeña de lo que se permite');
				}

				// Cargamos el driver encargado.
				$driver_name = 'Upload_Imagen_Driver_'.ucfirst(strtolower($this->config['image']));

				// Configuracion para el driver.
				$driver_config = isset($this->config['image_'.strtolower($this->config['image'])]) ? $this->config['image_'.strtolower($this->config['image'])] : NULL;

				// Delegamos al driver.
				$driver = new $driver_name($driver_config);
				return $driver->save($file['tmp_name']);
			}
			else
			{
				throw new Exception('Error cargando el archivo.', $file['error']);
			}
		}
		else
		{
			return FALSE;
		}
	}

}
