<?php
/**
 * queue.php is part of Marifa.
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
 * @license	http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.3
 * @filesource
 * @package	Marifa\Base
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Cola de envio de correos. Permite enviar correos por medio de un cronjob.
 * Esto permite limitar la cantidad de correos que se envian por unidad de tiempo.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Email_Queue {

	/**
	 * Directorio donde se guardan los correos.
	 * @var string
	 */
	protected $directorio;

	/**
	 * Constructor de la clase.
	 * @param string $directorio Directorio donde guardar los mensajes.
	 */
	public function __construct($directorio = NULL)
	{
		// Verifico si es directorio por defecto.
		if ($directorio === NULL)
		{
			$directorio = CACHE_PATH.DS.'email'.DS;
		}

		// Asigno el directorio.
		$this->directorio = $directorio;

		// Verifico existencia.
		if ( ! file_exists($this->directorio))
		{
			mkdir($this->directorio, 0777, TRUE);
		}
	}

	/**
	 * Agregamos un mensaje a la cola.
	 * @param Swift_Message $mensaje Mensaje a agregar a la cola.
	 */
	public function add($mensaje)
	{
		// Guardo correo.
		return @file_put_contents($this->directorio.uniqid('email_', TRUE), serialize($mensaje));
	}

	/**
	 * Procesamos la cola.
	 */
	public function procesar()
	{
		// Cargamos información de la cola.
		$config = configuracion_obtener(CONFIG_PATH.DS.'email.php');

		if ( ! isset($config['queue']))
		{
			Log::info('[CRON] [QUEUE] Cola de correos sin configurar.');
			return;
		}

		// Datos de la cola.
		$limite = $config['queue']['limit'];
		$limite_hour = $config['queue']['limit_hour'];
		$limite_day = $config['queue']['limit_day'];

		// Cargo datos de los límites.

		$datos_procesamiento = $this->load_statistics();

		// Verifico si hay que limitar ejecución.
		if ($limite_hour !== NULL || $limite_day !== NULL)
		{
			if ($limite_hour !== NULL && $datos_procesamiento['hour']['end'] >= time())
			{
				$limite = min($limite, $limite_hour - $datos_procesamiento['hour']['cantidad']);
			}

			if ($limite_day !== NULL && $datos_procesamiento['day']['end'] >= time())
			{
				$limite = min($limite, $limite_hour - $datos_procesamiento['day']['cantidad']);
			}
		}

		// Verifico si hay que procesar
		if ($limite > 0)
		{
			$cantidad = $this->procesar_elementos($limite);
		}
		else
		{
			$cantidad = 0;
		}

		// Guardo resultados.
		$this->save_statistics($datos_procesamiento, $cantidad);
	}

	/**
	 * Cargamos las estadísticas de procesamiento.
	 */
	protected function load_statistics()
	{
		if (file_exists(CACHE_PATH.'/queue.txt'))
		{
			return unserialize(file_get_contents(CACHE_PATH.'/queue.txt'));
		}
		else
		{
			return array(
				'last_call' => NULL,
				'day' => array('start' => time(), 'end' => time() + 86400, 'cantidad' => 0),
				'hour' => array('start' => time(), 'end' => time() + 3600, 'cantidad' => 0)
			);
		}
	}

	/**
	 * Guardamos las estadísticas de procesamiento.
	 */
	protected function save_statistics($stats, $cantidad)
	{
		// Fecha actual.
		$time = time();

		// Ultima ejecución.
		$stats['last_call'] = $time;

		// Ejecuciones de un día.
		if ($stats['day']['end'] < $time)
		{
			$stats['day']['start'] = $time;
			$stats['day']['end'] = $time + 86400;
			$stats['day']['cantidad'] = $cantidad;
		}
		else
		{
			$stats['day']['cantidad'] += $cantidad;
		}

		// Ejecuciones por hora.
		if ($stats['hour']['end'] < $time)
		{
			$stats['hour']['start'] = $time;
			$stats['hour']['end'] = $time + 3600;
			$stats['hour']['cantidad'] = $cantidad;
		}
		else
		{
			$stats['hour']['cantidad'] += $cantidad;
		}

		file_put_contents(CACHE_PATH.'/queue.txt', serialize($stats));
	}

	/**
	 * Proceso elementos de la cola.
	 * @param int $limit Cantidad de elementos a procesar. NULL para indefinido.
	 */
	protected function procesar_elementos($limit)
	{
		// Obtengo mensajes de la cola.
		$dh = @opendir($this->directorio);

		if ($dh)
		{
			// Cantidad de procesados por la cola.
			$procesados = 0;

			// Cargo clase de envio de correos.
			$mailer = Email::get_mailer();

			// Recorro el listado de archivos.
			while(($file = readdir($dh)) !== FALSE)
			{
				// Verifico formato del archivo.
				if ( ! preg_match('/email_[0-9a-z]{14}\.[0-9a-z]{8}/', $file))
				{
					continue;
				}

				// Obtengo los datos.
				$datos = @file_get_contents($this->directorio.$file);

				if ($datos)
				{
					$mensaje = unserialize($datos);
					unset($datos);

					// Envio el correo.
					$mailer->send($mensaje);
					unset($mensaje);

					// Borro el archivo.
					@unlink($this->directorio.$file);

					// Actualizo cantidad de procesados.
					$procesados += 1;

					// Verifico cantidad a procesar.
					if ($limit !== NULL && $limit <= $procesados)
					{
						break;
					}
				}
				else
				{
					Log::warning('[CRON] [QUEUE] Error procesando elemento de la cola. Archivo: '.$file);
				}
			}

			// Informo la cantidad de procesados.
			Log::info('[CRON] [QUEUE] Procesados '.$procesados.' elementos de la cola.');

			closedir($dh);

			return $procesados;
		}
		else
		{
			Log::warning('[CRON] [QUEUE] Imposible procesar la cola. Directorio incorrecto.');
			return FALSE;
		}
	}
}