<?php
/**
 * profiler.php is part of Marifa.
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
 * @since       Versión 0.1
 * @filesource
 * @package		Marifa\Base
 * @subpackage  Profiler
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase del perfilador.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Profiler
 */
class Base_Profiler_Profiler {

	/**
	 * Holds log data collected by Profiler_Console
	 *
	 * @var array
	 */
	protected $_output = array();

	/**
	 * Holds config data passed inot the constructor
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * The list of query types we care about for type specific stats
	 *
	 * @var array
	 *
	 */
	protected $_query_types = array(
		'select',
		'update',
		'delete',
		'insert'
	);

	/**
	 * Object console.
	 * @var Profiler_Console
	 */
	protected $_console;

	/**
	 * Execution start time.
	 * @var int|mixed|NULL
	 */
	protected $_start_time;

	/**
	 * Profiler enabled.
	 * @var bool
	 */
	protected $_enabled = TRUE;

	/**
	 * Singleton pattern instance
	 * @var Profiler_Profiler
	 */
	protected static $instance;

	/**
	 * Sets the configuration options for this object and sets the start time.
	 *
	 * Possible configuration options include:
	 * query_explain_callback - Callback used to explain queries. Follow format used by call_user_func
	 *
	 * @param array $config    List of configuration options
	 * @param int   $start_time Time to use as the start time of the profiler
	 */
	private function __construct(array $config = array(), $start_time = NULL)
	{
		if (is_NULL($start_time))
		{
			$start_time = microtime(TRUE);
		}

		$this->_start_time = $start_time;
		$this->_config = $config;

		$this->_console = new Profiler_Console;
	}

	/**
	 * Obtenemos la instancia del perfilador.
	 * @return Profiler_Profiler
	 */
	public static function get_instance()
	{
		if ( ! isset(static::$instance))
		{
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * No se puede clonar.
	 */
	public function __clone()
	{
	}

	/**
	 * No se puede serealizar.
	 */
	public function __wakeup()
	{
	}

	/**
	 * enable profiler
	 */
	public function enable()
	{
		$this->_enabled = TRUE;
	}

	/**
	 * disable profiler
	 */
	public function disable()
	{
		$this->_enabled = FALSE;
	}

	/**
	 * Is disabled.
	 * @return mixed
	 */
	public function is_enabled()
	{
		return $this->_enabled;
	}

	/**
	 * Shortcut for setting the callback used to explain queries.
	 *
	 * @param string|array $callback
	 */
	public function set_query_explain_callback($callback)
	{
		$this->_config['query_explain_callback'] = $callback;
	}

	/**
	 * Shortcut for setting the callback used to interact with the MySQL
	 * query profiler.
	 *
	 * @param string|array $callback
	 */
	public function set_query_profiler_callback($callback)
	{
		$this->_config['query_profiler_callback'] = $callback;
	}

	/**
	 * Collects and aggregates data recorded by Profiler_Console.
	 */
	protected function _gather_console_data()
	{
		$logs = $this->_console->get_logs();
		$result = $logs;

		foreach ($logs as $type => $item)
		{
			// Console data will already be properly formatted.
			if ($type == 'console')
			{
				continue;
			}

			// Ignore empty message lists
			if ( ! $item['count'])
			{
				continue;
			}

			foreach ($item['messages'] as $message)
			{
				$data = $message;

				switch ($type)
				{
					case 'memory':
						$data['type'] = 'memory';
						$data['data'] = $this->_get_readable_file_size($data['data']);
						break;
					case 'speed':
						$data['type'] = 'speed';
						$data['data'] = $this->_get_readable_time($message['data'] - $this->_startTime);
						break;
					case 'benchmarks':
						$data['type'] = 'benchmark';
						$data['data'] = $this->_get_readable_time($message['end_time'] - $message['start_time']);
						break;
				}

				if (isset($data['type']))
				{
					$result['console']['messages'][] = $data;
				}
			}
		}

		$this->_output['logs'] = $result;
	}

	/**
	 * Gathers and aggregates data on included files such as size
	 */
	protected function _gather_file_data()
	{
		$files = get_included_files();
		$file_list = array();
		$file_totals = array(
			'count' => count($files),
			'size' => 0,
			'largest' => 0
		);

		foreach ($files as $file)
		{
			$size = filesize($file);
			$file_list[] = array(
				'name' => $file,
				'size' => $this->_get_readable_file_size($size)
			);
			$file_totals['size'] += $size;

			if ($size > $file_totals['largest'])
			{
				$file_totals['largest'] = $size;
			}
		}

		$file_totals['size'] = $this->_get_readable_file_size($file_totals['size']);
		$file_totals['largest'] = $this->_get_readable_file_size($file_totals['largest']);

		$this->_output['files'] = $file_list;
		$this->_output['fileTotals'] = $file_totals;
	}

	/**
	 * Gets the peak memory usage the configured memory limit
	 */
	protected function _gather_memory_data()
	{
		$memory_totals = array();
		$memory_totals['used'] = $this->_get_readable_file_size(memory_get_peak_usage());
		$memory_totals['script'] = $this->_get_readable_file_size(memory_get_peak_usage() - START_MEMORY);
		$memory_totals['total'] = ini_get('memory_limit');

		$this->_output['memoryTotals'] = $memory_totals;
	}

	/**
	 * Gathers and aggregates data regarding executed queries
	 */
	protected function _gather_query_data()
	{
		$queries = array();
		$type_default = array('total' => 0, 'time' => 0, 'percentage' => 0, 'time_percentage' => 0);
		$types = array(
			'select' => $type_default,
			'update' => $type_default,
			'insert' => $type_default,
			'delete' => $type_default
		);
		$query_totals = array('all' => 0, 'count' => 0, 'time' => 0, 'duplicates' => 0, 'types' => $types);

		foreach ($this->_output['logs']['queries']['messages'] as $entries)
		{
			if (count($entries) > 1)
			{
				$query_totals['duplicates'] += 1;
			}

			$query_totals['count'] += 1;
			foreach ($entries as $i => $log)
			{
				if (isset($log['end_time']))
				{
					$query = array(
						'sql' => $log['sql'],
						'explain' => $log['explain'],
						'time' => ($log['end_time'] - $log['start_time']),
						'duplicate' => ($i > 0) ? TRUE : FALSE
					);

					// Lets figure out the type of query for our counts
					$trimmed = trim($log['sql']);
					$type = strtolower(substr($trimmed, 0, strpos($trimmed, ' ')));

					if (in_array($type, $this->_query_types) && isset($query_totals['types'][$type]))
					{
						$query_totals['types'][$type]['total'] += 1;
						$query_totals['types'][$type]['time'] += $query['time'];
					}

					// Need to get total times and a readable format of our query time
					$query_totals['time'] += $query['time'];
					$query_totals['all'] += 1;
					$query['time'] = $this->_get_readable_time($query['time']);

					// If an explain callback is setup try to get the explain data
					if ($type == 'select' && in_array($type, $this->_query_types) && isset($this->_config['query_explain_callback'])
							&& ! empty($this->_config['query_explain_callback']))
					{
						$query['explain'] = $this->_attempt_to_explain_query($query['sql']);
					}

					// If a query profiler callback is setup get the profiler data
					if (isset($this->_config['query_profiler_callback'])
							&& ! empty($this->_config['query_profiler_callback']))
					{
						$query['profile'] = $this->_attempt_to_profile_query($query['sql']);
					}

					$queries[] = $query;
				}
			}
		}

		// Go through the type totals and calculate percentages
		foreach ($query_totals['types'] as $type => $stats)
		{
			$total_perc = ( ! $stats['total']) ? 0 : round(($stats['total'] / $query_totals['count']) * 100, 2);
			$time_perc = ( ! $stats['time']) ? 0 : round(($stats['time'] / $query_totals['time']) * 100, 2);

			$query_totals['types'][$type]['percentage'] = $total_perc;
			$query_totals['types'][$type]['time_percentage'] = $time_perc;
			$query_totals['types'][$type]['time'] = $this->_get_readable_time($query_totals['types'][$type]['time']);
		}

		$query_totals['time'] = $this->_get_readable_time($query_totals['time']);
		$this->_output['queries'] = $queries;
		$this->_output['queryTotals'] = $query_totals;
	}

	/**
	 * Calculates the execution time from the start of profiling to *now* and
	 * collects the congirued maximum execution time.
	 */
	protected function _gather_speed_data()
	{
		$speed_totals = array();
		$speed_totals['total'] = $this->_get_readable_time(microtime(TRUE) - $this->_start_time);
		$speed_totals['allowed'] = ini_get('max_execution_time');
		$this->_output['speedTotals'] = $speed_totals;
	}

	/**
	 * Converts a number of bytes to a more readable format
	 *
	 * @param int   $size      The number of bytes
	 * @param string $format_string The format of the return string
	 *
	 * @return string
	 */
	protected function _get_readable_file_size($size, $format_string = '')
	{
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB');

		if ( ! $format_string)
		{
			$format_string = '%01.2f %s';
		}

		$last_size_string = end($sizes);

		foreach ($sizes as $size_string)
		{
			if ($size < 1024)
			{
				break;
			}

			if ($size_string != $last_size_string)
			{
				$size /= 1024;
			}
		}

		if ($size_string == $sizes[0])
		{
			$format_string = '%01d %s';
		}

		return sprintf($format_string, $size, $size_string);
	}

	/**
	 * Converts a small time format (fractions of a millisecond) to a more readable format
	 *
	 * @param float $time
	 *
	 * @return int
	 */
	protected function _get_readable_time($time)
	{
		if ($time < 0.001)
		{
			// microseconds
			$units = 'µs';
			$value = $time * 1000000;
		}
		elseif ($time < 1)
		{
			// milliseconds
			$units = 'ms';
			$value = $time * 1000;
		}
		elseif ($time >= 1 && $time < 60)
		{
			// seconds
			$units = 's';
			$value = $time;
		}
		else
		{
			// minutes
			$units = 'm';
			$value = $time / 60;
		}

		$value = number_format($value, 3, '.', '').' '.$units;
		return $value;
	}

	/**
	 * Collects data from the console and performs various calculations on it before
	 * displaying the console on screen.
	 *
	 * @param bool $return_as_string
	 *
	 * @return mixed
	 */
	public function display($return_as_string = FALSE)
	{
		$this->_gather_console_data();
		$this->_gather_file_data();
		$this->_gather_memory_data();
		$this->_gather_query_data();
		$this->_gather_speed_data();

		return Profiler_Display::display($this->_output, $return_as_string);
	}

	/**
	 * Used with a callback to allow integration into DAL's to explain an executed query.
	 *
	 * @param string $sql The query that is being explained
	 *
	 * @return array
	 */
	protected function _attempt_to_explain_query($sql)
	{
		try
		{
			$sql = 'EXPLAIN '.$sql;
			return call_user_func($this->_config['query_explain_callback'], $sql);
		}
		catch (Exception $e)
		{
			return array();
		}
	}

	/**
	 * Used with a callback to allow integration into DAL's to profiler an execute query.
	 *
	 * @param string $sql The query being profiled
	 *
	 * @return array
	 */
	protected function _attempt_to_profile_query($sql)
	{
		try
		{
			return call_user_func_array($this->_config['query_profiler_callback'], $sql);
		}
		catch (Exception $e)
		{
			return array();
		}
	}

	/**
	 * Logs a variable to the console
	 *
	 * @param mixed $data The data to log to the console
	 *
	 * @return void
	 */
	public function log($data)
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log($data);
	}

	/**
	 * Logs the memory usage of the provided variable, or entire script
	 *
	 * @param string $name   Optional name used to group variables and scripts together
	 * @param mixed $variable Optional variable to log the memory usage of
	 *
	 * @return void
	 */
	public function log_memory($name = 'Memory usage at this point', $variable = NULL)
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_memory($name, $variable);
	}

	/**
	 * Show var memory usage or variable usage.
	 * @param string $name  Log mesage
	 * @param mixed $variable Variable
	 */
	public function log_var_memory($name = 'Variable memory usage at this point', $variable = NULL)
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_var_memory($name, $variable);
	}

	/**
	 * Log peak memory usar.
	 * @param string $name Log mesage
	 */
	public function log_peak_memory($name = 'Peak memory usage at this point')
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_peak_memory($name);
	}

	/**
	 * Logs an exception or error
	 *
	 * @param Exception $exception
	 * @param string    $message
	 *
	 * @return void
	 */
	public function log_error($exception, $message = '')
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_error($exception, $message);
	}

	/**
	 * Starts a timer, a second call to this method will end the timer and cause the
	 * time to be recorded and displayed in the console.
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function log_speed($name = 'Point in time')
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_speed($name);
	}

	/**
	 * Records how long a query took to run when the same query is passed in twice.
	 *
	 * @param string $sql
	 * @param NULL $explain
	 *
	 * @return mixed
	 */
	public function log_query($sql, $explain = NULL)
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_query($sql, $explain);
	}

	/**
	 * Records the time it takes for an action to occur
	 *
	 * @param string $name The name of the benchmark
	 *
	 * @return void
	 *
	 */
	public function log_benchmark($name)
	{
		if ( ! $this->is_enabled())
		{
			return;
		}

		$this->_console->log_benchmark($name);
	}

}