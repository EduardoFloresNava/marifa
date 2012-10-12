<?php
/**
 * paginator.php is part of Marifa.
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
 * Clase para el manejo de la paginación de resultados.
 *
 * @author     Cody Roodaka <roodakazo@hotmail.com>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Paginator {

	/**
	 * Cantidad de Páginas
	 * @var int
	 */
	protected $pages;

	/**
	 * Nro de páginas para mostrar
	 * @var int
	 */
	protected $show;

	/**
	 * Cantidad total de elementos.
	 * @var int
	 */
	protected $total;

	/**
	 * Cantidad de elementos por página.
	 * @var int
	 */
	protected $cpp;

	/**
	 * Constructor de la clase.
	 * @param int $total Cantidad de elementos total.
	 * @param int $nodes_x_page Cantidad de elementos por página.
	 * @param int $show Cantidad de páginas a mostrar.
	 */
	public function __construct($total, $nodes_x_page, $show = 10)
	{
		$this->pages = ceil($total / $nodes_x_page);
		$this->show = $show;
		$this->total = $total;
		$this->cpp = $nodes_x_page;
	}

	/**
	 * Obtenemos el listado de páginas.
	 * @param int $page Número de página actual.
	 * @return array Arreglo con los numeros de páginas o FALSE si no hay páginas.
	 */
	public function paginate($page)
	{
		if ($this->pages !== 1)
		{
			// Inicializamos el arreglo principal
			$result = array();

			// Seteamos los botones de previo e inicio
			$result['first'] = 1;
			if ($page != 1)
			{
				$result['prev'] = ($page - 1);
			}
			else
			{
				$result['prev'] = 0;
			}

			// Calculamos el punto de partida para el conteo
			$start = floor($this->show / 2);
			// Nos aseguramos de que si es posible siempre arranque desde el medio
			if ($start < $this->pages && $start > 0)
			{
				// indicamos que la actual estará (o lo intentará) estar en el medio.
				$calc = ($page - $start);
				// chequeamos que no sea ni negativo ni cero.
				if ($calc < 1)
				{
					$c = 1;
				}
				else
				{
					$c = $calc;
				}
			}
			else
			{
				// iniciamos desde 1
				$c = 1;
			}

			// Bucle! Corremos el paginado.
			// $l indica la cantidad de páginas que se están mostrando
			// $c indica el número de página que se está mostrando
			$l = 1;
			while ($l <= $this->show)
			{
				if ($c <= $this->pages)
				{
					$result['pages'][] = $c;
				}
				++$l;
				++$c;
			}

			if ($page == $this->pages)
			{
				$result['next'] = 0;
			}
			else
			{
				$result['next'] = ($page + 1);
			}

			$result['last'] = $this->pages;
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Obtenemos la vista por defecto del paginador.
	 * @param int $pagina Numero de página actual.
	 * @param string URL de la paginación.
	 * @return string
	 */
	public function get_view($pagina, $url)
	{
		$vista = View::factory('helper/paginacion');

		// Paso datos.
		$vista->assign('actual', $pagina);
		$vista->assign('total', $this->total);
		$vista->assign('cpp', $this->cpp);
		$vista->assign('paginacion', $this->paginate($pagina));
		$vista->assign('url', $url);

		return $vista->parse();
	}

}