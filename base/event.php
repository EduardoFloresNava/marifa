<?php
/**
 * event.php is part of Marifa.
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
 * @since		Versión 0.2RC5
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para el manejo de eventos del sistema.
 *
 * Permite el envio y subscripción de eventos del sistema.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.2RC5
 * @package    Marifa\Base
 */
class Base_Event {

	/**
	 * Listado de eventos.
	 * @var array
	 */
	protected static $eventos = array();

	/**
	 * Registramos un nuevo evento.
	 * @param string $event Nombre del evento.
	 * @param mixed $callback Elemento a llamar.
	 */
	public static function register($event, $callback)
	{
		// Creo el evento si no existe.
		if ( ! isset(self::$eventos[$event]))
		{
			self::$eventos[$event] = array();
		}

		// Agrego el evento.
		self::$eventos[$event][] = $callback;
	}

	/**
	 * Quitamos un evento de la lista de registrados.
	 * @param string $event Nombre del evento.
	 * @param mixed $callback Elemento a llamar. NULL para quitar todos los elementos de un evento.
	 * @return bool
	 */
	public static function unregister($event, $callback = NULL)
	{
		// Verifico existencia del evento.
		if (isset(self::$eventos[$event]))
		{
			// Verifico si un callback o todos.
			if ($callback !== NULL)
			{
				// Busco el callback.
				$encontrado = FALSE;
				foreach (self::$eventos[$event] as $k => $v)
				{
					if ($v == $callback)
					{
						unset(self::$eventos[$event][$k]);
						$encontrado = TRUE;
						break;
					}
				}

				// Limpio si está vacio.
				if (count(self::$eventos[$event]) <= 0)
				{
					unset(self::$eventos[$event]);
				}
				return $encontrado;
			}
			else
			{
				// Borro todas las llamadas del evento.
				unset(self::$eventos[$event]);
			}
		}
		return TRUE;
	}

	/**
	 * Emitimos un evento.
	 * @param string $event Nombre del evento.
	 * @param mixed $data Parámetros del evento.
	 * @param bool $reversed Si se ejecuta en el orden inverso al registrado.
	 * @return mixed
	 */
	public static function trigger($event, $data = '', $reversed = FALSE)
	{
		// Verifico si hay algo registrado.
		if (isset(self::$eventos[$event]))
		{
			if ($reversed)
			{
				$rst = NULL;
				$arr = array_reverse(self::$eventos[$event]);
				foreach($arr as $v)
				{
					$rst = call_user_func($v, $data, $rst);
				}
				return $rst;
			}
			else
			{
				$rst = NULL;
				foreach(self::$eventos[$event] as $v)
				{
					$rst = call_user_func($v, $data, $rst);
				}
				return $rst;
			}
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Verificamos si el evento tiene elementos a la espera.
	 * @param string $event Nombre del evento.
	 * @return bool
	 */
	public static function has_events($event)
	{
		return isset(self::$eventos[$event]) && count(self::$eventos[$event]) > 0;
	}

	/**
	 * Cargamos los eventos desde los plugins.
	 */
	public static function load_from_plugins()
	{
		// Obtengo la lista de plugins existente.
		$pl = Plugin_Manager::get_instance()->get_actives();

		// Cargo eventos si hay plugins.
		if (count($pl) > 0)
		{
			if (count($pl) == 1)
			{
				// Cargo eventos del único plugin.
				if (file_exists(APP_BASE.DS.PLUGINS_PATH.DS.$pl[0].DS.'events.'.FILE_EXT))
				{
					$eventos = include(APP_BASE.DS.PLUGINS_PATH.DS.$pl[0].DS.'events.'.FILE_EXT);
					foreach ($eventos as $event => $callback)
					{
						self::register($event, $callback);
					}
				}
			}
			else
			{
				// Cargo los eventos que existan.
				foreach (glob(APP_BASE.DS.PLUGINS_PATH.DS.'{'.(implode(',', $pl)).'}'.DS.'events.'.FILE_EXT, GLOB_BRACE) as $v)
				{
					$eventos = include($v);
					foreach ($eventos as $event => $callback)
					{
						self::register($event, $callback);
					}
				}
			}
		}
	}
}
