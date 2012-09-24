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
		parent::__construct();
		//TODO: verificar permisos para la accion.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/usuario/login');
		}
	}

	/**
	 * Listado de usuarios.
	 * @param int $pagina Número de página.
	 */
	public function action_index($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/index');

		// Noticia Flash.
		if (Session::is_set('usuario_correcto'))
		{
			$vista->assign('success', Session::get_flash('usuario_correcto'));
		}

		if (Session::is_set('usuario_error'))
		{
			$vista->assign('error', Session::get_flash('usuario_error'));
		}

		// Modelo de usuarios.
		$model_usuarios = new Model_Usuario( (int) Session::get('usuario_id'));

		// Cargamos el listado de usuarios.
		$lst = $model_usuarios->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$total = $model_usuarios->cantidad();
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
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		if ($id == Session::get('usuario_id'))
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
				Session::set('usuario_error', 'Usuario con suspensión en efecto.');
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
				$model_suspension->nueva($id, (int) Session::get('usuario_id'), $motivo	, $fin);

				// Seteamos mensaje flash y volvemos.
				Session::set('usuario_correcto', 'Usuario suspendido correctamente.');
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		if ($id == Session::get('usuario_id'))
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
		Session::set('usuario_correcto', 'Suspensión anulada correctamente.');
		Request::redirect('/admin/usuario');
	}

	public function action_advertir_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Session::get('usuario_id'))
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
				$model_advertencia->nueva($id, (int) Session::get('usuario_id'), $asunto, $contenido);

				//TODO: agregar el suceso.

				// Seteamos mensaje flash y volvemos.
				Session::set('usuario_correcto', 'Advertencia enviada correctamente.');
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		if ($id == Session::get('usuario_id'))
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
				$model_baneos->nuevo($id, (int) Session::get('usuario_id'), 0, $razon);

				//TODO: agregar el suceso.

				// Seteamos mensaje flash y volvemos.
				Session::set('usuario_correcto', 'Baneo realizado correctamente.');
				Request::redirect('/admin/usuario');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		if ($id == Session::get('usuario_id'))
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
		Session::set('usuario_correcto', 'El baneo fue anulado correctamente.');
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
		if (Session::is_set('rango_correcto'))
		{
			$vista->assign('success', Session::get_flash('rango_correcto'));
		}

		if (Session::is_set('rango_error'))
		{
			$vista->assign('error', Session::get_flash('rango_error'));
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
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		//TODO: implementar funcion para obtener URL completa.
		$imagenes_rangos = scandir(APP_BASE.DS.VIEW_PATH.'default'.DS.'assets'.DS.'img'.DS.'rangos'.DS);
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
				Session::set('rango_correcto', 'El rango se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/usuario/rangos');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		//TODO: implementar funcion para obtener URL completa.
		$imagenes_rangos = scandir(APP_BASE.DS.VIEW_PATH.'default'.DS.'assets'.DS.'img'.DS.'rangos'.DS);
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
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
		$permisos[0] = array('Administrador', 'Permiso de administrador general del sitio. Engloba todas las acciones posibles.');
		$permisos[1] = array('Moderador', 'Permiso de moderador del sitio. Engloba las acciones de manejo de los contenido del sitio.');
		$permisos[2] = array('Puntuar post', 'Puede dar puntos a un post.');
		$permisos[3] = array('Crear post', 'Puede crear posts.');
		$permisos[4] = array('Comentar post', 'Puede comentar los posts que se permitan.');
		$permisos[5] = array('Votar comentario post', 'Puede votar los comentarios de los post.');
		$permisos[6] = array('Editar comentario propio', 'Puede editar sus comentarios.');
		$permisos[7] = array('Eliminar comentario propio', 'Puede eliminar sus comentarios.');
		$permisos[8] = array('Crear fotos', 'Puede cargar fotos.');
		$permisos[9] = array('Comentar fotos', 'Puede comentar fotos.');
		$permisos[10] = array('Revistar post', 'Los posts del usuario deberán ser revisados antes de ser mostrados.');
		$permisos[11] = array('Acceso mantenimiento', 'El usuario puede acceder cuando el sitio está en mantenimiento. Esto expresa que su IP estará en lista de las permitidas.');
		$permisos[12] = array('Panel moderacion', 'Pueden acceder al panel de moderación.');
		$permisos[13] = array('Denuncias usuarios', 'Ver y cancelar reportes de usuarios.');
		$permisos[14] = array('Denuncias fotos', 'Ver y cancelar reportes de fotos.');
		$permisos[15] = array('Denuncias posts', 'Ver y cancelar reportes de posts.');
		$permisos[16] = array('Denuncias mensajes', 'Ver y cancelar reportes de mensajes.');
		$permisos[17] = array('Ver usuarios baneados', 'Ver los usuarios baneados. Es para ver los elementos de los comentarios que han sido baneados.');
		$permisos[18] = array('Ver papelera posts', 'Ver los posts que hay que en la papelera y los eliminados.');
		$permisos[19] = array('Ver papelera fotos', 'Ver las fotos que hay en la papelera y eliminados.');
		$permisos[20] = array('Ver posts desaprobados', 'Ver posts que están desaprobados.');
		$permisos[21] = array('Ver comentarios desaprobados', 'Ver comentarios que están desaprobados y/o ocultos.');
		$permisos[22] = array('Fijar posts', 'Pueden fijar o quitar posts.');
		$permisos[23] = array('Ver cuentas desactivadas', 'Ver listado de usuarios con cuentas suspendidas.');
		$permisos[24] = array('Ver cuentas baneadas', 'Ver listado de usuarios con cuentas baneadas.');
		$permisos[25] = array('Suspender usuarios', 'Suspender usuarios.');
		$permisos[26] = array('Banear usuarios', 'Banear usuarios.');
		$permisos[27] = array('Eliminar posts', 'Puede eliminar posts de otros usuarios.');
		$permisos[28] = array('Editar posts', 'Puede editar posts de otros usuarios.');
		$permisos[29] = array('Ocultar posts', 'Puede ocultar posts de otros usuarios.');
		$permisos[30] = array('Comentr posts cerrado', 'Puede comentar en posts que tienen los comentarios cerrados.');
		$permisos[31] = array('Editar comentarios posts', 'Editar comentarios en los posts.');
		$permisos[32] = array('Revisar comentarios', 'Aprobar/desaprobar comentario en posts y revisión de comentarios.');
		$permisos[33] = array('Eliminar comentarios posts', 'Eliminar comentarios de los post.');
		$permisos[34] = array('Eliminar fotos', 'Eliminar fotos.');
		$permisos[35] = array('Eliminar comentario fotos', 'Eliminar comentario de las fotos.');
		$permisos[36] = array('Editar fotos', 'Editar fotos.');
		$permisos[37] = array('Eliminar publicaciones muros', 'Eliminar publicaciones en los muros.');
		$permisos[38] = array('Eliminar comentarios muros', 'Eliminar comentarios en los muros.');

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
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
				Session::set('rango_error', 'El rango tiene usuarios y no puede ser eliminado.');
				Request::redirect('/admin/usuario/rangos');
			}

			if (count($model_rango->listado()) < 2)
			{
				Session::set('rango_error', 'No se puede eliminar al único rango existente.');
				Request::redirect('/admin/usuario/rangos');
			}

			// Borramos la noticia.
			$model_rango->borrar_rango();
			Session::set('rango_correcto', 'Se borró correctamente el rango.');
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
		if (Session::is_set('session_correcto'))
		{
			$vista->assign('success', Session::get_flash('session_correcto'));
		}

		if (Session::is_set('session_error'))
		{
			$vista->assign('error', Session::get_flash('session_error'));
		}

		// Modelo de sessiones.
		$model_session = new Model_Session(Session::$id);

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
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
			Session::set('session_correcto', 'Se terminó correctamente la sessión.');
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
		if (Session::is_set('nick_correcto'))
		{
			$vista->assign('success', Session::get_flash('nick_correcto'));
		}

		if (Session::is_set('nick_error'))
		{
			$vista->assign('error', Session::get_flash('nick_error'));
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario_nicks'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
