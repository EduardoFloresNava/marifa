<?php
/**
 * traducciones.php is part of Marifa.
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
 * Controlador encargado de procesar las traducciones.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Controller_Traducciones extends Shell_Controller {

	/**
	 * Descripcion corta del comando.
	 * @var string
	 */
	public $descripcion = "Procesamiento de cadenas I18N.";

	/**
	 * Listado de variantes del comando.
	 * @var array
	 */
	public $lines = array('<idioma>');

	/**
	 * Descripción detallada del comando.
	 * @var string
	 */
	public $help = "Creamos o actualizamos el listado de traducciones.
    <idioma> ISO 639-2 de 3 letras del idioma a crear/actualizar.";

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

		// Verifico idioma.
		$idioma = isset($this->params[1]) ? $this->params[1] : 'esp';

		// Verifico validez.
		if ( ! preg_match('/^[a-z]{3}$/', $idioma))
		{
			Shell_Cli::write_line(Shell_Cli::get_colored_string('El idioma es inválido', 'red'));
			exit;
		}

		// Proceso listado de entradas.
		$traducciones = self::obtener_traducciones();

		// Genero el archivo de traducciones.
		$fp = fopen(APP_BASE.DS.'traducciones'.DS.$idioma.'.php', 'w+');

		// Agrego cabecera.
		fwrite($fp, '<?php defined(\'APP_BASE\') || die(\'No direct access allowed.\');'.PHP_EOL.PHP_EOL);
		fwrite($fp, 'return array('.PHP_EOL);

		// Proceso listado.
		foreach ($traducciones as $traduccion)
		{
			$a = str_replace('\'', '\\\'', $traduccion);
			fwrite($fp,  "\t'$a' => '[$a]',\n");
		}

		// Agrego pie.
		fwrite($fp, ');');

		// Cierro y termino.
		fclose($fp);

		Shell_Cli::write_line(Shell_Cli::get_colored_string('Idioma creado correctamente', 'green'));
	}

	/**
	 * Obtenemos las traducciones disponibles en Marifa.
	 * @return array Arreglo con las traducciones.
	 */
	protected function obtener_traducciones()
	{
		// Traducciones de las vistas.
		$lst = self::obtener_traducciones_directorio(APP_BASE.DS.'theme'.DS.'default'.DS.'views'.DS, 'self::obtener_traducciones_raintpl');

		// Traducciones de los archivos PHP.
		$lst = array_merge($lst, self::obtener_traducciones_directorio(APP_BASE.DS.'base'.DS, 'self::obtener_traducciones_php'));

		return $lst;
	}

	protected function obtener_traducciones_directorio($directorio, $traductor)
	{
		// Donde unir las traducciones.
		$rst = array();

		// Leo directorio.
		$directory = scandir($directorio);

		// Proceso los elementos.
		foreach ($directory as $d)
		{
			if ($d == '.' || $d == '..')
			{
				continue;
			}

			// Verifico que es.
			if (is_dir($directorio.DS.$d))
			{
				$rst = array_merge($rst, self::obtener_traducciones_directorio($directorio.DS.$d, $traductor));
			}
			else
			{
				$rst = array_merge($rst, call_user_func($traductor, $directorio.DS.$d));
			}
		}

		return $rst;
	}

	/**
	 * Procesamos una plantilla de RainTPL en busca de traducciones (etiqueta '{@@}').
	 * @param  string $file Path del archivo que en el que se buscan traducciones.
	 * @return array Arreglo con las traducciones encontradas.
	 */
	protected static function obtener_traducciones_raintpl($file)
	{
		// Abro el archivo.
		$file_data = file_get_contents($file);

		// Busco las llamadas.
		$matches = array();
		preg_match_all('/\{\@([^\@\}]+)\@{0,1}\}/', $file_data, $matches);

		// Devuelvo la lista.
		return $matches[1];
	}

	/**
	 * Procesamos un archivo PHP en buscar de traducciones. Función __.
	 * @param  string $file Path del archivo que en el que se buscan traducciones.
	 * @return array Arreglo con las traducciones encontradas.
	 */
	protected static function obtener_traducciones_php($file)
	{
		// Abro el archivo.
		$file_data = file_get_contents($file);

		// Obtengo elementos.
		preg_match_all('/__\(\'(.*?)\'(,.*)?\)/', $file_data, $matches1);
		preg_match_all('/__\("(.*?)"(,.*)?\)/', $file_data, $matches2);

		return array_map('stripslashes', array_merge($matches1[1], $matches2[1]));
	}
}
