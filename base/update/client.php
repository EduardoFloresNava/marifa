<?php
/**
 * client.php is part of Marifa.
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
 * @subpackage  Update
 * @package		Marifa\Base
 */
defined('APP_BASE') or die('No direct access allowed.');

/**
 * Clase para realizar las peticiones al servidor.
 * Nos permite consultar información sobre versiones y/o plugins.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update
 * @package    Marifa\Base
 */
class Base_Update_Client {

    /**
     * Token de la aplicacion.
	 * @var string
     */
    protected $token;

	/**
	 * Objeto para realizar las peticiones.
	 * @var Rest_Request
	 */
	protected $request;

	/**
	 * Creamos la instancia del cliente.
	 * @param string $server Url del servidor de actualizaciones.
	 * @param string $token Token del usuario
	 */
    public function __construct($server, $token)
    {
        $this->token = $token;
		$this->request = new Update_Rest_Request($server);
    }

    /**
     * Verificamos que tengamos permisos.
	 * @return bool
     */
    public function check_token()
    {
    	// Armamos la consulta.
    	$url = '/token/check/'.$this->token;

		// Realizamos la consulta.
		try {
			$rst = $this->request->call($url);
		}
		catch (Exception $e)
		{
			return FALSE;
		}

		return $rst->is_valid();
    }

    /**
     * Obtenemos la lista de nuevas versiones.
	 * @param array $pkg_list Listado de paquetes.
	 * @return FALSE|array Arreglo
     */
    public function check_updates($pkg_list)
    {
		// Armamos la petición.
		$post = array();
		foreach($pkg_list as $hash => $version)
		{
			$post[] = "pkg_list[]=$hash,$version";
		}
		$post = implode('&', $post);

		$url = '/paquete/checkUpdates/'.$this->token;

		// Realizamos la consulta.
		try {
			$rst = $this->request->call($url, $post);
		}
		catch (Exception $e)
		{
			return FALSE;
		}

		if ($rst->is_valid())
		{
			return $rst->get_content();
		}
		else
		{
			return FALSE;
		}
    }

	/**
	 * Obtenemos el listado de compresiones posibles para una descarga.
	 * @param string $hash Hash de la aplicacion.
	 * @param int $version Número de versión.
	 * @return bool
	 */
	public function get_package_compresion_list($hash, $version)
	{
		try {
			$rst = $this->request->call("/paquete/get_update/{$this->token}/$hash/$version");
			if ($rst->is_valid())
			{
				return $rst->get_content();
			}
			else
			{
				return FALSE;
			}
		}
		catch (Exception $e)
		{
			return FALSE;
		}
	}

	/**
	 * Obtenemos la última versión de un paquete.
	 * @param string $hash Hash de la aplicacion.
	 * @return bool
	 */
	public function get_last_version($hash)
	{
		//try {
			$rst = $this->request->call("/paquete/lastVersion/{$this->token}/$hash");
			if ($rst->is_valid())
			{
				return $rst->get_content()->version;
			}
			else
			{
				return FALSE;
			}
		//}
		//catch (Exception $e)
		//{
		//	var_dump($e->getMessage(), $e->getCode());
		//	return FALSE;
		//}
	}

}
