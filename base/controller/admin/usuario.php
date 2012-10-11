<?php
/**
 * usuario.php is part of Marifa.
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

//TODO: VERIFICAR LAS ACCIONES DEPENDIENDO DEL ESTADO DE LOS USUARIOS.

/**
 * Controlador de administración de usuarios.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Usuario extends Controller {

	public function __construct()
	{
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR))
		{
			$_SESSION['flash_error'] = 'No tienes permisos para acceder a esa sección.';
			Request::redirect('/');
		}

		parent::__construct();
	}

	/**
	 * Listado de usuarios.
	 * @param int $pagina Número de página.
	 * @param int $tipo Tipo de usuarios a mostrar.
	 */
	public function action_index($pagina, $tipo)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// TIPO, 0->todos, 1->activos, 2->suspendidos, 3->baneados
		$tipo = (int) $tipo;
		if ($tipo !== 0 && $tipo !== 1 && $tipo !== 2 && $tipo !== 3)
		{
			Request::redirect('/admin/usuario/');
		}

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/index');

		// Asigno el tipo.
		$vista->assign('tipo', $tipo);

		// Noticia Flash.
		if (isset($_SESSION['usuario_correcto']))
		{
			$vista->assign('success', get_flash('usuario_correcto'));
		}

		if (isset($_SESSION['usuario_error']))
		{
			$vista->assign('error', get_flash('usuario_error'));
		}

		// Limpio antiguos.
		Model_Usuario_Suspension::clean();

		// Modelo de usuarios.
		$model_usuarios = new Model_Usuario( (int) $_SESSION['usuario_id']);

		// Cargo las cantidades.
		$cantidad_activas = $model_usuarios->cantidad(Model_Usuario::ESTADO_ACTIVA);
		$cantidad_suspendidas = $model_usuarios->cantidad(Model_Usuario::ESTADO_SUSPENDIDA);
		$cantidad_baneadas = $model_usuarios->cantidad(Model_Usuario::ESTADO_BANEADA);
		$cantidad_total = $cantidad_activas + $cantidad_suspendidas + $cantidad_baneadas;

		// Seteo las cantidad.
		$vista->assign('cantidad_activas', $cantidad_activas);
		$vista->assign('cantidad_suspendidas', $cantidad_suspendidas);
		$vista->assign('cantidad_baneadas', $cantidad_baneadas);
		$vista->assign('cantidad_total', $cantidad_total);

		// Cargamos el listado de usuarios.
		switch ($tipo)
		{
			case 0:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina);
				$total = $cantidad_total;
				break;
			case 1:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_ACTIVA);
				$total = $cantidad_activas;
				break;
			case 2:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_SUSPENDIDA);
				$total = $cantidad_suspendidas;
				break;
			case 3:
				$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina, Model_Usuario::ESTADO_BANEADA);
				$total = $cantidad_baneadas;
				break;
		}

		// Paginación.
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			//$a['rango'] = $v->rango()->nombre;
			//$a['contenido'] = Decoda::procesar($a['contenido']);
			//$a['usuario'] = $v->usuario()->as_array();

			$lst[$k] = $a;
		}

		// Seteamos listado de noticias.
		$vista->assign('usuarios', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Suspendemos a un usuario.
	 * @param int $id ID del usuario a suspender.
	 */
	public function action_suspender_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == $_SESSION['usuario_id'])
		{
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario( (int) $id);
		if ( ! $model_usuario->existe())
		{
			Request::redirect('/admin/usuario/');
		}

		// Verifico no esté suspendido.
		$s = $model_usuario->suspension();
		if ($s !== NULL)
		{
			if ($s->restante() <= 0)
			{
				$s->anular();
			}
			else
			{
				$_SESSION['usuario_error'] = 'Usuario con suspensión en efecto.';
				Request::redirect('/admin/usuario/');
			}
		}
		unset($s);

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/suspender_usuario');

		// Información del usuario a suspender.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('motivo', '');
		$vista->assign('error_motivo', FALSE);
		$vista->assign('fin', '');
		$vista->assign('error_fin', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;
			$fin = isset($_POST['fin']) ? $_POST['fin'] : NULL;

			// Valores para cambios.
			$vista->assign('motivo', $motivo);
			$vista->assign('fin', $fin);

			// Quitamos BBCode para dimenciones.
			$motivo_clean = preg_replace('/\[([^\[\]]+)\]/', '', $motivo);

			if ( ! isset($motivo_clean{10}) || isset($motivo_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_motivo', 'El motivo debe tener entre 10 y 200 caractéres');
			}
			unset($motivo_clean);

			// Verificamos la fecha.
			if (empty($fin))
			{
				$error = TRUE;
				$vista->assign('error_fin', 'La fecha de finalización no es correcta.');
			}
			else
			{
				$fin = strtotime($fin);

				if ($fin <= time())
				{
					$error = TRUE;
					$vista->assign('error_fin', 'La fecha de finalización no es correcta.');
				}
			}

			if ( ! $error)
			{
				// Evitamos XSS.
				$motivo = htmlentities($motivo, ENT_NOQUOTES, 'UTF-8');

				// Cargamos el modelo de suspensiones.
				$model_suspension = new Model_Usuario_Suspension;
				$model_suspension->nueva($id, (int) $_SESSION['usuario_id'], $motivo	, $fin);

				// Seteamos mensaje flash y volvemos.
				$_SESSION['usuario_correcto'] = 'Usuario suspendido correctamente.';
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Quitamos la suspensión de un usuario.
	 * @param int $id ID del usuario a quitar la suspensión.
	 */
	public function action_quitar_suspension_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == $_SESSION['usuario_id'])
		{
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario( (int) $id);
		if ( ! $model_usuario->existe())
		{
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$suspension = $model_usuario->suspension();

		if ($suspension === NULL)
		{
			// Verifico el estado.
			if ($model_usuario->estado === Model_Usuario::ESTADO_SUSPENDIDA)
			{
				$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
			}
		}
		else
		{
			$suspension->anular();
			$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
		}
		// Seteamos mensaje flash y volvemos.
		$_SESSION['usuario_correcto'] = 'Suspensión anulada correctamente.';
		Request::redirect('/admin/usuario');
	}

	public function action_advertir_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == $_SESSION['usuario_id'])
		{
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/advertir_usuario');

		// Información del usuario a advertir.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('asunto', '');
		$vista->assign('error_asunto', FALSE);
		$vista->assign('contenido', '');
		$vista->assign('error_contenido', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$asunto = isset($_POST['asunto']) ? $_POST['asunto'] : NULL;
			$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : NULL;

			// Limpiamos asunto.
			$asunto = preg_replace('/\s+/', ' ', trim($asunto));

			// Valores para cambios.
			$vista->assign('asunto', $asunto);
			$vista->assign('contenido', $contenido);

			// Verifico el asunto.
			if ( ! preg_match('/^[a-záéíóúñ ,.:;\-_]{5,100}$/Di', $asunto))
			{
				$error = TRUE;
				$vista->assign('error_asunto', 'El asunto de la advertencia debe tener entre 5 y 100 caractéres alphanuméricos.');
			}

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

				// Cargamos el modelo de advertencias.
				$model_advertencia = new Model_Usuario_Aviso;
				$model_advertencia->nueva($id, (int) $_SESSION['usuario_id'], $asunto, $contenido);

				//TODO: agregar el suceso.

				// Seteamos mensaje flash y volvemos.
				$_SESSION['usuario_correcto'] = 'Advertencia enviada correctamente.';
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	public function action_banear_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == $_SESSION['usuario_id'])
		{
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			Request::redirect('/admin/usuario/');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/banear_usuario');

		//TODO: implementar tipo.

		// Información del usuario a advertir.
		$vista->assign('usuario', $model_usuario->as_array());

		// Valores por defecto y errores.
		$vista->assign('razon', '');
		$vista->assign('error_razon', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$razon = isset($_POST['razon']) ? $_POST['razon'] : NULL;

			// Valores para cambios.
			$vista->assign('razon', $razon);

			// Quitamos BBCode para dimenciones.
			$razon_clean = preg_replace('/\[([^\[\]]+)\]/', '', $razon);

			if ( ! isset($razon_clean{10}) || isset($razon_clean{200}))
			{
				$error = TRUE;
				$vista->assign('error_contenido', 'La razón debe tener entre 10 y 200 caractéres');
			}
			unset($razon_clean);

			if ( ! $error)
			{
				// Evitamos XSS.
				$razon = htmlentities($razon, ENT_NOQUOTES, 'UTF-8');

				// Cargamos el modelo de advertencias.
				$model_baneos = new Model_Usuario_Baneo;
				$model_baneos->nuevo($id, (int) $_SESSION['usuario_id'], 0, $razon);

				//TODO: agregar el suceso.

				// Seteamos mensaje flash y volvemos.
				$_SESSION['usuario_correcto'] = 'Baneo realizado correctamente.';
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Quitamos lel baneo de un usuario.
	 * @param int $id ID del usuario a quitar la suspensión.
	 */
	public function action_desbanear_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == $_SESSION['usuario_id'])
		{
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario( (int) $id);
		if ( ! $model_usuario->existe())
		{
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$baneo = $model_usuario->baneo();

		if ($baneo !== NULL)
		{
			$baneo->borrar();
		}
		// Seteamos mensaje flash y volvemos.
		$_SESSION['usuario_correcto'] = 'El baneo fue anulado correctamente.';
		Request::redirect('/admin/usuario');
	}

	/**
	 * Listado de rangos.
	 */
	public function action_rangos()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/rangos');

		// Noticia Flash.
		if (isset($_SESSION['rango_correcto']))
		{
			$vista->assign('success', get_flash('rango_correcto'));
		}

		if (isset($_SESSION['rango_error']))
		{
			$vista->assign('error', get_flash('rango_error'));
		}

		// Modelo de rangos.
		$model_rangos = new Model_Usuario_Rango;

		// Cargamos el listado de rangos.
		$lst = $model_rangos->listado();

		// Obtenemos datos de los rangos.
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Seteamos listado de rangos.
		$vista->assign('rangos', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Creamos un nuevo rango.
	 */
	public function action_nuevo_rango()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/nuevo_rango');

		// Valores por defecto y errores.
		$vista->assign('nombre', '');
		$vista->assign('error_nombre', FALSE);
		$vista->assign('color', '');
		$vista->assign('error_color', FALSE);
		$vista->assign('imagen', '');
		$vista->assign('error_imagen', FALSE);

		// Cargamos el listado de imagens para rangos disponibles.
		$imagenes_rangos = scandir(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		unset($imagenes_rangos[1], $imagenes_rangos[0]); // Quitamos . y ..

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
			$color = isset($_POST['color']) ? $_POST['color'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('color', $color);
			$vista->assign('imagen', $imagen);


			// Formateamos el nombre.
			$nombre = preg_replace('/\s+/', ' ', trim($nombre));

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre del rango deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-z]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', 'El color debe ser HEXADECIMAL de 6 digitos. Por ejemplo: 00FF00.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_rangos))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			if ( ! $error)
			{
				// Convertimos el color a entero.
				$color = hexdec($color);

				// Creamos el rango.
				$model_rango = new Model_Usuario_Rango;
				$model_rango->nuevo_rango($nombre, $color, $imagen);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				$_SESSION['rango_correcto'] = 'El rango se creó correctamente';

				// Redireccionamos.
				Request::redirect('/admin/usuario/rangos');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Editamos el rango.
	 * @param int $id ID del rango a editar.
	 */
	public function action_editar_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);
		if ( ! $model_rango->existe())
		{
			Request::redirect('/admin/usuario/rangos');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/editar_rango');

		// Cargamos el listado de imagens para rangos disponibles.
		$imagenes_rangos = scandir(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS);
		unset($imagenes_rangos[1], $imagenes_rangos[0]); // Quitamos . y ..

		$vista->assign('imagenes_rangos', $imagenes_rangos);

		// Valores por defecto y errores.
		$vista->assign('nombre', $model_rango->nombre);
		$vista->assign('error_nombre', FALSE);
		$vista->assign('color', strtoupper(dechex($model_rango->color)));
		$vista->assign('error_color', FALSE);
		$vista->assign('imagen', $model_rango->imagen);
		$vista->assign('error_imagen', FALSE);

		if (Request::method() == 'POST')
		{
			// Seteamos sin error.
			$error = FALSE;

			// Obtenemos los campos.
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : NULL;
			$color = isset($_POST['color']) ? $_POST['color'] : NULL;
			$imagen = isset($_POST['imagen']) ? $_POST['imagen'] : NULL;

			// Valores para cambios.
			$vista->assign('nombre', $nombre);
			$vista->assign('color', $color);
			$vista->assign('imagen', $imagen);

			// Formateamos el nombre.
			$nombre = preg_replace('/\s+/', ' ', trim($nombre));

			// Verificamos el nombre.
			if ( ! preg_match('/^[a-z0-9\sáéíóúñ]{5,32}$/iD', $nombre))
			{
				$error = TRUE;
				$vista->assign('error_nombre', 'El nombre del rango deben ser entre 5 y 32 caractéres alphanuméricos.');
			}

			// Verificamos el color.
			if ( ! preg_match('/^[0-9a-z]{6}$/Di', $color))
			{
				$error = TRUE;
				$vista->assign('error_color', 'El color debe ser HEXADECIMAL de 6 digitos. Por ejemplo: 00FF00.');
			}

			// Verificamos la imagen.
			if ( ! in_array($imagen, $imagenes_rangos))
			{
				$error = TRUE;
				$vista->assign('error_imagen', 'No ha seleccionado una imagen válida.');
			}

			if ( ! $error)
			{
				// Convertimos el color a entero.
				$color = hexdec($color);

				// Actualizo el color.
				if ($model_rango->color != $color)
				{
					$model_rango->cambiar_color($color);
				}

				// Actualizo el imagen.
				if ($model_rango->imagen != $imagen)
				{
					$model_rango->cambiar_imagen($imagen);
				}

				// Actualizo el nombre.
				if ($model_rango->nombre != $nombre)
				{
					$model_rango->renombrar($nombre);
				}

				// Informamos suceso.
				$vista->assign('success', 'Información actualizada correctamente');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Vemos detalles de rango. Como los permisos y usuarios asignados.
	 * @param int $id ID del rango a ver.
	 */
	public function action_ver_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);
		if ( ! $model_rango->existe())
		{
			Request::redirect('/admin/usuario/rangos');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/ver_rango');

		// Listado de permisos.
		$permisos = array();
		$permisos[0] = array('PERMISO_USUARIO_VER_DENUNCIAS', 'Ver las denuncias de usuarios y actuar sobre ellas.');
		$permisos[1] = array('PERMISO_USUARIO_SUSPENDER', 'Ver suspensiones de usuarios y modificarlas.');
		$permisos[2] = array('PERMISO_USUARIO_BANEAR', 'Ver baneos a usuarios y modificarlos.');
		$permisos[3] = array('PERMISO_USUARIO_ADVERTIR', 'Enviar advertencias a usuarios.');
		$permisos[4] = array('PERMISO_USUARIO_REVISAR_CONTENIDO', 'Revisar posts y fotos agregadas por el usuario. Es decir, el contenido creado por el usuario va a revisión antes de postearse.');
		$permisos[5] = array('PERMISO_USUARIO_ADMINISTRAR', 'Permite realizar tareas de administración de usuarios. Entre ellas está la asignación de rangos, su creación, etc.');
		$permisos[20] = array('PERMISO_POST_CREAR', 'Puede crear un post.');
		$permisos[21] = array('PERMISO_POST_PUNTUAR', 'Puede dar puntos a un post.');
		$permisos[22] = array('PERMISO_POST_ELIMINAR', 'Eliminar posts de TODOS los usuarios.');
		$permisos[23] = array('PERMISO_POST_OCULTAR', 'Oculta/muestra posts de TODOS los usuarios.');
		$permisos[24] = array('PERMISO_POST_VER_DENUNCIAS', 'Ver las denuncias de posts y actuar sobre ellas.');
		$permisos[25] = array('PERMISO_POST_VER_DESAPROBADO', 'Ver los posts que no se encuentran aprobados.');
		$permisos[26] = array('PERMISO_POST_FIJAR_PROMOVER', 'Modificar el parámetro sticky y sponsored de los posts.');
		$permisos[27] = array('PERMISO_POST_EDITAR', 'Editar posts de TODOS los usuarios.');
		$permisos[28] = array('PERMISO_POST_VER_PAPELERA', 'Ver los posts que se encuentran en la papelera de TODOS los usuarios.');
		$permisos[40] = array('PERMISO_FOTO_CREAR', 'Puede agregar fotos.');
		$permisos[41] = array('PERMISO_FOTO_VOTAR', 'Puede votar las fotos.');
		$permisos[42] = array('PERMISO_FOTO_ELIMINAR', 'Eliminar fotos de TODOS los usuarios.');
		$permisos[43] = array('PERMISO_FOTO_OCULTAR', 'Oculta/muestra fotos de TODOS los usuarios.');
		$permisos[44] = array('PERMISO_FOTO_VER_DENUNCIAS', 'Ver las denuncias y actuar sobre ellas.');
		$permisos[45] = array('PERMISO_FOTO_VER_DESAPROBADO', 'Ver el contenido que no se encuentra aprobado.');
		$permisos[46] = array('PERMISO_FOTO_EDITAR', 'Editar fotos de TODOS los usuarios.');
		$permisos[47] = array('PERMISO_FOTO_VER_PAPELERA', 'Ver la papelera de TODOS los usuarios.');
		$permisos[60] = array('PERMISO_COMENTARIO_COMENTAR', 'Crear comentarios.');
		$permisos[61] = array('PERMISO_COMENTARIO_COMENTAR_CERRADO', 'Comentar aún cuando están cerrados.');
		$permisos[62] = array('PERMISO_COMENTARIO_VOTAR', 'Puede votar comentarios.');
		$permisos[63] = array('PERMISO_COMENTARIO_ELIMINAR', 'Puede eliminar comentarios de TODOS los usuarios.');
		$permisos[64] = array('PERMISO_COMENTARIO_OCULTAR', 'Ocultar y mostrar comentarios de TODOS los usuarios.');
		$permisos[65] = array('PERMISO_COMENTARIO_EDITAR', 'Editar comentarios de TODOS los usuarios.');
		$permisos[66] = array('PERMISO_COMENTARIO_VER_DESAPROBADO', 'Ver los comentarios que se encuentran desaprobados y tomar acciones sobre ellos.');
		$permisos[80] = array('PERMISO_SITIO_ACCESO_MANTENIMIENTO', 'Puede ingresar aún con el sitio en mantenimiento.');
		$permisos[81] = array('PERMISO_SITIO_CONFIGURAR', 'Permisos para modificar configuraciones globales, acciones sobre temas y plugins. Modificar la publicidades y todo lo relacionado a configuracion general.');
		$permisos[82] = array('PERMISO_SITIO_ADMINISTRAR_CONTENIDO', 'Acceso a la administración de contenido del panel de administración.');
		$permisos[83] = array('PERMISO_SITIO_CONTROL_ACCESOS', 'Acceso a los controles de censuras, bloqueos, etc. en el panel de administración.');

		$vista->assign('permisos', $permisos);

		if (Request::method() == 'POST')
		{
			// Obtenemos permisos usuario.
			$permisos_usuario = $model_rango->permisos();

			// Obtenemos permisos marcados.
			$activos = array();
			foreach (array_keys($permisos) as $k)
			{
				if (isset($_POST[$k]))
				{
					$activos[$k] = $k;
				}
			}

			// Calculamos nuevos y quitados.
			$i = array_intersect($permisos_usuario, $activos);
			$quitar = array_diff($permisos_usuario, $i);
			$nuevos = array_diff($activos, $i);

			// Realizamos modificaciones.
			foreach ($quitar as $q)
			{
				$model_rango->borrar_permiso($q);
			}

			foreach ($nuevos as $q)
			{
				$model_rango->agregar_permiso($q);
			}

			$vista->assign('success', 'Permisos actualizados correctamente.');
		}

		// Seteamos datos del rango.
		$vista->assign('rango', $model_rango->as_array());

		// Permisos del rango.
		$vista->assign('permisos_rango', $model_rango->permisos());

		// Usuarios del rango.
		$lst = $model_rango->usuarios();
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_rangos'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos un rango.
	 * @param int $id ID del rango a borrar.
	 */
	public function action_borrar_rango($id)
	{
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango( (int) $id);

		// Verificamos exista.
		if ($model_rango->existe())
		{
			// Verificamos exista otro y además no tenga usuarios.
			if ($model_rango->tiene_usuarios())
			{
				$_SESSION['rango_error'] = 'El rango tiene usuarios y no puede ser eliminado.';
				Request::redirect('/admin/usuario/rangos');
			}

			if (count($model_rango->listado()) < 2)
			{
				$_SESSION['rango_error'] = 'No se puede eliminar al único rango existente.';
				Request::redirect('/admin/usuario/rangos');
			}

			// Borramos la noticia.
			$model_rango->borrar_rango();
			$_SESSION['rango_correcto'] = 'Se borró correctamente el rango.';
		}
		Request::redirect('/admin/usuario/rangos');
	}

	public function action_sesiones($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/sesiones');

		// Noticia Flash.
		if (isset($_SESSION['session_correcto']))
		{
			$vista->assign('success', get_flash('session_correcto'));
		}

		if (isset($_SESSION['session_error']))
		{
			$vista->assign('error', get_flash('session_error'));
		}

		// Modelo de sessiones.
		$model_session = new Model_Session(session_id());

		$model_session->limpiar();

		// Cargamos el listado de usuarios.
		$lst = $model_session->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$total = $model_session->cantidad();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['usuario'] = $v->usuario()->as_array();
			$a['ip_raw'] = $a['ip'];
			$a['ip'] = long2ip($a['ip']);

			$lst[$k] = $a;
		}

		// Seteamos listado de noticias.
		$vista->assign('sesiones', $lst);
		unset($lst);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_sesiones'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Borramos un rango.
	 * @param string $id ID de la session a borrar.
	 */
	public function action_terminar_session($id)
	{
		// Cargamos el modelo del session.
		$model_session = new Model_Session( (int) $id);

		// Verificamos exista.
		if ($model_session->existe())
		{
			// Terminamos la session.
			$model_session->borrar();
			$_SESSION['session_correcto'] = 'Se terminó correctamente la sessión.';
		}
		Request::redirect('/admin/usuario/sesiones');
	}

	public function action_nicks()
	{
		//TODO: implementar manejo de NICKS.

		// Formato de la página.
		//$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		//$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/nicks');

		// Noticia Flash.
		if (isset($_SESSION['nick_correcto']))
		{
			$vista->assign('success', get_flash('nick_correcto'));
		}

		if (isset($_SESSION['nick_error']))
		{
			$vista->assign('error', get_flash('nick_error'));
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_nicks'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
