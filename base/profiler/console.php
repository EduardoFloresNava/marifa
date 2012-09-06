<?php
/**
 * console.php is part of Marifa.
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
class Base_Profiler_Console {

    /**
     * Holds the logs used when the console is displayed.
     * @var array
     */
    private $_logs = array(
            'console'    => array('messages' => array(), 'count' => 0),
            'memory'     => array('messages' => array(), 'count' => 0),
            'errors'     => array('messages' => array(), 'count' => 0),
            'speed'      => array('messages' => array(), 'count' => 0),
            'benchmarks' => array('messages' => array(), 'count' => 0),
            'queries'    => array('messages' => array(), 'count' => 0),
        );

    /**
	 * Add item to a category
     * @param string $category Category name
     * @param mixed $item Item data.
     */
    protected function _write($category, $item)
    {
        $this->_logs[$category]['messages'][] = $item;
        $this->_logs[$category]['count'] += 1;
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
        $logItem = array(
            'data'     => $data,
            'type'     => 'log',
        );

        $this->_write('console', $logItem);
    }

    /**
     * Logs the memory usage of the provided variable, or entire script
     *
     * @param string $name   Optional name used to group variables and scripts together
     * @param mixed $variable Optional variable to log the memory usage of
     *
     * @return void
     */
    public function logMemory($name = 'Memory usage at this point', $variable = NULL)
    {
        if (!is_NULL($variable)) {
            $this->logVarMemory($name, $variable);
        }

        $logItem = array(
            'data' => memory_get_usage(),
            'name' => $name
        );

        $this->_write('memory', $logItem);
    }

    /**
	 * Variable memory usage at this point
     * @param string $name Log message
     * @param        $variable
     */
    public function logVarMemory($name = 'Variable memory usage at this point', $variable = NULL)
    {
        $logItem = array(
            'data'     => strlen(serialize($variable)),
            'name'     => $name,
            'dataType' => gettype($variable)
        );

        $this->_write('memory', $logItem);
    }

    /**
	 * Peak memory usage at this point
     * @param string $name Log message
     */
    public function logPeakMemory($name = 'Peak memory usage at this point')
    {
        $logItem = array(
            'data' => memory_get_peak_usage(),
            'name' => $name
        );

        $this->_write('memory', $logItem);
    }

    /**
     * Logs an exception or error
     *
     * @param Exception $exception
     * @param string    $message
     *
     * @return void
     */
    public function logError($exception, $message = '')
    {
        $logItem = array(
            'data' => ($message) ? $message : $exception->getMessage(),
            'type' => 'error',
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        );

        $this->_write('errors', $logItem);
    }

    /**
     * Starts a timer, a second call to this method will end the timer and cause the
     * time to be recorded and displayed in the console.
     *
     * @param string $name
     *
     * @return void
     */
    public function logSpeed($name = 'Point in Time')
    {
        $logItem = array(
            'data' => microtime(TRUE),
            'name' => $name
        );

        $this->_write('speed', $logItem);
    }

    /**
     * Records how long a query took to run when the same query is passed in twice.
     *
     * @param      $sql
     * @param NULL $explain
     *
     * @return mixed
     */
    public function logQuery($sql, $explain = NULL)
    {
        // We use a hash of the query for two reasons. One is because for large queries the
        // hash will be considerably smaller in memory. The second is to make a dump of the
        // logs more easily readable.
        $hash = md5($sql);

        // If this query is in the log we need to see if an end time has been set. If no
        // end time has been set then we assume this call is closing a previous one.
        if (isset($this->_logs['queries']['messages'][$hash])) {
            $query = array_pop($this->_logs['queries']['messages'][$hash]);
            if (!$query['end_time']) {
                $query['end_time'] = microtime(TRUE);
                $query['explain'] = $explain;

                $this->_logs['queries']['messages'][$hash][] = $query;
            } else {
                $this->_logs['queries']['messages'][$hash][] = $query;
            }

            $this->_logs['queries']['count'] += 1;
            return;
        }

        $logItem = array(
            'start_time' => microtime(TRUE),
            'end_time'   => FALSE,
            'explain'    => FALSE,
            'sql'        => $sql
        );

        $this->_logs['queries']['messages'][$hash][] = $logItem;
    }

    /**
     * Records the time it takes for an action to occur
     *
     * @param string $name The name of the benchmark
     *
     * @return void
     *
     */
    public function logBenchmark($name)
    {
        $key = 'benchmark_ ' . $name;

        if (isset($this->_logs['benchmarks']['messages'][$key])) {
            $benchKey = md5(microtime(TRUE));

            $this->_logs['benchmarks']['messages'][$benchKey] = $this->_logs['benchmarks']['messages'][$key];
            $this->_logs['benchmarks']['messages'][$benchKey]['end_time'] = microtime(TRUE);
            $this->_logs['benchmarks']['count'] += 1;

            unset($this->_logs['benchmarks']['messages'][$key]);
            return;
        }

        $logItem = array(
            'start_time' => microtime(TRUE),
            'end_time'   => FALSE,
            'name'       => $name
        );

        $this->_logs['benchmarks']['messages'][$key] = $logItem;
    }

    /**
     * Returns all log data
     *
     * @return array
     */
    public function getLogs()
    {
        return $this->_logs;
    }
}