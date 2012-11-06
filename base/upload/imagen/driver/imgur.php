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
 * Driver de almacenamiento de imagenes utilizando imgur.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Upload\Imagen
 */
class Base_Upload_Imagen_Driver_Imgur implements Upload_Imagen_Driver {

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
		// Armamos arreglo de campos.
		$pvars = array();
		$pvars['key'] = $this->config['api_key'];
		$pvars['image'] = '@'.$path;// base64_encode(file_get_contents($path));

		// Creamos llamada curl.
		$curl = curl_init();

		// Timeout.
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->config['timeout']);

		// El resto de los parametros.
		curl_setopt($curl, CURLOPT_URL, 'http://api.imgur.com/2/upload.json');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);

		// Realizamos la llamada.
		$json = curl_exec($curl);

		// Limpiamos buffer.
		curl_close($curl);
		unset($curl);

		// Procesamos resultado.
		$rst = json_decode($json);

		if (is_object($rst) && isset($rst->upload))
		{
			if (is_object($rst->upload) && isset($rst->upload->links))
			{
				return $rst->upload->links->original;
			}
		}
		return FALSE;
	}

}
