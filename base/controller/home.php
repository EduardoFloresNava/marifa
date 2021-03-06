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
		$menu->element_set(__('Inicio', FALSE), '/', 'inicio');
		$menu->element_set(__('Usuarios', FALSE), '/home/usuarios/', 'usuarios');
		$menu->element_set(__('Buscador', FALSE), '/buscador/', 'buscador');

		// Listado de elementos ONLINE.
		if (Usuario::is_login())
		{
			$menu->element_set(__('Agregar Post', FALSE), '/post/nuevo/', 'nuevo');
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
	 * @param string $categoria Categoría de los posts. Si no se especifica se toman todas.
	 */
	public function action_index($pagina, $categoria = NULL)
	{
		// Cargamos la portada.
		$portada = View::factory('home/index');

		// Asigno el menú.
		$this->template->assign('master_bar', parent::base_menu('posts'));

		// La barra es un elemento de la vista.
		$portada->assign('top_bar', self::submenu('inicio'));

		// Verifico categoría.
		if ($categoria !== NULL)
		{
			// Verifico formato.
			if ( ! preg_match('/[a-z0-9_]+/i', $categoria))
			{
				add_flash_message(FLASH_ERROR, __('La categoría no es correcta.', FALSE));
				Request::redirect('/post/'.$pagina);
			}

			// Cargo la categoría.
			$model_categoria = new Model_Categoria;

			// Verifico sea válida.
			if ( ! $model_categoria->existe_seo($categoria))
			{
				add_flash_message(FLASH_ERROR, __('La categoría no es correcta.', FALSE));
				Request::redirect('/post/'.$pagina);
			}
			else
			{
				// Cargo la categoría.
				$model_categoria->load_by_seo($categoria);
			}
		}

		// Asigno id de la categoría.
		$categoria_id = isset($model_categoria) ? $model_categoria->id : NULL;

		// Asigno categorías y actual.
		$portada->assign('categorias', Model::factory('categoria')->lista());
		$portada->assign('categoria', isset($model_categoria) ? $model_categoria->seo : NULL);

		// Cargamos datos de posts.
		$model_post = new Model_Post;

		// Cantidad posts y comentarios en posts.
		$portada->assign('cantidad_posts', $model_post->cantidad(Model_Post::ESTADO_ACTIVO));
		$portada->assign('cantidad_comentarios_posts', $model_post->cantidad_comentarios(Model_Comentario::ESTADO_VISIBLE));

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

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

			// Asigno y limpio.
			$portada->assign('sticky', $post_sticky);
			unset($post_sticky);
		}
		else
		{
			$portada->assign('sticky', array());
		}

		// Últimos posts
		$post_list = $model_post->obtener_ultimos($pagina, $cantidad_por_pagina, $categoria_id);

		// Verifico valides de la pagina.
		if (count($post_list) == 0 && $pagina != 1)
		{
			Request::redirect('/');
		}

		// Paginación.
		$paginador = new Paginator($model_post->cantidad(Model_Post::ESTADO_ACTIVO, $categoria_id, FALSE), $cantidad_por_pagina);
		if ($categoria !== NULL)
		{
			$portada->assign('paginacion', $paginador->get_view($pagina, SITE_URL.'/post/categoria/'.$categoria.'/%d'));
		}
		else
		{
			$portada->assign('paginacion', $paginador->get_view($pagina, SITE_URL.'/post/%d/'));
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
		$this->template->assign('title_raw', Model_Configuracion::get_instance()->nombre.' - '.Model_Configuracion::get_instance()->descripcion);

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

	/**
	 * Mostramos el listado de últimos posts. ES UNA PETICION AJAX.
	 * @param int $pagina Número de página para lo últimos posts.
	 * @param string $categoria Categoría de los posts. Si no se especifica se toman todas.
	 */
	public function action_ultimos_posts($categoria, $pagina)
	{
		// Cargamos la portada.
		$portada = View::factory('home/ultimos_posts_ajax');

		// Verifico categoría.
		if ($categoria !== NULL && $categoria !== 'todas')
		{
			// Verifico formato.
			if ( ! preg_match('/[a-z0-9_]+/i', $categoria))
			{
				add_flash_message(FLASH_ERROR, __('La categoría no es correcta.', FALSE));
				Request::redirect('/post/'.$pagina);
			}

			// Cargo la categoría.
			$model_categoria = new Model_Categoria;

			// Verifico sea válida.
			if ( ! $model_categoria->existe_seo($categoria))
			{
				add_flash_message(FLASH_ERROR, __('La categoría no es correcta.', FALSE));
				Request::redirect('/post/'.$pagina);
			}
			else
			{
				// Cargo la categoría.
				$model_categoria->load_by_seo($categoria);
			}
		}

		// Asigno id de la categoría.
		$categoria_id = isset($model_categoria) ? $model_categoria->id : NULL;

		// Cargamos datos de posts.
		$model_post = new Model_Post;

		// Cantidad posts y comentarios en posts.
		$portada->assign('cantidad_posts', $model_post->cantidad(Model_Post::ESTADO_ACTIVO));

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

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

			// Asigno y limpio.
			$portada->assign('sticky', $post_sticky);
			unset($post_sticky);
		}
		else
		{
			$portada->assign('sticky', array());
		}

		// Últimos posts
		$post_list = $model_post->obtener_ultimos($pagina, $cantidad_por_pagina, $categoria_id);

		// Verifico valides de la pagina.
		if (count($post_list) == 0 && $pagina != 1)
		{
			Request::http_response_code(404);
			die();
		}

		// Paginación.
		$paginador = new Paginator($model_post->cantidad(Model_Post::ESTADO_ACTIVO, $categoria_id, FALSE), $cantidad_por_pagina);
		if ($categoria !== NULL)
		{
			$portada->assign('paginacion', $paginador->get_view($pagina, SITE_URL.'/post/categoria/'.$categoria.'/%d'));
		}
		else
		{
			$portada->assign('paginacion', $paginador->get_view($pagina, SITE_URL.'/post/%d/'));
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

		// Muestro salida.
		$portada->show();
	}

	/**
	 * Listado de usuarios del sitio.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_usuarios($pagina)
	{
		// Cargamos la portada.
		$portada = View::factory('home/usuarios');

		// Asigno el menú.
		$this->template->assign('master_bar', parent::base_menu('posts'));
		$this->template->assign('top_bar', self::submenu('usuarios'));

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = Model_Configuracion::get_instance()->get('elementos_pagina', 20);

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cargamos modelo de usuarios.
		$model_usuario = new Model_Usuario;

		// Cargo usuarios.
		$listado = $model_usuario->listado($pagina, $cantidad_por_pagina);

		// Verifico validez de la pagina.
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
		$portada->assign('paginacion', $paginador->get_view($pagina, SITE_URL.'/home/usuarios/%d/'));
		unset($paginador);

		$portada->assign('usuarios', $listado);
		unset($listado);

		// Asigno el título.
		$this->template->assign('title', __('Usuarios', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

	/**
	 * Fomulario de contacto.
	 */
	public function action_contacto()
	{
		// Verifico el tipo.
		$tipo_contacto = Utils::configuracion()->get('contacto_tipo', 1);

		if ($tipo_contacto == 0)
		{
			add_flash_message(FLASH_ERROR, __('No tiene permisos para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Menú principal.
		$this->template->assign('master_bar', parent::base_menu('inicio'));

		// Asignamos la vista.
		$view = View::factory('/home/contacto');

		// Valores por defecto.
		$view->assign('nombre', '');
		$view->assign('error_nombre', FALSE);
		$view->assign('asunto', '');
		$view->assign('error_asunto', FALSE);
		$view->assign('mensaje', '');
		$view->assign('error_mensaje', FALSE);

		// Verifico datos enviados.
		if (Request::method() == 'POST')
		{
			// Marco sin errores.
			$error = FALSE;

			// Obtengo los campos.
			$nombre = arr_get($_POST, 'nombre', '');
			$asunto = arr_get($_POST, 'asunto', '');
			$mensaje = arr_get($_POST, 'mensaje', '');

			// Envío al formulario.
			$view->assign('nombre', $nombre);
			$view->assign('asunto', $asunto);
			$view->assign('mensaje', $mensaje);

			// Verifico nombre.
			if ( ! isset($nombre{4}) || isset($nombre{100}))
			{
				$error = TRUE;
				$view->assign('error_nombre', __('El nombre debe tener entre 4 y 100 caracteres.', FALSE));
			}

			// Verifico el asunto.
			if ( ! isset($asunto{4}) || isset($asunto{100}))
			{
				$error = TRUE;
				$view->assign('error_asunto', __('El asunto debe tener entre 4 y 100 caracteres.', FALSE));
			}

			// Verifico el mensaje.
			if ( ! isset($mensaje{20}) || isset($mensaje{300}))
			{
				$error = TRUE;
				$view->assign('error_mensaje', __('El mensaje debe tener entre 20 y 300 caracteres.', FALSE));
			}

			if ( ! $error)
			{
				// Verifico tipo de envío.
				if ($tipo_contacto == 1)
				{
					$model_contacto = new Model_Contacto;
					$model_contacto->nueva(htmlentities(trim($nombre), ENT_QUOTES, 'UTF-8'), htmlentities(trim($asunto), ENT_QUOTES, 'UTF-8'), htmlentities(trim($mensaje), ENT_QUOTES, 'UTF-8'));
				}
				else
				{
					// Asunto del mensaje.
					$asunto_mensaje = sprintf(__('CONTACTO: %s', FALSE), htmlentities(trim($asunto), ENT_QUOTES, 'UTF-8'));
					$mensaje = sprintf(__("%s envió: \n %s", FALSE), htmlentities(trim($nombre), ENT_QUOTES, 'UTF-8'), htmlentities(trim($mensaje), ENT_QUOTES, 'UTF-8'));

					// Obtengo a quienes enviar.
					$listado_usuarios = explode(PHP_EOL, trim(Utils::configuracion()->get('contacto_valor', '')));

					$usuarios_enviar = array();

					foreach ($listado_usuarios as $v)
					{
						$v = trim($v);

						if ($v{0} == '@')
						{
							// Cargo el modelo.
							$model_rango = new Model_Usuario_Rango;

							// Cargo el rango.
							$model_rango->load(array('nombre' => substr($v, 1)));

							// Verifico existencia.
							if ($model_rango->existe())
							{
								array_merge($usuarios_enviar, $model_rango->listado_usuarios());
							}
						}
						else
						{
							// Cargo el usuario.
							$model_usuario = new Model_Usuario;

							// Cargo el usuario.
							$model_usuario->load_by_nick($v);

							// Verifico existencia.
							if ($model_usuario->existe())
							{
								$usuarios_enviar[] = $model_usuario->id;
							}
						}
					}

					// Envío los mensajes.
					$model_mensaje = new Model_Mensaje;
					$model_mensaje->enviar(NULL, array_unique($usuarios_enviar, SORT_NUMERIC), $asunto_mensaje, $mensaje);
					unset($model_mensaje);
				}

				// Informo resultado.
				add_flash_message(FLASH_SUCCESS, __('El mensaje se ha enviado correctamente.', FALSE));
				Request::redirect('/');
			}
		}

		// Compilamos la vista.
		$this->template->assign('contenido', $view->parse());

		// Seteamos título.
		$this->template->assign('title', __('Contacto', FALSE));
	}
}