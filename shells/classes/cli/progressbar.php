<?php
/**
 * progressbar.php is part of Marifa.
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
 * @package		Marifa\Shell
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * PHP CLI Progress bar
 *
 * Static wrapper class for generating progress bars for cli tasks
 *
 * @copyright Copyright 2011, Andy Dawson
 * @link http://ad7six.com
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @since      Versión 0.1
 * @package    Marifa\Shell
 */
class Shell_Cli_ProgressBar {

	/**
	 * Merged with options passed in start function
	 * @var array
	 */
	protected static $defaults = array(
		'format' => "\r:message::padding:%.01f%% %2\$d/%3\$d ETC: %4\$s. Elapsed: %5\$s [%6\$s]",
		'message' => 'Running',
		'size' => 30,
		'width' => NULL
	);

	/**
	 * Runtime options
	 * @var array
	 */
	protected static $options = array();

	/**
	 * How much have we done already
	 * @var int
	 */
	protected static $done = 0;

	/**
	 * The format string used for the rendered status bar - see $defaults
	 * @var string
	 */
	protected static $format;

	/**
	 * message to display prefixing the progress bar text
	 * @var string
	 */
	protected static $message;

	/**
	 * How many chars to use for the progress bar itself. Not to be confused with $width
	 * @var int
	 */
	protected static $size = 30;

	/**
	 * When did we start (timestamp)
	 * @var int
	 */
	protected static $start;

	/**
	 * The width in characters the whole rendered string must fit in. defaults to the width of the
	 * terminal window
	 * @var int
	 */
	protected static $width;

	/**
	 * What's the total number of times we're going to call set
	 * @var int
	 */
	protected static $total;

	/**
	 * Show a progress bar, actually not usually called explicitly. Called by next()
	 *
	 * @param int $done what fraction of $total to set as progress uses internal counter if not passed
	 *
	 * @static
	 * @return string, the formatted progress bar prefixed with a carriage return
	 */
	public static function display($done = NULL)
	{
		if ($done)
		{
			self::$done = $done;
		}

		$now = time();

		if (self::$total)
		{
			$fraction_complete = (double) (self::$done / self::$total);
		}
		else
		{
			$fraction_complete = 0;
		}

		$bar = floor($fraction_complete * self::$size);
		$bar_size = min($bar, self::$size);

		$bar_contents = str_repeat('=', $bar_size);
		if ($bar < self::$size)
		{
			$bar_contents .= '>';
			$bar_contents .= str_repeat(' ', self::$size - $bar_size);
		}
		elseif ($fraction_complete > 1)
		{
			$bar_contents .= '!';
		}
		else
		{
			$bar_contents .= '=';
		}

		$percent = number_format($fraction_complete * 100, 0);

		$elapsed = $now - self::$start;
		if (self::$done)
		{
			$rate = $elapsed / self::$done;
		}
		else
		{
			$rate = 0;
		}
		$left = self::$total - self::$done;
		$etc = round($rate * $left, 2);

		if (self::$done)
		{
			$etc_now_text = '< 1 sec';
		}
		else
		{
			$etc_now_text = '???';
		}
		$time_remaining = self::human_time($etc, $etc_now_text);
		$time_elapsed = self::human_time($elapsed);

		$return = sprintf(self::$format, $percent, self::$done, self::$total, $time_remaining, $time_elapsed, $bar_contents);

		$width = strlen(preg_replace('@(?:\r|:\w+:)@', '', $return));

		if (strlen(self::$message) > (self::$width - $width - 3))
		{
			$message = substr(self::$message, 0, (self::$width - $width - 4)).'...';
			$padding = '';
			echo "\n".strlen($return);
		}
		else
		{
			$message = self::$message;
			$width += strlen($message);
			$padding = str_repeat(' ', (self::$width - $width));
		}

		$return = str_replace(':message:', $message, $return);
		$return = str_replace(':padding:', $padding, $return);

		return $return;
	}

	/**
	 * Show progress bar.
	 */
	public static function show_bar()
	{
		Shell_Cli::write(self::display());
	}

	/**
	 * reset internal state, and send a new line so that the progress bar text is "finished"
	 *
	 * @static
	 * @return string, a new line
	 */
	public static function finish()
	{
		self::reset();
		return "\n";
	}

	/**
	 * Increment the internal counter, and returns the result of display
	 *
	 * @param int $inc Amount to increment the internal counter
	 * @param string $message If passed, overrides the existing message
	 *
	 * @static
	 * @return string - the progress bar
	 */
	public static function next($inc = 1, $message = '')
	{
		self::$done += $inc;

		if ($message)
		{
			self::$message = $message;
		}

		return self::display();
	}

	/**
	 * Called by start and finish
	 *
	 * @param array $options array
	 *
	 * @static
	 * @return void
	 */
	public static function reset($options = array())
	{
		$options = array_merge(self::$defaults, $options);

		if (empty($options['done']))
		{
			$options['done'] = 0;
		}
		if (empty($options['start']))
		{
			$options['start'] = time();
		}
		if (empty($options['total']))
		{
			$options['total'] = 0;
		}

		self::$done = $options['done'];
		self::$format = $options['format'];
		self::$message = $options['message'];
		self::$size = $options['size'];
		self::$start = $options['start'];
		self::$total = $options['total'];
		self::set_width($options['width']);
	}

	/**
	 * change the message to be used the next time the display method is called
	 *
	 * @param string $message the string to display
	 *
	 * @static
	 * @return void
	 */
	public static function set_message($message = '')
	{
		self::$message = $message;
	}

	/**
	 * change the total on a running progress bar
	 *
	 * @param int $total the new number of times we're expecting to run for
	 *
	 * @static
	 * @return void
	 */
	public static function set_total($total = '')
	{
		self::$total = $total;
	}

	/**
	 * Initialize a progress bar
	 *
	 * @param mixed $total number of times we're going to call set
	 * @param int $message message to prefix the bar with
	 * @param int $options overrides for default options
	 *
	 * @static
	 * @return string - the progress bar string with 0 progress
	 */
	public static function start($total = NULL, $message = '', $options = array())
	{
		if ($message)
		{
			$options['message'] = $message;
		}
		$options['total'] = $total;
		$options['start'] = time();
		self::reset($options);

		return self::display();
	}

	/**
	 * Convert a number of seconds into something human readable like "2 days, 4 hrs"
	 *
	 * @param int $seconds how far in the future/past to display
	 * @param string $now_tText if there are no seconds, what text to display
	 *
	 * @static
	 * @return string representation of the time
	 */
	protected static function human_time($seconds, $now_text = '< 1 sec')
	{
		$prefix = '';
		if ($seconds < 0)
		{
			$prefix = '- ';
			$seconds = -$seconds;
		}

		$days = $hours = $minutes = 0;

		if ($seconds >= 86400)
		{
			$days = ( int ) ($seconds / 86400);
			$seconds = $seconds - $days * 86400;
		}
		if ($seconds >= 3600)
		{
			$hours = ( int ) ($seconds / 3600);
			$seconds = $seconds - $hours * 3600;
		}
		if ($seconds >= 60)
		{
			$minutes = ( int ) ($seconds / 60);
			$seconds = $seconds - $minutes * 60;
		}
		$seconds = ( int ) $seconds;

		$return = array();

		if ($days)
		{
			$return[] = "$days days";
		}
		if ($hours)
		{
			$return[] = "$hours hrs";
		}
		if ($minutes)
		{
			$return[] = "$minutes mins";
		}
		if ($seconds)
		{
			$return[] = "$seconds secs";
		}

		if ( ! $return)
		{
			return $now_text;
		}
		return $prefix.implode(array_slice($return, 0, 2), ', ');
	}

	/**
	 * Set the width the rendered text must fit in
	 *
	 * @param int $width passed in options
	 *
	 * @static
	 * @return void
	 */
	protected static function set_width($width = NULL)
	{
		if ($width === NULL)
		{
			if (DIRECTORY_SEPARATOR === '/')
			{
				$width = `tput cols`;
			}
			if ($width < 80)
			{
				$width = 80;
			}
		}
		self::$width = $width;
	}

}