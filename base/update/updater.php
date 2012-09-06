<?php
/**
 * updater.php is part of Marifa.
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
 * Clase base de la actualización.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @subpackage Update
 * @package    Marifa\Base
 */
class Base_Update_Updater {

	/**
	 * Url del servidor de actualizaciones.
	 * @var string
	 */
	protected $server;

	/**
	 * Token del usuario.
	 * @var string
	 */
	protected $token;

	/**
	 * Creamos una instancia del servidor de actualizaciones.
	 */
    public function __construct()
    {
    	// Seteamos el servidor de actualizaciones.
    	$this->server = Configuraciones::get('update.server', NULL);

		// Seteamos el token.
		$this->token = Configuraciones::get('update.token', NULL);
    }

    /**
     * Función para consultar actualizaciones desde el servidor.
	 * Devuelve un arreglo con las actualizaciones. En caso de error se
	 * retorna FALSE.
	 *
	 * @return array|FALSE
     */
    public function find_updates()
    {
		// Obtenemos el manejador de paquetes.
		$pkg_manager = Plugin_Manager::getInstance();

		// Generamos el listado de paquetes.
		$pkg_list = array();
		foreach(array_keys($pkg_manager->load()) as $nombre)
		{
			// TODO: ver si es lógico omitir los desactivados.

			// Cargamos el paquete.
			$pkg_info = $pkg_manager->get($nombre)->info();

			$pkg_list[Update_Utils::make_hash($pkg_info->nombre)] = $pkg_info->version;
		}
		unset($pkg_manager);

    	// Cargamos la clase de peticiones.
    	$c_o = new Update_Client($this->server, $this->token);

    	// Pedimos la lista de actualizaciones.
    	try {
	    	$upd_list = $c_o->check_updates($pkg_list);
	    }
	    catch (Update_Exception_Client_Token $e) // Token de usuario invalido.
	    {
	    	return FALSE;
	    }
	    catch (Update_Exception_Client_Forbiden $e) // No tenemos acceso con ese token.
	    {
	    	return FALSE;
	    }
	    catch (Update_Exception_Client_Missed $e) // Paquete no encontrado.
		{
			return FALSE;
		}

    	return $upd_list;
    }

	/**
	 * Instalamos o actualizamos un paquete.
	 * @param string $hash Hash del paquete a instalar.
	 * @param int $version Número de version a descargar e instalar. Si no se
	 * especifica utilizamos la última.
	 * @return bool
	 */
	public function install_package($hash, $version = NULL)
	{
		// Cliente para realizar las peticiones.
		$o_client = new Update_Client($this->server, $this->token);

		// Verificamos si hay versión especificada.
		if($version === NULL)
		{
			// Obtenemos la última versión.
			$version = $o_client->get_last_version($hash);
		}

		// Obtenemos la compresion de descarga. (zip, tar, gz, bz2, etc)
		$compresion_list = $o_client->get_package_compresion_list($hash, $version);
		unset($o_client);

		if ( ! is_array($compresion_list))
		{
			throw new Exception('Imposible obtener la lista de compresiones.');
			return FALSE;
		}
		else
		{
			// Verificamos la lista de compatibles.
			$compresiones = array_intersect($compresion_list, Update_Compresion::get_list());
			unset($compresion_list);

			if (count($compresiones) === 0)
			{
				throw new Exception('No hay una compresión compatible para utilizar.');
				return FALSE;
			}
		}

		// Seleccionamos la primer compresion.
		$type = array_shift($compresiones);
		unset($compresiones);

		// Armamos directorio temporal de la descarga.
		$temp_file = TMP_PATH.uniqid('upd_fule_');

		// Descargamos el archivo.
		$rqs = new Update_Rest_Request($this->server);
		if ( ! $rqs->download("/paquete/get_update/{$this->token}/$hash/$version/$type", $temp_file))
		{
			@unlink($temp_file);
			// No se pudo descargar.
			throw new Exception('No se pudo descargar el archivo.');
			return FALSE;
		}

		// Cargamos el paquete.
		$pkg = new Update_Package($temp_file);

		// Realizamos la instalación.
		return $pkg->install();
	}

}
