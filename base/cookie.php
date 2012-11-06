<?php

/**
 * cookie.php is part of Marifa.
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
 * Clase para el manejo de Cookies de manera segura.
 *
 * @copyright  (c) 2008, Matthieu Huguet
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Cookie {

	/**
	 * Server secret key
	 * @var string
	 */
	protected static $_secret = '';

	/**
	 * Cryptographic algorithm used to encrypt cookies data
	 * @var string
	 */
	protected static $_algorithm = MCRYPT_RIJNDAEL_256;

	/**
	 * Cryptographic mode (CBC, CFB ...)
	 * @var mixed
	 */
	protected static $_mode = MCRYPT_MODE_CBC;

	/**
	 * mcrypt module resource
	 * @var mixed
	 */
	protected static $_crypt_module = NULL;

	/**
	 * Enable high confidentiality for cookie value (symmetric encryption)
	 * @var bool
	 */
	protected static $_high_confidentiality = TRUE;

	/**
	 * Enable SSL support
	 * @var bool
	 */
	protected static $_ssl = FALSE;

	/**
	 * Initialize cookie manager and mcrypt module.
	 *
	 *
	 * @param string $secret  server's secret key
	 * @param array $config
	 */
	public static function start($secret, $config = NULL)
	{
		if (empty($secret))
		{
			throw new Exception('You must provide a secret key');
		}
		self::$_secret = $secret;

		if ($config !== NULL && ! is_array($config))
		{
			throw new Exception('Config must be an array');
		}

		if (is_array($config))
		{
			if (isset($config['high_confidentiality']))
			{
				self::$_high_confidentiality = $config['high_confidentiality'];
			}

			if (isset($config['mcrypt_algorithm']))
			{
				self::$_algorithm = $config['mcrypt_algorithm'];
			}

			if (isset($config['mcrypt_mode']))
			{
				self::$_mode = $config['mcrypt_mode'];
			}

			if (isset($config['enable_ssl']))
			{
				self::$_ssl = $config['enable_ssl'];
			}
		}

		self::$_crypt_module = mcrypt_module_open(self::$_algorithm, '', self::$_mode, '');

		if (self::$_crypt_module === FALSE)
		{
			throw new Exception('Error while loading mcrypt module');
		}
	}

	/**
	 * Get the high confidentiality mode
	 *
	 * @return bool TRUE if cookie data encryption is enabled, or FALSE if it isn't
	 */
	public static function get_high_confidentiality()
	{
		return self::$_high_confidentiality;
	}

	/**
	 * Set the high confidentiality mode
	 * Enable or disable cookie data encryption
	 *
	 * @param bool $enable  TRUE to enable, FALSE to disable
	 */
	public static function set_high_confidentiality($enable)
	{
		self::$_high_confidentiality = $enable;
	}

	/**
	 * Get the SSL status (enabled or disabled?)
	 *
	 * @return bool TRUE if SSL support is enabled, or FALSE if it isn't
	 */
	public static function get_ssl()
	{
		return self::$_ssl;
	}

	/**
	 * Enable SSL support (not enabled by default)
	 * pro: protect against replay attack
	 * con: cookie's lifetime is limited to SSL session's lifetime
	 *
	 * @param bool $enable TRUE to enable, FALSE to disable
	 */
	public static function set_ssl($enable)
	{
		self::$_ssl = $enable;
	}

	/**
	 * Send a secure cookie
	 *
	 * @param string $cookiename cookie name
	 * @param string $value cookie value
	 * @param string $username user name (or ID)
	 * @param integer $expire expiration time
	 * @param string $path cookie path
	 * @param string $domain cookie domain
	 * @param bool $secure when TRUE, send the cookie only on a secure connection
	 * @param bool $httponly when TRUE the cookie will be made accessible only through the HTTP protocol
	 */
	public static function set_cookie($cookiename, $value, $username, $expire = 0, $path = '', $domain = '', $secure = FALSE, $httponly = NULL)
	{
		$secure_value = self::_secure_cookie_value($value, $username, $expire);
		self::set_classic_cookie($cookiename, $secure_value, $expire, $path, $domain, $secure, $httponly);
	}

	/**
	 * Delete a cookie
	 *
	 * @param string $name cookie name
	 * @param string $path cookie path
	 * @param string $domain cookie domain
	 * @param bool $secure when TRUE, send the cookie only on a secure connection
	 * @param bool $httponly when TRUE the cookie will be made accessible only through the HTTP protocol
	 */
	public static function delete_cookie($name, $path = '/', $domain = '', $secure = FALSE, $httponly = NULL)
	{
		/* 1980-01-01 */
		$expire = 315554400;
		setcookie($name, '', $expire, $path, $domain, $secure, $httponly);
	}

	/**
	 * Get a secure cookie value
	 *
	 * Verify the integrity of cookie data and decrypt it.
	 * If the cookie is invalid, it can be automatically destroyed (default behaviour)
	 *
	 * @param string $cookiename cookie name
	 * @param bool $delete_if_invalid destroy the cookie if invalid
	 */
	public static function get_cookie_value($cookiename, $delete_if_invalid = TRUE)
	{
		if (self::cookie_exists($cookiename))
		{
			$cookie_values = explode('|', $_COOKIE[$cookiename]);
			if ((count($cookie_values) === 4) &&
					($cookie_values[1] == 0 || $cookie_values[1] >= time()))
			{
				$key = hash_hmac('sha1', $cookie_values[0].$cookie_values[1], $this->_secret);
				$cookie_data = base64_decode($cookie_values[2]);
				if (self::get_high_confidentiality())
				{
					$data = self::_decrypt($cookie_data, $key, md5($cookie_values[1]));
				}
				else
				{
					$data = $cookie_data;
				}

				if (self::$_ssl && isset($_SERVER['SSL_SESSION_ID']))
				{
					$verif_key = hash_hmac('sha1', $cookie_values[0].$cookie_values[1].$data.$_SERVER['SSL_SESSION_ID'], $key);
				}
				else
				{
					$verif_key = hash_hmac('sha1', $cookie_values[0].$cookie_values[1].$data, $key);
				}

				if ($verif_key == $cookie_values[3])
				{
					return ($data);
				}
			}
		}
		if ($delete_if_invalid)
		{
			self::delete_cookie($cookiename);
		}
		return (FALSE);
	}

	/**
	 * Send a classic (unsecure) cookie
	 *
	 * @param string $cookiename cookie name
	 * @param string $value cookie value
	 * @param integer $expire expiration time
	 * @param string $path cookie path
	 * @param string $domain cookie domain
	 * @param bool $secure when TRUE, send the cookie only on a secure connection
	 * @param bool $httponly when TRUE the cookie will be made accessible only through the HTTP protocol
	 */
	public static function set_classic_cookie($cookiename, $value, $expire = 0, $path = '', $domain = '', $secure = FALSE, $httponly = NULL)
	{
		/* httponly option is only available for PHP version >= 5.2 */
		if ($httponly === NULL)
		{
			setcookie($cookiename, $value, $expire, $path, $domain, $secure);
		}
		else
		{
			setcookie($cookiename, $value, $expire, $path, $domain, $secure, $httponly);
		}
	}

	/**
	 * Verify if a cookie exists
	 *
	 * @param string $cookiename
	 * @return bool TRUE if cookie exist, or FALSE if not
	 */
	public static function cookie_exists($cookiename)
	{
		return isset($_COOKIE[$cookiename]);
	}

	/**
	 * Secure a cookie value
	 *
	 * The initial value is transformed with this protocol :
	 *
	 *  secureValue = username|expire|base64((value)k,expire)|HMAC(user|expire|value,k)
	 *  where k = HMAC(user|expire, sk)
	 *  and sk is server's secret key
	 *  (value)k,md5(expire) is the result an cryptographic function (ex: AES256) on "value" with key k and initialisation vector = md5(expire)
	 *
	 * @param string $value unsecure value
	 * @param string $username user name (or ID)
	 * @param integer $expire expiration time
	 * @return string secured value
	 */
	protected static function _secure_cookie_value($value, $username, $expire)
	{
		$key = hash_hmac('sha1', $username.$expire, self::$_secret);
		if (self::get_high_confidentiality())
		{
			$encrypted_value = base64_encode(self::_encrypt($value, $key, md5($expire)));
		}
		else
		{
			$encrypted_value = base64_encode($value);
		}


		if (self::$_ssl && isset($_SERVER['SSL_SESSION_ID']))
		{
			$verif_key = hash_hmac('sha1', $username.$expire.$value.$_SERVER['SSL_SESSION_ID'], $key);
		}
		else
		{
			$verif_key = hash_hmac('sha1', $username.$expire.$value, $key);
		}

		$result = array($username, $expire, $encrypted_value, $verif_key);
		return implode('|', $result);
	}

	/**
	 * Encrypt a given data with a given key and a given initialisation vector
	 *
	 * @param string $data data to crypt
	 * @param string $key secret key
	 * @param string $iv initialisation vector
	 * @return string encrypted data
	 */
	protected static function _encrypt($data, $key, $iv)
	{
		$iv = self::_validate_iv($iv);
		$key = self::_validate_key($key);

		mcrypt_generic_init(self::$_crypt_module, $key, $iv);
		$res = mcrypt_generic(self::$_crypt_module, $data);
		mcrypt_generic_deinit(self::$_crypt_module);

		return $res;
	}

	/**
	 * Decrypt a given data with a given key and a given initialisation vector
	 *
	 * @param string $data data to crypt
	 * @param string $key secret key
	 * @param string $iv initialisation vector
	 * @return string encrypted data
	 */
	protected static function _decrypt($data, $key, $iv)
	{
		$iv = self::_validate_iv($iv);
		$key = self::_validate_key($key);
		mcrypt_generic_init(self::$_crypt_module, $key, $iv);
		$decrypted_data = mdecrypt_generic(self::$_crypt_module, $data);
		$res = str_replace("\x0", '', $decrypted_data);
		mcrypt_generic_deinit(self::$_crypt_module);
		return $res;
	}

	/**
	 * Validate Initialization vector
	 *
	 * If given IV is too long for the selected mcrypt algorithm, it will be truncated
	 *
	 * @param string $iv Initialization vector
	 */
	protected static function _validate_iv($iv)
	{
		$iv_size = mcrypt_enc_get_iv_size(self::$_crypt_module);
		if (strlen($iv) > $iv_size)
		{
			$iv = substr($iv, 0, $iv_size);
		}
		return $iv;
	}

	/**
	 * Validate key

	 * If given key is too long for the selected mcrypt algorithm, it will be truncated
	 *
	 * @param string $key key
	 */
	protected static function _validate_key($key)
	{
		$key_size = mcrypt_enc_get_key_size(self::$_crypt_module);
		if (strlen($key) > $key_size)
		{
			$key = substr($key, 0, $key_size);
		}
		return $key;
	}

}