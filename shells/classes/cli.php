<?php

/**
 * Clase para el manejo de la linea de comandos.
 */
class Shell_Cli {

	private static $foreground_colors = array(
		'black' => '0;30',
		'dark_gray' => '1;30',
		'blue' => '0;34',
		'light_blue' => '1;34',
		'green' => '0;32',
		'light_green' => '1;32',
		'cyan' => '0;36',
		'light_cyan' => '1;36',
		'red' => '0;31',
		'light_red' => '1;31',
		'purple' => '0;35',
		'light_purple' => '1;35',
		'brown' => '0;33',
		'yellow' => '1;33',
		'light_gray' => '0;37',
		'white' => '1;37'
	);

	private static $background_colors = array(
		'black' => '40',
		'red' => '41',
		'green' => '42',
		'yellow' => '43',
		'blue' => '44',
		'magenta' => '45',
		'cyan' => '46',
		'light_gray' => '47'
	);

	public static function read_line()
	{
		return trim(fgets(STDIN));
	}

	public static function write_line($line)
	{
		self::write($line."\n");
	}

	public static function write($line)
	{
		fwrite(STDOUT, $line);
	}

	public static function read_value($name, $default = NULL, $options = NULL)
	{
		self::write("$name");
		if ($default !== NULL)
		{
			self::write("[$default]: ");
		}
		else
		{
			self::write(": ");
		}

		while(true) {
			$v = self::read_line();

			if (empty($v))
			{
				if ($default === NULL)
				{
					self::write_line("Valor invalido. Debe ingresar un valor.");
					self::write("$name: ");
					continue;
				}
				else
				{
					$v = $default;
				}
			}

			if ($options !== NULL)
			{
				if (is_array($options))
				{
					foreach($options as $o) {
						if ($o == $v) {
							return $v;
						}
					}
					self::write_line("Valor invalido.");
				}
				else
				{
					if ($options == $v) {
						return $v;
					}
					else
					{
						self::write_line("Valor invalido.");
					}
				}
			}
			else
			{
				return $v;
			}

			self::write("$name");
			if ($default !== NULL)
			{
				self::write("[$default]: ");
			}
			else
			{
				self::write(": ");
			}
		}
		return $v;
	}

	public static function progressBar($current, $total, $label)
	{
	    $percent = round($current / $total * 100);
	    if ($current == 0)
	    {
	    	if ($label == "")
			{
				self::write_line("Progress: ");
			}
	    	else if ($label != "none")
			{
	        	self::write_line($label);
			}
	    	self::write("|");
		}
		else
		{
	    	for ($place = 30; $place >= 0; $place--)
	    	{
		        self::write("\010");
	    	}
		}
		for ($place = 0; $place < 25; $place++)
		{
	    	if ($place <= ($percent*0.25))
			{
		        self::write("*");
			}
		    else
			{
	    	    self::write(" ");
			}
		}
		self::write("| ".sprintf('%3.0f',$percent)."%");
		if ($current == $total)
		{
		    self::write("\n");
		}
	}

	public static function option($options, $title = 'Seleccione una opción:', $option_text = 'Opción')
	{
	    CLI::write_line($title);

	    $opts = array_map(create_function('$str', 'return chr(ord("a")+$str);'), array_keys($options));

        foreach($options as $k => $m)
        {
            $l = chr(ord('a')+$k);
            CLI::write("\t$l - $m\n");
        }

        $v = CLI::read_value($option_text, NULL, $opts);
        return $options[ord($v) - ord('a')];
	}

	/**
	 * Returns one or more command-line options. Options are specified using
	 * standard CLI syntax:
	 *
	 *     php index.php --username=john.smith --password=secret --var="some value with spaces"
	 *
	 *     // Get the values of "username" and "password"
	 *     $auth = CLI::options('username', 'password');
	 *
	 * @param   string  option name
	 * @param   ...
	 * @return  array
	 */
	public static function options($options)
	{
		// Get all of the requested options
		$options = func_get_args();

		// Found option values
		$values = array();

		// Skip the first option, it is always the file executed
		for ($i = 1; $i < $_SERVER['argc']; $i++)
		{
			if ( ! isset($_SERVER['argv'][$i]))
			{
				// No more args left
				break;
			}

			// Get the option
			$opt = $_SERVER['argv'][$i];

			if (substr($opt, 0, 2) !== '--')
			{
				// This is not an option argument
				continue;
			}

			// Remove the "--" prefix
			$opt = substr($opt, 2);

			if (strpos($opt, '='))
			{
				// Separate the name and value
				list ($opt, $value) = explode('=', $opt, 2);
			}
			else
			{
				$value = NULL;
			}

			if (in_array($opt, $options))
			{
				// Set the given value
				$values[$opt] = $value;
			}
		}

		return $values;
	}

	/**
	 * Realizamos el parseado de argumentos.
	 */
	public static function parseArgs($argv)
	{
		array_shift($argv);
		$o = array();
		foreach ($argv as $a)
		{
			if (substr($a,0,2) == '--')
			{
				$eq = strpos($a,'=');
				if ($eq !== false)
				{
					$o[substr($a,2,$eq-2)] = substr($a,$eq+1);
				}
				else
				{
					$k = substr($a,2);
					if ( ! isset($o[$k]))
					{
						$o[$k] = true;
					}
				}
			}
			else if (substr($a,0,1) == '-')
			{
				if (substr($a,2,1) == '=')
				{
					$o[substr($a,1,1)] = substr($a,3);
				}
				else
				{
					foreach (str_split(substr($a,1)) as $k)
					{
						if ( ! isset($o[$k]))
						{
							$o[$k] = true;
						}
					}
				}
			}
			else
			{
				$o[] = $a;
			}
		}
		return $o;
	}

	/**
	 * Obtenemos el nivel de depuración. Depende de la cantidad de -v existente.
	 * @param array $argv Arreglo con la lista de parámetros.
	 * @return int|bool Falso si no esta presente o un entero con la cantidad.
	 */
	public function getDebugMode($argv)
	{
		// Quitamos el primer valor.
		array_shift($argv);

		// Procesamos la lista.
		foreach($argv as $arg)
		{
			if (preg_match('/^[\-]{1,2}[v]+$/', $arg))
			{
				return strlen(trim($arg, '-'));
			}
		}
		return FALSE;
	}

	// Returns colored string
	public static function getColoredString($string, $foreground_color = null, $background_color = null)
	{
		$colored_string = "";

		// Check if given foreground color found
		if (isset(self::$foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset(self::$background_colors[$background_color])) {
			$colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	// Returns all foreground color names
	public static function getForegroundColors()
	{
		return array_keys(self::$foreground_colors);
	}

	// Returns all background color names
	public static function getBackgroundColors()
	{
		return array_keys(self::$background_colors);
	}
}
