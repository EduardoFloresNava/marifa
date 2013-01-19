<?php
/**
 * contenido.php is part of Marifa.
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
 * Controlador encargado de generar contenido de forma automática.
 *
 * @author  Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since   Versión 0.1
 * @package Marifa\Shell
 */
class Shell_Controller_Contenido extends Shell_Controller {

	/**
	 * @var string Descripcion corta del comando.
	 */
	public $descripcion = "Generador de contenido aleatorio para pruebas.";

	/**
	 * @var array Listado de variantes del comando.
	 */
	public $lines = array(
		'usuario',
		'post',
		'foto',
		'publicacion',
	);

	/**
	 * @var string Descripción detallada del comando.
	 */
	public $help = "Generador de contenido aleatorio para pruebas. Se pueden generar usuarios, posts, fotos y publicaciones.
	usuario      Generamos usuarios.
	post         Generamos posts.
	foto         Generamos fotos.
	publicacion  Generamos publicaciones.";

	/**
	 * @var string Nombre de la clase para solventar problemas de la versión de PHP.
	 */
	protected $class = __CLASS__;

	/**
	 * Acción de inicio del controlador.
	 */
	public function start()
	{
		// Acciones del padre (ayuda y demás).
		parent::start();

		// Selecciono la acción.
		$accion = isset($this->params[1]) ? $this->params[1] : NULL;
		switch ($accion)
		{
			case 'usuario':
				// Conectamos a la base de datos.
				Shell_Cli::write_line(Shell_Cli::get_colored_string('Conectando a la base de datos...', 'yellow'));
				Database::get_instance();
				Shell_Cli::write_line(Shell_Cli::get_colored_string('Conección correcta. Continuando el proceso.', 'green'));

				// Pedimos contraseña para los usuarios.
				$password = '';
				while ($password == '')
				{
					$password = trim(Shell_Cli::read_value('Contraseña para los usuarios', 'password'));
				}

				// Prefijo para los usuarios.
				$prefijo = trim(Shell_Cli::read_value('Prefijo para los usuarios', 'auto_'));

				// Cantidad de usuarios.
				$cantidad = 0;
				while ($cantidad <= 0)
				{
					$cantidad = (int) Shell_Cli::read_value('Cantidad de usuarios', '20');
				}

				// Cargo rango por defecto.
				$model_config = new Model_Configuracion;
				$rango_defecto = (int) $model_config->get('rango_defecto', 1);
				unset($model_config);

				// Nombre del rango por defecto.
				$model_rango = new Model_Usuario_Rango($rango_defecto);

				// Verifico si quiere usar el rango por defecto.
				if (Shell_Cli::read_value('Desea usar el rango "'.Shell_Cli::get_colored_string($model_rango->nombre, 'yellow').'" (Y/N)', 'Y', array('Y', 'N')) == 'Y')
				{
					$rango = $rango_defecto;
					$rango_string = $model_rango->nombre;
				}
				else
				{
					// Obtengo listado de rangos.
					$rangos = Database::get_instance()->query('SELECT id, nombre FROM usuario_rango')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_STRING));

					// Pido el rango a usar.
					$rango = array_search(Shell_Cli::option(array_values($rangos), 'Rango para los usuarios'), $rangos);
					$rango_string = $rangos[$rango];

					// Limpio memoria.
					unset($rangos);
				}
				unset($model_rango, $rango_defecto);

				Shell_Cli::write_line('');
				Shell_Cli::write_line('');
				Shell_Cli::write_line('Resumen de datos a crear:');
				Shell_Cli::write_line('    Cantidad:   '.Shell_Cli::get_colored_string($cantidad, 'yellow'));
				Shell_Cli::write_line('    Contraseña: '.Shell_Cli::get_colored_string($password, 'yellow'));
				Shell_Cli::write_line('    Rango:      '.Shell_Cli::get_colored_string($rango_string, 'yellow'));
				Shell_Cli::write_line('    Prefijo:    '.Shell_Cli::get_colored_string($prefijo, 'yellow'));
				Shell_Cli::write_line('');

				if (Shell_Cli::read_value("Los datos son correctos (Y/N)", NULL, array('Y', 'N')) == 'Y')
				{
					// Informo que está en proceso.
					Shell_Cli::write_line(Shell_Cli::get_colored_string('Creando usuarios...', 'yellow'));

					// Creamos los usuarios.
					$this->crear_usuarios($cantidad, $prefijo, $password, $rango);

					// Informo resultado.
					Shell_Cli::write_line(Shell_Cli::get_colored_string('Usuarios creados correctamente.', 'green'));
				}
				break;
			case 'post':
				break;
			case 'foto':
				break;
			case 'publicacion':
				break;
			default:
				Shell_Cli::write_line(Shell_Cli::get_colored_string('La acción elegida es incorrecta.', 'red'));
		}
	}

	/**
	 * Creamos un conjunto de usuarios de prueba.
	 * @param int $cantidad Cantidad de usuarios a crear.
	 * @param string $prefijo Prefijo para los usuarios.
	 * @param string $password Contraseña para los usuarios.
	 * @param int $rango Rango para los usuarios.
	 */
	private function crear_usuarios($cantidad, $prefijo, $password, $rango)
	{
		for ($i = 0; $i < $cantidad; $i++)
		{
			// Genero los parámetros.
			$nick = $prefijo.Texto::random_string(10, 'abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789');

			// Creo el usuario.
			$model_usuario = new Model_Usuario;
			$model_usuario->register($nick, $nick.'@example.com', $password, $rango);

			// Realizo la activación.
			$model_usuario->load_by_nick($nick);
			$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
		}
	}
}