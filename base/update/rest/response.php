<?php
/**
 * response.php is part of Marifa.
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
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase que representa una respuesta enviada por la API REST
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update\Rest
 * @package    Marifa\Base
 */
class Base_Update_Rest_Response {

	/**
	 * Código de la respuesta.
	 * @var int
	 */
	private $code;

	/**
	 * Si hubo un error.
	 * @var bool
	 */
	private $error;

	/**
	 * Contenido de la respuesta.
	 * @var mixed
	 */
	private $content;

	/**
	 * Constructor de la clase
	 * @param mixed $data Información json decodificada.
	 */
	public function __construct($data)
	{
		$this->parse($data);
	}

	/**
	 * Procesamos la respuesta del servidor
	 * @param mixed $data
	 * @throws Exception_Rest_Invalid_Response
	 * @throws Exception_Rest_Error
	 * @throws Exception_Rest_Invalid_Code
	 */
	private function parse($data)
	{
		if (is_object($data))
		{
			// Verificamos que mantenga el formato.
			if ( ! isset($data->response) || ! isset($data->code))
			{
				// Error, no sigue el formato.
				throw new Update_Exception_Rest_Invalid_Response('Response format is invalid');
			}

			// Verificamos error interno.
			if (is_object($data->response) && isset($data->response->error) && $data->response->error === TRUE)
			{
				// Error a travez de una excepcion.
				throw new Update_Exception_Rest_Error($data->response->title, $data->response->code);
			}

			// Clasificamos respuesta según código devuelto.
			switch ($data->code)
			{
				case 200: // OK
					$this->error = FALSE;
					$this->code = 200;
					$this->content = $data->response;
					break;
				case 400: // BAD REQUEST
				case 403: // FORBIDEN
				case 404: // NOT FOUND
				case 500: // INTERNAL ERROR
					$this->error = TRUE;
					$this->code = (int) $data->code;
					$this->content = $data->response;
					throw new Update_Exception_Rest_Error("Internal server error");
					break;
				default:
					throw new Update_Exception_Rest_Invalid_Code("Response code: '{$data->code}'", $data->code);
					// CODIGO INESPERADO
			}
		}
		else
		{
			// Error, no sigue el formato.
			throw new Update_Exception_Rest_Invalid_Response('Response format is invalid');
		}
	}

	/**
	 * Verificamos si se produjo un error.
	 * @return bool
	 */
	public function has_error()
	{
		return $this->error;
	}

	/**
	 * Verificamos si el token tenia acceso.
	 * @return bool
	 */
	public function has_access()
	{
		return $this->code !== 403;
	}

	/**
	 * Verificamos si la petición fue válida.
	 * @return bool
	 */
	public function is_valid()
	{
		return $this->code !== 400 && $this->code !== 403 && ! $this->has_error();
	}

	/**
	 * Obtenemos el contenido devuelto por la petición.
	 * @return mixed
	 */
	public function get_content()
	{
		return $this->content;
	}
}
