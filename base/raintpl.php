<?php
/**
 * raintpl.php is part of Marifa.
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
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase alias de Lib_RainTPL. Es para mantener compatibilidad con RainTPL.
 * Agregamos compatibilidad para plugins sobreescribiendo draw.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_RainTPL extends Lib_RainTPL {

	/**
	 * Path de la vista utilizada.
	 * @var string
	 */
	private $view;

	/**
	 * Constructor de la clase.
	 *
	 * Agregamos propiedades interesantes como evitar sobreescritura de vistas
	 * desde plugins para separar ambientes.
	 * @param string $view Path de la vista.
	 */
	public function __construct($view = NULL)
	{
		$this->view = $view;
	}

	/**
	 * Parseamos una vista. Es una abstracción de Lib_RainTPL->draw.
	 * Agrega soporte para la carga de vistas desde plugins sobre escribiendo
	 * el comportamiento.
	 * @param string $tpl_name Nombre de la vista, puede contener / para
	 * subdirectorios.
	 * @param boolean $return_string Si se devuelve o se envia al navegador.
	 * @return mixed Template parseado o el resultado.
	 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
	 */
	public function draw($tpl_name, $return_string = FALSE)
	{
		// Verificamos que tengamos una vista.
		if (( ! isset($tpl_name) || $tpl_name === NULL) && $this->view === NULL)
		{
			throw new Exception('Vista no especificada');
			return NULL;
		}

		// Colocamos el nombre correcto de la vista.
		if ($this->view !== NULL)
		{
			$tpl_name = $this->view;
		}

		// Eliminamos barra inicial y final.
		$tpl_name = trim($tpl_name, '/\\');

		// Verificamos el PATH, para determinar si es plugin o no.
		if (substr($tpl_name, 0, 6) == 'plugin')
		{
			// Es la vista de un plugin, verificamos el path.

			// Obtenemos el nombre del plugin.
			$s_list = explode('/', $tpl_name, 2);
			$plugin = strtolower($s_list[1]);

			// Generamos la ruta de la vista.
			$template_name = PLUGINS_PATH.DS.$plugin.DS.VIEW_PATH.DS.'views'.DS.$tpl_name;
		}
		else
		{
			// Es la vista del nucleo.

			// Generamos el nombre de la vista.
			$template_name = VIEW_PATH.THEME.DS.'views'.DS.$tpl_name;
		}

		// Enviamos a rainTPL para que lo procese.
		return parent::draw($template_name, $return_string);
	}

	/**
	 * Similar a draw, aunque solo devolvemos la plantilla.
	 * @return string
	 */
	public function parse()
	{
		return $this->draw(NULL, TRUE);
	}

	/**
	 * Similar a draw, aunque mostramos directamente por pantalla.
	 */
	public function show()
	{
		$this->draw(NULL, FALSE);
	}

	/**
	 * Reduce a path, eg. www/library/../filepath//file => www/filepath/file
	 * @param mixed $path Path.
	 * @return mixed
	 */
	protected function reduce_path($path)
	{
		return self::$base_url;
	}

}
