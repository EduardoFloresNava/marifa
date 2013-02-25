<?php
/**
 * configuracion.php is part of Marifa.
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
 * Controlador para administrar configuraciones.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Configuracion extends Controller {

	/**
	 * Verificamos los permisos.
	 */
	public function before()
	{
		// Verifico estar logueado.
		if ( ! Usuario::is_login())
		{
			Request::redirect('/usuario/login');
		}

		// Verifico los permisos.
		if ( ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR))
		{
			add_flash_message(FLASH_ERROR, __('No tienes permisos para acceder a esa sección.', FALSE));
			Request::redirect('/');
		}

		parent::before();
	}

	/**
	 * Portada de las configuraciones.
	 */
	public function action_index()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/index');

		// Cargamos las configuraciones.
		//TODO: Implementar sistema más flexible de carga y edición.
		$model_configuracion = new Model_Configuracion;

		// Cargamos los datos iniciales.
		$vista->assign('nombre', $model_configuracion->get('nombre', 'Marifa'));
		$vista->assign('error_nombre', FALSE);
		$vista->assign('success_nombre', FALSE);
		$vista->assign('descripcion', $model_configuracion->get('descripcion', 'Tu comunidad de forma simple'));
		$vista->assign('error_descripcion', FALSE);
		$vista->assign('success_descripcion', FALSE);
		$vista->assign('registro', (int) $model_configuracion->get('registro', 1));
		$vista->assign('error_registro', FALSE);
		$vista->assign('success_registro', FALSE);
		$vista->assign('activacion_usuario', (int) $model_configuracion->get('activacion_usuario', 1));
		$vista->assign('error_activacion_usuario', FALSE);
		$vista->assign('success_activacion_usuario', FALSE);
		$vista->assign('rango_defecto', (int) $model_configuracion->get('rango_defecto', 3));
		$vista->assign('error_rango_defecto', FALSE);
		$vista->assign('success_rango_defecto', FALSE);
		$vista->assign('habilitar_fotos', (bool) $model_configuracion->get('habilitar_fotos', 1));
		$vista->assign('error_habilitar_fotos', FALSE);
		$vista->assign('success_habilitar_fotos', FALSE);
		$vista->assign('privacidad_fotos', (bool) $model_configuracion->get('privacidad_fotos', 1));
		$vista->assign('error_privacidad_fotos', FALSE);
		$vista->assign('success_privacidad_fotos', FALSE);

		$vista->assign('elementos_pagina', (int) $model_configuracion->get('elementos_pagina', 20));
		$vista->assign('error_elementos_pagina', FALSE);
		$vista->assign('success_elementos_pagina', FALSE);

		// Cargo listado rangos.
		$model_rangos = new Model_Usuario_Rango;
		$vista->assign('rangos_permitidos', $model_rangos->to_list());

		if (Request::method() == 'POST')
		{
			// Verifico el nombre.
			if (isset($_POST['nombre']))
			{
				// Limpio el valor.
				$nombre = preg_replace('/\s+/', ' ', trim($_POST['nombre']));

				// Seteo el nuevo valor a la vista.
				$vista->assign('nombre', $nombre);

				// Verifico el contenido.
				if ( ! preg_match('/^[a-z0-9áéíóúñ !\-_\.]{2,20}$/iD', $nombre))
				{
					$vista->assign('error_nombre', __('El nombre debe tener entre 2 y 20 caracteres. Pueden ser letras, números, espacios, !, -, _, . y \\', FALSE));
				}
				else
				{
					if ($nombre !== $model_configuracion->get('nombre', NULL))
					{
						$model_configuracion->nombre = $nombre;
						$vista->assign('success_nombre', __('El nombre se ha actualizado correctamente.', FALSE));
					}
				}
			}

			// Verifico la descripción.
			if (isset($_POST['descripcion']))
			{
				// Limpio el valor.
				$descripcion = preg_replace('/\s+/', ' ', trim($_POST['descripcion']));

				// Seteo el nuevo valor a la vista.
				$vista->assign('descripcion', $descripcion);

				// Verifico el contenido.
				if ( ! preg_match('/^[a-z0-9áéíóúñ !\-_\.]{5,30}$/iD', $descripcion))
				{
					$vista->assign('error_descripcion', __('La descripción debe tener entre 5 y 30 caracteres. Pueden ser letras, números, espacios, !, -, _, . y \\', FALSE));
				}
				else
				{
					if ($descripcion !== $model_configuracion->get('descripcion', NULL))
					{
						$model_configuracion->descripcion = $descripcion;
						$vista->assign('success_descripcion', __('La descripción se ha actualizado correctamente.', FALSE));
					}
				}
			}

			// Verifico el registro.
			if (isset($_POST['registro']))
			{
				// Limpio el valor.
				$registro = (bool) $_POST['registro'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('registro', $registro);

				// Actualizo el valor.
				$actual = $model_configuracion->get('registro', NULL);
				if ($actual === NULL || $registro !== (bool) $actual)
				{
					$model_configuracion->registro = $registro;
					$vista->assign('success_registro', __('El registro se ha editado correctamente.', FALSE));
				}
			}

			// Verifico como se activan los usuarios.
			if (isset($_POST['activacion_usuario']))
			{
				// Limpio el valor.
				$activacion_usuario = (int) $_POST['activacion_usuario'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('activacion_usuario', $activacion_usuario);

				// Verifico el valor.
				if ($registro == 0 || $registro == 1 || $registro == 0)
				{
					// Actualizo el valor.
					$actual = $model_configuracion->get('activacion_usuario', NULL);
					if ($actual === NULL || $activacion_usuario !== (int) $actual)
					{
						$model_configuracion->activacion_usuario = $activacion_usuario;
						$vista->assign('success_activacion_usuario', __('La forma de activación se ha actualizado correctamente.', FALSE));
					}
				}
				else
				{
					$vista->assign('error_activacion_usuario', __('La forma de activación seleccionada no es válida.', FALSE));
				}
			}

			// Verifico rango por defecto.
			if (isset($_POST['rango_defecto']))
			{
				// Limpio el valor.
				$rango_defecto = (int) $_POST['rango_defecto'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('rango_defecto', $rango_defecto);

				// Verifico el valor.
				if (in_array($rango_defecto, array_keys($model_rangos->to_list())))
				{
					// Actualizo el valor.
					$actual = $model_configuracion->get('rango_defecto', NULL);
					if ($actual === NULL || $rango_defecto !== (int) $actual)
					{
						$model_configuracion->rango_defecto = $rango_defecto;
						$vista->assign('success_rango_defecto', __('Se ha actualizado el rango para los usuarios por defecto.', FALSE));
					}
				}
				else
				{
					$vista->assign('error_rango_defecto', __('El rango seleccionado no es correcto.', FALSE));
				}
			}

			// Verifico el estado de las fotos.
			if (isset($_POST['habilitar_fotos']))
			{
				// Limpio el valor.
				$habilitar_fotos = (bool) $_POST['habilitar_fotos'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('habilitar_fotos', $habilitar_fotos);

				// Actualizo el valor.
				$actual = $model_configuracion->get('habilitar_fotos', NULL);
				if ($actual === NULL || $habilitar_fotos !== (bool) $actual)
				{
					$model_configuracion->habilitar_fotos = $habilitar_fotos;
					$vista->assign('success_habilitar_fotos', __('El estado de las fotos se ha editado correctamente.', FALSE));
				}
			}

			// Verifico la privacidad de las fotos.
			if (isset($_POST['privacidad_fotos']))
			{
				// Limpio el valor.
				$privacidad_fotos = (bool) $_POST['privacidad_fotos'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('privacidad_fotos', $privacidad_fotos);

				// Actualizo el valor.
				$actual = $model_configuracion->get('privacidad_fotos', NULL);
				if ($actual === NULL || $privacidad_fotos !== (bool) $actual)
				{
					$model_configuracion->privacidad_fotos = $privacidad_fotos;
					$vista->assign('success_habilitar_fotos', __('La privacidad de las fotos se ha editado correctamente.', FALSE));
				}
			}

			// Verifico como se activan los usuarios.
			if (isset($_POST['elementos_pagina']))
			{
				// Limpio el valor.
				$elementos_pagina = (int) $_POST['elementos_pagina'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('elementos_pagina', $elementos_pagina);

				// Verifico el valor.
				if ($elementos_pagina < 5 || $elementos_pagina > 100)
				{
					$vista->assign('error_elementos_pagina', __('La cantidad de elementos por página ser un entero entre 5 y 100.', FALSE));
				}
				else
				{
					// Actualizo el valor.
					$actual = $model_configuracion->get('elementos_pagina', NULL);
					if ($actual === NULL || $elementos_pagina !== (int) $actual)
					{
						$model_configuracion->elementos_pagina = $elementos_pagina;
						$vista->assign('success_elementos_pagina', __('La cantidad de elementos por página se ha actualizado correctamente.', FALSE));
					}
				}
			}
		}

		// Seteamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion.configuracion'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Configuración del modo mantenimiento.
	 */
	public function action_mantenimiento()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/mantenimiento');

		// Cargo listado de IP's que pueden acceder en modo mantenimiento.
		$model_configuracion = new Model_Configuracion;
		$ips_matenimiento = unserialize($model_configuracion->get('ip_mantenimiento', 'a:0:{}'));

		// Datos del hard-lock.
		$vista->assign('ip', implode(PHP_EOL, $ips_matenimiento));
		$vista->assign('error_ip', FALSE);
		$vista->assign('success_ip', FALSE);

		if (Request::method() == 'POST')
		{
			// Verifico accion.
			$accion = arr_get($_POST, 'submit', NULL);

			if ($accion == NULL)
			{
				// Obtengo el listado de IP's.
				$ips = isset($_POST['ip']) ? explode(PHP_EOL, trim($_POST['ip'])) : array();

				// Verifico cada uno de los IP's.
				$error = FALSE;
				foreach ($ips as $k => $ip)
				{
					$ip = trim($ip);
					$ips[$k] = $ip;

					// Verifico IP.
					if ($ip == long2ip(ip2long($ip)))
					{
						continue;
					}

					// Verifico rango del tipo a.b.c.d-a.b.c.d
					if (strpos($ip, '-'))
					{
						list($a, $b) = explode('-', $ip);
						if ($a != long2ip(ip2long($a)) || $b != long2ip(ip2long($b)))
						{
							$error = TRUE;
							break;
						}
						else
						{
							continue;
						}
					}

					$error = TRUE;
					break;

					//TODO: agregar soporte a rangos faltantes (CIFS /netmask,  *).
				}

				// Asigno valor a la vista.
				$vista->assign('ip', implode(PHP_EOL, $ips));

				if ($error)
				{
					$vista->assign('error_ip', __('Los IP\'s ingresados no son válidos.', FALSE));
				}
				else
				{
					// Verifico si hay cambios.
					if (count(array_diff($ips, $ips_matenimiento)) > 0)
					{
						// Actualizo los valores.
						$model_configuracion->ip_mantenimiento = serialize($ips);
						$ips_matenimiento = $ips;

						// Actualizo si es necesario.
						if (Mantenimiento::is_locked())
						{
							Mantenimiento::lock($ips);
						}

						// Informo resultado.
						$vista->assign('success_ip', __('Listado de IP\'s actualizada correctamente.', FALSE));
					}
				}
			}
			elseif ($accion == 'agregar-rango')
			{
				// Obtengo el rango.
				$rango = (int) arr_get($_POST, 'nuevo-rango', 0);

				// Verifico existencia del rango.
				$o_rango = new Model_Usuario_Rango($rango);

				if ( ! $o_rango->existe())
				{
					$vista->assign('error_rango_nuevo', __('El rango que quiere agregar es incorrecto.', FALSE));
				}
				else
				{
					// Verifico permisos.
					if ($o_rango->tiene_permiso(Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO))
					{
						$vista->assign('error_rango_nuevo', __('El rango ya tiene acceso en modo mantenimiento.', FALSE));
					}
					else
					{
						// Agrego el permiso.
						$o_rango->agregar_permiso(Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO);

						add_flash_message(FLASH_SUCCESS, __('Se le ha dado acceso en modo mantenimiento al rango correctamente.', FALSE));
					}
				}
			}
			elseif ($accion == 'agregar-usuario')
			{
				// Obtengo el usuario.
				$usuario = arr_get($_POST, 'nuevo-usuario', '');

				// Verifico existencia del usuario.
				$o_usuario = new Model_Usuario();

				if ( ! $o_usuario->exists_nick($usuario))
				{
					$vista->assign('error_usuario_nuevo', __('El usuario que quiere agregar es incorrecto.', FALSE));
				}
				else
				{
					$o_usuario->load_by_nick($usuario);

					// Usuarios actuales.
					$u_act = Mantenimiento::usuarios_permitidos();


					// Verifico permisos.
					if (in_array($o_usuario->id, $u_act))
					{
						$vista->assign('error_usuario_nuevo', __('El usuario ya tiene acceso en modo mantenimiento.', FALSE));
					}
					else
					{
						$u_act[] = $o_usuario->id;

						// Agrego el usuario.
						Utils::configuracion()->mantenimiento_usuarios = serialize($u_act);

						add_flash_message(FLASH_SUCCESS, __('Se le ha dado acceso en modo mantenimiento al usuario correctamente.', FALSE));
					}
				}
			}
		}

		// Datos del soft-lock.
		$g_lst_aux = Mantenimiento::grupos_permitidos();
		$g_lst = $g_lst_aux;
		foreach ($g_lst as $k => $v)
		{
			$g_lst[$k] = Model::factory('Usuario_Rango', $v)->as_array();
		}
		$vista->assign('rangos', $g_lst);

		$r_lst_aux = Model::factory('Usuario_Rango')->listado();
		$r_lst = array();
		foreach ($r_lst_aux as $v)
		{
			if ( ! in_array($v->id, $g_lst_aux))
			{
				$r_lst[] = $v->as_array();
			}
		}
		$vista->assign('rangos_disponibles', $r_lst);
		unset($r_lst_aux);

		$u_lst = Mantenimiento::usuarios_permitidos();
		foreach ($u_lst as $k => $v)
		{
			$u_lst[$k] = Model::factory('Usuario', $v)->as_array();
		}
		$vista->assign('usuarios', $u_lst);

		// Verifico si está habilitado el bloqueo.
		$vista->assign('is_locked_hard', Mantenimiento::is_locked());
		$vista->assign('is_locked_soft', Mantenimiento::is_locked(FALSE));

		// Verifico permisos.
		if (Mantenimiento::is_locked())
		{
			$locked_for_me_ip = FALSE;
		}
		else
		{
			$locked_for_me_ip = TRUE;
			$my_ip = get_ip_addr();
			foreach ($ips_matenimiento as $ip)
			{
				if ($my_ip == $ip || IP::ip_in_range($my_ip, $ip))
				{
					$locked_for_me_ip = FALSE;
					break;
				}
			}
			unset($my_ip);
		}
		$vista->assign('locked_for_me_ip', $locked_for_me_ip);
		unset($locked_for_me_ip);

		if (Mantenimiento::is_locked(FALSE))
		{
			$locked_for_me_usuario = FALSE;
		}
		else
		{
			$locked_for_me_usuario = ! (in_array(Usuario::$usuario_id, Mantenimiento::usuarios_permitidos()) || in_array(Usuario::usuario()->rango, Mantenimiento::grupos_permitidos()));
		}
		$vista->assign('locked_for_me_usuario', $locked_for_me_usuario);
		unset($locked_for_me_usuario);

		// Seteamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion.mantenimiento'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Quito el rango de la lista que puede entrar en modo mantenimiento.
	 * @param int $rango Rango a quitar.
	 */
	public function action_mantenimiento_quitar_rango($rango)
	{
		// Cargo el rango.
		$o_rango = new Model_Usuario_Rango( (int) $rango);

		// Verifico existencia.
		if ( ! $o_rango->existe())
		{
			add_flash_message(FLASH_ERROR, __('El rango que deseas sacar de la lista de mantenimiento no es correcto.', FALSE));
			Request::redirect('/admin/configuracion/mantenimiento/');
		}

		// Verifico permiso.
		if ( ! $o_rango->tiene_permiso(Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO))
		{
			add_flash_message(FLASH_ERROR, __('El rango que deseas sacar de la lista de mantenimiento no es correcto.', FALSE));
			Request::redirect('/admin/configuracion/mantenimiento/');
		}

		// Quito permiso.
		$o_rango->borrar_permiso(Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO);

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, __('El rango se ha quitado correctamente de la lista de mantenimiento.', FALSE));
		Request::redirect('/admin/configuracion/mantenimiento/');
	}

	/**
	 * Quito el usuario de la lista que puede acceder en modo mantenimiento.
	 * @param int $usuario ID del usuario a quitar.
	 */
	public function action_mantenimiento_quitar_usuario($usuario)
	{
		// Aseguro tipo del usuario.
		$usuario = (int) $usuario;

		// Cargo lista de usuarios.
		$u_lst = Mantenimiento::usuarios_permitidos();

		// Verifico existencia del usuario.
		if (in_array($usuario, $u_lst))
		{
			// Actualizo lista.
			Utils::configuracion()->mantenimiento_usuarios = serialize(array_diff($u_lst, array($usuario)));

			// Informo resultado.
			add_flash_message(FLASH_SUCCESS, __('El rango se ha quitado correctamente de la lista de mantenimiento.', FALSE));
			Request::redirect('/admin/configuracion/mantenimiento/');
		}
		else
		{
			add_flash_message(FLASH_ERROR, __('El usuario que deseas sacar de la lista de mantenimiento no es correcto.', FALSE));
			Request::redirect('/admin/configuracion/mantenimiento/');
		}
	}

	/**
	 * Activo/Desactivo el modo mantenimiento.
	 * @param bool $tipo 0 para deshabilitar, 1 para habilitar.
	 * @param bool $hard 0 para habilitar por IP, 1 para habilitar por Usuario.
	 */
	public function action_habilitar_mantenimiento($tipo, $hard)
	{
		$tipo = (bool) $tipo;
		$hard = (bool) $hard;

		// Verifico según acción.
		if ($tipo)
		{
			// Verifico no exista bloqueo.
			if (Mantenimiento::is_locked() || Mantenimiento::is_locked(FALSE))
			{
				add_flash_message(FLASH_ERROR, __('El modo mantenimiento ya se encuentra activo.', FALSE));
				Request::redirect('/admin/configuracion/mantenimiento/');
			}

			// Activo el bloqueo.
			Mantenimiento::lock($hard);

			// Envío notificación.
			add_flash_message(FLASH_SUCCESS, __('El modo mantenimiento se ha activado correctamente.', FALSE));
			Request::redirect('/admin/configuracion/mantenimiento/');
		}
		else
		{
			// Desactivo el mantenimiento.
			Mantenimiento::unlock();

			// Envío notificación.
			add_flash_message(FLASH_SUCCESS, __('El modo mantenimiento se ha desactivado correctamente.', FALSE));
			Request::redirect('/admin/configuracion/mantenimiento/');
		}
	}

	/**
	 * Configuración del envió de correos.
	 */
	public function action_correo($correo)
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/correo');

		// Verifico si está configurado.
		if ( ! file_exists(CONFIG_PATH.DS.'email.php'))
		{
			$vista->assign('configuracion', NULL);
		}
		else
		{
			// Cargo la configuración actual.
			$configuracion = configuracion_obtener(CONFIG_PATH.DS.'email.php');

			// Envío la configuración.
			$vista->assign('configuracion', $configuracion);

			// Mi correo.
			$vista->assign('email', $correo !== NULL ? urldecode($correo) : Usuario::usuario()->email);
		}

		// Seteamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion.correo'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Enviamos un correo de prueba para verificar que todo sea correcto.
	 */
	public function action_test_mail()
	{
		// Verifico el método de envío.
		if (Request::method() !== 'POST')
		{
			add_flash_message(FLASH_ERROR, __('No puedes enviar un correo de prueba si no especificas el destinatario.', FALSE));
			Request::redirect('/admin/configuracion/correo');
		}

		// Verifico que se encuentre configurado.
		if ( ! file_exists(CONFIG_PATH.DS.'email.php'))
		{
			add_flash_message(FLASH_ERROR, __('No puedes enviar un correo de prueba ya que no has lo has configurado.', FALSE));
			Request::redirect('/admin/configuracion/correo');
		}

		// Verifico el correo enviado.
		if ( ! isset($_POST['email']) || ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $_POST['email']))
		{
			add_flash_message(FLASH_ERROR, __('La casilla de correo ingresada no es válida.', FALSE));
			Request::redirect('/admin/configuracion/correo/'.(isset($_POST['email']) ? urlencode($_POST['email']) : '' ));
		}

		// Cargo el modelo de configuraciones.
		$model_config = new Model_Configuracion;

		// Creo el mensaje de correo.
		$message = Email::get_message();
		$message->setSubject(sprintf(__('Verificación configuración correos de %s', FALSE), $model_config->get('nombre', 'Marifa')));
		$message->setTo($_POST['email']);

		// Cargo la vista.
		$message_view = View::factory('emails/test');
		$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
		$message->setBody($message_view->parse());
		unset($message_view);

		// Envío el correo electrónico.
		$mailer = Email::get_mailer();
		$mailer->send($message);

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('El correo de prueba se ha enviado correctamente.', FALSE));
		Request::redirect('/admin/configuracion/correo');
	}

	/**
	 * Configuración de todas las opciones relacionadas al SEO.
	 */
	public function action_seo()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/seo');

		// Cargamos las configuraciones.
		$model_configuracion = new Model_Configuracion;

		// Cargamos los datos iniciales.
		$vista->assign('largo_minimo', (int) $model_configuracion->get('keyword_largo_minimo', 3));
		$vista->assign('error_largo_minimo', FALSE);
		$vista->assign('success_largo_minimo', FALSE);
		$vista->assign('cantidad_minima_ocurrencias', (int) $model_configuracion->get('keyword_ocurrencias_minima', 2));
		$vista->assign('error_cantidad_minima_ocurrencias', FALSE);
		$vista->assign('success_cantidad_minima_ocurrencias', FALSE);
		$vista->assign('palabras_comunes', unserialize($model_configuracion->get('keyword_palabras_comunes', 'a:0:{}')));
		$vista->assign('error_palabras_comunes', FALSE);
		$vista->assign('success_palabras_comunes', FALSE);

		if (Request::method() == 'POST')
		{
			// Verifico el largo mínimo.
			if (isset($_POST['largo_minimo']))
			{
				// Limpio el valor.
				$largo_minimo = (int) $_POST['largo_minimo'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('largo_minimo', $largo_minimo);

				// Verifico el contenido.
				if ($largo_minimo < 0)
				{
					$vista->assign('error_largo_minimo', __('El largo mínimo debe ser mayor o igual a 0 (cero).', FALSE));
				}
				else
				{
					if ($largo_minimo != $model_configuracion->get('keyword_largo_minimo', NULL))
					{
						$model_configuracion->keyword_largo_minimo = $largo_minimo;
						$vista->assign('success_largo_minimo', __('El largo mínimo se ha actualizado correctamente.', FALSE));
					}
				}
			}

			// Verifico la cantidad de ocurrencias mínima.
			if (isset($_POST['cantidad_minima_ocurrencias']))
			{
				// Limpio el valor.
				$cantidad_minima_ocurrencias = (int) $_POST['cantidad_minima_ocurrencias'];

				// Seteo el nuevo valor a la vista.
				$vista->assign('cantidad_minima_ocurrencias', $cantidad_minima_ocurrencias);

				// Verifico el contenido.
				if ($cantidad_minima_ocurrencias < 1)
				{
					$vista->assign('error_cantidad_minima_ocurrencias', __('La cantidad de ocurrencias mínima debe ser mayor o igual a 1.', FALSE));
				}
				else
				{
					if ($cantidad_minima_ocurrencias != $model_configuracion->get('keyword_ocurrencias_minima', NULL))
					{
						$model_configuracion->keyword_ocurrencias_minima = $cantidad_minima_ocurrencias;
						$vista->assign('success_cantidad_minima_ocurrencias', __('La cantidad de ocurrencias mínima se ha actualizado correctamente.', FALSE));
					}
				}
			}

			// Verifico las palabras no permitidas.
			if (isset($_POST['palabras_comunes']))
			{
				// Limpio el valor.
				$palabras_comunes = trim($_POST['palabras_comunes']);

				if ( ! empty($palabras_comunes))
				{
					// Obtengo la lista.
					$keyword_list = explode("\n", $palabras_comunes);

					// Quito espacios de cada una y verifico valides.
					$error = FALSE;
					foreach ($keyword_list as $k => $v)
					{
						// Quito espacios.
						$v = trim($v);

						if ( ! isset($v{0}))
						{
							$error = $v;
							break;
						}

						// Verifico sea correcto.
						if (preg_match('/\s+/', $v))
						{
							$error = $v;
							break;
						}

						// Inserto nueva palabra.
						$keyword_list[$k] = $v;
					}
				}
				else
				{
					$keyword_list = array();
					$error = FALSE;
				}

				// Seteo el nuevo valor a la vista.
				$vista->assign('palabras_comunes', $keyword_list);

				// Verifico el contenido.
				if ($error !== FALSE)
				{
					$vista->assign('error_palabras_comunes', sprintf(__('La lista de palabras claves no permitidas deben ser una por linea. \'%s\' no es correcta.', FALSE), $error));
				}
				else
				{
					if (serialize($keyword_list) != $model_configuracion->get('keyword_palabras_comunes', NULL))
					{
						$model_configuracion->keyword_palabras_comunes = serialize($keyword_list);
						$vista->assign('success_palabras_comunes', __('La lista de palabras claves no permitidas se ha actualizado correctamente.', FALSE));
					}
				}
			}
		}

		// Seteamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion.seo'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Configuración de la base de datos.
	 */
	public function action_bd()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/bd');

		// Verifico permisos de escritura.
		$vista->assign('error_permisos', ! is_writable(CONFIG_PATH.DS.'database.php'));

		// Cargamos configuraciones de la base de datos.
		$config = configuracion_obtener(CONFIG_PATH.DS.'database.php');

		// Armo listado de drivers disponibles.
		$drivers = array();

		if (function_exists('mysql_connect'))
		{
			$drivers['mysql'] = 'MySQL';
		}

		if (function_exists('mysqli_connect'))
		{
			$drivers['mysqli'] = 'MySQLi';
		}

		if (class_exists('pdo'))
		{
			$drivers['pdo'] = 'PDO';
		}

		$vista->assign('drivers', $drivers);


		// Seteo sin errores.
		$vista->assign('error_driver', FALSE);
		$vista->assign('error_host', FALSE);
		$vista->assign('error_db_name', FALSE);
		$vista->assign('error_usuario', FALSE);
		$vista->assign('error_password', FALSE);

		if (Request::method() == 'POST')
		{
			// Verifico los campos.
			$error = FALSE;
			foreach (array('driver', 'host', 'db_name', 'usuario', 'password') as $v)
			{
				if (isset($_POST[$v]))
				{
					$$v = $_POST[$v];
					$vista->assign($v, $_POST[$v]);
				}
				else
				{
					$error = TRUE;
					$vista->assign($v, '');
					$vista->assign('error_'.$v, __('Debe ingresar el campo.', FALSE));
				}
			}

			// Verifico driver.
			if (isset($driver) && ! in_array($driver, array_keys($drivers)))
			{
				$error = TRUE;
				$vista->assign('error_driver', __('El driver ingresado no es correcto.', FALSE));
			}

			if ( ! $error)
			{
				// Genero arreglo de configuraciones.
				if ($driver == 'pdo')
				{
					// Genero arreglo de configuraciones.
					$cfg = array(
						'type'     => $driver,
						'dsn'      => "mysql:dbname=$db_name;host=$host;charset=utf-8",
						'username' => $usuario,
						'password' => $password,
						'options'  => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
					);
				}
				else
				{
					// Genero arreglo de configuraciones.
					$cfg = array(
						'type'     => $driver,
						'host'     => $host,
						'db_name'  => $db_name,
						'username' => $usuario,
						'password' => $password,
						'utf8'     => TRUE
					);
				}

				// Testeo la configuraciones.
				if ( ! Database::test($cfg))
				{
					$vista->assign('error', __('No se ha podido conectar a la base de datos. Verifique las configuraciones.', FALSE));
				}
				else
				{
					if (is_writable(CONFIG_PATH.DS.'database.php'))
					{
						// Aplico la configuraciones.
						file_put_contents(CONFIG_PATH.DS.'database.php', '<?php defined(\'APP_BASE\') || die(\'No direct access allowed.\');'.PHP_EOL.'return '.$this->value_to_php($cfg).';');
						add_flash_message(FLASH_SUCCESS, __('<strong>¡Felicitaciones!</strong> Las configuraciones se han guardado correctamente.', FALSE));
					}
					else
					{
						add_flash_message(FLASH_SUCCESS, __('<strong>¡Felicitaciones!</strong> Las configuraciones son correctas pero no se puede realizar la actualización por falta de permisos.', FALSE));
					}
				}
			}
		}
		else
		{
			$vista->assign('driver', $config['type']);

			if (strtoupper($config['type']) == 'PDO')
			{
				//TODO: Compatibilidad para otros sistemas.

				// Busco parámetros.
				preg_match('/^mysql:dbname=(.*?);host=(.*?);(.*)$/i', arr_get($config, 'dsn', ''), $m);

				$vista->assign('host', arr_get($m, 2, ''));
				$vista->assign('db_name', arr_get($m, 1, ''));
				$vista->assign('usuario', arr_get($config, 'username', ''));
				$vista->assign('password', arr_get($config, 'password', ''));
			}
			else
			{
				$vista->assign('host', arr_get($config, 'host', ''));
				$vista->assign('db_name', arr_get($config, 'db_name', ''));
				$vista->assign('usuario', arr_get($config, 'username', ''));
				$vista->assign('password', arr_get($config, 'password', ''));
			}
		}

		// Seteamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion.bd'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Obtenemos la representación PHP de una variable.
	 * @param mixed $value Variable a representar.
	 * @return string
	 */
	private function value_to_php($value)
	{
		if ($value === TRUE || $value === FALSE)
		{
			return $value ? 'TRUE' : 'FALSE';
		}
		elseif (is_int($value) || is_float($value))
		{
			return "$value";
		}
		elseif (is_string($value))
		{
			return "'".str_replace("'", "\\'", $value)."'";
		}
		elseif (is_array($value))
		{
			$rst = array();
			foreach ($value as $k => $v)
			{
				if (is_int($k))
				{
					$rst[] = $this->value_to_php($v);
				}
				else
				{
					$rst[] = "'$k' => ".$this->value_to_php($v);
				}
			}

			return 'array('.implode(', ', $rst).')';
		}
		return 'NULL';
	}
}
