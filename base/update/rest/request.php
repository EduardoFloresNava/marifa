<?php
/**
 * request.php is part of Marifa.
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
 * @subpackage  Update\Rest
 * @package		Marifa\Base
 */

/**
 * Clase encargada de realizar las llamadas al API.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update\Rest
 * @package    Marifa\Base
 */
class Base_Update_Rest_Request {

	/**
	 * URL del servidor de actualizaciones.
	 * @var string
	 */
	protected $server;

	/**
	 * Constructor de la clase.
	 * @param string $server URL del servidor.
	 */
	public function __construct($server)
	{
		$this->server = $server;
	}

	/**
	 * Realizamos una peticion al servidor.
	 * @param string $url URL a realizar la petición.
	 * @param array $post Listado de campos a enviar por post. En caso de ser
	 * un archivo, usar @path como valor.
	 * @param callable $callback Callback a llamar cuando se produce un proceso
	 * en la descarga o en la carga de archivos.
	 */
	private function remote_call($url, $post = NULL, $callback = NULL)
	{
		if (function_exists('curl_init'))
		{
			$petition = curl_init();
			curl_setopt($petition, CURLOPT_URL, $url);
			curl_setopt($petition, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($petition, CURLOPT_TIMEOUT, 5); // Evitamos mucho tiempo para la respuesta.

			// Seteamos callback de subida.
			if ($callback !== NULL)
			{
				curl_setopt($petition, CURLOPT_NOPROGRESS, FALSE);
				//curl_setopt($petition, CURLOPT_BUFFERSIZE, 24);
				curl_setopt($petition, CURLOPT_PROGRESSFUNCTION, $callback);
			}

			// Agregamos los parametros post.
			if ($post !== NULL)
			{
				//curl_setopt($petition, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
				curl_setopt($petition, CURLOPT_POST, 1);
				//curl_setopt($petition, CURLOPT_POST, count($post));
				curl_setopt($petition, CURLOPT_POSTFIELDS, $post);
			}

			$data = curl_exec($petition);
			if (curl_errno($petition) === 0)
			{
				curl_close($petition);
				return $data;
			}
			else
			{
				throw new Update_Exception_Rest(curl_error($petition), curl_errno($petition));
				curl_close($petition);
				return FALSE;
			}
		}
		else
		{
			return @file_get_contents($url);
		}
	}

	/**
	 * Parsear una respuesta
	 * @param mixed $data
	 * @return Update_Rest_Response|NULL
	 * @throws Exception_Rest_Invalid_Code
	 * @throws Exception_Rest_Invalid_Response
	 */
	private function parse_response($data)
	{
		$decoded_data = json_decode($data);

		//if ($decoded_data === NULL)
		//{
		//	return FALSE;
		//}

		return new Update_Rest_Response($decoded_data);
	}

	/**
	 * Realizamos una petición al servidor.
	 * @param string $url
	 * @param array $post
	 * @return mixed
	 */
	public function call($url, $post = NULL)
	{
		$rst = $this->remote_call($this->server.$url, $post);
		return $this->parse_response($rst);
	}

	/**
	 * Realizamos una carga. Es identico a call pero agregamos $callback que debe
	 * ser una función y nos permite informar del proceso.
	 * @param string $url
	 * @param array $post
	 * @param array|string $callback
	 * @return mixed
	 */
	public function upload($url, $post = NULL, $callback = NULL)
	{
		$rst = $this->remote_call($this->server.$url, $post, $callback);
		return $this->parse_response($rst);
	}

	/**
	 * Realizamos la descarga de un archivo.
	 * @param string $url Url del archivo a descargar.
	 * @param string $file Archivo donde guardar la descarga.
	 */
	public function download($url, $file)
	{
		// Verificamos presencia de cURL.
		if (function_exists('curl_init'))
		{
			// Abrimos el archivo.
			$fp = fopen($file, 'w+');

			// Iniciamos el objeto.
			$petition = curl_init();

			// Configuramos la peticion.
			curl_setopt($petition, CURLOPT_URL, $this->server.$url);
			curl_setopt($petition, CURLOPT_TIMEOUT, 50);
			curl_setopt($petition, CURLOPT_FILE, $fp);
			//curl_setopt($petition, CURLOPT_FOLLOWLOCATION, true);

			// Realizamos la peticion.
			curl_exec($petition);

			// Verificamos presencia de errores.
			if (curl_errno($petition) === 0)
			{
				curl_close($petition);
				fclose($fp);
				return TRUE;
			}
			else
			{
				fclose($fp);
				throw new Update_Exception_Rest(curl_error($petition), curl_errno($petition));
				curl_close($petition);
				return FALSE;
			}
		}
		elseif (ini_get('allow_url_fopen') === TRUE)
		{
			// Intentamos con lectura remota.

			// Tratamos de abrir el fichero.
			$r_fp = @fopen($url, 'r');

			// Verificamos su apertura.
			if ( ! $r_fp)
			{
				return FALSE;
			}

			// Abrimos el local
			$l_fp = @fopen($file, 'w+');

			// Verificamos su apertura.
			if ( ! $r_fp)
			{
				@fclose($r_fp);
				return FALSE;
			}

			// Comenzamos a mover los bytes.
			while( ! feof($r_fp))
			{
				fwrite($l_fp, fread($r_fp, 1024));
			}

			// Cerramos los archivos.
			@fclose($r_fp);
			@fclose($l_fp);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}
