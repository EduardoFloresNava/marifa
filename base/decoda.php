<?php
/**
 * decoda.php is part of Marifa.
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
include(APP_BASE.DS.'base'.DS.'decoda'.DS.'Decoda.'.FILE_EXT);

/**
 * Clase para manejo de BBCode.
 * Utiliza php-decoda.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Decoda extends Decoda_Decoda {

	/**
	 * Contructor de la clase.
	 * Configuramos Decoda para homogeneizar su comportamiento.
	 * @param string $string Cadena a procesar.
	 */
	public function __construct($string = '')
	{
		// Llamamos constructor.
		parent::__construct($string);

		// Cargo y seteo el motor de vistas.
		$engine = new DecodaPhpEngine;
		$engine->setPath(APP_BASE.DS.VIEW_PATH.THEME.DS.'views'.DS.'decoda'.DS);

		$this->setTemplateEngine($engine);

		// Seteo XHTML.
		$this->setXhtml(TRUE);
	}

	/**
	 * Cargo filtros y configuraciones por defecto.
	 * @param bool $preview Si es preview o no. En preview no se envian sucesos.
	 */
	public function load_defaults($preview = TRUE)
	{
		// Cargamos los Filtros y las configuraciones por defecto.
		$this->addFilter(new BlockFilter);
		$this->addFilter(new CodeFilter);
		$this->addFilter(new TextFilter);
		$this->addFilter(new DefaultFilter);
		$this->addFilter(new ImageFilter);
		$this->addFilter(new ListFilter);
		$this->addFilter(new QuoteFilter($preview));
		$this->addFilter(new UrlFilter);
		$this->addHook(new EmoticonHook(array('path' => THEME_URL.DS.'assets'.DS.'emoticons'.DS)));
		$this->addHook(new ClickableHook);
		$this->whitelist(
				'b',
				'i',
				'u',
				's',

				'img',
				'image',

				'list',
				'olist',
				'li',

				'quote',

				'url',
				'link',

				'align',
				'left',
				'right',
				'center',
				'justify',

				'code',

				'var',
				'color',

				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6'
		);
	}

	/**
	 * Procesado rápido de BBCode
	 * @param type $string
	 * @param bool $preview Si es preview o no. En preview no se envian sucesos.
	 * @return type
	 */
	public static function procesar($string, $preview = TRUE)
	{
		// Procesamos BBCode.
		$decoda = new Decoda($string);
		$decoda->load_defaults($preview);
		return $decoda->parse(FALSE);
	}
}
