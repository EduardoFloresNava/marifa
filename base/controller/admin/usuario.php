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

	/**
	 * Constructor de la clase.
	 * Verificamos permisos para acceder a la sección.
	 */
	public function __construct()
	{
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			$_SESSION['flash_error'] = 'Debes iniciar sessión para poder acceder a esta sección.';
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
		$pagina = $pagina > 0 ? (int) $pagina : 1;

		// TIPO, 0->todos, 1->activos, 2->suspendidos, 3->baneados
		$tipo = (int) $tipo;
		if ($tipo !== 0 && $tipo !== 1 && $tipo !== 2 && $tipo !== 3)
		{
			Request::redirect('/admin/usuario/');
		}

		// Cantidad de elementos por pagina.
		$model_configuracion = new Model_Configuracion;
		$cantidad_por_pagina = $model_configuracion->get('elementos_pagina', 20);

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/index');

		// Asigno el tipo.
		$vista->assign('tipo', $tipo);

		// Limpio antiguos.
		Model_Usuario_Suspension::clean();

		// Modelo de usuarios.
		$model_usuarios = Usuario::usuario();

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
		$vista->assign('actual', $pagina);

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
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/index/%s/'.$tipo));

		// Obtenemos datos de las noticias.
		foreach ($lst as $k => $v)
		{
			$a = $v->as_array();
			$a['rango_id'] = $v->rango;
			$a['rango'] = $v->rango()->nombre;
			$lst[$k] = $a;
		}

		// Seteamos listado de usuarios.
		$vista->assign('usuarios', $lst);
		unset($lst);

		// Cargamos listado de rangos que podemos asignar.
		$lst = Usuario::usuario()->rango()->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

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
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El usuario que quieres suspender no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario que quieres suspender no se encuentra disponible.';
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
				$_SESSION['flash_error'] = 'El usuario que quieres suspender no se encuentra disponible.';
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
				$s_id = $model_suspension->nueva($id, Usuario::$usuario_id, $motivo, $fin);

				// Envio el suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array(Usuario::$usuario_id, $id), 'usuario_suspender', $s_id);

				// Informamos el resultado.
				$_SESSION['flash_success'] = 'Usuario suspendido correctamente.';
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
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El usuario al que quieres quitar la suspensión no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario al que quieres quitar la suspensión no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$suspension = $model_usuario->suspension();

		if ($suspension === NULL)
		{
			// Verifico el estado.
			if ($model_usuario->estado === Model_Usuario::ESTADO_SUSPENDIDA)
			{
				// Actualizamos el estado.
				$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);
			}
		}
		else
		{
			// Anulamos la suspensión.
			$suspension->anular();

			// Actualizamos el estado.
			$model_usuario->actualizar_estado(Model_Usuario::ESTADO_ACTIVA);

			// Envio el suceso.
			$model_suceso = new Model_Suceso;
			$model_suceso->crear(array(Usuario::$usuario_id, $id), 'usuario_fin_suspension', $suspension->id, $suspension->restante() > 0 ? Usuario::$usuario_id : NULL);
		}
		// Informo el resultado.
		$_SESSION['flash_success'] = 'Suspensión anulada correctamente.';
		Request::redirect('/admin/usuario');
	}

	/**
	 * Enviamos una advertencia a un usuario.
	 * @param int $id ID del usuario a advertir.
	 */
	public function action_advertir_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El usuario que quieres advertir no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario que quieres advertir no se encuentra disponible.';
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

				// Creamos la advertencia.
				$model_advertencia = new Model_Usuario_Aviso;
				$adv_id = $model_advertencia->nueva($id, Usuario::$usuario_id, $asunto, $contenido);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array(Usuario::$usuario_id, $id), 'usuario_suspender', $adv_id);

				// Seteamos mensaje flash y volvemos.
				$_SESSION['flash_success'] = 'Advertencia enviada correctamente.';
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
	 * Baneamos a un usuario
	 * @param int $id ID del usuario a banear.
	 */
	public function action_banear_usuario($id)
	{
		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El usuario que quieres banear no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Aseguramos un ID entero.
		$id = (int) $id;

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario que quieres banear no se encuentra disponible.';
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

				// Baneamos al usuario.
				$model_baneos = new Model_Usuario_Baneo;
				$ban_id = $model_baneos->nuevo($id, Usuario::$usuario_id, 0, $razon);

				// Enviamos el suceso.
				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array(Usuario::$usuario_id, $id), 'usuario_baneo', $ban_id);

				// Informamos el resultado.
				$_SESSION['flash_success'] = 'Baneo realizado correctamente.';
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
		$id = (int) $id;

		// Verificamos no sea actual.
		if ($id == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El usuario que deseas banear no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($id);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario que deseas banear no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Verificamos esté suspendido.
		$baneo = $model_usuario->baneo();

		if ($baneo !== NULL)
		{
			// Quito el baneo.
			$baneo->borrar();

			// Genero el suceso.
			$model_suceso = new Model_Suceso;
			$model_suceso->crear(array(Usuario::$usuario_id, $id), 'usuario_fin_baneo', $id, Usuario::$id);
		}

		// Informo el resultado.
		$_SESSION['flash_success'] = 'El baneo fue anulado correctamente.';
		Request::redirect('/admin/usuario');
	}

	/**
	 * Cambiamos el rango de un usuario.
	 * @param int $usuario ID del usuario al que se le cambia el rango.
	 * @param int $rango ID del rango a setear.
	 */
	public function action_cambiar_rango($usuario, $rango)
	{
		$usuario = (int) $usuario;

		// Verificamos no sea actual.
		if ($usuario == Usuario::$usuario_id)
		{
			$_SESSION['flash_error'] = 'El usuario que deseas cambiarle el rango no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Cargamos el modelo del usuario.
		$model_usuario = new Model_Usuario($usuario);
		if ( ! $model_usuario->existe())
		{
			$_SESSION['flash_error'] = 'El usuario que deseas cambiarle el rango no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		// Verifico su orden.
		if ($model_usuario->rango()->es_superior(Usuario::usuario()->rango))
		{
			$_SESSION['flash_error'] = 'El usuario que deseas cambiarle el rango no se encuentra disponible.';
			Request::redirect('/admin/usuario/');
		}

		$rango = (int) $rango;

		// Verifico el rango.
		$model_rango = new Model_Usuario_Rango($rango);
		if ($model_rango->existe())
		{
			// Verifico el nivel.
			if ($rango == Usuario::usuario()->rango || $model_rango->es_superior(Usuario::usuario()->rango))
			{
				$_SESSION['flash_error'] = 'Rango que deseas asignar no se encuentra disponible.';
				Request::redirect('/admin/usuario/');
			}

			// Actualizo el rango.
			$model_usuario->actualizar_campo('rango', $rango);

			// Envio el suceso.
			$model_suceso = new Model_Suceso;
			$model_suceso->crear(array(Usuario::$usuario_id, $model_usuario->id), 'usuario_cambio_rango	', $model_usuario->id, $rango, Usuario::$usuario_id);

			// Informo el resultado.
			$_SESSION['flash_success'] = 'El rango fue cambiado correctamente correctamente.';
			Request::redirect('/admin/usuario');
		}

		// Cargo la vista.
		$vista = View::factory('admin/usuario/cambiar_rango');

		// Seteo la información.
		$vista->assign('usuario', $model_usuario->as_array());

		// Cargamos los rangos.
		$lst = $model_rango->listado(Usuario::usuario()->rango()->orden);
		foreach ($lst as $k => $v)
		{
			$lst[$k] = $v->as_array();
		}
		$vista->assign('rangos', $lst);

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('usuario'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de rangos.
	 */
	public function action_rangos()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/usuario/rangos');

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
	 * Cambiamos el orden de un rango.
	 * @param int $rango ID del rango al que cambiar su orden.
	 * @param int $posicion Posición que debe adoptar el nuevo rango. Empieza en 1.
	 */
	public function action_mover_rango($rango, $posicion)
	{
		// Verifico la posición.
		$posicion = (int) $posicion;
		if ($posicion <= 0)
		{
			$_SESSION['flash_error'] = 'La posición que deseas asignar no es correcta.';
			Request::redirect('/admin/usuario/rangos');
		}

		$rango = (int) $rango;

		// Verifico existencia del rango.
		$model_rango = new Model_Usuario_Rango($rango);
		if ( ! $model_rango->existe())
		{
			$_SESSION['flash_error'] = 'El rango que deseas mover no se encuentra disponible.';
			Request::redirect('/admin/usuario/rangos');
		}

		// Verifico la posición.
		if ($model_rango->orden === $posicion || $posicion > $model_rango->cantidad())
		{
			$_SESSION['flash_error'] = 'La posición que deseas asignar no es correcta.';
			Request::redirect('/admin/usuario/rangos');
		}

		// Asignamos la posición.
		$model_rango->posicionar($posicion);

		// Informamos.
		$_SESSION['flash_success'] = 'El rango se ha movido correctamente.';
		Request::redirect('/admin/usuario/rangos');
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
			if ( ! preg_match('/^[0-9a-f]{6}$/Di', $color))
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
		$id = (int) $id;
		// Cargamos el modelo del rango.
		$model_rango = new Model_Usuario_Rango($id);
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
		$vista->assign('color', strtoupper(sprintf('%06s', dechex($model_rango->color))));
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
			if ( ! preg_match('/^[0-9a-f]{6}$/Di', $color))
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
		$permisos[0] = array('Permiso usuario ver denuncias', 'Ver las denuncias de usuarios y actuar sobre ellas.');
		$permisos[1] = array('Permiso usuario suspender', 'Ver suspensiones de usuarios y modificarlas.');
		$permisos[2] = array('Permiso usuario banear', 'Ver baneos a usuarios y modificarlos.');
		$permisos[3] = array('Permiso usuario advertir', 'Enviar advertencias a usuarios.');
		$permisos[4] = array('Permiso usuario revisar contenido', 'Revisar posts y fotos agregadas por el usuario. Es decir, el contenido creado por el usuario va a revisión antes de postearse.');
		$permisos[5] = array('Permiso usuario administrar', 'Permite realizar tareas de administración de usuarios. Entre ellas está la asignación de rangos, su creación, etc.');
		$permisos[20] = array('Permiso post crear', 'Puede crear un post.');
		$permisos[21] = array('Permiso post puntuar', 'Puede dar puntos a un post.');
		$permisos[22] = array('Permiso post eliminar', 'Eliminar posts de todos los usuarios.');
		$permisos[23] = array('Permiso post ocultar', 'Oculta/muestra posts de todos los usuarios.');
		$permisos[24] = array('Permiso post ver denuncias', 'Ver las denuncias de posts y actuar sobre ellas.');
		$permisos[25] = array('Permiso post ver desaprobado', 'Ver los posts que no se encuentran aprobados.');
		$permisos[26] = array('Permiso post fijar promover', 'Modificar el parámetro sticky y sponsored de los posts.');
		$permisos[27] = array('Permiso post editar', 'Editar posts de todos los usuarios.');
		$permisos[28] = array('Permiso post ver papelera', 'Ver los posts que se encuentran en la papelera de todos los usuarios.');
		$permisos[40] = array('Permiso foto crear', 'Puede agregar fotos.');
		$permisos[41] = array('Permiso foto votar', 'Puede votar las fotos.');
		$permisos[42] = array('Permiso foto eliminar', 'Eliminar fotos de todos los usuarios.');
		$permisos[43] = array('Permiso foto ocultar', 'Oculta/muestra fotos de todos los usuarios.');
		$permisos[44] = array('Permiso foto ver denuncias', 'Ver las denuncias y actuar sobre ellas.');
		$permisos[45] = array('Permiso foto ver desaprobado', 'Ver el contenido que no se encuentra aprobado.');
		$permisos[46] = array('Permiso foto editar', 'Editar fotos de todos los usuarios.');
		$permisos[47] = array('Permiso foto ver papelera', 'Ver la papelera de TODOS los usuarios.');
		$permisos[60] = array('Permiso comentario comentar', 'Crear comentarios.');
		$permisos[61] = array('Permiso comentario comentar cerrado', 'Comentar aún cuando están cerrados.');
		$permisos[62] = array('Permiso comentario votar', 'Puede votar comentarios.');
		$permisos[63] = array('Permiso comentario eliminar', 'Puede eliminar comentarios de todos los usuarios.');
		$permisos[64] = array('Permiso comentario ocultar', 'Ocultar y mostrar comentarios de todos los usuarios.');
		$permisos[65] = array('Permiso comentario editar', 'Editar comentarios de todos los usuarios.');
		$permisos[66] = array('Permiso comentario ver desaprobado', 'Ver los comentarios que se encuentran desaprobados y tomar acciones sobre ellos.');
		$permisos[80] = array('Permiso sitio acceso mantenimiento', 'Puede ingresar aún con el sitio en mantenimiento.');
		$permisos[81] = array('Permiso sitio configurar', 'Permisos para modificar configuraciones globales, acciones sobre temas y plugins. modificar la publicidades y todo lo relacionado a configuracion general.');
		$permisos[82] = array('Permiso sitio administrar contenido', 'Acceso a la administración de contenido del panel de administración.');

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
				$_SESSION['flash_error'] = 'El rango tiene usuarios y no puede ser eliminado.';
				Request::redirect('/admin/usuario/rangos');
			}

			if (count($model_rango->listado()) < 2)
			{
				$_SESSION['flash_error'] = 'No se puede eliminar al único rango existente.';
				Request::redirect('/admin/usuario/rangos');
			}

			// Borramos la noticia.
			$model_rango->borrar_rango();
			$_SESSION['flash_success'] = 'Se borró correctamente el rango.';
		}
		Request::redirect('/admin/usuario/rangos');
	}

	/**
	 * Listado de sessiones de usuarios activas.
	 * @param int $pagina Número de página a mostrar.
	 */
	public function action_sesiones($pagina)
	{
		// Formato de la página.
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/usuario/sesiones');

		// Modelo de sessiones.
		$model_session = new Model_Session(session_id());

		// Quitamos sessiones terminadas.
		$model_session->limpiar();

		// Cargamos el listado de usuarios.
		$lst = $model_session->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$paginador = new Paginator($model_session->cantidad(), $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->get_view($pagina, '/admin/usuario/sessiones/%s/'));

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
			$_SESSION['flash_success'] = 'Se terminó correctamente la sessión.';
		}
		Request::redirect('/admin/usuario/sesiones');
	}
}
