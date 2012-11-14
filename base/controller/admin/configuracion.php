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
	 * Verficiamos los permisos.
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
			$_SESSION['flash_error'] = 'No tienes permisos para acceder a esa sección.';
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
					$vista->assign('error_nombre', 'El nombre debe tener entre 2 y 20 caracteres. Pueden ser letras, números, espacios, !, -, _, . y \\');
				}
				else
				{
					if ($nombre !== $model_configuracion->get('nombre', NULL))
					{
						$model_configuracion->nombre = $nombre;
						$vista->assign('success_nombre', 'El nombre se ha actualizado correctamente.');
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
					$vista->assign('error_descripcion', 'La descripción debe tener entre 5 y 30 caracteres. Pueden ser letras, números, espacios, !, -, _, . y \\');
				}
				else
				{
					if ($descripcion !== $model_configuracion->get('descripcion', NULL))
					{
						$model_configuracion->descripcion = $descripcion;
						$vista->assign('success_descripcion', 'La descripción se ha actualizado correctamente.');
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
					$vista->assign('success_registro', 'El registro se ha editado correctamente.');
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
						$vista->assign('success_activacion_usuario', 'La forma de activación se ha actualizado correctamente.');
					}
				}
				else
				{
					$vista->assign('error_activacion_usuario', 'La forma de activación seleccionada no es válida.');
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
						$vista->assign('success_rango_defecto', 'Se ha actualizado el rango para los usuarios por defecto.');
					}
				}
				else
				{
					$vista->assign('error_rango_defecto', 'El rango seleccionado no es correcto.');
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
					$vista->assign('error_elementos_pagina', 'La cantidad de elementos por página ser un entero entre 5 y 100.');
				}
				else
				{
					// Actualizo el valor.
					$actual = $model_configuracion->get('elementos_pagina', NULL);
					if ($actual === NULL || $elementos_pagina !== (int) $actual)
					{
						$model_configuracion->elementos_pagina = $elementos_pagina;
						$vista->assign('success_elementos_pagina', 'La cantidad de elementos por página se ha actualizado correctamente.');
					}
				}
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion'));

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

		// Cargo manejador del modo mantenimiento.
		$mantenimiento = new Mantenimiento;

		// Cargo listado de IP's que pueden acceder en modo mantenimiento.
		$model_configuracion = new Model_Configuracion;
		$ips_matenimiento = unserialize($model_configuracion->get('ip_mantenimiento', 'a:0:{}'));

		// Pasamos los datos a la vista.
		$vista->assign('ip', implode(PHP_EOL, $ips_matenimiento));
		$vista->assign('error_ip', FALSE);
		$vista->assign('success_ip', FALSE);

		if (Request::method() == 'POST')
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
				$vista->assign('error_ip', 'Los IP\'s ingresados no son válidos.');
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
					if ($mantenimiento->is_locked())
					{
						$mantenimiento->lock($ips);
					}

					// Informo resultado.
					$vista->assign('success_ip', 'Listado de IP\'s actualizada correctamente.');
				}
			}
		}

		// Verifico si está habilitado el bloqueo.
		$vista->assign('is_locked', $mantenimiento->is_locked());
		if ($mantenimiento->is_locked())
		{
			$locked_for_me = $mantenimiento->is_locked_for(IP::get_ip_addr());
		}
		else
		{
			$locked_for_me = TRUE;
			$my_ip = IP::get_ip_addr();
			foreach ($ips_matenimiento as $ip)
			{
				if ($my_ip == $ip || IP::ip_in_range($my_ip, $ip))
				{
					$locked_for_me = FALSE;
					break;
				}
			}
			unset($my_ip);
		}
		$vista->assign('is_locked_for_me', $locked_for_me);
		unset($locked_for_me);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_mantenimiento'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Activo/Desactivo el modo mantenimiento.
	 * @param bool $tipo 0 para deshabilitar, 1 para habilitar.
	 */
	public function action_habilitar_mantenimiento($tipo)
	{
		$tipo = (bool) $tipo;

		$mantenimiento = new Mantenimiento;

		// Verifico la acción.
		if ($tipo == $mantenimiento->is_locked())
		{
			$_SESSION['flash_error'] = 'El modo mantenimiento ya posee ese estado.';
		}
		else
		{
			// Ejecuto la acción deseada.
			if ($tipo)
			{
				$_SESSION['flash_success'] = 'Modo mantenimiento activado correctamente.';
				$c = new Model_Configuracion;
				//TODO: Verificar que alguien pueda acceder.
				$mantenimiento->lock(unserialize($c->get('ip_mantenimiento', 'a:0:{}')));
			}
			else
			{
				$_SESSION['flash_success'] = 'Modo mantenimiento activado correctamente.';
				$mantenimiento->unlock();
			}
		}
		Request::redirect('/admin/configuracion/mantenimiento');
	}

	/**
	 * Listado de plugins.
	 */
	public function action_plugins()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/plugins');

		// Cargo listado de plugins.
		$pm = Plugin_Manager::get_instance();

		// Regenero el listado de plugins.
		$pm->regenerar_lista();
		$plugins = $pm->load();

		foreach ($plugins as $k => $v)
		{
			$plugins[$k] = (array) $pm->get($k)->info();
		}
		$vista->assign('plugins', $plugins);
		unset($plugins);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_plugins'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Activamos un plugin realizando su migración y puesta en funcionamiento.
	 * @param string $plugin Plugin a activar.
	 */
	public function action_activar_plugin($plugin)
	{
		// Cargamos el administrado de plugins.
		$pm = Plugin_Manager::get_instance();

		// Verificamos si el plugin existe.
		$p = $pm->get($plugin);
		if ( ! is_object($p))
		{
			$_SESSION['flash_error'] = 'El plugin no existe.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico su estado.
		if ($p->estado())
		{
			$_SESSION['flash_error'] = 'El plugin ya se encuentra activo.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico posibilidad de aplicar.
		if ( ! $p->check_support())
		{
			$_SESSION['flash_error'] = 'El plugin no puede ser instalado por la existencia de incompatibilidades.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Realizamos la instalación.
		$p->install();

		$_SESSION['flash_success'] = 'El plugin se ha instalado correctamente.';
		Request::redirect('/admin/configuracion/plugins');
	}

	/**
	 * Desactivamos un plugin.
	 * @param string $plugin Plugin a desactivar.
	 */
	public function action_desactivar_plugin($plugin)
	{
		// Cargamos el administrado de plugins.
		$pm = Plugin_Manager::get_instance();

		// Verificamos si el plugin existe.
		$p = $pm->get($plugin);
		if ( ! is_object($p))
		{
			$_SESSION['flash_error'] = 'El plugin no existe.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico su estado.
		if ( ! $p->estado())
		{
			$_SESSION['flash_error'] = 'El plugin ya se encuentra desactivado.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Realizamos la desinstalación.
		$p->remove();

		$_SESSION['flash_success'] = 'El plugin se ha desinstalado correctamente.';
		Request::redirect('/admin/configuracion/plugins');
	}

	/**
	 * Borramos el plugin.
	 * @param string $plugin Plugin a borrar.
	 */
	public function action_borrar_plugin($plugin)
	{
		// Cargamos el administrado de plugins.
		$pm = Plugin_Manager::get_instance();

		// Verificamos si el plugin existe.
		$p = $pm->get($plugin);
		if ( ! is_object($p))
		{
			$_SESSION['flash_error'] = 'El plugin no existe.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico su estado.
		if ($p->estado())
		{
			$_SESSION['flash_error'] = 'El plugin se encuentra activado, no se puede borrar.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Eliminamos.
		Update_Utils::unlink(Plugin_Manager::nombre_as_path($plugin));
		Plugin_Manager::get_instance()->regenerar_lista();

		$_SESSION['flash_success'] = 'El plugin se ha borrado correctamente.';
		Request::redirect('/admin/configuracion/plugins');
	}

	/**
	 * Agregamos un nuevo plugins al sistema.
	 * NO REALIZA LA INSTALACIÓN. Por motivos de recursos hacer ambas cosas
	 * puede provocar que la instalación no termine.
	 *
	 */
	public function action_agregar_plugin()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/agregar_plugin');

		// Valores por defecto.
		$vista->assign('error_carga', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Verifico el envio correcto de datos.
			if (isset($_FILES['plugin']))
			{
				// Cargo los datos del archivo.
				$file = $_FILES['plugin'];

				// Verifico el estado.
				if ($file['error'] !== UPLOAD_ERR_OK)
				{
					$error = TRUE;
					switch ($file['error'])
					{
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							$vista->assign('error_carga', 'El tamaño del archivo es incorrecto.');
							break;
						case UPLOAD_ERR_PARTIAL:
							$vista->assign('error_carga', 'Los datos enviados están corruptos.');
							break;
						case UPLOAD_ERR_NO_FILE:
							$vista->assign('error_carga', 'No has seleccionado un archivo.');
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
						case UPLOAD_ERR_CANT_WRITE:
							$vista->assign('error_carga', 'Error interno al cargar el archivo. Reintente. Si el error persiste contacte al administrador.');
							break;
						case UPLOAD_ERR_EXTENSION:
							$vista->assign('error_carga', 'La configuración del servidor no permite archivo con esa extensión.');
							break;
					}
				}
				else
				{
					// Cargo el mime.
					$file['type'] = Update_Utils::get_mime($file['tmp_name']);

					// Verifico esté dentro de los permitidos.
					if ( ! in_array(Update_Utils::mime2compresor($file['type']), Update_Compresion::get_list()))
					{
						$error = TRUE;
						$vista->assign('error_carga', 'El tipo de archivo no es soportado. Verifique la configuración del servidor.');
					}
				}
			}
			else
			{
				$error = TRUE;
				$vista->assign('error_carga', 'No has seleccionado un archivo.');
			}

			// Verifico el contenido de los datos.
			if ( ! $error)
			{
				// Armo directorio temporal para la descargar.
				$tmp_dir = TMP_PATH.uniqid('pkg_').DS;
				mkdir($tmp_dir, 0777, TRUE);

				// Realizo la descompresión.
				$compresor = Update_Compresion::get_instance(Update_Utils::mime2compresor($file['type']));
				$compresor->set_temp_path($tmp_dir);
				if ( ! $compresor->decompress($file['tmp_name']))
				{
					// Limpio salidas.
					Update_Utils::unlink($file['tmp_name']);
					Update_Utils::unlink($tmp_dir);

					// Informo del error.
					$error = TRUE;
					$vista->assign('error_carga', 'No se pudo descomprimir el archivo. Compruebe que sea correcto.');
				}
				else
				{
					// Obtenemos la información del paquete.
					if ( ! file_exists($tmp_dir.'info.json'))
					{
						// Limpio salidas.
						Update_Utils::unlink($file['tmp_name']);
						Update_Utils::unlink($tmp_dir);

						// Informo del error.
						$error = TRUE;
						$vista->assign('error_carga', 'El paquete no es un plugins válido.');
					}
					else
					{
						// Obtengo la información.
						$data = json_decode(file_get_contents($tmp_dir.'info.json'));

						// Obtenemos el nombre del paquete.
						$pkg_name = $data->nombre;

						// Verifico no exista.
						if (Plugin_Manager::get_instance()->get(Plugin_Manager::make_name($pkg_name)) !== NULL)
						{
							// Limpio salidas.
							Update_Utils::unlink($file['tmp_name']);
							Update_Utils::unlink($tmp_dir);

							//TODO: Efectuar actualizacion.

							// Informo del error.
							$error = TRUE;
							$vista->assign('error_carga', 'El plugin no puede ser importado porque ya existe.');
						}
						else
						{
							// Cargamos el archivo para personalizar la actualización.
							if (file_exists($tmp_dir.'/install.php'))
							{
								@include($tmp_dir.'/install.php');

								if (class_exists('Install'))
								{
									// Cargamos el instalador.
									$install = new Install($tmp_dir, $update);
								}
							}

							// Ejecutamos pre_instalacion.
							if (isset($install))
							{
								// Verificamos soporte.
								if (method_exists($install, 'before'))
								{
									$install->before();
								}
							}

							// Movemos los archivos.
							Update_Utils::copyr($tmp_dir.DS.'files'.DS, Plugin_Manager::nombre_as_path($pkg_name));

							// Ejecutamos post_instalacion.
							if (isset($install))
							{
								// Verificamos soporte.
								if (method_exists($install, 'after'))
								{
									$install->after();
								}
							}

							// Actualizo la cache.
							Plugin_Manager::get_instance()->regenerar_lista();

							// Limpiamos archivos de la instalación y salimos.
							Update_Utils::unlink($tmp_dir);
							Update_Utils::unlink($file['tmp_name']);

							// Informo resultado.
							$_SESSION['flash_success'] = 'El plugin se importó correctamente.';

							// Redireccionamos.
							Request::redirect('/admin/configuracion/plugins');
						}
					}
				}
			}
		}

		// Cargo listado de compresores disponibles.
		$vista->assign('compresores', Update_Compresion::get_list());
		
		// Directorio de los plugins.
		$vista->assign('plugin_dir', DS.PLUGINS_PATH.DS);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_plugins'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de temas.
	 */
	public function action_temas()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/temas');

		// Cargo tema previsualizado y el actual.
		if (isset($_SESSION['preview-theme']))
		{
			$vista->assign('preview', Theme::actual());
			$vista->assign('actual', Theme::actual(TRUE));
		}
		else
		{
			$vista->assign('preview', '');
			$vista->assign('actual', Theme::actual());
		}

		// Cargamos el listado de temas.
		$themes = Theme::lista(TRUE);

		foreach ($themes as $k => $v)
		{
			// Cargo información del tema.
			$a = configuracion_obtener(APP_BASE.DS.VIEW_PATH.$v.DS.'theme.php');
			$a['key'] = $v;
			$a['nombre'] = isset($a['nombre']) ? $a['nombre'] : $v;
			$a['descripcion'] = isset($a['descripcion']) ? $a['descripcion'] : 'Sin descripción';
			$themes[$k] = $a;
		}
		$vista->assign('temas', $themes);
		unset($themes);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_temas'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Activamos la visualización temporal del tema al usuario.
	 * @param string $tema ID del tema a activar.
	 */
	public function action_previsualizar_tema($tema)
	{
		// Verificamos exista.
		if ( ! in_array($tema, Theme::lista()))
		{
			$_SESSION['flash_error'] = 'El tema seleccionado para previsualizar es incorrecto.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Verifico no sea actual.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			$_SESSION['flash_error'] = 'El tema seleccionado para previsualizar es el actual.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Activo el tema.
		$_SESSION['preview-theme'] = $tema;
		$_SESSION['flash_success'] = 'El tema se a colocado para previsualizar correctamente';
		Request::redirect('/admin/configuracion/temas');
	}

	/**
	 * Terminamos la visualización temporal del tema al usuario.
	 */
	public function action_terminar_preview_tema()
	{
		// Verificamos si existe la previsualizacion.
		if (isset($_SESSION['preview-theme']))
		{
			// Quitamos la vista previa.
			unset($_SESSION['preview-theme']);

			$_SESSION['flash_success'] = 'Vista previa terminada correctamente.';
		}
		else
		{
			$_SESSION['flash_error'] = 'No hay vista previa para deshabilitar.';
		}
		Request::redirect('/admin/configuracion/temas');
	}

	/**
	 * Cambiamos el tema por defecto.
	 * @param string $tema ID del tema a activar.
	 */
	public function action_activar_tema($tema)
	{
		// Obtengo el tema base.
		$base = Theme::actual(TRUE);

		// Verifico no sea el actual.
		if ($tema == $base)
		{
			$_SESSION['flash_error'] = 'El tema ya es el predeterminado.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Verificamos exista.
		if ( ! in_array($tema, Theme::lista()))
		{
			$_SESSION['flash_error'] = 'El tema seleccionado para setear como predeterminado es incorrecto.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Borro preview.
		if (isset($_SESSION['preview-theme']))
		{
			unset($_SESSION['preview-theme']);
		}

		// Activo tema.
		Theme::setear_tema($tema);

		$_SESSION['flash_success'] = 'El tema se ha seteado como predeterminado correctamente.';
		Request::redirect('/admin/configuracion/temas');
	}

	/**
	 * Borramos el tema del disco.
	 * @param string $tema ID del tema a activar.
	 */
	public function action_eliminar_tema($tema)
	{
		// Verificamos exista.
		if ( ! in_array($tema, Theme::lista()))
		{
			$_SESSION['flash_error'] = 'El tema seleccionado para eliminar es incorrecto.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Verifico no sea el actual ni el de previsualizacion.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			$_SESSION['flash_error'] = 'El tema no se puede borrar por estar en uso.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Lo eliminamos.
		Update_Utils::unlink(APP_BASE.DS.VIEW_PATH.$tema);

		// Refrescamos la cache.
		Theme::lista(TRUE);

		$_SESSION['flash_success'] = 'El tema se ha eliminado correctamente.';
		Request::redirect('/admin/configuracion/temas');
	}

	/**
	 * Instalamos un nuevo tema.
	 */
	public function action_instalar_tema()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/instalar_tema');

		// Valores por defecto.
		$vista->assign('error_carga', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Verifico el envio correcto de datos.
			if (isset($_FILES['theme']))
			{
				// Cargo los datos del archivo.
				$file = $_FILES['theme'];

				// Verifico el estado.
				if ($file['error'] !== UPLOAD_ERR_OK)
				{
					$error = TRUE;
					switch ($file['error'])
					{
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							$vista->assign('error_carga', 'El tamaño del archivo es incorrecto.');
							break;
						case UPLOAD_ERR_PARTIAL:
							$vista->assign('error_carga', 'Los datos enviados están corruptos.');
							break;
						case UPLOAD_ERR_NO_FILE:
							$vista->assign('error_carga', 'No has seleccionado un archivo.');
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
						case UPLOAD_ERR_CANT_WRITE:
							$vista->assign('error_carga', 'Error interno al cargar el archivo. Reintente. Si el error persiste contacte al administrador.');
							break;
						case UPLOAD_ERR_EXTENSION:
							$vista->assign('error_carga', 'La configuración del servidor no permite archivo con esa extensión.');
							break;
					}
				}
				else
				{
					// Cargo el mime.
					$file['type'] = Update_Utils::get_mime($file['tmp_name']);

					// Verifico esté dentro de los permitidos.
					if ( ! in_array(Update_Utils::mime2compresor($file['type']), Update_Compresion::get_list()))
					{
						$error = TRUE;
						$vista->assign('error_carga', 'El tipo de archivo no es soportado. Verifique la configuración del servidor.');
					}
				}
			}
			else
			{
				$error = TRUE;
				$vista->assign('error_carga', 'No has seleccionado un archivo.');
			}

			// Verifico el contenido de los datos.
			if ( ! $error)
			{
				// Armo directorio temporal para la descargar.
				$tmp_dir = TMP_PATH.uniqid('pkg_').DS;
				mkdir($tmp_dir, 0777, TRUE);

				// Realizo la descompresión.
				$compresor = Update_Compresion::get_instance(Update_Utils::mime2compresor($file['type']));
				$compresor->set_temp_path($tmp_dir);
				if ( ! $compresor->decompress($file['tmp_name']))
				{
					// Limpio salidas.
					Update_Utils::unlink($file['tmp_name']);
					Update_Utils::unlink($tmp_dir);

					// Informo del error.
					$error = TRUE;
					$vista->assign('error_carga', 'No se pudo descomprimir el archivo. Compruebe que sea correcto.');
				}
				else
				{
					// Verifico que sea correcto.
					if (is_dir($tmp_dir) && file_exists($tmp_dir.DS.'theme.php') && file_exists($tmp_dir.DS.'views') && $tmp_dir.DS.'assets')
					{
						// Cargo configuraciones.
						$data = configuracion_obtener($tmp_dir.DS.'theme.php');

						// Verifico su contenido.
						if (is_array($data))
						{
							if (isset($data['nombre']) && isset($data['author']))
							{
								$theme_name = preg_replace('/(\s|[^a-z0-9])/', '', strtolower($data['nombre']));
							}
						}

						if ( ! isset($theme_name))
						{
							// Limpio salidas.
							Update_Utils::unlink($file['tmp_name']);
							Update_Utils::unlink($tmp_dir);

							// Informo del error.
							$error = TRUE;
							$vista->assign('error_carga', 'El archivo de descripción del tema es inválido.');
						}
					}
					else
					{
						// Limpio salidas.
						Update_Utils::unlink($file['tmp_name']);
						Update_Utils::unlink($tmp_dir);

						// Informo del error.
						$error = TRUE;
						$vista->assign('error_carga', 'No se trata de un tema válido.');
					}
				}
			}

			// Genero directorios.
			if ( ! $error)
			{
				// Generamos el path donde se va a alojar.
				$target_path = APP_BASE.DS.VIEW_PATH.$theme_name.DS;

				// Verifico directorio donde alojar.
				if ( ! file_exists($target_path))
				{
					// Creo el directorio del tema.
					if ( ! @mkdir($target_path, 0777, TRUE))
					{
						// Limpio salidas.
						Update_Utils::unlink($target_path);
						Update_Utils::unlink($file['tmp_name']);
						Update_Utils::unlink($tmp_dir);

						// Informo del error.
						$vista->assign('error_carga', '1No se pudo alojar el tema en su lugar correspondiente. Verifica los permisos del directorio de temas.');
						$error = TRUE;
					}
				}
			}

			// Realizo actualizacion.
			if ( ! $error)
			{
				// Realizo el movimiento.
				if ( ! Update_Utils::copyr($tmp_dir, $target_path))
				{
					// Limpio salidas.
					Update_Utils::unlink($file['tmp_name']);
					Update_Utils::unlink($tmp_dir);
					Update_Utils::unlink($target_path);

					// Informo del error.
					$vista->assign('error_carga', 'No se pudo alojar el tema en su lugar correspondiente. Verifica los permisos del directorio de temas.');
				}
				else
				{
					// Limpio directorios.
					Update_Utils::unlink($file['tmp_name']);
					Update_Utils::unlink($tmp_dir);

					// Informo resultado.
					$_SESSION['flash_success'] = 'El tema se instaló correctamente.';

					// Redireccionamos.
					Request::redirect('/admin/configuracion/temas');
				}
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_temas'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Configuración del envio de correos.
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
					
			// Envio la configuración.
			$vista->assign('configuracion', $configuracion);
			
			// Mi correo.
			$vista->assign('email', $correo !== NULL ? urldecode($correo) : Usuario::usuario()->email);
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_correo'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}
	
	/**
	 * Enviamos un correo de prueba para verificar que todo sea correcto.
	 */
	public function action_test_mail()
	{
		// Verifico el método de envio.
		if (Request::method() !== 'POST')
		{
			$_SESSION['flash_error'] = 'No puedes enviar un correo de prueba si no especificas el destinatario.';
			Request::redirect('/admin/configuracion/correo');
		}
		
		// Verifico que se encuentre configurado.
		if ( ! file_exists(CONFIG_PATH.DS.'email.php'))
		{
			$_SESSION['flash_error'] = 'No puedes enviar un correo de prueba ya que no has lo has configurado.';
			Request::redirect('/admin/configuracion/correo');
		}
		
		// Verifico el correo enviado.
		if ( ! isset($_POST['email']) || ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $_POST['email']))
		{
			$_SESSION['flash_error'] = 'La casilla de correo ingresada no es válida.';
			Request::redirect('/admin/configuracion/correo/'.(isset($_POST['email']) ? urlencode($_POST['email']) : '' ));
		}

		// Cargo el modelo de configuraciones.
		$model_config = new Model_Configuracion;
		
		// Creo el mensaje de correo.
		$message = Email::get_message();
		$message->setSubject('Verificación configuración correos de '.$model_config->get('nombre', 'Marifa'));
		$message->setTo($_POST['email']);

		// Cargo la vista.
		$message_view = View::factory('emails/test');
		$message_view->assign('titulo', $model_config->get('nombre', 'Marifa'));
		$message->setBody($message_view->parse());
		unset($message_view);

		// Envio el email.
		$mailer = Email::get_mailer();
		$mailer->send($message);
		
		// Informo el resultado.
		$_SESSION['flash_success'] = 'El correo de prueba se ha enviado correctamente.';
		Request::redirect('/admin/configuracion/correo');
	}
}
