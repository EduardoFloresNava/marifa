<?php
/**
 * keyword.php is part of Marifa.
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
 * Clase para el manejo de las palabras claves de fotos, posts, etc.
 * Se utiliza para mejorar el seo.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Keyword {

	/**
	 * Cantidad mínima de caracteres que debe tener la palabra.
	 * @var int
	 */
	protected $largo_minimo;

	/**
	 * Cantidad mínima de veces que debe aparecer para considerarse clave.
	 * @var int
	 */
	protected $cantidad_minima_ocurrencias;

	/**
	 * Listado de palabras que no pueden ser palabras claves.
	 * @var array
	 */
	protected $palabras_comunes;

	/**
	 * Constructor de la clase.
	 * Iniciamos las configuraciones.
	 */
	public function __construct()
	{
		// Cargo modelo de configuraciones.
		$model_config = new Model_Configuracion;

		// Obtengo las configuraciones.
		$this->largo_minimo = (int) $model_config->get('keyword_largo_minimo', 3);
		$this->cantidad_minima_ocurrencias = (int) $model_config->get('keyword_ocurrencias_minima', 2);
		$this->palabras_comunes = unserialize($model_config->get('keyword_palabras_comunes', 'a:0:{}'));
	}

	/**
	 * Obtenemos el listado de etiquetas de un texto.
	 * @param string $str Texto de donde extraer las etiquetas.
	 * @param bool $asArray Si se devuelve un arreglo o un lista separada por comas.
	 * @param int $maxWords Cantidad máxima de etiquetas a devolver.
	 * @return string|array Arreglo con las etiquetas o lista separada por comas.
	 */
	public function extract_keywords($str, $asArray = FALSE, $maxWords = 8)
	{
		// Transformo caracteres especiales a espacios y todo a minúsculas.
	    //$str = strtolower(str_replace(array("?","!",";","(",")",":","[","]"), " ", $str));

		// Quito caracteres especiales.
	    //$str = preg_replace('/[^\p{L}0-9 ]/', ' ', strtolower($str));

		// Borro espacios multiples.
	    //$str = trim(preg_replace('/\s+/', ' ', $str));
	    $str = trim(preg_replace('/\s+/', ' ', preg_replace('/[^\p{L}0-9 ]/', ' ', strtolower($str))));

		// Separo en palabras.
	    $words = explode(' ', $str);

		// Elimino listado de palabras comunes.
		if (count($this->palabras_comunes) > 0)
		{
			$words = array_udiff($words, $this->palabras_comunes, 'strcasecmp');
		}

		// Listado de etiquetas posibles.
	    $keywords = array();
	    while(($c_word = array_shift($words)) !== null)
	    {
			// Verifico largo mínimo.
			if (strlen($c_word) < $this->largo_minimo)
			{
				continue;
			}

			// Verifico si existe o si es nueva.
	        if (isset($keywords[$c_word]))
			{
				$keywords[$c_word]++;
			}
			else
			{
				$keywords[$c_word] = 1;
			}
	    }

		// Ordeno las palabras.
	    arsort($keywords, SORT_NUMERIC);

		// Quitamos las que no cumplen requisitos.
		$final_keywords = array();
	    foreach($keywords as $k => $v)
	    {
			if($v < $this->cantidad_minima_ocurrencias)
			{
				break;
			}
	        array_push($final_keywords, $k);
	    }

		// Obtengo cantidad de palabras requerida.
	    $final_keywords = array_slice($final_keywords, 0, $maxWords);

		// Obtengemos el listado de etiquetas.
	    return $asArray ? $final_keywords : implode(', ', $final_keywords);
	}

}
