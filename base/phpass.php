<?php
/**
 * phpass.php is part of marifa.
 *
 * marifa is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * marifa is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with marifa. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa\Base
 */
defined('APP_BASE') or exit('No tienes permisos.');

/**
 * Clase de encriptación de contraseñas.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 */
class Base_Phpass {

	/**
	 * Cadena de caracteres permitidos.
	 * @var string
	 */
	private $itoa64;

	/**
	 * Semilla para el salt.
	 * @var int
	 */
	private $iteration_count_log2;

	/**
	 * Si generamos o no hash portables.
	 * @var bool
	 */
	private $portable_hashes;

	/**
	 * Estado aleatorio.
	 * @var string
	 */
	private $random_state;

	/**
	 * Constructor de la clase.
	 * @param int $iteration_count_log2 Semilla para el salt.
	 * @param bool $portable_hashes Si debe ser portable el hash generado.
	 */
	public function __construct($iteration_count_log2, $portable_hashes)
	{
		$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
		{
			$iteration_count_log2 = 8;
		}
		$this->iteration_count_log2 = $iteration_count_log2;

		$this->portable_hashes = $portable_hashes;

		$this->random_state = microtime().uniqid(rand(), TRUE);
	}

	/**
	 * Generamos una cantidad de bytes aleatorios.
	 * @param int $count Cantidad de bytes.
	 * @return string
	 */
	private function get_random_bytes($count)
	{
		$output = '';
		if (@is_readable('/dev/urandom') && ($fh = @fopen('/dev/urandom', 'rb')))
		{
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count)
		{
			$output = '';
			for ($i = 0; $i < $count; $i += 16)
			{
				$this->random_state = md5(microtime().$this->random_state);
				$output .= pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}

	/**
	 * Codificamos cadena
	 * @param string $input
	 * @param int $count
	 * @return string
	 */
	private function encode64($input, $count)
	{
		$output = '';
		$i = 0;
		do
		{
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
			{
				$value |= ord($input[$i]) << 8;
			}
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
			{
				break;
			}
			if ($i < $count)
			{
				$value |= ord($input[$i]) << 16;
			}
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
			{
				break;
			}
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

	/**
	 * Generamos salt.
	 * @param string $input
	 * @return string
	 */
	private function gensalt_private($input)
	{
		$output = '$P$';
		$output .= $this->itoa64[min($this->iteration_count_log2 + ((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);

		return $output;
	}

	/**
	 * Encriptamos.
	 * @param string $password Contraseña.
	 * @param string $setting Configuraciones.
	 * @return string
	 */
	private function crypt_private($password, $setting)
	{
		$output = '*0';
		if (substr($setting, 0, 2) == $output)
		{
			$output = '*1';
		}

		if (substr($setting, 0, 3) != '$P$')
		{
			return $output;
		}

		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
		{
			return $output;
		}

		$count = 1 << $count_log2;

		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
		{
			return $output;
		}
		if (PHP_VERSION >= '5')
		{
			$hash = md5($salt.$password, TRUE);
			do
			{
				$hash = md5($hash.$password, TRUE);
			} while (--$count);
		}
		else
		{
			$hash = pack('H*', md5($salt.$password));
			do
			{
				$hash = pack('H*', md5($hash.$password));
			} while (--$count);
		}

		$output = substr($setting, 0, 12);
		$output .= $this->encode64($hash, 16);

		return $output;
	}

	/**
	 * Generamos salt extendido.
	 * @param string $input Bytes aleatorios.
	 * @return string
	 */
	function gensalt_extended($input)
	{
		$count_log2 = min($this->iteration_count_log2 + 8, 24);
		$count = (1 << $count_log2) - 1;

		$output = '_';
		$output .= $this->itoa64[$count & 0x3f];
		$output .= $this->itoa64[($count >> 6) & 0x3f];
		$output .= $this->itoa64[($count >> 12) & 0x3f];
		$output .= $this->itoa64[($count >> 18) & 0x3f];

		$output .= $this->encode64($input, 3);

		return $output;
	}

	/**
	 * Generamos el salt.
	 * @param string $input Bytes aleatorios de entrada.
	 * @return string
	 */
	private function gensalt_blowfish($input)
	{
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '$2a$';
		$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
		$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
		$output .= '$';

		$i = 0;
		do
		{
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16)
			{
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
	}

	/**
	 * Generamos el hash de la contraseña.
	 * @param string $password Contraseña a encriptar.
	 * @return string
	 */
	public function HashPassword($password)
	{
		$random = '';

		if (CRYPT_BLOWFISH == 1 && ! $this->portable_hashes)
		{
			$random = $this->get_random_bytes(16);
			$hash = crypt($password, $this->gensalt_blowfish($random));
			if (strlen($hash) == 60)
			{
				return $hash;
			}
		}

		if (CRYPT_EXT_DES == 1 && ! $this->portable_hashes)
		{
			if (strlen($random) < 3)
			{
				$random = $this->get_random_bytes(3);
			}
			$hash = crypt($password, $this->gensalt_extended($random));
			if (strlen($hash) == 20)
			{
				return $hash;
			}
		}

		if (strlen($random) < 6)
		{
			$random = $this->get_random_bytes(6);
		}
		$hash = $this->crypt_private($password, $this->gensalt_private($random));
		if (strlen($hash) == 34)
		{
			return $hash;
		}
		return '*';
	}

	/**
	 * Verificamos si la contraseña provista es equivalente a la almacenada en la base de datos.
	 * @param string $password Contraseña a validar.
	 * @param string $stored_hash Contraseña almacenada.
	 * @return bool
	 */
	public function CheckPassword($password, $stored_hash)
	{
		$hash = $this->crypt_private($password, $stored_hash);
		if ($hash[0] == '*')
		{
			$hash = crypt($password, $stored_hash);
		}

		return $hash == $stored_hash;
	}

}