<?php
/**
 * gestion.php is part of Marifa.
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
 * Controlador para gestionar usuarios y buscar contenido.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Moderar
 */
class Base_Controller_Moderar_Gestion extends Controller {

	/**
	 * Verificamos que esté logueado para poder realizar las acciones.
	 */
	public function before()
	{
		// Verifico esté logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para poder acceder a esta sección.');
			Request::redirect('/usuario/login');
		}
		parent::before();
	}

	/**
	 * Listado de suspensiones a usuarios.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_usuarios($pagina)
	{
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			add_flash_message(FLASH_ERROR, 'No tienes permiso para acceder a esa sección.');
			Request::redirect('/');
		}

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('moderar/gestion/usuarios');

		// Modelo de suspensiones.
		$model_suspension = new Model_Usuario_Suspension;

		// Limpio antiguos.
		Model_Usuario_Suspension::clean();

		// Cargamos el listado de posts.
		$lst = $model_suspension->listado($pagina, $cantidad_por_pagina);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Paginación.
		$total = Model_Usuario_Suspension::cantidad();
		$vista->assign('cantidad_pendientes', $total);
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/moderar/gestion/usuarios/%s/'));
		unset($total);

		// Obtenemos datos de las denuncias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['motivo'] = Decoda::procesar($a['motivo']);
			$a['usuario'] = $v->usuario()->as_array();
			$a['moderador'] = $v->moderador()->as_array();
			$lst[$k] = $a;
		}

		// Seteamos listado de suspensiones.
		$vista->assign('suspensiones', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion_usuarios'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Terminamos la suspensión de un usuario.
	 * @param int $usuario ID del usuario a quitar la suspensión.
	 */
	public function action_terminar_suspension($usuario)
	{
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			add_flash_message(FLASH_ERROR, 'No tienes permiso para acceder a esa sección.');
			Request::redirect('/');
		}

		// Valido el ID.
		$usuario = (int) $usuario;

		// Verifico que exista el usuario.
		$model_usuario = new Model_Usuario($usuario);

		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, 'El usuario del que desea terminar la suspención no se encuentra disponible.');
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Verifico el estado.
		if ($model_usuario->estado !== Model_Usuario::ESTADO_SUSPENDIDA)
		{
			add_flash_message(FLASH_ERROR, 'El usuario del que desea terminar la suspención no se encuentra disponible.');
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Borramos la suspensión.
		$model_usuario->suspension()->anular();

		// Creamos el suceso.
		$model_suceso = new Model_Suceso;
		if (Usuario::$usuario_id != $model_usuario->id)
		{
			$model_suceso->crear($model_usuario->id, 'usuario_fin_suspension', TRUE, $model_usuario->id, Usuario::$usuario_id);
			$model_suceso->crear(Usuario::$usuario_id, 'usuario_fin_suspension', FALSE, $model_usuario->id, Usuario::$usuario_id);
		}
		else
		{
			$model_suceso->crear($model_usuario->id, 'usuario_fin_suspension', FALSE, $model_usuario->id, Usuario::$usuario_id);
		}

		// Informamos el resultado.
		add_flash_message(FLASH_SUCCESS, 'Suspensión anulada correctamente.');
		Request::redirect('/moderar/gestion/usuarios');
	}

	/**
	 * Búsqueda avanzada de contenido.
	 */
	public function action_buscador()
	{
		/*if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			add_flash_message(FLASH_ERROR, 'No tienes permiso para acceder a esa sección.');
			Request::redirect('/');
		}*/

		// Cargamos la vista.
		$vista = View::factory('moderar/gestion/buscador');

		// Seteo consulta enviada.
		$vista->assign('query', '');

		if (Request::method() == 'POST')
		{
			// Obtengo la frase.
			$query = trim(arr_get($_POST, 'query', ''));

			// Obtengo el tipo de búsqueda.
			$tipo = (int) arr_get($_POST, 'find', 0);

			if ($tipo < 0 || $tipo > 5)
			{
				$tipo = 0;
			}

			// Seteo en la vista.
			$vista->assign('query', $query);

			// Armo conjunto de búsqueda.
			$palabras = $this->conjunto_busqueda($query);

			if ($tipo == 0 || $tipo == 1)
			{
				// Busqueda de usuarios.
				$usuarios = Model::factory('usuario')->buscar_por_palabras($palabras, array('nick', 'email'), 1, 10);

				foreach ($usuarios as $k => $v)
				{
					$usuarios[$k] = $v->as_array();
				}
				$vista->assign('usuarios', $usuarios);
				unset($usuarios);
			}

			if ($tipo == 0 || $tipo == 2)
			{
				// Busqueda de posts.
				$posts = Model::factory('post')->buscar_por_palabras($palabras, array('titulo', 'contenido'), 1, 10);

				foreach ($posts as $k => $v)
				{
					$posts[$k] = $v->as_array();
					$posts[$k]['usuario'] = $v->usuario()->as_array();
				}
				$vista->assign('posts', $posts);
				unset($posts);
			}

			if ($tipo == 0 || $tipo == 3)
			{
				// Busqueda de comentarios en posts.
				$post_comentarios = Model::factory('post_comentario')->buscar_por_palabras($palabras, array('contenido'), 1, 10);

				foreach ($post_comentarios as $k => $v)
				{
					$post_comentarios[$k] = $v->as_array();
					$post_comentarios[$k]['post'] = $v->post()->as_array();
					$post_comentarios[$k]['usuario'] = $v->usuario()->as_array();
				}
				$vista->assign('post_comentarios', $post_comentarios);
				unset($post_comentarios);
			}

			if ($tipo == 0 || $tipo == 4)
			{
				// Busqueda de fotos.
				$fotos = Model::factory('foto')->buscar_por_palabras($palabras, array('titulo', 'descripcion', 'url'), 1, 10);

				foreach ($fotos as $k => $v)
				{
					$fotos[$k] = $v->as_array();
					$fotos[$k]['usuario'] = $v->usuario()->as_array();
				}
				$vista->assign('fotos', $fotos);
				unset($fotos);
			}

			if ($tipo == 0 || $tipo == 5)
			{
				// Busqueda de comentarios en fotos.
				$foto_comentarios = Model::factory('foto_comentario')->buscar_por_palabras($palabras, array('comentario'), 1, 10);

				foreach ($foto_comentarios as $k => $v)
				{
					$foto_comentarios[$k] = $v->as_array();
					$foto_comentarios[$k]['usuario'] = $v->usuario()->as_array();
					$foto_comentarios[$k]['foto'] = $v->foto()->as_array();
				}
				$vista->assign('foto_comentarios', $foto_comentarios);
				unset($foto_comentarios);
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion_buscador'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Obtenemos conjunto de palabras que forman la frase para buscar de forma sencilla.
	 * @param  string $cadena Cadena a descomponer.
	 * @return array Arreglo de palabras que componen la cadena.
	 */
	protected function conjunto_busqueda($cadena)
	{
		// Divido en palabras.
		$palabras = explode(' ', $cadena);

		// Proceso el listado de palabras.
		foreach ($palabras as $k => $palabra)
		{
			$palabras[$k] = trim($palabra);
		}

		// Devuelvo los elementos.
		return $palabras;
	}

}
