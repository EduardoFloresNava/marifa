<?php
/**
 * route.php is part of Marifa.
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
 * @since		Versión 0.2RC2
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase que representa una ruta del sistema de rutas.
 *
 * @author     Danny van Kooten <dannyvankooten@gmail.com>
 * @since      Versión 0.2RC2
 * @package    Marifa\Base
 */
class Base_Route {

	/**
	 * URL of this Route
	 * @var string
	 */
	private $url;

	/**
	 * Accepted HTTP methods for this route
	 * @var array
	 */
	private $methods = array('GET', 'POST', 'PUT', 'DELETE');

	/**
	 * Target for this route, can be anything.
	 * @var mixed
	 */
	private $target;

	/**
	 * The name of this route, used for reversed routing
	 * @var string
	 */
	private $name;

	/**
	 * Custom parameter filters for this route
	 * @var array
	 */
	private $filters = array();

	/**
	 * Array containing parameters passed through request URL
	 * @var array
	 */
	private $params = array();

	/**
	 * Get url
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Set url
	 * @param string $url URL
	 */
	public function setUrl($url)
	{
		$url = (string) $url;

		// make sure that the URL is suffixed with a forward slash
		if (substr($url, -1) !== '/')
			$url .= '/';

		$this->url = $url;
	}

	public function getTarget()
	{
		return $this->target;
	}

	public function setTarget($target)
	{
		$this->target = $target;
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function setMethods(array $methods)
	{
		$this->methods = $methods;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = (string) $name;
	}

	public function setFilters(array $filters)
	{
		$this->filters = $filters;
	}

	/**
	 * get regex
	 * @return string
	 */
	public function getRegex()
	{
		return preg_replace_callback("/:(\w+)/", array(&$this, 'substituteFilter'), $this->url);
	}

	/**
	 * Substitute filter
	 * @param array $matches Matches
	 * @return string
	 */
	private function substituteFilter($matches)
	{
		if (isset($matches[1]) && isset($this->filters[$matches[1]]))
		{
			return $this->filters[$matches[1]];
		}

		return "([\w-]+)";
	}

	/**
	 * Get parameters
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Set parameters
	 * @param array $parameters Parameters
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
	}

}