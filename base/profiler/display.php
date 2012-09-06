<?php
/**
 * display.php is part of Marifa.
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
class Base_Profiler_Display {

	/**
	 * Outputs the HTML, CSS and JavaScript that builds the console display
	 *
	 * @static
	 *
	 * @param      $data
	 * @param bool $returnAsString
	 *
	 * @return mixed
	 */
	public static function display($data, $returnAsString = FALSE)
	{
		$output = '';

		$output .= '<div id="profiler-container" class="hideDetails">';
		$output .= '<div id="profiler" class="console">';

		$output .= self::getMainTabs($data);

		$output .= self::getConsoleTab($data);
		$output .= self::getLoadTimeTab($data);
		$output .= self::getDatabaseTab($data);
		$output .= self::getMemoryTab($data);
		$output .= self::getFilesTab($data);
		$output .= self::getFooter();

		$output .= '</div></div>';

		if ($returnAsString)
		{
			return $output;
		}
		else
		{
			echo $output;
			return NULL;
		}
	}

	/**
	 * Main tabs HTML
	 * @static
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function getMainTabs($data)
	{
		$logCount = count($data['logs']['console']['messages']);
		$fileCount = count($data['files']);
		$memoryUsed = $data['memoryTotals']['used'];
		$queryCount = $data['queryTotals']['all'];
		$speedTotal = $data['speedTotals']['total'];

		$tabs = array(
			'console' => array('title' => 'Console', 'value' => $logCount),
			'speed' => array('title' => 'Time', 'value' => $speedTotal),
			'queries' => array('title' => 'Database', 'value' => $queryCount),
			'memory' => array('title' => 'Memory', 'value' => $memoryUsed),
			'files' => array('title' => 'Files', 'value' => $fileCount),
		);

		$output = '<div id="profiler-metrics">';
		foreach ($tabs as $tabId => $tabData)
		{
			$output .= '<div id="'.$tabId.'" class="tab">';
			$output .= '<var>'.$tabData['value'].'</var>';
			$output .= '<h4>'.$tabData['title'].'</h4>';
			$output .= '</div>';
		}
		$output .= '<div style="clear: both;"></div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Console tab HTML
	 * @static
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function getConsoleTab($data)
	{
		$output = '<div id="profiler-console" class="profiler-box">';

		if (count($data['logs']['console']['messages']) == 0)
		{
			$output .= '<h3>This panel has no log items.</h3>';
		}
		else
		{
			$output .= '<table class="side" cellspacing="0">';
			$output .= '<tr>';
			$output .= '<td class="console-log" id="console-log"><var>'.$data['logs']['console']['count']
					.'</var><h4>Logs</h4></td>';
			$output .= '<td class="console-errors" id="console-error"><var>'.$data['logs']['errors']['count']
					.'</var> <h4>Errors</h4></td>';
			$output .= '</tr>';
			$output .= '<tr>';
			$output .= '<td class="console-memory" id="console-memory"><var>'.$data['logs']['memory']['count']
					.'</var> <h4>Memory</h4></td>';
			$output .= '<td class="console-speed" id="console-speed"><var>'.$data['logs']['speed']['count']
					.'</var> <h4>Speed</h4></td>';
			$output .= '</tr>';
			$output .= '<tr>';
			$output
					.= '<td class="console-benchmarks" id="console-benchmark"><var>'.$data['logs']['benchmarks']['count']
					.'</var><h4>Benchmarks</h4></td>';
			$output .= '</tr>';
			$output .= '</table>';
			$output .= '<table class="main" cellspacing="0">';

			$class = '';
			foreach ($data['logs']['console']['messages'] as $log)
			{
				$output .= '<tr class="log-'.$log['type'].'">';
				$output .= '<td class="type">'.$log['type'].'</td>';
				$output .= '<td class="data '.$class.'">';

				$output .= '<div>';

				switch ($log['type'])
				{
					case 'log':
						$output .= '<pre>'.$log['data'].'</pre>';
						break;
					case 'memory':
						$output .= '<pre>'.$log['data'].'</pre>';
						if (isset($log['dataType']) && $log['dataType'] != 'NULL')
						{
							$output .= ' <em>'.$log['dataType'].'</em>: ';
						}
						$output .= $log['name'];
						break;
					case 'benchmark':
					case 'speed':
						$output .= '<pre>'.$log['data'].'</pre> <em>'.$log['name'].'</em>';
						break;
					case 'error':
						$output .= '<em>Line '.$log['line'].'</em> : '.$log['data'];
						$output .= ' <pre>'.$log['file'].'</pre>';
						break;
				}

				$output .= '</div></td></tr>';
				$class = ($class == '') ? 'alt' : '';
			}

			$output .= '</table>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Load Time tab HTML
	 * @static
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function getLoadTimeTab($data)
	{
		$output = '<div id="profiler-speed" class="profiler-box">';
		if ($data['logs']['speed']['count'] == 0)
		{
			$output .= '<h3>This panel has no log items.</h3>';
		}
		else
		{
			$output .= '<table class="side" cellspacing="0">';
			$output .= '<tr><td><var>'.$data['speedTotals']['total']
					.'</var><h4>Load Time</h4></td></tr>';
			$output .= '<tr><td class="alt"><var>'.$data['speedTotals']['allowed']
					.'</var> <h4>Max Execution Time</h4></td></tr>';
			$output .= '</table>';
			$output .= '<table class="main" cellspacing="0">';

			$class = '';
			foreach ($data['logs']['console']['messages'] as $log)
			{
				if (isset($log['type']) && $log['type'] == 'speed')
				{
					$output .= '<tr class="log-speed"><td class="'.$class.'">';
					$output .= '<div><pre>'.$log['data'].'</pre> <em>'.$log['name'].'</em></div>';
					$output .= '</td></tr>';
					$class = ($class == '') ? 'alt' : '';
				}
			}

			$output .= '</table>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Database tab HTML
	 * @static
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function getDatabaseTab($data)
	{
		$output = '<div id="profiler-queries" class="profiler-box">';
		if ($data['queryTotals']['count'] == 0)
		{
			$output .= '<h3>This panel has no log items.</h3>';
		}
		else
		{
			$output .= '<table class="side" cellspacing="0">';
			$output .= '<tr><td><var>'.$data['queryTotals']['count'].'</var><h4>Total Queries</h4></td></tr>';
			$output
					.= '<tr><td class="alt"><var>'.$data['queryTotals']['time'].'</var> <h4>Total Time</h4></td></tr>';
			$output
					.= '<tr><td><var>'.$data['queryTotals']['duplicates'].'</var> <h4>Duplicates</h4></td></tr>';
			$output .= '<tr><td class="alt">';
			$output .= '<var>'.$data['queryTotals']['types']['select']['total'].' ('
					.$data['queryTotals']['types']['select']['percentage'].'%)</var>';
			$output .= '<var>'.$data['queryTotals']['types']['select']['time'].' ('
					.$data['queryTotals']['types']['select']['time_percentage'].'%)</var>';
			$output .= '<h4>Selects</h4>';
			$output .= '</td></tr>';
			$output .= '<tr><td>';
			$output .= '<var>'.$data['queryTotals']['types']['update']['total'].' ('
					.$data['queryTotals']['types']['update']['percentage'].'%)</var>';
			$output .= '<var>'.$data['queryTotals']['types']['update']['time'].' ('
					.$data['queryTotals']['types']['update']['time_percentage'].'%)</var>';
			$output .= '<h4>Updates</h4>';
			$output .= '</td></tr>';
			$output .= '<tr><td class="alt">';
			$output .= '<var>'.$data['queryTotals']['types']['insert']['total'].' ('
					.$data['queryTotals']['types']['insert']['percentage'].'%)</var>';
			$output .= '<var>'.$data['queryTotals']['types']['insert']['time'].' ('
					.$data['queryTotals']['types']['insert']['time_percentage'].'%)</var>';
			$output .= '<h4>Inserts</h4>';
			$output .= '</td></tr>';
			$output .= '<tr><td>';
			$output .= '<var>'.$data['queryTotals']['types']['delete']['total'].' ('
					.$data['queryTotals']['types']['delete']['percentage'].'%)</var>';
			$output .= '<var>'.$data['queryTotals']['types']['delete']['time'].' ('
					.$data['queryTotals']['types']['delete']['time_percentage'].'%)</var>';
			$output .= '<h4>Deletes</h4>';
			$output .= '</td></tr>';
			$output .= '</table>';
			$output .= '<table class="main" cellspacing="0">';

			$class = '';
			foreach ($data['queries'] as $query)
			{
				$output .= '<tr><td class="'.$class.'">'.$query['sql'];
				if ($query['duplicate'])
				{
					$output .= '<strong style="display: block; color: #B72F09;">** Duplicate **</strong>';
				}

				if (isset($query['explain']) && $query['explain'])
				{
					$explain = $query['explain'];
					$output .= '<em>';

					if (isset($explain['possible_keys']))
					{
						$output .= 'Possible keys: <b>'.$explain['possible_keys'].'</b> &middot;';
					}

					if (isset($explain['key']))
					{
						$output .= 'Key Used: <b>'.$explain['key'].'</b> &middot;';
					}

					if (isset($explain['type']))
					{
						$output .= 'Type: <b>'.$explain['type'].'</b> &middot;';
					}

					if (isset($explain['rows']))
					{
						$output .= 'Rows: <b>'.$explain['rows'].'</b> &middot;';
					}

					$output .= 'Speed: <b>'.$query['time'].'</b>';
					$output .= '</em>';
				}
				else
				{
					if (isset($query['time']))
					{
						$output .= '<em>Speed: <b>'.$query['time'].'</b></em>';
					}
				}

				if (isset($query['profile']) && is_array($query['profile']))
				{
					$output .= '<div class="query-profile"><h4>&#187; Show Query Profile</h4>';
					$output .= '<table style="display: none">';

					foreach ($query['profile'] as $line)
					{
						$output
								.= '<tr><td><em>'.$line['Status'].'</em></td><td>'.$line['Duration'].'</td></tr>';
					}

					$output .= '</table>';
					$output .= '</div>';
				}

				$output .= '</td></tr>';
				$class = ($class == '') ? 'alt' : '';
			}

			$output .= '</table>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Memory tab HTML
	 * @static
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function getMemoryTab($data)
	{
		$output = '<div id="profiler-memory" class="profiler-box">';
		if ($data['logs']['memory']['count'] == 0)
		{
			$output .= '<h3>This panel has no log items.</h3>';
		}
		else
		{
			$output .= '<table class="side" cellspacing="0">';
			$output .= '<tr><td><var>'.$data['memoryTotals']['used'].'</var><h4>Used Memory</h4></td></tr>';
			$output .= '<tr><td class="alt"><var>'.$data['memoryTotals']['total']
					.'</var> <h4>Total Available</h4></td></tr>';
			$output .= '</table>';
			$output .= '<table class="main" cellspacing="0">';

			$class = '';
			foreach ($data['logs']['console']['messages'] as $log)
			{
				if (isset($log['type']) && $log['type'] == 'memory')
				{
					$output .= '<tr class="log-message">';
					$output .= '<td class="'.$class.'"><b>'.$log['data'].'</b>';
					if (isset($log['dataType']) && $log['dataType'] != 'NULL')
					{
						$output .= '<em>'.$log['dataType'].'</em>: ';
					}
					$output .= $log['name'].'</td>';
					$output .= '</tr>';
					$class = ($class == '') ? 'alt' : '';
				}
			}

			$output .= '</table>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Files tab HTML
	 * @static
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function getFilesTab($data)
	{
		$output = '<div id="profiler-files" class="profiler-box">';
		if ($data['fileTotals']['count'] == 0)
		{
			$output .= '<h3>This panel has no log items.</h3>';
		}
		else
		{
			$output .= '<table class="side" cellspacing="0">';
			$output .= '<tr><td><var>'.$data['fileTotals']['count']
					.'</var><h4>Total Files</h4></td></tr>';
			$output .= '<tr><td class="alt"><var>'.$data['fileTotals']['size']
					.'</var> <h4>Total Size</h4></td></tr>';
			$output .= '<tr><td><var>'.$data['fileTotals']['largest']
					.'</var> <h4>Largest</h4></td></tr>';
			$output .= '</table>';
			$output .= '<table class="main" cellspacing="0">';

			$class = '';
			foreach ($data['files'] as $file)
			{
				$output
						.= '<tr><td class="'.$class.'"><b>'.$file['size'].'</b> '.$file['name'].'</td></tr>';
				$class = ($class == '') ? 'alt' : '';
			}

			$output .= '</table>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Footer HTML
	 * @static
	 * @return string
	 */
	public static function getFooter()
	{
		$output = '<div id="profiler-footer">';
		$output .= '<div class="credit"><a href="https://github.com/MAXakaWIZARD/PHP-Profiler" target="_blank"><strong>PHP</strong>&nbsp;Profiler</a></div>';
		$output .= '<div class="actions">';
		$output .= '<a class="detailsToggle">Details</a>';
		$output .= '<a class="heightToggle">Toggle Height</a>';
		$output .= '</div>';
		$output .= '<div style="clear: both;"></div>';
		$output .= '</div>';

		return $output;
	}

}