<?php
/**
 * home.php is part of Marifa.
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
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador de la portada.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Home extends Controller {

	/**
	 * Submenu de la portada.
	 * @param string $selected Elemento seleccionado.
	 */
	public static function submenu($selected = NULL)
	{
		// Creo el menu.
		$menu = new Menu('submenu_home');

		// Listado de elemento OFFLINE.
		$menu->element_set('Inicio', '/', 'inicio');
		$menu->element_set('Usuarios', '/home/usuarios/', 'usuarios');
		$menu->element_set('Buscador', '/buscador/', 'buscador');

		// Listado de elementos ONLINE.
		if (Usuario::is_login())
		{
			$menu->element_set('Agregar Post', '/post/nuevo/', 'nuevo');
		}

		return $menu->as_array($selected == NULL ? 'inicio' : $selected);
	}

	/**
	 * Obtenemos un CAPTCHA.
	 */
	public function action_captcha($width, $heigth)
	{
		// Evito salida de la plantilla.
		$this->template = NULL;

		// Obtengo tamaño del captcha.
		$width = abs( (int) $width);
		$heigth = abs( (int) $heigth);

		// Valido.
		$width = $width < 50 ? 100 : $width;
		$heigth = $heigth < 20 ? 50 : $heigth;

		// Cargo archivo de terceros.
		include_once(VENDOR_PATH.'securimage'.DS.'securimage.php');

		// Genero el CAPTCHA
		$img = new securimage;
		$img->image_height = $heigth;
		$img->image_width = $width;
		$img->code_length = ceil($width / $heigth * .7);
		$img->show();

		// Evito salida de depuración.
		exit;
	}

	/**
	 * Portada del sitio.
	 * @param int $pagina Número de página para lo últimos posts.
	 * @param string $categoria Categoria de los posts. Si no se especifica se toman todas.
	 */
	public function action_index($pagina, $categoria = NULL)
	{
		// Cargamos la portada.
		$portada = View::factory('home/index');

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', self::submenu('inicio'));

		// Verifico categoria.
		if ($categoria !== NULL)
		{
			// Verifico formato.
			if ( ! preg_match('/[a-z0-9_]+/i', $categoria))
			{
				add_flash_message(FLASH_ERROR, 'La categoría no es correcta.');
				Request::redirect('/post/'.$pagina);
			}

			// Cargo la categoria.
			$model_categoria = new Model_Categoria;

			// Verifico sea válida.
			if ( ! $model_categoria->existe_seo($categoria))
			{
				add_flash_message(FLASH_ERROR, 'La categoría no es correcta.');
				Request::redirect('/post/'.$pagina);
			}
			else
			{
				// Cargo la categoria.
				$model_categoria->load_by_seo($categoria);
			}
		}

		// Seteo id de la categoria.
		$categoria_id = isset($model_categoria) ? $model_categoria->id : NULL;

		// Cargamos datos de posts.
		$model_post = new Model_Post;

		// Cantidad posts y comentarios en posts.
		$portada->assign('cantidad_posts', $model_post->cantidad(Model_Post::ESTADO_ACTIVO));
		$portada->assign('cantidad_comentarios_posts', $model_post->cantidad_comentarios(Model_Comentario::ESTADO_VISIBLE));

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		if ($pagina == 1)
		{
			// Cargo fijos.
			$post_sticky = $model_post->sticky(TRUE, $categoria_id);

			// Extendemos la información de los posts.
			foreach ($post_sticky as $k => $v)
			{
				$a = $v->as_array();
				$a['usuario'] = $v->usuario()->as_array();
				$a['puntos'] = $v->puntos();
				$a['comentarios'] = $v->cantidad_comentarios(Model_Post_Comentario::ESTADO_VISIBLE);
				$a['categoria'] = $v->categoria()->as_array();

				$post_sticky[$k] = $a;
			}

			// Seteo y limpio.
			$portada->assign('sticky', $post_sticky);
			unset($post_sticky);
		}
		else
		{
			$portada->assign('sticky', array());
		}

		// Ultimos posts
		$post_list = $model_post->obtener_ultimos($pagina, $cantidad_por_pagina, $categoria_id);

		// Verifivo validez de la pagina.
		if (count($post_list) == 0 && $pagina != 1)
		{
			Request::redirect('/');
		}

		// Paginación.
		$paginador = new Paginator($model_post->cantidad(Model_Post::ESTADO_ACTIVO, $categoria_id, FALSE), $cantidad_por_pagina);
		if ($categoria !== NULL)
		{
			$portada->assign('paginacion', $paginador->get_view($pagina, '/post/categoria/'.$categoria.'/%d'));
		}
		else
		{
			$portada->assign('paginacion', $paginador->get_view($pagina, '/post/%d/'));
		}
		unset($paginador);

		// Extendemos la información de los posts.
		foreach ($post_list as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['puntos'] = $v->puntos();
			$a['comentarios'] = $v->cantidad_comentarios(Model_Post_Comentario::ESTADO_VISIBLE);
			$a['categoria'] = $v->categoria()->as_array();

			$post_list[$k] = $a;
		}

		$portada->assign('ultimos_posts', $post_list);
		unset($post_list);

		// Cargamos TOP posts.
		$post_top_list = $model_post->obtener_tops();

		// Extendemos la información de los posts.
		foreach ($post_top_list as $k => $v)
		{
			$a = $v->as_array();
			$a['puntos'] = $v->puntos();
			$a['categoria'] = $v->categoria()->as_array();
			$post_top_list[$k] = $a;
		}

		$portada->assign('top_posts', $post_top_list);
		unset($post_top_list, $model_post);

		// Cargamos últimos comentarios.
		$m_comentarios = new Model_Comentario;
		$comentario_list = $m_comentarios->listado(1);

		// Extendemos la información de los comentarios.
		foreach ($comentario_list as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			if ($v instanceof Model_Foto_Comentario)
			{
				$a['foto'] = $v->foto()->as_array();
				$a['foto']['categoria'] = $v->foto()->categoria()->as_array();
			}
			else
			{
				$a['post'] = $v->post()->as_array();
				$a['post']['categoria'] = $v->post()->categoria()->as_array();
			}
			$comentario_list[$k] = $a;
		}

		$portada->assign('ultimos_comentarios', $comentario_list);
		unset($comentario_list, $m_comentarios);

		// Cargamos top usuarios.
		$model_usuario = new Model_Usuario;

		// Cantidad de usuarios
		$portada->assign('cantidad_usuarios', $model_usuario->cantidad());
		$portada->assign('cantidad_usuarios_online', $model_usuario->cantidad_activos());

		// Top de usuarios.
		$usuario_top_list = $model_usuario->obtener_tops();

		// Extendemos la información de los usuarios.
		foreach ($usuario_top_list as $k => $v)
		{
			$a = $v->as_array();
			$a['puntos'] = $v->cantidad_puntos();

			$usuario_top_list[$k] = $a;
		}
		$portada->assign('usuario_top', $usuario_top_list);
		unset($usuario_top_list, $model_usuario);

		// Verifico si se deben mostrar las fotos.
		if (Utils::configuracion()->get('habilitar_fotos', 1) && (Utils::configuracion()->get('privacidad_fotos', 1) || Usuario::is_login()))
		{
			// Cargamos ultimas fotos.
			$model_foto = new Model_Foto;
			$foto_list = $model_foto->obtener_ultimas(1, 1);

			// Extendemos la información de las fotos.
			foreach ($foto_list as $k => $v)
			{
				$foto_list[$k] = $v->as_array();
				$foto_list[$k]['descripcion_clean'] = preg_replace('/\[([^\[\]]+)\]/', '', $v->descripcion);
				$foto_list[$k]['categoria'] = $v->categoria()->as_array();
			}
			$portada->assign('ultimas_fotos', $foto_list);
			unset($foto_list);

			// Cantidad fotos y comentarios en fotos.
			$portada->assign('cantidad_fotos', $model_foto->cantidad(Model_Foto::ESTADO_ACTIVA));
			$portada->assign('cantidad_comentarios_fotos', $model_foto->cantidad_comentarios(Model_Comentario::ESTADO_VISIBLE));
			unset($model_foto);
		}

		// Titulo del sitio.
		$this->template->assign('brand_title', '');
		$this->template->assign('title_raw', $model_configuracion->nombre.' - '.$model_configuracion->descripcion);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

	/**
	 * Listado de usuarios del sitio.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_usuarios($pagina)
	{
		// Cargamos la portada.
		$portada = View::factory('home/usuarios');

		// Seteo el menu.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', self::submenu('usuarios'));

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos modelo de usuarios.
		$model_usuario = new Model_Usuario;

		// Cargo usuarios.
		$listado = $model_usuario->listado($pagina, $cantidad_por_pagina);

		// Verifivo validez de la pagina.
		if (count($listado) == 0 && $pagina != 1)
		{
			Request::redirect('/home/usuarios/');
		}

		// Listado de los online.
		$online = Model_Session::online_list();

		// Extendemos la información de los usuarios.
		foreach ($listado as $k => $v)
		{
			$a = $v->as_array();
			$a['online'] = in_array($v->id, $online);
			$listado[$k] = $a;
		}

		// Paginación.
		$paginador = new Paginator($model_usuario->cantidad(), $cantidad_por_pagina);
		$portada->assign('paginacion', $paginador->get_view($pagina, '/home/usuarios/%d/'));
		unset($paginador);

		$portada->assign('usuarios', $listado);
		unset($listado);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

	/**
	 * Prueba descarga de un plugin.
	 */
	/**
	public function action_install()
	{
		Dispatcher::call(''); // Prueba del uso de memoria.

		// Nombre del plugin.
		$p_nombre = "Test Plugin";

		// Borramos el plugin.
		// if (file_exists(Plugin_Manager::nombre_as_path($p_nombre)))
		// {
		// Update_Utils::unlink(Plugin_Manager::nombre_as_path($p_nombre));
		// }

		// Objeto manejador de plugins.
		$pkg_manager = Plugin_Manager::get_instance();

		// Verificamos su existencia
		$o_plugin = $pkg_manager->get(Plugin_Manager::make_name($p_nombre));

		if ($o_plugin === NULL)
		{
			// Realizamos la instalación.

			// Cargamos el actualizador.
			$o_updater = new Update_Updater;

			// Descargamos el paquete e instalamos el paquete. Se usa 1 para mostrar actualizaciones.
			if ($o_updater->install_package(Update_Utils::make_hash($p_nombre), 1))
			{
				// Actualizamos la cache.
				$pkg_manager->regenerar_lista();

				// Cargamos el paquete.
				$o_plugin = new Plugin_Plugin($p_nombre);

				// Realizamos la actualizacion.
				$o_plugin->install();

				// Activamos el paquete.
				$pkg_manager->set_state(Plugin_Manager::make_name($p_nombre), TRUE, TRUE);

				echo "Instalación existosa";
			}
			else
			{
				echo "Problema al realizar la instalación";
			}
		}
		else
		{
			// Buscamos actualizaciones.
			$upd_id = $o_plugin->check_updates();

			if ($upd_id === FALSE)
			{
				echo "No hay actualizaciones";
			}
			else
			{
				// Instalamos la actualizacion.

				// Desactivo el plugin.
				if ($o_plugin->info()->estado)
				{
					$o_plugin->remove();
				}

				// Directorio del plugin.
				$orig_path = Plugin_Manager::nombre_as_path($p_nombre);
				$tmp_path = rtrim($orig_path, '/').'.bkp';

				// Realizamos una copia.
				Update_Utils::copyr($orig_path, $tmp_path);

				// Borramos el original.
				Update_Utils::unlink($orig_path);

				// Cargamos el actualizador.
				$o_updater = new Update_Updater;

				// Descargamos el paquete e instalamos el paquete.
				if ( ! $o_updater->install_package(Update_Utils::make_hash($p_nombre), $upd_id))
				{
					// Recuperamos el original.
					Update_Utils::copyr($tmp_path, $orig_path);

					echo "No se pudo actualizar a la versión $upd_id.";
				}
				else
				{
					echo "Actualización a la versión $upd_id exitosa.";
				}

				// Realizamos la instalación.
				$o_plugin->install();

				// Borramos la copia.
				Update_Utils::unlink($tmp_path);
			}
		}

		if ( ! Request::is_cli())
		{
			echo "<br />";
		}
	}*/

}
