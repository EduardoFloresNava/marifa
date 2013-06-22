<?php
/**
 * CensurasHook.php is part of Marifa.
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
 * Clase para manejo realizar el procesamiento de las censuras.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class CensurasHook extends DecodaHook {

	/**
	 * Listado de censuras a aplicar.
	 * @var array
	 */
	protected $censuras = array();

	/**
	 * Si se encuentra activado el modo de depuración que permite reemplazar cada censura de forma de ser identificada.
	 * @var bool
	 */
	protected $debug = FALSE;

	/**
	 * Cargo las censuras.
	 * @param array $censuras Arreglo con censuras a aplicar o NULL para que se cargen automáticamente.
	 * @param bool $debug Si se aplica el modo depuración o no. Esto permite un reemplazo significativo para cada censura.
	 */
	public function __construct($censuras = NULL, $debug = FALSE)
	{
		// Marco si está o no en depuración.
		$this->debug = $debug;

		// Verifico de donde sacar las censuras.
		if ( ! is_array($censuras))
		{
			// Cargo las censuras activas.
			$lst = Model::factory('Censura')->activas();

			// Proceso las censuras.
			foreach ($lst as $k => $v)
			{
				$lst[$k] = $v->as_object();
			}

			// Mantengo el listado.
			$this->censuras = $lst;
		}
		else
		{
			$this->censuras = $censuras;
		}
	}

	/**
	 * Realizo las censuras antes de procesar el BBCode,
	 *
	 * @param string $content Contenido a parsear.
	 * @return string
	 */
	public function afterParse($content)
	{
		// Verifico existencia de censuras.
		if (count($this->censuras) <= 0)
		{
			return $content;
		}

		// Realizo las censuras.
		foreach ($this->censuras as $censura)
		{
			// Valor a reemplazar.
			if ($this->debug)
			{
				$reemplazar = "{{#{$censura->id}}}";
			}
			else
			{
				$reemplazar = $censura->censura;
			}

			// Tipo de reemplazo.
			if ($censura->tipo == Model_Censura::TIPO_TEXTO)
			{
				$content = str_replace($censura->valor, $reemplazar, $content);
			}
			elseif ($censura->tipo == Model_Censura::TIPO_PALABRA)
			{
				$content = preg_replace('/((^|[\pP\pZ])+('.preg_quote($censura->valor).')($|[\pP\pZ]))+/ui', '\2'.$reemplazar.'\4', $content);
			}
			elseif ($censura->tipo == Model_Censura::TIPO_REGEX)
			{
				$content = preg_replace($censura->valor, $reemplazar, $content);
			}
		}
		return $content;
	}
}
