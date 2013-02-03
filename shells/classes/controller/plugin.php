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
 * @package		Marifa\Shell
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador encargado de realizar las migraciones.
 *
 * @author  Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since   Versión 0.1
 * @package Marifa\Shell
 */
class Shell_Controller_Plugin extends Shell_Controller {

	/**
	 * Descripcion corta del comando.
	 * @var string
	 */
	public $descripcion = "Automatizaciones para los plugins.";

	/**
	 * Listado de variantes del comando.
	 * @var array
	 */
	public $lines = array(
		'crear <directorio>',
		'generar <directorio> [-t=path] [--exclude-default]'
	);

	/**
	 * Descripción detallada del comando.
	 * @var string
	 */
	public $help = "Realizamos tareas que automatizan tareas relacionadas a los plugins.
    crear Creamos la estructura base de un plugin. Debe especificar el directorio donde se debe colocar el plugin. En caso de no existir será creado.
	generar Generamos el paquete para distribuir un plugin.
	    --exclude-default  Excluye archivos de desarrollo como .git, .gitignore, nbproject";

	/**
	 * Nombre de la clase para solventar problemas de la versión de PHP.
	 * @var string
	 */
	protected $class = __CLASS__;

	/**
	 * Función de inicio del controlador.
	 */
	public function start()
	{
		parent::start();

		$action = isset($this->params[1]) ? $this->params[1] : NULL;

		// Seleccionamos la acción a tomar.
		switch ($action)
		{
			case 'crear':
				// Cargo directorio.
				$directorio = isset($this->params[2]) ? $this->params[2] : NULL;

				// Verifico si se ingreso.
				if ($directorio == NULL || $directorio == '')
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('El directorio para crear el plugin es incorrecto.', 'red'));
					exit;
				}

				// Verifico el directorio.
				if (file_exists($directorio) && ! is_dir($directorio))
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('El directorio para crear el plugin es incorrecto.', 'red'));
					exit;
				}

				// Verifico existencia plugin.
				if (file_exists($directorio) && file_exists($directorio.'/index.php'))
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('El directorio ya contiene un plugin.', 'red'));
					exit;
				}

				// Pido los datos al usuario.
				Shell_Cli::write_line('Complete los datos del plugin.');

				do {
					$nombre = Shell_Cli::read_value('Nombre');
				} while (strlen($nombre) < 2);

				do {
					$version = Shell_Cli::read_value('Versión', 1);
				} while (empty($version));

				do {
					$autor = Shell_Cli::read_value('Autor');
				} while (empty($version));

				// Proceso el nombre del plugin.
				$pname = preg_replace('/(\s|[^a-z0-9])/', '', strtolower($nombre));
				$class_name = ucfirst($pname);

				// Genero el listado de directorios.
				foreach (array('controller', 'model', 'view', 'vendor', 'marifa') as $v)
				{
					mkdir($directorio.'/'.$v);
				}

				// Genero el archivo de configuraciones.
				file_put_contents($directorio.'/index.php', "<?php defined('APP_BASE') || die('No direct access allowed.');
class Plugin_{$class_name}_Index extends Plugin {

    protected \$nombre = '$nombre';

    protected \$descripcion = '';

    protected \$version = '$version';

    protected \$autor = '$autor';

    public function install()
    {
       return TRUE;
    }

    public function remove()
    {
        return TRUE;
    }

    public function check_support()
    {
       return TRUE;
    }
}
");
					Shell_Cli::write_line(Shell_Cli::get_colored_string('El plugin se ha creado correctamente.', 'green'));
				break;
			case 'generar':
				// Cargo directorio.
				$directorio = isset($this->params[2]) ? $this->params[2] : NULL;

				// Verifico sea válido.
				if ( ! file_exists($directorio) || ! is_dir($directorio) || ! file_exists($directorio.'/index.php') || ! is_file($directorio.'/index.php'))
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('El directorio no contiene un plugin válido.', 'red'));
					break;
				}

				// Obtengo datos del plugin.
				$pre = get_declared_classes();
				include ($directorio.'/index.php');
				$post = get_declared_classes();

				$class = array_diff($post, $pre);

				foreach ($class as $c)
				{
					if ($c == 'Plugin' || $c == 'Base_Plugin')
					{
						continue;
					}

					// Instancia.
					$rc = new ReflectionClass($c);

					// Obtenemos el listado de propiedades del objeto.
					$props = $rc->getDefaultProperties();

					// Verifico propiedades.
					if (isset($props['nombre']) && isset($props['descripcion']) && isset($props['version']) && isset($props['autor']))
					{
						$plugin = $props;
					}
				}

				if ( ! isset($plugin))
				{
					Shell_Cli::write_line(Shell_Cli::get_colored_string('El directorio no contiene un plugin válido.', 'red'));
					break;
				}

				// Obtengo compresiones disponibles.
				$compresores = Update_Compresion::get_list();

				// Cargo compresor a utilizar
				$compresor = Update_Compresion::get_instance(Shell_Cli::option($compresores, 'Compresor a utilizar'));
				unset($compresores);

				// Genero directorio donde armar el paquete.
				$path = Update_Utils::sys_get_temp_dir().'/'.uniqid('marifa_').'/';
				mkdir($path);

				// Copio los archivos del plugin.
				Update_Utils::copyr($directorio, $path.'/files/');

				// Borro directorios de depuración.
				if (isset($this->params['exclude-default']))
				{
					foreach (array('.gitignore', '.git', 'nbproject') as $v)
					{
						if (file_exists($path.'/files/'.$v))
						{
							Update_Utils::unlink($path.'/files/'.$v);
						}
					}
				}

				// Genero archivo de descripción.
				file_put_contents($path.'/info.json', '{"nombre":"'.$plugin['nombre'].'","version":"'.$this->version_to_int($plugin['version']).'","version_string":"'.$plugin['version'].'","descripcion":"'.$plugin['descripcion'].'","tipo":"estable"}');

				// Comprimo.
				$target = isset($this->params['t']) ? $this->params['t'] : Plugin_Manager::make_name($plugin['nombre']).'.'.$compresor->get_extension();

				$compresor->compress($target, $path, $path);

				// Borro archivos temporales.
				Update_Utils::unlink($path);

				// Informo resultado.
				Shell_Cli::write_line(Shell_Cli::get_colored_string('El plugin se ha empaquetado correctamente y colocado en '.Shell_Cli::get_colored_string("'$target'", 'yellow'), 'green'));
				break;
			default:
				Shell_Cli::write_line(Shell_Cli::get_colored_string('Acción incorrecta', 'red'));
		}
	}

	/**
	 * Convertimos una versión a un número entero.
	 * @param string $version Versión a convertir.
	 * @return int
	 */
	protected function version_to_int($version)
	{
		return (int) preg_replace('/[^0-9]+/', '', $version);
	}
}
