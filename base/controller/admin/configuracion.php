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

	public function __construct()
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

		parent::__construct();
	}

	/**
	 * Portada de las configuraciones.
	 */
	public function action_index()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/index');

		// Noticia Flash.
		if (isset($_SESSION['index_correcta']))
		{
			$vista->assign('success', get_flash('index_correcta'));
		}
		if (isset($_SESSION['index_error']))
		{
			$vista->assign('error', get_flash('index_error'));
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
	 * Listado de plugins.
	 */
	public function action_plugins()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/plugins');

		// Noticia Flash.
		if (isset($_SESSION['plugin_correcta']))
		{
			$vista->assign('success', get_flash('plugin_correcta'));
		}
		if (isset($_SESSION['plugin_error']))
		{
			$vista->assign('error', get_flash('plugin_error'));
		}

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
			$_SESSION['plugin_error'] = 'El plugin no existe.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico su estado.
		if ($p->estado())
		{
			$_SESSION['plugin_error'] = 'El plugin ya se encuentra activo.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico posibilidad de aplicar.
		if ( ! $p->check_support())
		{
			$_SESSION['plugin_error'] = 'El plugin no puede ser instalado por la existencia de incompatibilidades.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Realizamos la instalación.
		$p->install();

		$_SESSION['plugin_correcta'] = 'El plugin se ha instalado correctamente.';
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
			$_SESSION['plugin_error'] = 'El plugin no existe.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico su estado.
		if ( ! $p->estado())
		{
			$_SESSION['plugin_error'] = 'El plugin ya se encuentra desactivado.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Realizamos la desinstalación.
		$p->remove();

		$_SESSION['plugin_correcta'] = 'El plugin se ha desinstalado correctamente.';
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
			$_SESSION['plugin_error'] = 'El plugin no existe.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Verifico su estado.
		if ($p->estado())
		{
			$_SESSION['plugin_error'] = 'El plugin se encuentra activado, no se puede borrar.';
			Request::redirect('/admin/configuracion/plugins');
		}

		// Eliminamos.
		Update_Utils::unlink(Plugin_Manager::nombre_as_path($plugin));
		Plugin_Manager::get_instance()->regenerar_lista();

		$_SESSION['plugin_correcta'] = 'El plugin se ha borrado correctamente.';
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
				if($file['error'] !== UPLOAD_ERR_OK)
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
							$_SESSION['plugin_correcta'] = 'El plugin se importó correctamente.';

							// Redireccionamos.
							Request::redirect('/admin/configuracion/plugins');
						}
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

		// Noticia Flash.
		if (isset($_SESSION['tema_correcta']))
		{
			$vista->assign('success', get_flash('tema_correcta'));
		}
		if (isset($_SESSION['tema_error']))
		{
			$vista->assign('error', get_flash('tema_error'));
		}

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
			$_SESSION['tema_error'] = 'El tema seleccionado para previsualizar es incorrecto.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Verifico no sea actual.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			$_SESSION['tema_error'] = 'El tema seleccionado para previsualizar es el actual.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Activo el tema.
		$_SESSION['preview-theme'] = $tema;
		$_SESSION['tema_correcta'] = 'El tema se a colocado para previsualizar correctamente';
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

			$_SESSION['tema_correcta'] = 'Vista previa terminada correctamente.';
		}
		else
		{
			$_SESSION['tema_error'] = 'No hay vista previa para deshabilitar.';
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
			$_SESSION['tema_error'] = 'El tema ya es el predeterminado.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Verificamos exista.
		if ( ! in_array($tema, Theme::lista()))
		{
			$_SESSION['tema_error'] = 'El tema seleccionado para setear como predeterminado es incorrecto.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Borro preview.
		if (isset($_SESSION['preview-theme']))
		{
			unset($_SESSION['preview-theme']);
		}

		// Activo tema.
		Theme::setear_tema($tema);

		$_SESSION['tema_correcta'] = 'El tema se ha seteado como predeterminado correctamente.';
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
			$_SESSION['tema_error'] = 'El tema seleccionado para eliminar es incorrecto.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Verifico no sea el actual ni el de previsualizacion.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			$_SESSION['tema_error'] = 'El tema no se puede borrar por estar en uso.';
			Request::redirect('/admin/configuracion/temas');
		}

		// Lo eliminamos.
		Update_Utils::unlink(APP_BASE.DS.VIEW_PATH.$tema);

		// Refrescamos la cache.
		Theme::lista(TRUE);

		$_SESSION['tema_correcta'] = 'El tema se ha eliminado correctamente.';
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
				if($file['error'] !== UPLOAD_ERR_OK)
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
					//die();
					// Limpio directorios.
					Update_Utils::unlink($file['tmp_name']);
					Update_Utils::unlink($tmp_dir);

					// Informo resultado.
					$_SESSION['tema_correcta'] = 'El tema se instaló correctamente.';

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
}
