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
 * @license	http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.3
 * @filesource
 * @package	Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase base de la cache. Configura y devuelve un driver para su uso.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
abstract class Base_Api_Request {

	/**
	 * Obtengo el código de respuesta de una petición.
	 * @param array|string $headers Listado de cabeceras o cabecera.
	 * @return int Codigo HTTP o 500 si no se encuentra.
	 */
	protected static function get_response_code($headers)
	{
		if (is_array($headers))
		{
			foreach ($headers as $header)
			{
				$matches = array();
				if (preg_match('/HTTP\/1\.[01]\s([0-9]{3})\s(.*)/', $header, $matches))
				{
					return (int) $matches[1];
				}
			}
			return 500;
		}
		else
		{
			$matches = array();
			if (preg_match('/HTTP\/1\.[01]\s([0-9]{3})\s(.*)/', $headers, $matches))
			{
				return (int) $matches[1];
			}
			return NULL;
		}
	}

	/**
	 * Realizo una llamada HTTP.
	 * @param string $url URL a obtener.
	 * @return array(HTTP_RESPONSE_HEADER, CONTENIDO)
	 * @throws Api_Exception Error al realiza la llamada.
	 */
	protected static function do_request($url)
	{
		// Verifico si puedo usar CURL.
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_REFERER, SITE_URL);
			$rst = curl_exec($ch);

			if ($rst === FALSE)
			{
				throw new Api_Exception('[cURL ERROR] '.curl_error($ch), curl_errno($ch));
			}
			else
			{
				return array($rst, curl_getinfo($ch, CURLINFO_HTTP_CODE));
			}

			curl_close($ch);
		}
		else
		{
			// Opciones generales.
			$opciones = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'Referer: '.SITE_URL
				)
			);

			// Seteo timeout.
			if (version_compare(PHP_VERSION, '5.2.1', '>='))
			{
				$opciones['http']['timeout'] = 5;
			}

			// Seteo follow_location.
			if (version_compare(PHP_VERSION, '5.3.4', '>='))
			{
				$opciones['http']['follow_location'] = 1;
			}

			// Realizo la llamada.
			$rst = file_get_contents($url, FALSE, stream_context_create($opciones));

			if ($rst === FALSE)
			{
				throw new Api_Exception('Invalid response', 330);
			}
			else
			{
				return array(self::get_response_code($http_response_header), $rst);
			}
		}
	}

	/**
	 * Realizo una petición GET.
	 * @param string $url URL a llamar.
	 * @param array $fields Listado de campos.
	 * @return array(HTTP_RESPONSE_HEADER, CONTENIDO) Resultado de la petición.
	 */
	protected static function do_get_request($url, $fields = array())
	{
		// Armo la URL.
		if (count($fields) > 0)
		{
			if (strpos($url, '?') === FALSE)
			{
				$url .= '?';
			}

			foreach ($fields as $k => $v)
			{
				$url .= '&'.$k.'='.urlencode($v);
			}
		}

		// Verifico si puedo usar CURL.
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_REFERER, SITE_URL);
			$rst = curl_exec($ch);

			if ($rst === FALSE)
			{
				throw new Api_Exception('[cURL ERROR] '.curl_error($ch), curl_errno($ch));
			}
			else
			{
				return array($rst, curl_getinfo($ch, CURLINFO_HTTP_CODE));
			}

			curl_close($ch);
		}
		else
		{
			// Opciones generales.
			$opciones = array(
				'http' => array(
					'method' => 'GET',
					'header' => 'Referer: '.SITE_URL
				)
			);

			// Seteo timeout.
			if (version_compare(PHP_VERSION, '5.2.1', '>='))
			{
				$opciones['http']['timeout'] = 5;
			}

			// Seteo follow_location.
			if (version_compare(PHP_VERSION, '5.3.4', '>='))
			{
				$opciones['http']['follow_location'] = 1;
			}

			// Realizo la llamada.
			$rst = file_get_contents($url, FALSE, stream_context_create($opciones));

			if ($rst === FALSE)
			{
				throw new Api_Exception('Invalid response', 330);
			}
			else
			{
				return array(self::get_response_code($http_response_header), $rst);
			}
		}
	}

	/**
	 * Realizo una petición POST.
	 * @param string $url URL a llamar.
	 * @param array $fields Listado de campos POST a enviar.
	 * @return array(HTTP_RESPONSE_HEADER, CONTENIDO) Resultado de la petición.
	 */
	protected static function do_post_request($url, $fields = array())
	{
		// Verifico si puedo usar CURL.
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_REFERER, SITE_URL);
			$rst = curl_exec($ch);

			if ($rst === FALSE)
			{
				throw new Api_Exception('[cURL ERROR] '.curl_error($ch), curl_errno($ch));
			}
			else
			{
				return array($rst, curl_getinfo($ch, CURLINFO_HTTP_CODE));
			}

			curl_close($ch);
		}
		else
		{
			// Opciones generales.
			$opciones = array(
				'http' => array(
					'method' => 'POST',
					'header'  => 'Content-type: multipart/form-data; Referer: '.SITE_URL,
					'content' => http_build_query($fields)
				)
			);

			// Seteo timeout.
			if (version_compare(PHP_VERSION, '5.2.1', '>='))
			{
				$opciones['http']['timeout'] = 5;
			}

			// Seteo follow_location.
			if (version_compare(PHP_VERSION, '5.3.4', '>='))
			{
				$opciones['http']['follow_location'] = 1;
			}

			// Realizo la llamada.
			$rst = file_get_contents($url, FALSE, stream_context_create($opciones));

			if ($rst === FALSE)
			{
				throw new Api_Exception('Invalid response', 330);
			}
			else
			{
				return array(self::get_response_code($http_response_header), $rst);
			}
		}
	}

	/**
	 * Descargo un archivo remoto.
	 * @param string $url URL de donde descargar el archivo.
	 * @param string $target Donde se va a guardar el archivo.
	 * @param string $method POST o GET para el método de la petición.
	 * @param array $fields Listado de campos POST o GET a enviar.
	 * @return mixed TRUE si todo fue bien, una arreglo con código de respuesta HTML y el contenido en caso contrario.
	 */
	protected static function download_file($url, $target, $method = 'GET', $fields = array())
	{
		// Verifico si tengo cURL.
        if(function_exists('curl_init'))
        {
            // Inicio cURL.
            $ch = curl_init();

            // Cargo URL y campos según método.
            if ($method == 'GET')
            {
                // Armo la URL.
                if (count($fields) > 0)
                {
                    if (strpos($url, '?') === FALSE)
                    {
                        $url .= '?';
                    }

                    foreach ($fields as $k => $v)
                    {
                        $url .= '&'.$k.'='.urlencode($v);
                    }
                }

                curl_setopt($ch, CURLOPT_URL, $url);
            }
            else
            {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Parámetros generales.
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:20.0) Gecko/20100101 Firefox/20.0");

            // Abro donde guardar.
            $fp = fopen($target, 'w');

            // Parámetros de la descarga.
            curl_setopt($ch, CURLOPT_FILE, $fp);

            // Ejecuto la descarga.
            $rst = curl_exec($ch);

            fclose($fp);

			if ( ! $rst)
			{
                @unlink($rst);
				throw new Api_Exception('[cURL ERROR] '.curl_error($ch), curl_errno($ch));
			}
			else
			{
                curl_close($ch);
				return TRUE;
			}
        }
        else
        {
            if ($method == 'GET')
            {
                list ($response, $code) = self::do_get_request($url, $fields);
            }
            else
            {
                list ($response, $code) = self::do_post_request($url, $fields);
            }

            if ($code == 200)
            {
                file_put_contents($target, $response);
                return TRUE;
            }
            else
            {
                throw new Api_Exception('ERROR ['.$code.']: '.$response, 335);
            }
        }
	}

}
