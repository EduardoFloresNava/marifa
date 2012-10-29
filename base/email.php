<?php
/**
 * email.php is part of Marifa.
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
 * Clase para la inicialización de los E-Mail's.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Email {

	/**
	 * Inicio el cargador de E-Mail's.
	 */
	public static function start()
	{
		// Cargo el cargador de shift_mailer.
		include_once(VENDOR_PATH.'swiftmailer'.DS.'lib'.DS.'swift_required.php');
	}

	/**
	 * Obtengo un transporte para el envio de e-mails.
	 * @return Swift_SmtpTransport
	 */
	public static function get_transport()
	{
		self::start();

		// Cargo las configuraciones.
		if (file_exists(CONFIG_PATH.DS.'email.php'))
		{
			$config = configuracion_obtener(CONFIG_PATH.DS.'email.php', FALSE);
		}
		else
		{
			$config = array();
		}

		// Verifico exista transporte.
		if ( ! isset($config['transport']))
		{
			throw new Exception('El tranporte no se encuentra diponible.');
		}

		// Armo nombre de la clase del transporte.
		$transport_name = 'Swift_'.ucfirst(strtolower($config['transport'])).'Transport';

		// Verifico exista el transporte.
		if ( ! class_exists($transport_name))
		{
			throw new Exception("El tranporte '$transport_name' no se encuentra diponible.");
		}

		// Cargo el transporte.
		$transporte = new $transport_name;

		// Seteo el resto de configuraciones.
		if (isset($config['parametros']) && is_array($config['parametros']))
		{
			foreach ($config['parametros'] as $k => $v)
			{
				if (strtolower($k) == 'username')
				{
					$transporte->setUsername($v);
					continue;
				}

				if (strtolower($k) == 'password')
				{
					$transporte->setPassword($v);
					continue;
				}

				if (method_exists($transporte, 'set'.ucfirst($k)))
				{
					call_user_func(array($transporte, 'set'.ucfirst($k)), $v);
				}
				else
				{
					throw new Exception("La propiedad '$k' no puede ser fijada en el transporte.");
				}
			}
		}

		// Devuelvo el transporte.
		return $transporte;
	}

	/**
	 * Obtenemos una instancia de Swift_Mailer configurada.
	 * @return Swift_Mailer
	 */
	public static function get_mailer()
	{
		self::start();

		return new Swift_Mailer(self::get_transport());
	}

	/**
	 * Obtenemos una instancia de Swift_Message configurada.
	 * @return Swift_Message
	 */
	public static function get_message()
	{
		self::start();

		return new Swift_Message();
	}
}
