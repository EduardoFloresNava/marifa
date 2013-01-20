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
		'(usuario|post|foto|publicacion) [-d|--default]',
	);

	/**
	 * @var string Descripción detallada del comando.
	 */
	public $help = "Generador de contenido aleatorio para pruebas. Se pueden generar usuarios, posts, fotos y publicaciones.
	usuario      Generamos usuarios.
	post         Generamos posts.
	foto         Generamos fotos.
	publicacion  Generamos publicaciones.

	-d, --default  Se utilizan los parámetros por defecto para generar el contenido.";

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

		// Verifico si es correcta y realizo acciones comunes.
		if (in_array($accion, array('usuario', 'post', 'foto', 'publicacion')))
		{
			// Conectamos a la base de datos.
			Shell_Cli::write_line(Shell_Cli::get_colored_string('Conectando a la base de datos...', 'yellow'));
			Database::get_instance();
			Shell_Cli::write_line(Shell_Cli::get_colored_string('Conección correcta. Continuando el proceso.', 'green'));

			// Verifico si uso por defecto o interactivo.
			$use_default = isset($this->params['d']) || isset($this->params['default']);
		}

		switch ($accion)
		{
			case 'usuario':
				if ($use_default)
				{
					$password = '';
					$prefijo = 'auto_';
					$cantidad = 20;

					// Cargo rango por defecto.
					$model_config = new Model_Configuracion;
					$model_rango = new Model_Usuario_Rango( (int) $model_config->get('rango_defecto', 1));
					$rango = $model_rango->id;
					$rango_string = $model_rango->nombre;
					unset($model_config, $model_rango);
				}
				else
				{
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
				}

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
					// Creamos los usuarios.
					$this->crear_usuarios($cantidad, $prefijo, $password, $rango);
				}
				break;
			case 'post':
				if ($use_default)
				{
					$prefijo = 'auto_';
					$cantidad = 20;
					$cantidad_seguidores = 5;
					$cantidad_favoritos = 5;
					$cantidad_puntos = 30;
					$cantidad_comentarios = 20;
					// Cargo categorias.
					$categorias = Database::get_instance()->query('SELECT id, nombre FROM categoria')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_STRING));
				}
				else
				{
					// Prefijo para los posts.
					$prefijo = trim(Shell_Cli::read_value('Prefijo para los posts', 'auto_'));

					// Cantidad de posts.
					do {
						$cantidad = (int) Shell_Cli::read_value('Cantidad de posts', '20');
					} while ($cantidad <= 0);

					// Cantidad de seguidores del posts.
					do {
						$cantidad_seguidores = (int) Shell_Cli::read_value('Cantidad de seguidores del post', '5');
					} while ($cantidad_seguidores <= 0);

					// Cantidad de favoritos del posts.
					do {
						$cantidad_favoritos = (int) Shell_Cli::read_value('Cantidad de favoritos del post', '5');
					} while ($cantidad_favoritos <= 0);

					// Cantidad de puntos del posts.
					do {
						$cantidad_puntos = (int) Shell_Cli::read_value('Cantidad de puntos del post', '30');
					} while ($cantidad_puntos <= 0);

					// Cantidad de comentario.
					do {
						$cantidad_comentarios = (int) Shell_Cli::read_value('Cantidad de comentarios del post', '20');
					} while ($cantidad_comentarios < 0);

					// Cargo categorias.
					$categorias = Database::get_instance()->query('SELECT id, nombre FROM categoria')->get_pairs(array(Database_Query::FIELD_INT, Database_Query::FIELD_STRING));

					// Verifico si es una en particular.
					if (Shell_Cli::read_value("Desea usar una categoria en particular (Y/N)", 'N', array('Y', 'N')) == 'Y')
					{
						// Pido el rango a usar.
						$categoria = array_search(Shell_Cli::option(array_values($categorias), 'Categoría para los posts'), $categorias);
						$categoria_string = $categorias[$categoria];

						unset($categorias);
					}
				}

				// Informe de selección.
				Shell_Cli::write_line('');
				Shell_Cli::write_line('');
				Shell_Cli::write_line('Resumen de datos a crear:');
				Shell_Cli::write_line('    Cantidad:    '.Shell_Cli::get_colored_string($cantidad, 'yellow'));
				Shell_Cli::write_line('    Seguidores:  '.Shell_Cli::get_colored_string($cantidad_seguidores, 'yellow'));
				Shell_Cli::write_line('    Favoritos:   '.Shell_Cli::get_colored_string($cantidad_favoritos, 'yellow'));
				Shell_Cli::write_line('    Puntos:      '.Shell_Cli::get_colored_string($cantidad_puntos, 'yellow'));
				Shell_Cli::write_line('    Comentarios: '.Shell_Cli::get_colored_string($cantidad_comentarios, 'yellow'));
				Shell_Cli::write_line('    Categoria:   '.Shell_Cli::get_colored_string(isset($categorias) ? '<ALEATORIO>' : $categoria_string, 'yellow'));
				Shell_Cli::write_line('    Prefijo:     '.Shell_Cli::get_colored_string($prefijo, 'yellow'));
				Shell_Cli::write_line('');

				if (Shell_Cli::read_value("Los datos son correctos (Y/N)", NULL, array('Y', 'N')) == 'Y')
				{
					// Creamos los usuarios.
					$this->crear_posts($cantidad, $cantidad_seguidores, $cantidad_favoritos, $cantidad_puntos, $cantidad_comentarios, isset($categorias) ? $categorias : $categoria, $prefijo);
				}
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
		// Configuro barra de progreso.
		Shell_Cli_ProgressBar::start($cantidad, Shell_Cli::get_colored_string('Creando usuarios...', 'yellow'));

		declare(ticks=5);

		// Configuro función de ticks.
		register_tick_function('Shell_Cli_ProgressBar::show_bar');

		for ($i = 0; $i < $cantidad; $i++)
		{
			// Modelo de usuarios.
			$model_usuario = new Model_Usuario;

			// Genero los parámetros.
			do {
				$nick = $prefijo.Texto::random_string(10, 'abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789');
			} while ($model_usuario->exists_nick($nick));

			// Creo el usuario.
			$model_usuario->register($nick, $nick.'@example.com', $password, $rango);

			// Realizo la activación.
			$model_usuario->load_by_nick($nick);
			$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);

			Shell_Cli_ProgressBar::next();
		}
		unregister_tick_function('Shell_Cli_ProgressBar::show_bar');

		Shell_Cli_ProgressBar::set_message(Shell_Cli::get_colored_string('Usuarios creados correctamente', 'green'));
		Shell_Cli_ProgressBar::show_bar();
	}

	private function crear_posts($cantidad, $cantidad_seguidores, $cantidad_favoritos, $cantidad_puntos, $cantidad_comentarios, $categorias, $prefijo)
	{
		// Obtengo listado de usuarios.
		$usuarios = Database::get_instance()->query('SELECT id FROM usuario')->get_pairs(Database_Query::FIELD_INT);
		$cantidad_usuarios = count($usuarios);

		// Configuro barra de progreso.
		Shell_Cli_ProgressBar::start($cantidad, Shell_Cli::get_colored_string('Creando posts...', 'yellow'));

		// Configuro función de ticks.
		register_tick_function('Shell_Cli_ProgressBar::show_bar');

		// Genero los posts.
		for ($j = 0; $j < $cantidad; $j++)
		{
			// Cargo modelos.
			$model_post = new Model_Post;
			$model_suceso = new Model_Suceso;

			// Título del post.
			$titulo = $prefijo.Texto::random_string(50, 'abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789 ');

			// Autor.
			$autor = $usuarios[mt_rand(0, $cantidad_usuarios - 1)];

			// Categoria.
			if (is_array($categorias))
			{
				$categorias = array_values($categorias);
				$categoria = $categorias[mt_rand(0, count($categorias) - 1)];
			}
			else
			{
				$categoria = $categorias;
			}

			// Privado.
			$privado = (bool) mt_rand(0, 1);

			// Patrocinado.
			$patrocinado = (bool) mt_rand(0, 1);

			// Comentar.
			$comentar = (bool) mt_rand(0, 1);

			// Estado.
			$estado = Model_Post::ESTADO_ACTIVO;

			$post_id = $model_post->crear($autor, $titulo, Shell_Loremipsum::get_content(50, 'plain'), $categoria, $privado, $patrocinado, FALSE, $comentar, $estado);

			// Cargo el post.
			$model_post->load(array('id' => $post_id));

			// Agrego etiquetas.
			$model_post->agregar_etiqueta(array('aleatorio', 'aleatorio_1', 'aleatorio_2', 'aleatorio_3', 'aleatorio_4'));

			// Agrego suceso.
			$model_suceso->crear($autor, 'post_nuevo', FALSE, $post_id);

			// Actualizar rango y medallas.
			//Usuario::usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_POST);
			//Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_POSTS);

			// Genero listado de usuarios que pueden interactuar (puntuar, agregar a favoritos, seguir, etc).
			$usuarios_interactuar = array_diff($usuarios, array($autor));
			shuffle($usuarios_interactuar);

			// Agrego seguidores.
			$seguidores = array_slice($usuarios_interactuar, 0, $cantidad_seguidores);

			foreach ($seguidores as $v) {
				$model_post->seguir($v);

				// Actualizo medallas.
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_SEGUIDORES);

				// Enviamos el suceso.
				$model_suceso->crear($autor, 'post_seguir', TRUE, $model_post->id, $v);
				$model_suceso->crear($v, 'post_seguir', FALSE, $model_post->id, $v);
			}
			unset($seguidores);

			// Agrego favoritos.
			shuffle($usuarios_interactuar);
			$favoritos = array_slice($usuarios_interactuar, 0, $cantidad_favoritos);

			foreach ($favoritos as $v) {
				$model_post->favorito($v);

				// Actualizo medallas.
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_FAVORITOS);

				// Enviamos el suceso.
				$model_suceso->crear($autor, 'post_favorito', TRUE, $model_post->id, $v);
				$model_suceso->crear($v, 'post_favorito', FALSE, $model_post->id, $v);
			}
			unset($favoritos);

			// Agrego puntos.
			$ppu = ceil($cantidad_puntos / count($usuarios_interactuar));
			shuffle($usuarios_interactuar);

			$i = 0;
			do {
				// Damos los puntos.
				$model_post->dar_puntos($usuarios_interactuar[$i], min(array($ppu, $cantidad_puntos)));

				// Verifico actualización del rango.
				$model_post->usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_PUNTOS);

				// Verifico actualización medallas.
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_PUNTOS);
				$model_post->usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_PUNTOS);

				// Enviamos el suceso.
				$model_suceso->crear($autor, 'post_puntuar', TRUE, $model_post->id, $usuarios_interactuar[$i], min(array($ppu, $cantidad_puntos)));
				$model_suceso->crear($usuarios_interactuar[$i], 'post_puntuar', FALSE, $model_post->id, $usuarios_interactuar[$i], min(array($ppu, $cantidad_puntos)));

				$cantidad_puntos -= $ppu;
				$i++;
			} while ($cantidad_puntos > 0);
			unset($ppu, $i);

			// Agrego comentarios.
			for ($i = 0; $i < $cantidad_comentarios; $i++)
			{
				$c_a = $usuarios[mt_rand(0, $cantidad_usuarios - 1)];

				// Insertamos el comentario.
				$id = $model_post->comentar($c_a, Shell_Loremipsum::get_content(10, 'plain'));

				if ($c_a != $model_post->usuario_id)
				{
					$model_suceso->crear($model_post->usuario_id, 'post_comentario_crear', TRUE, $id);
					$model_suceso->crear($c_a, 'post_comentario_crear', FALSE, $id);
				}
				else
				{
					$model_suceso->crear($model_post->usuario_id, 'post_comentario_crear', FALSE, $id);
				}

				// Envio sucesos de citas.
				//Decoda::procesar($comentario, FALSE);

				// Verifico actualización del rango.
				//Usuario::usuario()->actualizar_rango(Model_Usuario_Rango::TIPO_COMENTARIOS);

				// Verifico actualización de medallas.
				//Usuario::usuario()->actualizar_medallas(Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_POSTS);

				// Actualizar medallas post.
				$model_post->actualizar_medallas(Model_Medalla::CONDICION_POST_COMENTARIOS);
			}

			Shell_Cli_ProgressBar::next();
		}
		unregister_tick_function('Shell_Cli_ProgressBar::show_bar');

		Shell_Cli_ProgressBar::set_message(Shell_Cli::get_colored_string('Posts creados correctamente', 'green'));
		Shell_Cli_ProgressBar::show_bar();
	}
}