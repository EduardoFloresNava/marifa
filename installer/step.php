<?php
/**
 * step.php is part of Marifa.
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
 * @since		Versión 0.2RC4
 * @filesource
 * @package		Marifa\Installer
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase encargada del manejo de los pasos de la instalación.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.2.RC4
 * @package    Marifa\Installer
 */
class Installer_Step {

	/**
	 * Listado de pasos.
	 * @var array
	 */
	protected $steps;

	/**
	 * Intancia del Driver que maneja la base de datos.
	 */
	private static $instance;

	/**
	 * Por el patrón singleton se evita tener instancias de esta clase.
	 */
	private function __construct()
	{
	}

	/**
	 * No se permite clonar esta clase.
	 */
	public function __clone()
	{
	}

	/**
	 * No se permite deserealizar esta clase.
	 */
	public function __wakeup()
	{
	}

	/**
	 * Obtenemos el driver de la base de datos.
	 * El driver es el que nos permite interactuar con la base de datos.
	 * @return Installer_Step Driver de la base de datos.
	 */
	public static function get_instance()
	{
		if ( ! isset(self::$instance))
		{
			// Instanciamos el Driver correspondiente.
			self::$instance = new Installer_Step;
		}
		return self::$instance;
	}

	/**
	 * Obtenemos la URL al siguiente paso.
	 * @param int|string $step Número de paso a buscar el siguiente o string del método. NULL es el actual.
	 * @return string URL al paso siguiente.
	 */
	public static function url_siguiente($step = NULL)
	{
		return self::get_instance()->url_to_next($step);
	}

	/**
	 * Configuramos y validamos los pasos.
	 * @param array $steps Listado de pasos.
	 */
	public function setup($steps)
	{
		// Cargamos los pasos.
		$this->steps = $steps;

		// Fuerzo inicio de la sessión
		if (session_id() == "" || ! isset($_SESSION))
		{
			session_start();
		}

		// Cargo paso actual.
		if ( ! isset($_SESSION['step']))
		{
			$_SESSION['step'] = 1;
		}

		// Obtengo método donde debo estar.
		$request = Request::current();

		// Verifico existencia.
		if ( ! isset($this->steps[$request['action']]))
		{
			$this->ir_al_paso();
		}

		// Verifico número.
		if ($this->steps[$request['action']]['posicion'] > $this->actual())
		{
			$this->ir_al_paso();
		}
	}

	/**
	 * Volvemos al primer paso.
	 */
	public function reiniciar()
	{
		$_SESSION['step'] = 1;
	}

	/**
	 * Voy al paso siguiente.
	 */
	public function siguiente()
	{
		$_SESSION['step'] = $this->actual() + 1;
	}

	/**
	 * Seteo un paso como terminado
	 * @param int|string $step Número de paso a setear como terminado o string del método terminado. NULL es el actual.
	 */
	public function terminado($step = NULL)
	{
		if ($step === NULL)
		{
			$request = Request::current();
			$step = $this->steps[$request['action']]['posicion'];
			unset($request);
		}
		elseif ( ! is_int($step))
		{
			$step = $this->steps[$step]['posicion'];
		}

		if ($this->actual() <= $step)
		{
			$_SESSION['step'] = $step + 1;
		}
	}

	/**
	 * Redireccionamos al paso actual.
	 */
	public function ir_al_paso()
	{
		// Obtengo id del paso actual.
		$actual = $this->actual();

		// Busco la acción.
		foreach ($this->steps as $k => $v)
		{
			if ($v['posicion'] == $actual)
			{
				Request::redirect('/installer/'.$k);
			}
		}

		// Voy a la portada.
		Request::redirect('/installer/');
	}

	/**
	 * Obtenemos el paso actual.
	 * @return int
	 */
	public function actual()
	{
		return (int) $_SESSION['step'];
	}

	/**
	 * Listado de pasos para ser renderizado.
	 */
	public function listado()
	{
		// Cargo activo.
		$request = Request::current();
		$actual = $this->steps[$request['action']]['posicion'];
		unset($request);

		// Genero listado nuevo.
		$steps = array();
		foreach($this->steps as $v)
		{
			// Tipo de paso.
			if ($v['posicion'] < $actual)
			{
				$estado = 1;
			}
			elseif ($v['posicion'] == $actual)
			{
				$estado = 0;
			}
			else
			{
				$estado = -1;
			}

			$steps[($v['posicion'] - 1)] = array('caption' => $v['titulo'], 'estado' => $estado);
		}

		// Devuelvo la lista.
		return $steps;
	}

	/**
	 * Obtenemos la URL al siguiente paso.
	 * @param int|string $step Número de paso a buscar el siguiente o string del método. NULL es el actual.
	 * @return string URL al paso siguiente.
	 */
	public function url_to_next($step = NULL)
	{
		// Obtengo número actual.
		if ($step === NULL)
		{
			$request = Request::current();
			$step = $this->steps[$request['action']]['posicion'];
			unset($request);
		}
		elseif ( ! is_int($step))
		{
			$step = $this->steps[$step]['posicion'];
		}

		// Obtengo el siguiente.
		foreach ($this->steps as $k => $v)
		{
			if ($v['posicion'] == $step + 1)
			{
				return SITE_URL.'/installer/'.$k;
			}
		}

		// URL de inicio.
		return SITE_URL.'/installer/';
	}

}
