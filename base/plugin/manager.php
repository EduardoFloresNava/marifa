<?php
/**
 * manager.php is part of Marifa.
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
 * @subpackage  Plugin
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase encargada del manejo de plugins.
 *
 * Esta clase es la encargada de manejar los plugin's. Se encarga de todas las
 * tareas relacionadas a la administración de los mismos.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Plugin
 */
class Base_Plugin_Manager {

	/**
	 * Instancia del patrón singleton.
	 * @var Base_Plugin_Manager
	 */
	private static $instance;

	/**
	 * Listado de plugins con sus estados.
	 * @var array
	 */
	private $plugins;

	/**
	 * Constructor de la clase.
	 *
	 * No permitimos su llamada externa para evitar una instanciación para
	 * para seguir con el patrón singleton.
	 */
	private function __construct()
	{
	}

	/**
	 * Función de clonación de la clase.
	 *
	 * Como forma parte de un patrón singleton no se permite clonar el objeto.
	 */
	public function __clone()
	{
	}

	/**
	 * Función de deserealización de la clase.
	 *
	 * Como forma parte de un patrón singleton no se permite deserealizar el
	 * objeto para obtener una instancia desde una guardada.
	 */
	public function __wakeup()
	{
	}

	/**
	 * Obtener una instancia de la clase.
	 *
	 * Esta función nos genera una instancia de la clase. Siempre devuelve
	 * la misma como parte del patrón singleton.
	 * @return Lib_Plugin
	 */
	public static function get_instance()
	{
		if ( ! isset(self::$instance))
		{
			$c = loader_prefix_class(__CLASS__);
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Arma el nombre del directorio en función del nombre del paquete.
	 * @param string $nombre Nombre a convertir.
	 * @return string.
	 */
	public static function make_name($nombre)
	{
		return preg_replace('/(\s|[^a-z0-9])/', '', strtolower($nombre));
	}

	/**
	 * Obtenemos el path de un paquete en función de su nombre.
	 * @param string $nombre Nombre del plugin.
	 * @return string
	 */
	public static function nombre_as_path($nombre)
	{
		return APP_BASE.DS.PLUGINS_PATH.DS.self::make_name($nombre).DS;
	}

	/**
	 * Generamos una lista de plugins actualizada.
	 *
	 * Generamos la lista de plugins nueva usando como base la vieja si existe.
	 * En caso de haber una antigua, mantiene los estados de los plugins.
	 */
	public function regenerar_lista()
	{
		// Cargamos el estado actual.
		$old_data = $this->load();

		// Exploramos la lista de plugins actuales.
		$dirs = scandir(APP_BASE.DS.PLUGINS_PATH);

		// Arreglo para poner los plugins con su estado.

		// Recorremos el directorio en busca de plugins.
		$plugins = array();
		foreach ($dirs as $dir)
		{
			// Directorio actual o padre?
			if ($dir === '.' || $dir === '..')
			{
				// Lo omitimos.
				continue;
			}

			// Es un directorio?
			if ( ! is_dir(APP_BASE.DS.PLUGINS_PATH.DS.$dir))
			{
				// Lo omitimos.
				continue;
			}

			// Verificamos si es un plugin, debe tener plugin.xml
			if ( ! file_exists(APP_BASE.DS.PLUGINS_PATH.DS.$dir.DS.'index.php'))
			{
				// Lo omitimos.
				continue;
			}

			// Es un plugin válido. Verificamos si tenemos el estado antiguo.
			if (isset($old_data[$dir]))
			{
				$enabled = $old_data[$dir];
			}
			else
			{
				// Deshabilitado por defecto.
				$enabled = FALSE;
			}

			// Agregamos el plugin a la lista.
			$plugins[$dir] = $enabled;
		}

		// Liberamos memoria inutil.
		unset($dirs);
		unset($old_data);

		// Actualizamos la cache.
		$this->plugins = $plugins;

		// Guardamos la lista generada.
		$this->guardar_listado($plugins);
	}

	/**
	 * Guardamos el listado de plugins con sus estados en el disco.
	 * @param array $listado Listado de plugins con su estado.
	 */
	private function guardar_listado($listado)
	{
		// Abrimos el archivo para escribir.
		$fp = @fopen(APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php', 'w');

		// Como se produjo un error terminamos la ejecución.
		if ( ! $fp)
		{
			//TODO: manejar error.
			die('Permisos para el archivo de plugins inválido');
		}

		// Escribimos la cabecera.
		fwrite($fp, "<?php defined('APP_BASE') || die('No direct access allowed.');\nreturn array(");

		// Escribimos el lista de plugins.
		foreach ($listado as $key => $value)
		{
			$value = $value ? 'TRUE' : 'FALSE';
			fwrite($fp, "'$key' => $value,\n");
		}

		// Escribimos el pie.
		fwrite($fp, ");");

		// Cerramos el objeto.
		fclose($fp);
	}

	/**
	 * Cargamos el listado de plugins con su estado.
	 * @return array Arreglo con los plugins.
	 */
	public function load()
	{
		// Comprobamos exista la cache.
		if (isset($this->plugins) && is_array($this->plugins))
		{
			return $this->plugins;
		}

		// Verificamos la existencia del antiguo.
		if (file_exists(APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php'))
		{
			// Cargamos la configuración vieja.
			$old_data = @include (APP_BASE.DS.PLUGINS_PATH.DS.'plugin.php');

			// Verificamos que sea válido.
			if ( ! is_array($old_data))
			{
				// No obteniamos lo que esperabamos, usamos una configuración
				// por defecto.
				$old_data = array();
			}
		}
		else
		{
			// Ponemos una información vacia.
			$old_data = array();
		}

		// Actualizamos la cache.
		$this->plugins = $old_data;

		return $old_data;
	}

	/**
	 * Activar/desactivar un plugin.
	 * @param string $nombre Nombre del plugin a activar/desactivar
	 * @param bool   $estado Estado a setear. TRUE activado, FALSE desactivado.
	 * @param bool   $update Si guardamos en disco o no. Util para actualizar una
	 * larga lista.
	 */
	public function set_state($nombre, $estado, $update = TRUE)
	{
		// Obtenemos la lista de plugins.
		$pl = $this->load();

		// Verificamos exista
		if ( ! isset($pl[$nombre]))
		{
			return FALSE;
		}

		// Verificamos no posea ese estado.
		if ($pl[$nombre] === $estado)
		{
			// No hace falta efectuar cambios.
			return TRUE;
		}

		// Actualizamos el estado y refrescamos la lista en el disco.
		$pl[$nombre] = $estado;

		// Actualizamos si nos los piden
		if ($update)
		{
			$this->guardar_listado($pl);
		}

		return TRUE;
	}

	/**
	 * Guarda los cambios en el Disco.
	 */
	public function flush()
	{
		$this->guardar_listado($this->load());
	}

	/**
	 * Obtenemos la instancia de un plugin para su manejo.
	 * @param string $nombre Nombre del plugin.
	 * @return NULL|Plugin_Plugin Objeto o false si no existe el plugin.
	 */
	public function get($nombre)
	{
		// Obtenemos la lista de plugins.
		$pl = $this->load();

		// Verificamos exista
		if ( ! isset($pl[$nombre]))
		{
			return NULL;
		}

		return new Plugin_Plugin($nombre, $pl[$nombre]);
	}
}
