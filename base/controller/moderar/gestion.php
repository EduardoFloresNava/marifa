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
	 * Verificamos que esté identificado para poder realizar las acciones.
	 */
	public function before()
	{
		// Verifico esté identificado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para poder acceder a esta sección.', FALSE));
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
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
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

		// Asignamos listado de suspensiones.
		$vista->assign('suspensiones', $lst);
		unset($lst);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion.usuarios'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Gestión', FALSE).' - '.__('Usuarios', FALSE));

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
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Valido el ID.
		$usuario = (int) $usuario;

		// Verifico que exista el usuario.
		$model_usuario = new Model_Usuario($usuario);

		if ( ! $model_usuario->existe())
		{
			add_flash_message(FLASH_ERROR, __('El usuario del que desea terminar la suspensión no se encuentra disponible.', FALSE));
			Request::redirect('/moderar/gestion/usuarios');
		}

		// Verifico el estado.
		if ($model_usuario->estado !== Model_Usuario::ESTADO_SUSPENDIDA)
		{
			add_flash_message(FLASH_ERROR, __('El usuario del que desea terminar la suspensión no se encuentra disponible.', FALSE));
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
		add_flash_message(FLASH_SUCCESS, __('Suspensión anulada correctamente.', FALSE));
		Request::redirect('/moderar/gestion/usuarios');
	}

	/**
	 * Búsqueda avanzada de contenido.
	 */
	public function action_buscador()
	{
		//TODO: PERMISO ACCESO AL BUSCADOR DE CONTENIDO.
		/*if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			add_flash_message(FLASH_ERROR, 'No tienes permiso para acceder a esa sección.');
			Request::redirect('/');
		}*/

		// Cargamos la vista.
		$vista = View::factory('moderar/gestion/buscador');

		// Asigno consulta enviada.
		$vista->assign('query', '');
		$vista->assign('find', '');

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

			// Asigno en la vista.
			$vista->assign('query', $query);
			$vista->assign('find', $tipo);

			// Armo conjunto de búsqueda.
			$palabras = $this->conjunto_busqueda($query);

			if ($tipo == 0 || $tipo == 1)
			{
				// Búsqueda de usuarios.
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
				// Búsqueda de posts.
				$posts = Model::factory('post')->buscar_por_palabras($palabras, array('titulo', 'contenido'), 1, 10);

				foreach ($posts as $k => $v)
				{
					$posts[$k] = $v->as_array();
					$posts[$k]['usuario'] = $v->usuario()->as_array();
					$posts[$k]['categoria'] = $v->categoria()->as_array();
				}
				$vista->assign('posts', $posts);
				unset($posts);
			}

			if ($tipo == 0 || $tipo == 3)
			{
				// Búsqueda de comentarios en posts.
				$post_comentarios = Model::factory('post_comentario')->buscar_por_palabras($palabras, array('contenido'), 1, 10);

				foreach ($post_comentarios as $k => $v)
				{
					$post_comentarios[$k] = $v->as_array();
					$post_comentarios[$k]['post'] = $v->post()->as_array();
					$post_comentarios[$k]['post']['categoria'] = $v->post()->categoria()->as_array();
					$post_comentarios[$k]['usuario'] = $v->usuario()->as_array();
				}
				$vista->assign('post_comentarios', $post_comentarios);
				unset($post_comentarios);
			}

			if ($tipo == 0 || $tipo == 4)
			{
				// Búsqueda de fotos.
				$fotos = Model::factory('foto')->buscar_por_palabras($palabras, array('titulo', 'descripcion', 'url'), 1, 10);

				foreach ($fotos as $k => $v)
				{
					$fotos[$k] = $v->as_array();
					$fotos[$k]['usuario'] = $v->usuario()->as_array();
					$fotos[$k]['categoria'] = $v->categoria()->as_array();
				}
				$vista->assign('fotos', $fotos);
				unset($fotos);
			}

			if ($tipo == 0 || $tipo == 5)
			{
				// Búsqueda de comentarios en fotos.
				$foto_comentarios = Model::factory('foto_comentario')->buscar_por_palabras($palabras, array('comentario'), 1, 10);

				foreach ($foto_comentarios as $k => $v)
				{
					$foto_comentarios[$k] = $v->as_array();
					$foto_comentarios[$k]['usuario'] = $v->usuario()->as_array();
					$foto_comentarios[$k]['foto'] = $v->foto()->as_array();
					$foto_comentarios[$k]['foto']['categoria'] = $v->foto()->categoria()->as_array();
				}
				$vista->assign('foto_comentarios', $foto_comentarios);
				unset($foto_comentarios);
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion.buscador'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Gestión', FALSE).' - '.__('Buscador', FALSE));

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

	/**
	 * Listado de censuras.
	 * @param int $pagina
	 */
	public function action_censuras($pagina)
	{
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Formato de la página.
		$pagina = ( (int) $pagina) > 0 ? ( (int) $pagina) : 1;

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('moderar/gestion/censuras');

		// Modelo de censuras.
		$model_suspension = new Model_Censura;

		// Cargamos el listado de posts.
		$lst = $model_suspension->listado($pagina, $cantidad_por_pagina);

		if (count($lst) == 0 && $pagina != 1)
		{
			Request::redirect('/moderar/gestion/censuras');
		}

		// Paginación.
		$total = Model_Censura::cantidad();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/moderar/gestion/censuras/%s/'));
		unset($total);

		// Obtenemos datos de las denuncias.
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Asignamos listado de censuras.
		$vista->assign('censuras', $lst);
		unset($lst);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion.censuras'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Gestión', FALSE).' - '.__('Censuras', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Creamos una nueva censura.
	 */
	public function action_nueva_censura()
	{
		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Cargo la vista.
		$vista = View::factory('moderar/gestion/nueva_censura');

		// Asigno valores por defecto.
		$vista->assign('valor', '');
		$vista->assign('error_valor', FALSE);
		$vista->assign('tipo', 0);
		$vista->assign('error_tipo', FALSE);
		$vista->assign('censura', '**');
		$vista->assign('error_censura', FALSE);
		$vista->assign('estado', FALSE);
		$vista->assign('error_estado', FALSE);

		// Verifico contenido enviado por POST.
		if (Request::method() == 'POST')
		{
			// Marco sin errores.
			$error = FALSE;

			// Obtengo los datos.
			$valor = arr_get($_POST, 'valor', '');
			$tipo = (int) arr_get($_POST, 'tipo', 0);
			$censura = arr_get($_POST, 'censura', '');
			$estado = (bool) arr_get($_POST, 'estado', FALSE);

			// Reenviamos los datos.
			$vista->assign('valor', $valor);
			$vista->assign('tipo', $tipo);
			$vista->assign('censura', $censura);
			$vista->assign('estado', $estado);

			// Verifico el tipo.
			if ($tipo != 0 && $tipo != 1 && $tipo != 2)
			{
				$error = TRUE;
				$vista->assign('error_tipo', __('El tipo de censura es incorrecto.', FALSE));
			}

			// Verifico censura.
			if (empty($censura) || isset($censura[250]))
			{
				$error = TRUE;
				$vista->assign('error_censura', __('La cadena utilizada para censurar no puede estar vacía ni tener más de 250 caracteres.', FALSE));
			}

			// Verifico el valor en funcion del tipo.
			if ($tipo == 0 && (isset($valor[250]) || empty($valor)))
			{
				$error = TRUE;
				$vista->assign('error_valor', __('El valor debe se una cadena de no más de 250 caracteres.', FALSE));
			}
			elseif ($tipo == 1 && ! preg_match('/^[^\pZ\pP]{1,250}$/ui', $valor))
			{
				$error = TRUE;
				$vista->assign('error_valor', __('El valor debe se una palabra de no más de 250 caracteres.', FALSE));
			}
			elseif ($tipo == 2 && (isset($valor[250]) || empty($valor) || @preg_match($valor, '') === FALSE))
			{
				$error = TRUE;
				$vista->assign('error_valor', __('El valor debe se un cadena de no más de 250 caracteres.', FALSE));
			}

			// Creo la censura.
			if ( ! $error)
			{
				// Cargo el modelo.
				$model_censura = new Model_Censura;

				// Creo la censura.
				$model_censura->nueva($valor, $tipo, $censura, $estado);

				// Informo y regreso.
				add_flash_message(FLASH_SUCCESS, __('La censura se ha creado correctamente.', FALSE));
				Request::redirect('/moderar/gestion/censuras');
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion.censuras'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Gestión', FALSE).' - '.__('Censuras', FALSE).' - '.__('Crear', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Creamos una nueva censura.
	 */
	public function action_editar_censura($id)
	{
		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Cargo la censura.
		$id = (int) $id;
		$model_censura = new Model_Censura($id);

		// Verifico existencia.
		if ( ! $model_censura->existe())
		{
			add_flash_message(FLASH_ERROR, __('La censura que quiere editar es incorrecta,', FALSE));
			Request::redirect('/moderar/gestion/censuras');
		}

		// Cargo la vista.
		$vista = View::factory('moderar/gestion/editar_censura');

		// Asigno valores por defecto.
		$vista->assign('valor', $model_censura->valor);
		$vista->assign('error_valor', FALSE);
		$vista->assign('tipo', $model_censura->tipo);
		$vista->assign('error_tipo', FALSE);
		$vista->assign('censura', $model_censura->censura);
		$vista->assign('error_censura', FALSE);
		$vista->assign('estado', $model_censura->estado);
		$vista->assign('error_estado', FALSE);

		// Verifico contenido enviado por POST.
		if (Request::method() == 'POST')
		{
			// Marco sin errores.
			$error = FALSE;

			// Obtengo los datos.
			$valor = arr_get($_POST, 'valor', '');
			$tipo = (int) arr_get($_POST, 'tipo', 0);
			$censura = arr_get($_POST, 'censura', '');
			$estado = (bool) arr_get($_POST, 'estado', FALSE);

			// Reenviamos los datos.
			$vista->assign('valor', $valor);
			$vista->assign('tipo', $tipo);
			$vista->assign('censura', $censura);
			$vista->assign('estado', $estado);

			// Verifico el tipo.
			if ($tipo != 0 && $tipo != 1 && $tipo != 2)
			{
				$error = TRUE;
				$vista->assign('error_tipo', __('El tipo de censura es incorrecto.', FALSE));
			}

			// Verifico censura.
			if (empty($censura) || isset($censura[250]))
			{
				$error = TRUE;
				$vista->assign('error_censura', __('La cadena utilizada para censurar no puede estar vacía ni tener más de 250 caracteres.', FALSE));
			}

			// Verifico el valor en funcion del tipo.
			if ($tipo == 0 && (isset($valor[250]) || empty($valor)))
			{
				$error = TRUE;
				$vista->assign('error_valor', __('El valor debe se una cadena de no más de 250 caracteres.', FALSE));
			}
			elseif ($tipo == 1 && ! preg_match('/^[^\pZ\pP]{1,250}$/ui', $valor))
			{
				$error = TRUE;
				$vista->assign('error_valor', __('El valor debe se una palabra de no más de 250 caracteres.', FALSE));
			}
			elseif ($tipo == 2 && (isset($valor[250]) || empty($valor) || @preg_match($valor, '') === FALSE))
			{
				$error = TRUE;
				$vista->assign('error_valor', __('El valor debe se un cadena de no más de 250 caracteres.', FALSE));
			}

			// Actualizo la censura.
			if ( ! $error)
			{
				// Listado de campos a actualizar.
				$campos_actualizar = array();

				// Verifico cambios de valores.
				if ($model_censura->valor !== $valor)
				{
					$campos_actualizar['valor'] = $valor;
				}

				// Verifico cambios de valores.
				if ($model_censura->tipo !== $tipo)
				{
					$campos_actualizar['tipo'] = $tipo;
				}

				// Verifico cambios de valores.
				if ($model_censura->censura !== $censura)
				{
					$campos_actualizar['censura'] = $censura;
				}

				// Verifico cambios de valores.
				if ($model_censura->estado !== $estado)
				{
					$campos_actualizar['estado'] = $estado;
				}

				// Actualizo.
				if (count($campos_actualizar) > 0)
				{
					$model_censura->actualizar_campos($campos_actualizar);
				}

				// Informo y regreso.
				add_flash_message(FLASH_SUCCESS, __('La censura se ha creado correctamente.', FALSE));
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion.censuras'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Gestión', FALSE).' - '.__('Censuras', FALSE).' - '.__('Crear', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos una censura.
	 * @param int $censura ID de la censura a borrar.
	 */
	public function action_borrar_censura($censura)
	{
		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Cargo la censura.
		$censura = (int) $censura;
		$model_censura = new Model_Censura($censura);

		// Verifico existencia.
		if ( ! $model_censura->existe())
		{
			add_flash_message(FLASH_ERROR, __('La censura que quiere borrar no se encuentra disponible,', FALSE));
			Request::redirect('/moderar/gestion/censuras');
		}

		// Borro la censura.
		$model_censura->delete();

		// Informo y vuelvo.
		add_flash_message(FLASH_SUCCESS, __('La censura se ha borrado correctamente,', FALSE));
		Request::redirect('/moderar/gestion/censuras');
	}

	/**
	 * Cambio el estado de una censura.
	 * @param int $censura ID de la censura a cambiarle el estado.
	 * @param int $estado Estado a setearle a la censura.
	 */
	public function action_estado_censura($censura, $estado)
	{
		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Verifico el estado.
		$estado = (int) $estado;

		if ($estado !== 0 && $estado !== 1)
		{
			add_flash_message(FLASH_ERROR, __('El estado a asignar a la censura no es válido.', FALSE));
			Request::redirect('/moderar/gestion/censuras');
		}

		// Cargo la censura.
		$censura = (int) $censura;
		$model_censura = new Model_Censura($censura);

		// Verifico existencia.
		if ( ! $model_censura->existe())
		{
			add_flash_message(FLASH_ERROR, __('La censura a la que quiere cambiarle el estado no se encuentra disponible,', FALSE));
			Request::redirect('/moderar/gestion/censuras');
		}

		// Actualizo estado.
		$model_censura->actualizar_campo('estado', $estado);

		// Informo y vuelvo.
		add_flash_message(FLASH_SUCCESS, __('El estado de la censura se ha actualizado correctamente.', FALSE));
		Request::redirect('/moderar/gestion/censuras');
	}

	/**
	 * Realizamos un verificación de las censuras activas o de la seleccionada.
	 * @param int $censura ID de la censura a verificar o NULL para todas las activas.
	 */
	public function action_verificar_censura($censura = NULL)
	{
		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permiso para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		// Verifico censura a aplicar.
		if ($censura !== NULL)
		{
			// Cargo la censura.
			$censura = (int) $censura;
			$model_censura = new Model_Censura($censura);

			// Verifico existencia.
			if ( ! $model_censura->existe())
			{
				add_flash_message(FLASH_ERROR, __('La censura que quiere probar es incorrecta,', FALSE));
				Request::redirect('/moderar/gestion/censuras');
			}
		}

		// Listado de censuras a aplicar.
		if ( ! isset($model_censura))
		{
			// Obtengo las censuras.
			$o_c = new Model_Censura;

			// Proceso.
			$censuras = $o_c->activas();

			foreach ($censuras as $k => $v)
			{
				$censuras[$k] = $v->as_object();
			}
		}
		else
		{
			$censuras = array($model_censura->as_object());
		}

		// Cargo la vista.
		$vista = View::factory('moderar/gestion/verificar_censuras');

		// Asigno valores por defecto.
		$vista->assign('entrada', '');
		$vista->assign('error_entrada', FALSE);
		$vista->assign('censuras', $censuras);

		// Verifico contenido enviado por POST.
		if (Request::method() == 'POST')
		{
			// Marco sin errores.
			$error = FALSE;

			// Obtengo entrada.
			$entrada = trim(arr_get($_POST, 'entrada', ''));

			// Reenvio la entrada.
			$vista->assign('entrada', $entrada);

			// Verifico en la entrada.
			if (empty($entrada))
			{
				$error = TRUE;
				$vista->assign('error_entrada', 'Debe ingresar un texto de prueba.');
			}

			if ( ! $error)
			{
				// Cargo decoda.
				$decoda = new Decoda($entrada);

				// Cargo el hook para debug.
				$decoda->addHook(new CensurasHook($censuras, TRUE));

				// Proceso la salida.
				$vista->assign('salida_debug', $decoda->parse(FALSE));
				unset($decoda);

				// Cargo decoda.
				$o_decoda = new Decoda($entrada);

				// Cargo el hook son debug.
				$o_decoda->addHook(new CensurasHook($censuras, FALSE));

				// Salida son debug.
				$vista->assign('salida', $o_decoda->parse(FALSE));
				unset($o_decoda);
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($vista);
		$admin_template->assign('top_bar', Controller_Moderar_Home::submenu('gestion.censuras'));

		// Asigno el título.
		$this->template->assign('title', __('Moderación', FALSE).' - '. __('Gestión', FALSE).' - '.__('Censuras', FALSE).' - '.__('Verificar', FALSE));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}
}
