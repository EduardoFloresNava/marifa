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
 * @package		Marifa\Base
 * @subpackage  Controller\Admin
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador de administración de contenido.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Contenido extends Controller {

	/**
	 * Verifico los permisos para acceder a la sección.
	 */
	public function before()
	{
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para acceder a esta sección.');
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, 'No tienes permisos para acceder a esa sección.');
			Request::redirect('/');
		}

		parent::before();
	}

	/**
	 * Portada de la administración de contenido.
	 * Veo estadísticas sobre contenido en la comunidad.
	 */
	public function action_index()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/index');

		// Cantidades de posts por estado.
		$vista->assign('post_estado', Model_Post::cantidad_agrupados());

		// Cantidades de posts por categoria.
		$vista->assign('posts_categorias', Model_Post::cantidad_categorias());

		// Cantidad de comentarios en posts por estado.
		$vista->assign('post_comentarios_estado', Model_Post_Comentario::cantidad_agrupados());

		// Cantidad de fotos por estado.
		$vista->assign('foto_estado', Model_Foto::cantidad_agrupados());

		// Cantidad de comentarios en fotos por estado.
		$vista->assign('foto_comentarios_estado', Model_Foto_Comentario::cantidad_agrupados());

		// Cantidad de fotos por categoria.
		$vista->assign('fotos_categorias', Model_Foto::cantidad_categorias());

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de posts existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $tipo Tipos de posts a mostrar.
	 */
	public function action_posts($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = ( (int) $pagina > 0) ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Verifico el tipo de fotos a mostrar.
		$tipo = ($tipo === NULL) ? NULL : ( (int) $tipo);
		if ($tipo === 0)
		{
			$tipo = array('activo', 0);
		}
		elseif ($tipo === 1)
		{
			$tipo = array('borrador', 1);
		}
		elseif ($tipo === 2)
		{
			$tipo = array('borrado', 2);
		}
		elseif ($tipo === 3)
		{
			$tipo = array('pendiente', 3);
		}
		elseif ($tipo === 4)
		{
			$tipo = array('oculto', 4);
		}
		elseif ($tipo === 5)
		{
			$tipo = array('rechazado', 5);
		}
		elseif ($tipo === 6)
		{
			$tipo = array('papelera', 6);
		}
		else
		{
			$tipo = array('total', NULL);
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/posts');
		$vista->assign('tipo', ($tipo[1] == NULL) ? 7 : ($tipo[1]));

		// Modelo de posts.
		$model_posts = new Model_Post;

		// Cargamos el listado de posts.
		$lst = $model_posts->listado($pagina, $cantidad_por_pagina, $tipo[1]);

		// Verificamos páginas aleatorias sin elementos.
		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/admin/contenido/posts');
		}

		// Calculo las cantidades.
		$cantidades = Model_Post::cantidad_agrupados();

		// Paso datos para barra.
		$vista->assign('cantidades', $cantidades);
		$vista->assign('actual', $pagina);

		// Paginación.
		$paginador = new Paginator($cantidades[$tipo[0]], $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/contenido/posts/%s/'.$tipo[0]));

		// Obtenemos datos de los posts.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de posts.
		$vista->assign('posts', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_posts'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Eliminamos el post de un usuario.
	 * @param int $id ID del post a borrar.
	 */
	public function action_eliminar_post($id)
	{
		$id = (int) $id;

		// Cargamos el modelo del post.
		$model_post = new Model_Post($id);
		if ( ! $model_post->existe())
		{
			add_flash_message(FLASH_ERROR, 'El post que deseas eliminar no se encuentra disponible.');
			Request::redirect('/admin/contenido/posts/');
		}

		// Verifico cual es el estado actual.
		if ($model_post->estado === Model_Post::ESTADO_BORRADO)
		{
			add_flash_message(FLASH_ERROR, 'El post que deseas eliminar no se encuentra disponible.');
			Request::redirect('/admin/contenido/posts/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/eliminar_post');

		// Valores por defecto y errores.
		$vista->assign('tipo', '');
		$vista->assign('error_tipo', FALSE);
		$vista->assign('razon', '');
		$vista->assign('error_razon', FALSE);
		$vista->assign('borrador', FALSE);
		$vista->assign('error_borrador', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$tipo = isset($_POST['tipo']) ? (int) $_POST['tipo'] : NULL;
			$razon = isset($_POST['razon']) ? preg_replace('/\s+/', ' ', trim($_POST['razon'])) : NULL;
			$borrador = isset($_POST['borrador']) ? ($_POST['borrador'] == 1) : FALSE;

			// Valores para cambios.
			$vista->assign('tipo', $tipo);
			$vista->assign('razon', $razon);
			$vista->assign('borrador', $borrador);

			// Verifico el tipo.
			if ( ! in_array($tipo, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)))
			{
				$error = TRUE;
				$vista->assign('error_tipo', 'No ha seleccionado un tipo válido.');
			}
			else
			{
				// Verifico la razón si corresponde.
				if ($tipo === 12)
				{
					// Verificamos el nombre.
					if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{10,200}$/iD', $razon))
					{
						$error = TRUE;
						$vista->assign('error_razon', 'La razón dete tener entre 10 y 200 caractéres alphanuméricos.');
					}
				}
				else
				{
					$razon = NULL;
				}
			}

			if ( ! $error)
			{
				// Creo la moderación.
				$model_post->moderar($model_post->id, $tipo, $razon, $borrador);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $model_post->usuario_id)
				{
					$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
					$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
				}
				else
				{
					$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
				}

				// Informamos el resultado.
				add_flash_message(FLASH_SUCCESS, 'Post borrado correctamente.');
				Request::redirect('/admin/contenido/posts/');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_post'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Realizo el cambio de estados del post.
	 * @param int $post ID del post al cual cambiarle el estado.
	 * @param int $estado Estado que se debe colocar.
	 */
	public function action_cambiar_estado_post($post, $estado)
	{
		$post = (int) $post;

		// Cargo el post.
		$model_post = new Model_Post($post);

		// Verifico que exista.
		if ( ! $model_post->existe())
		{
			add_flash_message(FLASH_ERROR, 'El post al cual le quiere cambiar el estado no se encuentra disponible.');
			Request::redirect('/admin/contenido/posts');
		}

		// Obtengo el estado.
		switch ($model_post->estado)
		{
			case 0: // Activo
				if ($estado == Model_Post::ESTADO_BORRADO)
				{
					// Borramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(Model_Post::ESTADO_BORRADO);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'El post se a eliminado correctamente.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == Model_Post::ESTADO_OCULTO)
				{
					// Ocultamos.

					// Actualizo el estado.
					$model_post->actualizar_estado(Model_Post::ESTADO_OCULTO);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_ocultar', TRUE, $model_post->id, Usuario::$usuario_id, 0);
						$model_suceso->crear(Usuario::$usuario_id, 'post_ocultar', FALSE, $model_post->id, Usuario::$usuario_id, 0);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_ocultar', FALSE, $model_post->id, Usuario::$usuario_id, 0);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 5)
				{
					// Rechazamos.

					// Actualizo el estado.
					$model_post->actualizar_estado(5);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar',TRUE, $model_post->id, Usuario::$usuario_id, 0);
						$model_suceso->crear(Usuario::$usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 0);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 0);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 6)
				{
					// Enviamos a la papelera.

					// Actualizo el estado.
					$model_post->actualizar_estado(6);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_papelera', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_papelera', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_papelera', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				else
				{
					// Acción no permitida.
					add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
					Request::redirect('/admin/contenido/posts');
				}
				break;
			case 1: // Borrador
				if ($estado == 2)
				{
					// Borramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(2);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				else
				{
					// Acción no permitida.
					add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
					Request::redirect('/admin/contenido/posts');
				}
				break;
			case 2: // Borrado
				// No hay acciones posibles a este punto.
				add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
				Request::redirect('/admin/contenido/posts');
				break;
			case 3: // Pendiente
				if ($estado == 0)
				{
					// Aprobamos.

					// Actualizo el estado.
					$model_post->actualizar_estado(0);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', TRUE, $model_post->id, Usuario::$usuario_id, 1);
						$model_suceso->crear(Usuario::$usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 1);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 1);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 5)
				{
					// Rechazamos.

					// Actualizo el estado.
					$model_post->actualizar_estado(5);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', TRUE, $model_post->id, Usuario::$usuario_id, 0);
						$model_suceso->crear(Usuario::$usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 0);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 0);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 2)
				{
					// Borramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(2);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				else
				{
					// Acción no permitida.
					add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
					Request::redirect('/admin/contenido/posts');
				}
				break;
			case 4: // Oculto
				if ($estado == 0)
				{
					// Mostrar.

					// Actualizo el estado.
					$model_post->actualizar_estado(0);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_ocultar', TRUE, $model_post->id, Usuario::$usuario_id, 1);
						$model_suceso->crear(Usuario::$usuario_id, 'post_ocultar', FALSE, $model_post->id, Usuario::$usuario_id, 1);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_ocultar', FALSE, $model_post->id, Usuario::$usuario_id, 1);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 2)
				{
					// Borramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(2);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				else
				{
					// Acción no permitida.
					add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
					Request::redirect('/admin/contenido/posts');
				}
				break;
			case 5: // Rechazado
				if ($estado == 0)
				{
					// Aprobamos.

					// Actualizo el estado.
					$model_post->actualizar_estado(0);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', TRUE, $model_post->id, Usuario::$usuario_id, 1);
						$model_suceso->crear(Usuario::$usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 1);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_aprobar', FALSE, $model_post->id, Usuario::$usuario_id, 1);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 2)
				{
					// Borramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(2);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				else
				{
					// Acción no permitida.
					add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
					Request::redirect('/admin/contenido/posts');
				}
				break;
			case 6: // Papelera
				if ($estado == 0)
				{
					// Restauramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(0);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_restaurar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_restaurar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_restaurar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				elseif ($estado == 2)
				{
					// Borramos.

					// Actualizo el estado.
					$model_post->actualizar_estado(2);

					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					if (Usuario::$usuario_id != $model_post->usuario_id)
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', TRUE, $model_post->id, Usuario::$usuario_id);
						$model_suceso->crear(Usuario::$usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}
					else
					{
						$model_suceso->crear($model_post->usuario_id, 'post_borrar', FALSE, $model_post->id, Usuario::$usuario_id);
					}

					// Informo el resultado
					add_flash_message(FLASH_SUCCESS, 'Actualización correcta.');
					Request::redirect('/admin/contenido/posts');
				}
				else
				{
					// Acción no permitida.
					add_flash_message(FLASH_ERROR, 'No puedes realizar esa acción.');
					Request::redirect('/admin/contenido/posts');
				}
				break;
		}
	}

	/**
	 * Listado de fotos existentes.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $tipo Tipo de fotos a mostrar.
	 */
	public function action_fotos($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Verifico el tipo de fotos a mostrar.
		switch ($tipo)
		{
			case 0: // Activa.
				$tipo = array('activa', 0);
				break;
			case 1: // Oculta.
				$tipo = array('oculta', 1);
				break;
			case 2: // Papelera.
				$tipo = array('papelera', 2);
				break;
			case 3: // Borrada.
				$tipo = array('borrada', 3);
				break;
			case 4: // Todas.
			default: // Todas.
				$tipo = array('total', NULL);
		}

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/fotos');
		$vista->assign('tipo', ($tipo[1] === NULL) ? 4 : $tipo[1]);

		// Modelo de fotos.
		$model_fotos = new Model_Foto;

		// Cargamos el listado de fotos.
		$lst = $model_fotos->listado($pagina, $cantidad_por_pagina, $tipo[1]);

		// Si no hay elementos y no estamos en la inicial redireccionamos (Puso página incorrecta).
		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/admin/contenido/fotos');
		}

		// Calculo las cantidades.
		$cantidades = Model_Foto::cantidad_agrupados();

		// Paso datos para barra.
		$vista->assign('cantidades', $cantidades);
		$vista->assign('actual', $pagina);

		// Paginación.
		$paginador = new Paginator($cantidades[$tipo[0]], $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/contenido/fotos/%s/'.$tipo[0]));

		// Obtenemos datos de las fotos.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de fotos.
		$vista->assign('fotos', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_fotos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Ocultamos una foto.
	 * @param int $id ID de la foto a ocultar
	 */
	public function action_ocultar_foto($id)
	{
		// Cargamos el modelo de la foto.
		$model_foto = new Model_Foto( (int) $id);

		// Verifico que exista.
		if ( ! $model_foto->existe())
		{
			add_flash_message(FLASH_ERROR, 'No existe la foto que quiere ocultar.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Verifico que esté activa.
		if ($model_foto->estado == Model_Foto::ESTADO_OCULTA)
		{
			add_flash_message(FLASH_ERROR, 'La foto ya se encuentra oculta.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Ocultamos la foto.
		$model_foto->actualizar_estado(Model_Foto::ESTADO_OCULTA);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_ocultar', TRUE, $model_foto->id, Usuario::$usuario_id, 0);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_ocultar', FALSE, $model_foto->id, Usuario::$usuario_id, 0);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_ocultar', FALSE, $model_foto->id, Usuario::$usuario_id, 0);
		}

		// Informamos.
		add_flash_message(FLASH_SUCCESS, 'Foto ocultada correctamente.');
		Request::redirect('/admin/contenido/fotos');
	}

	/**
	 * Seteamos como visible una foto.
	 * @param int $id ID de la foto a mostrar
	 */
	public function action_mostrar_foto($id)
	{
		// Cargamos el modelo de la foto.
		$model_foto = new Model_Foto( (int) $id);

		// Verifico que exista.
		if ( ! $model_foto->existe())
		{
			add_flash_message(FLASH_ERROR, 'No existe la foto que quiere mostrar.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Verifico que esté oculta.
		if ($model_foto->estado == Model_Foto::ESTADO_ACTIVA)
		{
			add_flash_message(FLASH_ERROR, 'La foto ya se encuentra visible.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Mostramos la foto.
		$model_foto->actualizar_estado(Model_Foto::ESTADO_ACTIVA);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_ocultar', TRUE, $model_foto->id, Usuario::$usuario_id, 1);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_ocultar', FALSE, $model_foto->id, Usuario::$usuario_id, 1);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_ocultar', FALSE, $model_foto->id, Usuario::$usuario_id, 1);
		}

		// Informamos.
		add_flash_message(FLASH_SUCCESS, 'Foto seteada como visible correctamente.');
		Request::redirect('/admin/contenido/fotos');
	}

	/**
	 * La foto se ha borrado correctamente.
	 * @param int $id ID de la foto a borrar.
	 */
	public function action_eliminar_foto($id)
	{
		// Cargamos el modelo de la foto.
		$model_foto = new Model_Foto( (int) $id);

		// Verifico que exista.
		if ( ! $model_foto->existe())
		{
			add_flash_message(FLASH_ERROR, 'No existe la foto que quiere mostrar.');
			Request::redirect('/admin/contenido/fotos');
		}

		// Borramos la foto.
		$model_foto->borrar();

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_foto->usuario_id)
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_borrar', TRUE, $model_foto->id, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'foto_borrar', FALSE, $model_foto->id, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_foto->usuario_id, 'foto_borrar', FALSE, $model_foto->id, Usuario::$usuario_id);
		}

		// Informamos.
		add_flash_message(FLASH_SUCCESS, 'Foto borrrada correctamente.');
		Request::redirect('/admin/contenido/fotos');
	}

	/**
	 * Listado de categorias de los posts, fotos y comunidades.
	 */
	public function action_categorias()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/categorias');

		// Modelo de categorias.
		$model_categorias = new Model_Categoria;

		// Cargamos el listado de categorias.
		$lst = $model_categorias->lista();

		// Seteamos listado de las categorias.
		$vista->assign('categorias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_categorias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Agregamos una nueva categoria.
	 */
	public function action_agregar_categoria()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/nueva_categoria');

		// Cargamos el listado de imagens para rangos disponibles.
		$imagenes_categorias = scandir(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS);
		unset($imagenes_categorias[1], $imagenes_categorias[0]); // Quitamos . y ..

		$vista->assign('imagenes_categorias', $imagenes_categorias);

		// Valores por defecto y errores.
		$vista->assign('nombre', '');
		$vista->assign('error_nombre', FALSE);
		$vista->assign('imagen', '');
		$vista->assign('error_imagen', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('imagen', $imagen);

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{3,50}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre de la categoria deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_categorias))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			$model_categoria = new Model_Categoria;

			if ( ! $error)
			{
				// Verifico no exista campo con ese nombre.
				if ($model_categoria->existe_seo($model_categoria->make_seo($nombre)))
				{
					$error = TRUE;
					$vista->assign('error_nombre', 'Ya existe una categoria con ese nombre seo.');
				}
			}


			if ( ! $error)
			{
				// Creo la categoria.
				$model_categoria->nueva($nombre, $imagen);

				add_flash_message(FLASH_SUCCESS, 'Categoria creada correctamente.');
				Request::redirect('/admin/contenido/categorias');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_categorias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos una categoria.
	 * @param int $id ID de la categoria a borrar.
	 */
	public function action_eliminar_categoria($id)
	{
		// Cargamos el modelo de la categoria.
		$model_categoria = new Model_Categoria( (int) $id);

		// Verifico que exista.
		if ( ! $model_categoria->existe())
		{
			add_flash_message(FLASH_ERROR, 'No exista la categoria que quiere borrar.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Verifico no tenga posts ni fotos.
		if ($model_categoria->tiene_fotos() || $model_categoria->tiene_posts())
		{
			add_flash_message(FLASH_ERROR, 'No se puede borrar la categoria porque tiene fotos y/o posts asociados.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Verifico existan otras categorias.
		if ($model_categoria->cantidad() <= 1)
		{
			add_flash_message(FLASH_ERROR, 'No se puede borrar la categoría porque es la única existente.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Borramos la categoria.
		$model_categoria->borrar();

		// Informamos.
		add_flash_message(FLASH_SUCCESS, 'Categoria eliminada correctamente.');
		Request::redirect('/admin/contenido/categorias');
	}

	/**
	 * Editamos una categoria existente.
	 * @param int $id ID de la categoria a editar.
	 */
	public function action_editar_categoria($id)
	{
		// Cargamos el modelo de la categoria.
		$model_categoria = new Model_Categoria( (int) $id);

		// Verifico que exista.
		if ( ! $model_categoria->existe())
		{
			add_flash_message(FLASH_ERROR, 'No exista la categoria que quiere editar.');
			Request::redirect('/admin/contenido/categorias');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/editar_categoria');

		// Cargamos el listado de imagens para rangos disponibles.
		$imagenes_categorias = scandir(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS);
		unset($imagenes_categorias[1], $imagenes_categorias[0]); // Quitamos . y ..

		$vista->assign('imagenes_categorias', $imagenes_categorias);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_categoria->nombre);
		$vista->assign('error_nombre', FALSE);
		$vista->assign('imagen', $model_categoria->imagen);
		$vista->assign('error_imagen', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('imagen', $imagen);

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{3,50}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre de la categoria deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_categorias))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			if ( ! $error)
			{
				// Verifico no exista campo con ese nombre.
				if ($model_categoria->existe_seo($model_categoria->make_seo($nombre), TRUE))
				{
					$error = TRUE;
					$vista->assign('error_nombre', 'Ya existe una categoria con ese nombre seo.');
				}
			}


			if ( ! $error)
			{
				// Actualizo el imagen.
				if ($model_categoria->imagen != $imagen)
				{
					$model_categoria->cambiar_imagen($imagen);
				}

				// Actualizo el nombre.
				if ($model_categoria->nombre != $nombre)
				{
					$model_categoria->cambiar_nombre($nombre);
				}

				// Informamos suceso.
				add_flash_message(FLASH_SUCCESS, 'Información actualizada correctamente');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_categorias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de noticias.
	 * @param int $pagina Número de página de la cual mostrar noticias.
	 */
	public function action_noticias($pagina)
	{
		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/noticias');

		// Modelo de noticias.
		$model_noticias = new Model_Noticia;

		// Cargamos el listado de noticias.
		$lst = $model_noticias->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$total = $model_noticias->total();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/contenido/noticias/%s/'));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['contenido_raw'] = $a['contenido'];
			$a['contenido'] = Decoda::procesar($a['contenido']);
			$a['usuario'] = $v->usuario()->as_array();

			$lst[$k] = $a;
		}

		// Seteamos listado de noticias.
		$vista->assign('noticias', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Creamos una nueva noticia.
	 */
	public function action_nueva_noticia()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/contenido/nueva_noticia');

		// Valores por defecto y errores.
		$vista->assign('contenido', '');
		$vista->assign('error_contenido', FALSE);
		$vista->assign('visible', FALSE);
		$vista->assign('error_visible', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos el contenido.
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : NULL;

			// Obtenemos estado por defecto.
			$visible = isset($_POST['visible']) ? ($_POST['visible'] == 1) : FALSE;

			// Quitamos BBCode para dimenciones.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);

			if ( ! isset($contenido_clean{10}) || isset($contenido_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'El contenido debe tener entre 10 y 200 caractéres');
			}
			unset($contenido_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Creamos la noticia.
				$model_noticia = new Model_Noticia;
				$model_noticia->nuevo(Usuario::$usuario_id, $contenido, $visible ? Model_Noticia::ESTADO_VISIBLE : Model_Noticia::ESTADO_OCULTO);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				add_flash_message(FLASH_SUCCESS, 'La noticia se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/contenido/noticias');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vista preliminar de una noticia.
	 */
	public function action_preview()
	{
		// Obtengo el contenido y evitamos XSS.
		$contenido = isset($_POST['contenido']) ? htmlentities($_POST['contenido'], ENT_NOQUOTES, 'UTF-8') : '';

		// Evito salida por template.
		$this->template = NULL;

		// Proceso contenido.
		die(Decoda::procesar($contenido));
	}

	/**
	 * Activamos o desactivamos una noticia.
	 * @param int $id
	 * @param int $estado
	 */
	public function action_estado_noticia($id, $estado)
	{
		$id = (int) $id;

		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia($id);
		if ($model_noticia->existe())
		{
			$estado = (bool) $estado;
			if ($estado)
			{
				$model_noticia->activar();
				add_flash_message(FLASH_SUCCESS, 'Se habilitó correctamente la noticia #'.$id);
			}
			else
			{
				$model_noticia->desactivar();
				add_flash_message(FLASH_SUCCESS, 'Se ocultó correctamente la noticia #'.$id);
			}
		}
		Request::redirect('/admin/contenido/noticias');
	}

	/**
	 * Desactivamos todas las noticias.
	 */
	public function action_ocultar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->desactivar_todas();
		add_flash_message(FLASH_SUCCESS, 'Se han ocultado correctamente todas las noticias.');
		Request::redirect('/admin/contenido/noticias');
	}

	/**
	 * Activamos o desactivamos una noticia.
	 * @param int $id ID de la noticia a borrar.
	 */
	public function action_borrar_noticia($id)
	{
		$id = (int) $id;
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia($id);
		if ($model_noticia->existe())
		{
			// Borramos la noticia.
			$model_noticia->eliminar();
			add_flash_message(FLASH_SUCCESS, 'Se borró correctamente la noticia #'.$id);
		}
		Request::redirect('/admin/contenido/noticias');
	}

	/**
	 * Borramos todas las noticias.
	 */
	public function action_limpiar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->eliminar_todas();
		add_flash_message(FLASH_SUCCESS, 'Se han borrado correctamente todas las noticias.');
		Request::redirect('/admin/contenido/noticias');
	}

	/**
	 * Editamos una noticia.
	 * @param int $id ID de la noticia a editar.
	 */
	public function action_editar_noticia($id)
	{
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia( (int) $id);
		if ( ! $model_noticia->existe())
		{
			add_flash_message(FLASH_ERROR, 'Noticia incorrecta.');
			Request::redirect('/admin/contenido/noticias');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/contenido/editar_noticia');

		// Valores por defecto y errores.
		$vista->assign('noticia', $model_noticia->id);
		$vista->assign('contenido', $model_noticia->contenido);
		$vista->assign('error_contenido', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos el contenido.
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : NULL;

			// Quitamos BBCode para dimenciones.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);

			if ( ! isset($contenido_clean{10}) || isset($contenido_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'El contenido debe tener entre 10 y 200 caractéres');
			}
			else
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Actualizamos el contenido.
				$model_noticia->actualizar_contenido($contenido);
				$vista->assign('contenido', $model_noticia->contenido);
				add_flash_message(FLASH_SUCCESS, 'Contenido actualizado correctamente');
			}
			unset($contenido_clean);
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('contenido_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
