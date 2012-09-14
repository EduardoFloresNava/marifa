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

	public function __construct($string = '')
	{
		// Llamamos constructor.
		parent::__construct($string);

		// Cargamos los Filtros y las configuraciones por defecto.
		$this->setXhtml(TRUE);
		$this->addFilter(new BlockFilter());
		$this->addFilter(new CodeFilter());
		$this->addFilter(new TextFilter());
		$this->addFilter(new DefaultFilter());
		$this->addFilter(new ImageFilter());
		$this->addFilter(new ListFilter());
		$this->addFilter(new QuoteFilter());
		$this->addFilter(new UrlFilter());
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

				'spoiler',
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
}
