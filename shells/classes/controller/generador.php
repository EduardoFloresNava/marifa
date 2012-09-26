<?php

/**
 * Controlador encargador de generar las clases de redundancia de forma automática.
 *
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */
class Shell_Controller_Generador extends Shell_Controller {

	/**
	 * @var string Descripcion corta del comando.
	 */
	public $descripcion = "Generador de clases de redundancia.";

	/**
	 * @var array Listado de variantes del comando.
	 */
	public $lines = array();

	/**
	 * @var string Descripción detallada del comando.
	 */
	public $help = "Generamos las clases de redundancia de forma automática.";

	/**
	 * @var string Nombre de la clase para solventar problemas de la versión de PHP.
	 */
	protected $class = __CLASS__;

	public function start()
	{
		// Acciones del padre (ayuda y demás).
		parent::start();

		// Definicion de PATH's.
		$this->base_dir = APP_BASE.DS.'base'.DS;
		$this->p_dir = APP_BASE.DS.'marifa'.DS;

		// Listado de path a omitir.
		$this->skip_list = array($this->base_dir.'decoda/');

		// Arreglo de archivos.
		$this->archivos_generados = array();
		$this->archivos_iniciales = array();

		$this->listado_archivos($this->p_dir);

		// Limpiamos el directorio de clases.
		@mkdir($this->p_dir, 0777, TRUE);

		$rst = $this->recursive_search(APP_BASE.DS.'base');

		foreach (array_intersect($this->archivos_iniciales, array_diff($this->archivos_generados, $this->archivos_iniciales)) as $k)
		{
			$this->unlinkr($k);
		}

		foreach (array('CLASES' => 0, 'INTERFACES' => 3) as $n => $v)
		{
			if ($rst[1+$v] == 0)
			{
				$tail = "SIN ERRORES\n";
			}
			else
			{
				$tail = "CORRECTAMENTE\n{$rst[1+$v]} $n CON ERRORES\n";
			}

			$p = $rst[2+$v] > 1 ? 'PROCESADAS' : 'PROCESADA';
			$nn = $rst[2+$v] > 1 ? $n : substr($n, 0, -1);

			if ($rst[0+$v] == 0)
			{
				Shell_Cli::write("{$rst[2+$v]} $nn $p $tail");
			}
			else
			{
				Shell_Cli::write("{$rst[2+$v]}+{$rst[0+$v]} $nn $p $tail");
			}
		}
	}

	protected function make_template($file, $class, $subpackage, $alias)
	{
		$t = "<?php
/**
 * {{FILE}} is part of Marifa.
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
 * @filesource
 * @package		Marifa\Base{{SUBPACKAGE1}}
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Alias de {{BASE_CLASS_NAME}}
 *
 * @package    Marifa\Marifa{{SUBPACKAGE2}}
 */
{{DECLARACION}}";

		$t = str_replace('{{DECLARACION}}', $class, $t);
		$t = str_replace('{{BASE_CLASS_NAME}}', $alias, $t);
		$t = str_replace('{{SUBPACKAGE1}}', trim($subpackage) == '' ? '' : "\n * @subpackage  $subpackage", $t);
		$t = str_replace('{{SUBPACKAGE2}}', trim($subpackage) == '' ? '' : "\n * @subpackage $subpackage", $t);
		$t = str_replace('{{FILE}}', basename($file), $t);

		// Creamos el path.
		$base_dir = dirname($file);
		if ( ! file_exists($base_dir))
		{
			mkdir($base_dir, 0777, TRUE);
		}

		if ( ! file_exists($file))
		{
			if ( ! file_put_contents($file, $t))
			{
				var_dump($file, $t);
				Shell_Cli::write_line(Shell_Cli::getColoredString("ERROR: $file", 'red'));
				return FALSE;
			}
		}
		return TRUE;
	}

	protected function recursive_search($path)
	{
		$rst = array(0, 0, 0, 0, 0, 0);

		$dirs = scandir($path);
		foreach($dirs as $dir)
		{
			if ($dir == '.' || $dir == '..')
			{
				continue;
			}

			if ($path.DS.$dir == $this->base_dir.'view')
			{
				continue;
			}

			if (in_array($path.DS.$dir, $this->skip_list))
			{
				continue;
			}

			$this->archivos_generados[] = DS.substr($path.DS.$dir, strlen($this->base_dir));

			if (is_dir($path.DS.$dir))
			{
				$r = $this->recursive_search($path.DS.$dir);
				foreach($r as $k => $v)
				{
					$rst[$k] += $v;
				}
				continue;
			}

			if (substr($dir, -4) != '.php')
			{
				continue;
			}

			$r = $this->parse_file($path.DS.$dir);
			foreach($r as $k => $v)
			{
				$rst[$k] += $v;
			}
		}
		return $rst;
	}

    /**
	 * Borramos el archivo o directorio de forma recursiva.
	 * @param string $path Directorio a borrar.
	 * @return bool
	 */
	protected function unlinkr($path)
	{
		$lst = scandir($path);

		$rst = TRUE;

		foreach($lst as $file)
		{
			if ($file === '.' || $file === '..')
			{
				continue;
			}

			if (is_dir($path.DS.$file))
			{
				if ( ! $this->unlinkr($path.DS.$file))
				{
					$rst = FALSE;
				}
				rmdir($path.DS.$file);
			}
			else
			{
				if ( ! unlink($path.DS.$file))
				{
					$rst = FALSE;
				}
			}
		}
		return $rst;
	}

    protected function parse_file($f)
	{
		// Buscamos clases en ese archivo.
		//echo "BUSCANDO CLASES EN: $f:\n";
		$cl = $this->find_clases($f);

		$rst = array(0, 0, 0, 0, 0, 0);

		// Procesamos las clases.
		foreach($cl['clases'] as $c)
		{
			// Nombre y tipo de la clase.
			if (is_array($c))
			{
				$c_d = $c[1]; // Nombre de la clase.
				$l = $c[0].' '; // Tipo de la clase.
			}
			else
			{
				$c_d = $c; // Nombre de la clase.
				$l = ''; // Tipo de la clase.
			}

			// Generamos el nombre del archivo.
			$class_name = $this->p_dir.$this->r_inflector($c_d).'.php';

			// Subpackage.
			preg_match('/(\s)?\*(\s)?@subpackage(\s)+([^\s]+)\n/', file_get_contents($f), $rrst);

			if (isset($rrst[4]))
			{
				$s_p = $rrst[4];
			}
			else
			{
				$s_p = substr(dirname($class_name), strlen($this->p_dir));
				$s_p = preg_replace('/([\/])\s*(\w)/e', "strtoupper('\\1\\2')", ucfirst(strtolower($s_p)));
				$s_p = preg_replace('/[\/]+/', '\\', $s_p);
			}

			// Generamos la linea.
			$l .= "class $c_d extends Base_$c_d {}";

			if (file_exists($class_name))
			{
				$rst[2]++;
			}
			else
			{
				if ($this->make_template($class_name, $l, $s_p, 'Base_'.$c_d))
				{
					$rst[0]++;
				}
				else
				{
					$rst[1]++;
				}
			}
		}



		// Procesamos las interfaces.
		foreach($cl['interfaces'] as $c)
		{
			// Generamos el nombre del archivo.
			$class_name = $this->p_dir.$this->r_inflector($c).'.php';

			// Subpackage.
			$s_p = substr(dirname($class_name), strlen($this->p_dir));
			$s_p = preg_replace('/([\/])\s*(\w)/e', "strtoupper('\\1\\2')", ucfirst(strtolower($s_p)));
			$s_p = preg_replace('/[\/]+/', '\\', $s_p);

			$l = "interface $c extends Base_$c {}";

			if (file_exists($class_name))
			{
				$rst[5]++;
				continue;
			}

			if ($this->make_template($class_name, $l, $s_p, 'Base_'.$c))
			{
				$rst[3]++;
			}
			else
			{
				$rst[4]++;
			}
		}

		return $rst;
	}

	/**
	 * Buscamos clases e interfaces en un archivo.
	 * @param string $file Path donde se encuentra el archivo.
	 * @return array Arreglo con los elementos encontrados.
	 */
	protected function find_clases($file)
	{
		// Cargo el contenido del archivo.
		$data = file_get_contents($file);

		// Realizo la busqueda por medio de una expresion regular.
		$a = array();
		preg_match_all('/(abstract|final){0,1}\s+(class|interface)\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', $data, $a, PREG_SET_ORDER);

		// Inicio arreglo con clase e interfaces encontradas.
		$rst = array('clases' => array(), 'interfaces' => array());

		// Busco entre los resultados de la expresion regular.
		foreach ($a as $v)
		{
			// Verifico clase miembro de Base_
			if (substr($v[3], 0, 5) !== 'Base_')
			{
				continue;
			}
			if ($v[2] == 'class')
			{
				// Clase encontrada.
				if ($v[1] == 'final' || $v[1] == 'abstract')
				{
					// Tiene atributo final o abstract.
					$rst['clases'][] = array($v[1], substr($v[3], 5));
				}
				else
				{
					// Sin atributos extras.
					$rst['clases'][] = substr($v[3], 5);
				}
			}
			elseif ($v[2] == 'interface')
			{
				// Interface encontrada.
				$rst['interfaces'][] = substr($v[3], 5);
			}
		}

		return $rst;
	}

	/**
	 * Convertimos un path a un nombre de clase.
	 * @param string $class Lugar del archivo.
	 * @return string
	 */
	protected function inflector($class)
	{
		// Barras a guiones.
		$class = str_replace('/', '_', $class);

		// Quitamos extension.
		$class = str_replace('.php', '', $class);

		// Aplicamos CamelCase
		for($i = 0; $i < strlen($class); $i++)
		{
			// Despues de _ Mayuscula.
			if($class{$i} == '_')
			{
				$class{$i+1} = strtoupper($class{$i+1});
			}
		}

		// Primer letra mayuscula.
		$class{0} = strtoupper($class{0});

		// Devolvemos el resultado.
		return $class;
	}

	protected function r_inflector($class)
	{
		return strtolower(preg_replace('/\/+/', '/', preg_replace('/\_/', '/', $class)));
	}

	protected function listado_archivos($path)
	{
		$lst = scandir($path);

		foreach($lst as $file)
		{
			if ($file === '.' || $file === '..')
			{
				continue;
			}

			$this->archivos_iniciales[] = substr($path.'/'.$file, strlen($this->p_dir));

			if (is_dir($path.DS.$file))
			{
				$this->listado_archivos($path.DS.$file);
			}
		}
	}

}