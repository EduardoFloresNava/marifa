<?php
/**
 * plugin.php is part of Marifa.
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
 * Representación de un plugin. Tiene todos los métodos para su manejo.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version    0.1
 * @package    Marifa\Base
 * @subpackage Plugin
 */
class Base_Plugin_Plugin {

	/**
	 * Nombre codificado del plugin, es decir, el del directorio.
	 * @var string
	 */
	protected $nombre;

	/**
	 * Estado del plugin.
	 * @var bool
	 */
	protected $estado;

	/**
	 * Directorio donde se encuentra el plugin.
	 * @var string
	 */
	protected $path;

	/**
	 * Información extendida del plugin.
	 * @var NULL|stdClass
	 */
	protected $data;

	/**
	 * Creamos una instancia para manejar un plugin.
	 * @param string $nombre Nombre del plugin, puede ser el de GUI o de directorio.
	 * @param bool $estado Estado del plugin.
	 */
	public function __construct($nombre, $estado = FALSE)
	{
		// Nos aseguramos de que tenga el formato esperado.
		$this->nombre = preg_replace('/(\s|[^a-z0-9])/', '', strtolower($nombre));

		// Seteamos estado del plugin.
		$this->estado = $estado;

		// Path del plugin.
		$this->path = APP_BASE.DS.PLUGINS_PATH.DS.$this->nombre.DS;

		// Cargamos la información del plugin.
		$this->data = $this->info();
	}

	/**
	 * Obtiene la información extendida del plugin.
	 * @return NULL|stdClass Información extendida en un stdClass o NULL con error.
	 */
	public function info()
	{
		// Comprobamos que exista.
		if ( ! file_exists($this->path.'index.php'))
		{
			return NULL;
		}

		// Cargamos los datos.
		$data = new stdClass;

		// Agregamos el estado.
		$data->estado = $this->estado;

		// Cargamos la información.
		$rc = new ReflectionClass('Plugin_'.ucfirst($this->nombre).'_Index');

		// Obtenemos el listado de propiedades del objeto.
		$props = $rc->getDefaultProperties();

		foreach (array('nombre', 'descripcion', 'version', 'autor') as $item)
		{
			if ( ! isset($props[$item]) || $props[$item] === NULL)
			{
				return NULL;
			}
		}

		$data->nombre = (string) $props['nombre'];
		$data->descripcion = (string) $props['descripcion'];
		$data->version = (int) $props['version'];

		// TODO: ver arreglo.
		$data->autor = (string) $props['autor'];

		// Devolvemos el objeto.
		return $data;
	}

	/**
	 * Estado del plugin.
	 * @return bool
	 */
	public function estado()
	{
		return $this->estado;
	}

	/**
	 * Verificamos si se puede instalar o no el plugin.
	 * @return bool
	 */
	public function check_support()
	{
		// Comprobamos el estado.
		if ($this->data === NULL)
		{
			return FALSE;
		}

		// Verificamos la posibilidad de unir el núcleo.
		if ( ! $this->check_core())
		{
			return FALSE;
		}

		// Nombre de la clase del plugin.
		$pc = 'Plugin_'.ucfirst($this->nombre).'_Index';

		// Verificamos la posibilidad del plugin.
		//TODO: verificar si existe el método.
		$p = new $pc;
		return (bool) $p->check_support();
	}

	/**
	 * Realizamos la comprobación para saber si se puede unir al núcleo.
	 * @param string $base Path base para comprobar.
	 */
	private function check_core($base = '')
	{
		// Armamos el path.
		$path = $this->path.'marifa'.DS.$base;

		if ( ! file_exists($path))
		{
			return TRUE;
		}

		// Obtenemos todos los elementos del path.
		$files = scandir($path);

		// Vamos uniendo recursivamente.
		foreach ($files as $file)
		{
			// Omitimos
			if ($file == '.' || $file == '..')
			{
				continue;
			}

			// Si es un directorio hacemos llamada recursiva.
			if (is_dir($path.DS.$file))
			{
				if ( ! $this->check_core($base.$file.DS))
				{
					return FALSE;
				}
			}
			else
			{
				// Armamos el nombre de la clase.
				$class_name = preg_replace('/([\/])\s*(\w)/e', 'strtoupper(\'$1$2\')', ucfirst(strtolower($base.$file)));
				$class_name = str_replace('.'.FILE_EXT, '', $class_name);
				$class_name = str_replace('/', '_', $class_name);

				$plugin_class_name = 'Plugin_'.ucfirst($this->nombre).'_Marifa_'.$class_name;

				$mc = new Plugin_Merge($plugin_class_name, $class_name);
				if ( ! $mc->is_compatible())
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	/**
	 * Realiza el proceso de instalación del plugin.
	 * @return bool Si fue exitoso o no.
	 */
	public function install()
	{
		// Comprobamos el estado.
		if ($this->data === NULL)
		{
			return FALSE;
		}

		// Instalamos las sobreescrituras.
		$this->merge_core();

		// Nombre de la clase del plugin.
		$pc = 'Plugin_'.ucfirst($this->nombre).'_Index';

		// Llamamos al instalador.
		//TODO: verificar si existe install.
		$p = new $pc;
		$p->install();

		// Guardamos el estado en el sistema.
		Plugin_Manager::get_instance()->set_state($this->nombre, TRUE);

		return TRUE;
	}

	/**
	 * Realizamos la generación de las nuevas clases del nucleo.
	 * @param string $base Path base para unir.
	 */
	private function merge_core($base = '')
	{
		// Armamos el path.
		$path = $this->path.'marifa'.DS.$base;

		if ( ! file_exists($path))
		{
			return TRUE;
		}

		// Obtenemos todos los elementos del path.
		$files = scandir($path);

		// Vamos uniendo recursivamente.
		foreach ($files as $file)
		{
			// Omitimos
			if ($file == '.' || $file == '..')
			{
				continue;
			}

			// Si es un directorio hacemos llamada recursiva.
			if (is_dir($path.DS.$file))
			{
				$this->merge_core($base.$file.DS);
			}
			else
			{
				// Armamos el nombre de la clase.
				$class_name = preg_replace('/([\/])\s*(\w)/e', 'strtoupper(\'$1$2\')', ucfirst(strtolower($base.$file)));
				$class_name = str_replace('.'.FILE_EXT, '', $class_name);
				$class_name = str_replace('/', '_', $class_name);

				$plugin_class_name = 'Plugin_'.ucfirst($this->nombre).'_Marifa_'.$class_name;

				$mc = new Plugin_Merge($plugin_class_name, $class_name);
				$mc->merge();
			}
		}
	}

	/**
	 * Función para desinstalar un plugin.
	 * @return bool Si fue correcta o no la desistalación.
	 */
	public function remove()
	{
		// Comprobamos el estado.
		if ($this->data === NULL || $this->data->estado === FALSE)
		{
			return FALSE;
		}

		// Quitamos las sobreescrituras.
		$this->revert_core();

		// Nombre de la clase del plugin.
		$pc = 'Plugin_'.ucfirst($this->nombre).'_Index';

		// Llamamos al instalador.
		//TODO: verificar si existe remove.
		$p = new $pc;
		$p->remove();

		// Guardamos el estado en el sistema.
		Plugin_Manager::get_instance()->set_state($this->nombre, FALSE);

		return TRUE;
	}

	/**
	 * Realizamos la generación de las nuevas clases del nucleo quitando
	 * las del plugin.
	 * @param string $nombre Nombre del plugin.
	 * @param string $base Path base para unir.
	 */
	private function revert_core($base = '')
	{
		// Armamos el path.
		$path = $this->path.'marifa'.DS.$base;

		if ( ! file_exists($path))
		{
			return TRUE;
		}

		// Obtenemos todos los elementos del path.
		$files = scandir($path);

		// Vamos uniendo recursivamente.
		foreach ($files as $file)
		{
			// Omitimos
			if ($file == '.' || $file == '..')
			{
				continue;
			}

			// Si es un directorio hacemos llamada recursiva.
			if (is_dir($path.DS.$file))
			{
				$this->revert_core($base.$file.DS);
			}
			else
			{
				// Armamos el nombre de la clase.
				$class_name = preg_replace('/([\/])\s*(\w)/e', 'strtoupper(\'$1$2\')', ucfirst(strtolower($base.$file)));
				$class_name = str_replace('.'.FILE_EXT, '', $class_name);
				$class_name = str_replace('/', '_', $class_name);

				$plugin_class_name = 'Plugin_'.ucfirst($this->nombre).'_Marifa_'.$class_name;

				$mc = new Plugin_Merge($plugin_class_name, $class_name);
				$mc->revert();
			}
		}
	}

	//TODO: cache para minimizar las peticiones. Solo actualizamos 1 vez cada X horas.
	/**
	 * Verificamos nuevas versiones del plugin.
	 * Esta función no se recomienda por su costo, ya que para obtener las
	 * actualizaciones de un plugin ha de enviarse la lista para que el servidor
	 * resuelva dependencias (inclusivas o exclusivas).
	 * @return FALSE|int Numero de versión o false si no hay actualizaciones.
	 */
	public function check_updates()
	{
		// Cargamos objeto de actualizaciones.
		$o_update = new Update_Updater;

		// Obtenemos la lista de actualizaciones.
		$upd_list = $o_update->find_updates();

		if (is_object($upd_list))
		{
			if ($upd_list instanceof stdClass)
			{
				$upd_list = (array) $upd_list;
			}
		}

		// Verificamos el resultado.
		if (is_array($upd_list))
		{
			$k = (string) Update_Utils::make_hash($this->data->nombre);

			if (isset($upd_list[$k]))
			{
				return $upd_list[$k];
			}
			else
			{
				return FALSE;
			}
		}
		elseif (is_object($upd_list))
		{
			$k = (string) Update_Utils::make_hash($this->data->nombre);

			if (isset($upd_list->$k))
			{
				return $upd_list->$k;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			//TODO: verificar que no sea un error.
			return FALSE;
		}
	}
}
