<?php
/**
 * menu.php is part of Marifa.
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
 * @since		Versión 0.3
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para manejo de menús de usuario.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.3
 * @package    Marifa\Base
 */
class Base_Menu {

	/**
	 * Listados de grupos y elementos que pertenecen a un grupo en particular.
	 * @var array
	 */
	protected $grupos;

	/**
	 * Listado de elementos que no pertenecen a ningún grupo.
	 * @var array
	 */
	protected $elementos;

	/**
	 * Nombre del evento que se emite.
	 * @var string
	 */
	protected $evento;

	/**
	 * Creamos un menu de usuario.
	 * @param string $nombre Nombre del menu. Se usa para el evento generado (Menu.$nombre).
	 */
	public function __construct($nombre)
	{
		$this->grupos = array();
		$this->elementos = array();
		$this->evento = 'Menu.'.$nombre;
	}

	/**
	 * Agregamos un grupo de elementos.
	 * @param string $titulo Título a mostrar.
	 * @param string $clave Clave del grupo.
	 */
	public function group_set($titulo, $clave)
	{
		if ( ! isset($this->grupos[$clave]))
		{
			$this->grupos[$clave] = array('caption' => $titulo, 'items' => array());
		}
	}

	/**
	 * Borramos un grupo de elementos.
	 * @param string $clave Grupo a borrar.
	 */
	public function group_unset($clave)
	{
		if (isset($this->grupos[$clave]))
		{
			unset($this->grupos[$clave]);
		}
	}

	/**
	 * Verifico si existe el grupo.
	 * @param string $clave Clave del grupo a verificar su existencia.
	 * @return bool
	 */
	public function group_isset($clave)
	{
		return isset($this->grupos[$clave]);
	}

	/**
	 * Agrego un nuevo elemento.
	 * @param string $titulo Titulo del elemento.
	 * @param string $link Link al que dirije el elemento.
	 * @param string $clave Clave que identifica al elemento.
	 * @param string $grupo Grupo al que pertenece, NULL si no forma parte de ninguno.
	 * @param int $cantidad Cantidad del item. NULL si no aplica.
	 * @param bool $activo Si se encuentra activo o no.
	 */
	public function element_set($titulo, $link, $clave, $grupo = NULL, $cantidad = NULL, $activo = FALSE)
	{
		// Verifico exitencia.
		if ( ! $this->element_isset($clave, $grupo))
		{
			if ($grupo == NULL)
			{
				$this->elementos[$clave] = array('link' => $link, 'caption' => $titulo, 'active' => $activo, 'cantidad' => $cantidad);
			}
			else
			{
				// Verifico exitencia del grupo.
				if ($this->group_isset($grupo))
				{
					$this->grupos[$grupo]['items'][$clave] = array('link' => $link, 'caption' => $titulo, 'active' => $activo, 'cantidad' => $cantidad);
				}
				else
				{
					throw new Exception('El grupo al que quieres agregar el elemento no existe.');
				}
			}
		}
	}

	/**
	 * Borramos un elemento.
	 * @param string $clave Clave del elemento a borrar.
	 * @param string $grupo Clave del grupo al que debe pertenecer el elemento. NULL para ninguna.
	 */
	public function element_unset($clave, $grupo = NULL)
	{
		if ($this->element_isset($clave, $grupo))
		{
			if ($grupo == NULL)
			{
				unset($this->elementos[$grupo]);
			}
			else
			{
				unset($this->grupos[$grupo]['items'][$clave]);
			}
		}
	}

	/**
	 * Verifico la existencia del elemento.
	 * @param string $clave Clave del elemento.
	 * @param string $grupo Grupo al que pertenece el elemento. NULL para ninguno.
	 * @return bool
	 */
	public function element_isset($clave, $grupo = NULL)
	{
		if ($grupo == NULL)
		{
			return isset($this->elementos[$clave]);
		}
		else
		{
			return $this->group_isset($grupo) && isset($this->grupos[$grupo]['items'][$clave]);
		}
	}

	/**
	 * Activo un elemento.
	 * @param string $clave Clave del elemento.
	 * @param string $grupo Grupo al que pertenece el elemento. NULL para ninguno.
	 */
	protected function activate_element($clave, $grupo = NULL)
	{
		if ($this->element_isset($clave, $grupo))
		{
			if ($grupo == NULL)
			{
				$this->elementos[$clave]['active'] = TRUE;
			}
			else
			{
				$this->grupos[$grupo]['items'][$clave]['active'] = TRUE;
			}
		}
	}

	/**
	 * Obtengo un arreglo de elemento.
	 * @param string $clave Clave que se encuentra activa. NULL para ninguna.
	 * @oaram string $grupo Grupo que se encuentra activo. NULL para ninguno.
	 * @param bool $parse_empty_group Si hay que procesar grupos vacios.
	 * @return array
	 */
	public function as_array($clave, $grupo = NULL, $parse_empty_group = TRUE)
	{
		// Envio evento.
		Event::trigger($this->evento, $this);

		// Activo elemento.
		$this->activate_element($clave, $grupo);

		// Arreglo donde formar la salida.
		$salida = array();

		// Proceso elementos sin grupo.
		foreach ($this->elementos as $k => $v)
		{
			$salida['sin_grupo.'.$k] = $v;
		}

		// Proceso elementos con grupo.
		foreach ($this->grupos as $k => $v)
		{
			if ($parse_empty_group && count($v['items']) == 0)
			{
				continue;
			}

			$salida['grupo_principal_'.$k] = array('caption' => $v['caption']);
			foreach ($v['items'] as $kk => $vv)
			{
				$salida[$k.'.'.$kk] = $vv;
			}
		}

		// Devuelvo la salida.
		return $salida;
	}
}
