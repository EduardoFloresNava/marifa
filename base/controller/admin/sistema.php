<?php
/**
 * sistema.php is part of Marifa.
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
 * Controlador para administrar parámetros del sistema.
 * Con cosas relacionadas a agregados (temas, plugins, etc) y actualizaciones del sistema entre otros.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Admin
 */
class Base_Controller_Admin_Sistema extends Controller {

	/**
	 * Verificamos los permisos.
	 */
	public function before()
	{
		// Verifico estar identificado.
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
		$vista = View::factory('admin/sistema/index');

		// Obtengo actualizaciones.
		$actualizaciones_sistema = unserialize(Utils::configuracion()->get('update_sistema_actualizaciones', 'a:0:{}'));
		$actualizaciones_last_check = Utils::configuracion()->get('update_sistema_last_check', NULL);

		// Verifico si hay actualizaciones.
		if (is_array($actualizaciones_sistema))
		{
			// Actualizaciones del sistema descargadas.
			$vista->assign('sistema_descargadas', $this->actualizaciones_sistema_descargadas());

			// Proceso las actualizaciones.
			$vista->assign('sistema', $actualizaciones_sistema);
			$vista->assign('sistema_last_check', Fechahora::createFromTimestamp($actualizaciones_last_check));
		}
		else
		{
			$vista->assign('sistema', FALSE);
			$vista->assign('sistema_last_check', NULL);
		}

		// TODO: Resumen de temas, plugins y widget's (todas las actualizaciones).

		// Seteamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.informacion'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Obtenemos el listado de actualizaciones del sistema que se encuentran descargadas.
	 * @return array Listado de actualizaciones descargadas.
	 */
	protected function actualizaciones_sistema_descargadas()
	{
		// Verifico existencia del directorio.
		if ( ! file_exists(CACHE_PATH.DS.'updates'.DS) || ! is_dir(CACHE_PATH.DS.'updates'.DS))
		{
			return array();
		}

		// Cargo listado de elementos.
		$elementos = scandir(CACHE_PATH.DS.'updates'.DS);

		// Obtengo listado de extensiones disponibles.
		$extesiones = array_map('Update_Utils::compresion2extension', Update_Compresion::get_list());

		$rst = array();
		foreach ($elementos as $file)
		{
			foreach ($extesiones as $ext)
			{
				if (substr($file, (-1) * strlen($ext)) === $ext)
				{
					$rst[] = substr($file, 0, (-1) * (strlen($ext) + 1));
				}
			}
		}

		return $rst;
	}

	/**
	 * Listado de plugins.
	 */
	public function action_plugins()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/sistema/plugins');

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

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.plugins'));

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
			add_flash_message(FLASH_ERROR, __('El plugin no existe.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Verifico su estado.
		if ($p->estado())
		{
			add_flash_message(FLASH_ERROR, __('El plugin ya se encuentra activo.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Verifico posibilidad de aplicar.
		if ( ! $p->check_support())
		{
			add_flash_message(FLASH_ERROR, __('El plugin no puede ser instalado por la existencia de incompatibilidades.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Realizamos la instalación.
		$p->install();

		add_flash_message(FLASH_SUCCESS, __('El plugin se ha instalado correctamente.', FALSE));
		Request::redirect('/admin/sistema/plugins');
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
			add_flash_message(FLASH_ERROR, __('El plugin no existe.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Verifico su estado.
		if ( ! $p->estado())
		{
			add_flash_message(FLASH_ERROR, __('El plugin ya se encuentra desactivado.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Realizamos la desinstalación.
		$p->remove();

		add_flash_message(FLASH_SUCCESS, __('El plugin se ha desinstalado correctamente.', FALSE));
		Request::redirect('/admin/sistema/plugins');
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
			add_flash_message(FLASH_ERROR, __('El plugin no existe.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Verifico su estado.
		if ($p->estado())
		{
			add_flash_message(FLASH_ERROR, __('El plugin se encuentra activado, no se puede borrar.', FALSE));
			Request::redirect('/admin/sistema/plugins');
		}

		// Eliminamos.
		Update_Utils::unlink(Plugin_Manager::nombre_as_path($plugin));
		Plugin_Manager::get_instance()->regenerar_lista();

		add_flash_message(FLASH_SUCCESS, __('El plugin se ha borrado correctamente.', FALSE));
		Request::redirect('/admin/sistema/plugins');
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
		$vista = View::factory('admin/sistema/agregar_plugin');

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
							$vista->assign('error_carga', __('El tamaño del archivo es incorrecto.', FALSE));
							break;
						case UPLOAD_ERR_PARTIAL:
							$vista->assign('error_carga', __('Los datos enviados están corruptos.', FALSE));
							break;
						case UPLOAD_ERR_NO_FILE:
							$vista->assign('error_carga', __('No has seleccionado un archivo.', FALSE));
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
						case UPLOAD_ERR_CANT_WRITE:
							$vista->assign('error_carga', __('Error interno al cargar el archivo. Reintente. Si el error persiste contacte al administrador.', FALSE));
							break;
						case UPLOAD_ERR_EXTENSION:
							$vista->assign('error_carga', __('La configuración del servidor no permite archivo con esa extensión.', FALSE));
							break;
					}
				}
				else
				{
					// Cargo el mime.
					$file['type'] = Update_Utils::get_mime($file['name']);

					// Verifico esté dentro de los permitidos.
					if ( ! in_array(Update_Utils::mime2compresor($file['type']), Update_Compresion::get_list()))
					{
						$error = TRUE;
						$vista->assign('error_carga', __('El tipo de archivo no es soportado. Verifique la configuración del servidor.', FALSE));
					}
				}
			}
			else
			{
				$error = TRUE;
				$vista->assign('error_carga', __('No has seleccionado un archivo.', FALSE));
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
					$vista->assign('error_carga', __('No se pudo descomprimir el archivo. Compruebe que sea correcto.', FALSE));
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
						$vista->assign('error_carga', __('El paquete no es un plugin válido.', FALSE));
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

							//TODO: Efectuar actualización.

							// Informo del error.
							$error = TRUE;
							$vista->assign('error_carga', __('El plugin no puede ser importado porque ya existe.', FALSE));
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
							add_flash_message(FLASH_SUCCESS, __('El plugin se importó correctamente.', FALSE));

							// Redireccionamos.
							Request::redirect('/admin/sistema/plugins');
						}
					}
				}
			}
		}

		// Cargo listado de compresores disponibles.
		$vista->assign('compresores', Update_Compresion::get_list());

		// Directorio de los plugins.
		$vista->assign('plugin_dir', DS.PLUGINS_PATH.DS);

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.plugins'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Listado de temas.
	 */
	public function action_temas()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/sistema/temas');

		// Cargo tema que tiene vista preliminar activa y el actual.
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

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.temas'));

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
			add_flash_message(FLASH_ERROR, __('El tema seleccionado para previsualizar es incorrecto.', FALSE));
			Request::redirect('/admin/sistema/temas');
		}

		// Verifico no sea actual.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			add_flash_message(FLASH_ERROR, __('El tema seleccionado para previsualizar es el actual.', FALSE));
			Request::redirect('/admin/sistema/temas');
		}

		// Activo el tema.
		$_SESSION['preview-theme'] = $tema;
		add_flash_message(FLASH_SUCCESS, __('El tema se a colocado para previsualizar correctamente', FALSE));
		Request::redirect('/admin/sistema/temas');
	}

	/**
	 * Terminamos la visualización temporal del tema al usuario.
	 */
	public function action_terminar_preview_tema()
	{
		// Verificamos si hay una vista preliminar activa.
		if (isset($_SESSION['preview-theme']))
		{
			// Quitamos la vista previa.
			unset($_SESSION['preview-theme']);

			add_flash_message(FLASH_SUCCESS, __('Vista previa terminada correctamente.', FALSE));
		}
		else
		{
			add_flash_message(FLASH_ERROR, __('No hay vista previa para deshabilitar.', FALSE));
		}
		Request::redirect('/admin/sistema/temas');
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
			add_flash_message(FLASH_ERROR, __('El tema ya es el predeterminado.', FALSE));
			Request::redirect('/admin/sistema/temas');
		}

		// Verificamos exista.
		if ( ! in_array($tema, Theme::lista()))
		{
			add_flash_message(FLASH_ERROR, __('El tema seleccionado para setear como predeterminado es incorrecto.', FALSE));
			Request::redirect('/admin/sistema/temas');
		}

		// Borro vista preliminar.
		if (isset($_SESSION['preview-theme']))
		{
			unset($_SESSION['preview-theme']);
		}

		// Activo tema.
		Theme::setear_tema($tema);

		add_flash_message(FLASH_SUCCESS, __('El tema se ha seteado como predeterminado correctamente.', FALSE));
		Request::redirect('/admin/sistema/temas');
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
			add_flash_message(FLASH_ERROR, __('El tema seleccionado para eliminar es incorrecto.', FALSE));
			Request::redirect('/admin/sistema/temas');
		}

		// Verifico no sea el actual ni el que tiene la vista preliminar activa.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			add_flash_message(FLASH_ERROR, __('El tema no se puede borrar por estar en uso.', FALSE));
			Request::redirect('/admin/sistema/temas');
		}

		// Lo eliminamos.
		Update_Utils::unlink(APP_BASE.DS.VIEW_PATH.$tema);

		// Refrescamos la cache.
		Theme::lista(TRUE);

		add_flash_message(FLASH_SUCCESS, __('El tema se ha eliminado correctamente.', FALSE));
		Request::redirect('/admin/sistema/temas');
	}

	/**
	 * Instalamos un nuevo tema.
	 */
	public function action_instalar_tema()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/sistema/instalar_tema');

		// Valores por defecto.
		$vista->assign('error_carga', FALSE);

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Verifico el envío correcto de datos.
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
							$vista->assign('error_carga', __('El tamaño del archivo es incorrecto.', FALSE));
							break;
						case UPLOAD_ERR_PARTIAL:
							$vista->assign('error_carga', __('Los datos enviados están corruptos.', FALSE));
							break;
						case UPLOAD_ERR_NO_FILE:
							$vista->assign('error_carga', __('No has seleccionado un archivo.', FALSE));
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
						case UPLOAD_ERR_CANT_WRITE:
							$vista->assign('error_carga', __('Error interno al cargar el archivo. Reintente. Si el error persiste contacte al administrador.', FALSE));
							break;
						case UPLOAD_ERR_EXTENSION:
							$vista->assign('error_carga', __('La configuración del servidor no permite archivo con esa extensión.', FALSE));
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
						$vista->assign('error_carga', __('El tipo de archivo no es soportado. Verifique la configuración del servidor.', FALSE));
					}
				}
			}
			else
			{
				$error = TRUE;
				$vista->assign('error_carga', __('No has seleccionado un archivo.', FALSE));
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
					$vista->assign('error_carga', __('No se pudo descomprimir el archivo. Compruebe que sea correcto.', FALSE));
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
							$vista->assign('error_carga', __('El archivo de descripción del tema es inválido.', FALSE));
						}
					}
					else
					{
						// Limpio salidas.
						Update_Utils::unlink($file['tmp_name']);
						Update_Utils::unlink($tmp_dir);

						// Informo del error.
						$error = TRUE;
						$vista->assign('error_carga', __('No se trata de un tema válido.', FALSE));
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
						$vista->assign('error_carga', __('No se pudo alojar el tema en su lugar correspondiente. Verifica los permisos del directorio de temas.', FALSE));
						$error = TRUE;
					}
				}
			}

			// Realizo actualización.
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
					$vista->assign('error_carga', __('No se pudo alojar el tema en su lugar correspondiente. Verifica los permisos del directorio de temas.', FALSE));
				}
				else
				{
					// Limpio directorios.
					Update_Utils::unlink($file['tmp_name']);
					Update_Utils::unlink($tmp_dir);

					// Informo resultado.
					add_flash_message(FLASH_SUCCESS, __('El tema se instaló correctamente.', FALSE));

					// Redireccionamos.
					Request::redirect('/admin/sistema/temas');
				}
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.temas'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Realizamos optimizaciones del sitio.
	 * Por ejemplo, desfragmentar base de datos, limpiar cache, comprimir logs, etc.
	 */
	public function action_optimizar()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/sistema/optimizar');

		if (Request::method() == 'POST')
		{
			$tipo = isset($_POST['submit']) ? $_POST['submit'] : '';
			switch ($tipo)
			{
				case 'database': // Optimizamos las tablas.
					$db = Database::get_instance();

					foreach (array('categoria', 'configuracion', 'noticia', 'post_tag', 'session', 'suceso', 'rango', 'rango_permiso') as $tabla)
					{
						try {
							$db->update("REPAIR TABLE $tabla;");
						} catch (Database_Exception $e) {}

						try {
							$db->update("ANALYZE TABLE $tabla;");
						} catch (Database_Exception $e) {}

						try {
							$db->update("OPTIMIZE TABLE $tabla;");
						} catch (Database_Exception $e) {}
					}

					add_flash_message(FLASH_SUCCESS, __('Optimización de la base de datos realizada correctamente.', FALSE));
					break;
				case 'cache': // Eliminamos cache.

					// Limpio la cache del sistema.
					Cache::get_instance()->clean();

					// Limpio la cache de vistas.
					foreach (glob(CACHE_PATH.DS.'raintpl'.DS.'*', GLOB_ONLYDIR) as $file)
					{
						Update_Utils::unlink($file);
					}

					// Informo el resultado.
					add_flash_message(FLASH_SUCCESS, __('Limpieza de la cache realizada correctamente.', FALSE));
					break;
				case 'compress-logs': // Comprimimos log's viejos.

					// Verifico existencia de la compresión.
					if ( ! function_exists('gzcompress'))
					{
						add_flash_message(FLASH_ERROR, __('No se puede comprimir los log\'s ya que no se encuentra disponible la biblioteca ZLIB.', FALSE));
						break;
					}

					// Realizo la compresión.
					Log::compress_old();

					// Informo el resultado.
					add_flash_message(FLASH_SUCCESS, __('Compresión de log\'s realizada correctamente.', FALSE));
					break;
			}
		}

		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.optimizar'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Verificamos las actualizaciones del sistema.
	 */
	public function action_verificar_actualizaciones()
	{
		try {
			// Realizo petición.
			$response = $this->do_update_request('http://network.marifa.org/api/update/updates/v'.VERSION);

			// Verifico sea arreglo.
			if (is_array($response))
			{
				// Proceso los elementos.
				$rst = array();
				foreach ($response as $v)
				{
					$rst[$v->to] = get_object_vars($v->url);
				}

				// Guardo en cache.
				Utils::configuracion()->update_sistema_actualizaciones = serialize($rst);
				Utils::configuracion()->update_sistema_last_check = time();

				// Vuelvo.
				Request::redirect('/admin/sistema/');
			}
			else
			{
				// Envío a un log.
				Log::info('Error obteniendo actualizaciones: respuesta inesperada del servidor.');

				// Informo.
				add_flash_message(FLASH_ERROR, __('Error obteniendo actualizaciones:  respuesta inesperada del servidor.', FALSE));

				// Vuelvo a la portada.
				Request::redirect('/admin/sistema/');
			}
		}
		catch (HttpResponseException $e)
		{
			// Envío a un log.
			Log::info('Error obteniendo actualizaciones: '.$e->getMessage());

			// Informo.
			add_flash_message(FLASH_ERROR, sprintf(__('Error obteniendo actualizaciones: %s', FALSE), $e->getMessage()));

			// Vuelvo a la portada.
			Request::redirect('/admin/sistema/');
		}
	}

	/**
	 * Realizamos una petición al servidor de actualizaciones.
	 * @param string $url URL a llamar.
	 * @return mixed
	 */
	protected function do_update_request($url)
	{
		// Realizo la petición del sistema.
		$response = Utils::remote_call($url);

		// Trato decodificar json.
		$response_json = json_decode($response);

		// Verifico respuesta.
		if ( ! $response_json)
		{
			Log::info('Error al conectar con el servidor de actualizaciones con la URL: '.$url);
			return FALSE;
		}
		else
		{
			// Verifico la respuesta.
			if (is_object($response_json) && isset($response_json->response) && isset($response_json->body))
			{
				// Verifico la respuesta.
				if ($response_json->response == 'OK')
				{
					// Devuelvo el resultado.
					return $response_json->body;
				}
				else
				{
					// Informo el error.
					throw new HttpResponseException($response_json->body);
				}
			}
			else
			{
				Log::info('Respuesta del servidor de actualizaciones inválida: '.var_export($response_json, TRUE));
				return FALSE;
			}
		}
	}

	/**
	 * Actualizar el sistema.
	 * @param string $version Versión a actualizar.
	 */
	public function action_actualizar_sistema($version)
	{
		// Verifico directorio.
		if ( ! file_exists(CACHE_PATH.DS.'updates'.DS))
		{
			mkdir(CACHE_PATH.DS.'updates'.DS);
		}

		// Verifico existencia archivo.
		if (count(glob(CACHE_PATH.DS.'updates'.DS.$version.'.{'.implode(',', array_map('Update_Utils::compresion2extension', Update_Compresion::get_list())).'}', GLOB_BRACE)) <= 0)
		{
			// Busco URL's de la versión.
			$upd_list = arr_get(unserialize(Utils::configuracion()->get('update_sistema_actualizaciones', 'a:0:{}')), $version, NULL);

			// Verifico existencia.
			if ( ! is_array($upd_list))
			{
				add_flash_message(FLASH_ERROR, __('La versión a la que quiere actualizar no se encuentra disponible.', FALSE));
				Request::redirect('/admin/sistema/');
			}

			// Obtengo compresiones disponibles.
			$remotas = array_map('Update_Utils::extension2compresion', array_keys($upd_list));
			$locales = Update_Compresion::get_list();

			// Obtengo la compresión disponible.
			$posibles = array_intersect($remotas, $locales);
			$descargar = $upd_list[Update_Utils::compresion2extension($posibles[0])];
			unset($remotas, $locales);

			// Obtengo el nombre del archivo temporal.
			$tmp_file = sys_get_temp_dir().DS.uniqid();

			// Trato de descargar.
			Utils::download_file($descargar, $tmp_file);

			// Guardo el archivo.
			copy($tmp_file, CACHE_PATH.DS.'updates'.DS.$version.'.'.Update_Utils::compresion2extension($posibles[0]));
			unlink($tmp_file);

			// Informamos resultado.
			add_flash_message(FLASH_SUCCESS, __('La descarga de la actualización se ha realizado correctamente.', FALSE));
			Request::redirect('/admin/sistema/');
		}

		// Obtengo nombre del archivo.
		$f = glob(CACHE_PATH.DS.'updates'.DS.$version.'.{tar,tar.gz,tar.bz,zip}', GLOB_BRACE);
		$file = array_shift($f);
		unset($f);

		// Creo directorio temporal.
		$tmp_dir = Update_Utils::sys_get_temp_dir().DS.uniqid();
		mkdir($tmp_dir);

		// Descomprimo.
		$compresor = Update_Compresion::get_instance(Update_Utils::mime2compresor(Update_Utils::get_mime($file)));
		$compresor->set_temp_path($tmp_dir);
		$compresor->decompress($file);

		// Bloqueo para actualizar.
		Mantenimiento::lock(array(IP::get_ip_addr()));

		// Acciones personalizadas de actualización.
		if (file_exists($tmp_dir.DS.'install.php'))
		{
			// Cargo archivo de instalación.
			include($tmp_dir.DS.'install.php');
		}

		// Actualizo BD.
		if (file_exists($tmp_dir.DS.'database.php'))
		{
			// Cargamos las consultas.
			$queries = include($tmp_dir.DS.'database.php');

			// Las procesamos.
			$this->procesar_consultas($queries);
		}

		// Actualizo archivos.
		Update_Utils::copyr($tmp_dir.DS.'files'.DS, APP_BASE.DS);

		// Borro cache.
		Update_Utils::unlink($tmp_dir);
		unlink($file);

		// Actualizo versión del sistema.
		$m_config = new Model_Configuracion;
		$m_config->version = substr($version, 1);

		// Limpio cache.
		Cache::get_instance()->clean();

		// Borro cache de vistas.
		foreach (glob(CACHE_PATH.DS.'raintpl'.DS.'*', GLOB_ONLYDIR) as $file)
		{
			Update_Utils::unlink($file);
		}

		// Libero bloqueo.
		Mantenimiento::unlock();

		// Borro cache de versiones.
		Utils::configuracion()->update_sistema_actualizaciones = 'a:0:{}';
		Utils::configuracion()->update_sistema_last_check = NULL;

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('Actualización realizada correctamente.', FALSE));
		Request::redirect('/admin/sistema/');
	}

	/**
	 * Listado de consultas de actualización a ejecutar.
	 * @param array $database_list Arreglo de consultas a ejecutar.
	 * @return bool
	 */
	protected function procesar_consultas($database_list)
	{
		// Error global. Permite saber si todo fue correcto para continuar.
		$error_global = FALSE;

		// Ejecuto las consultas.
		foreach ($database_list as $k => $v)
		{
			// Ejecuto las consultas.
			$error = FALSE;
			foreach ($v[1] as $query)
			{
				try {
					switch ($query[0])
					{
						case 'INSERT':
							list(, $c) = $db->insert($query[1], isset($query[2]) ? $query[2] : NULL);
							if ($c <= 0)
							{
								throw new Exception("El resultado de la consulta: '{$query[1]}' es incorrecto.");
							}
							break;
						case 'DELETE':
							if ($db->delete($query[1], isset($query[2]) ? $query[2] : NULL) === FALSE)
							{
								throw new Exception("El resultado de la consulta: '{$query[1]}' es incorrecto.");
							}
							break;
						case 'UPDATE':
						case 'ALTER':
							if ($db->update($query[1], isset($query[2]) ? $query[2] : NULL) === FALSE)
							{
								throw new Exception("El resultado de la consulta: '{$query[1]}' es incorrecto.");
							}
							break;
						case 'QUERY':
							if ($db->query($query[1], isset($query[2]) ? $query[2] : NULL) === FALSE)
							{
								throw new Exception("El resultado de la consulta: {$query[1]}' es incorrecto.");
							}
							break;
					}
				}
				catch (Exception $e)
				{
					if (isset($query[3]) && isset($query[3]['error_no']))
					{
						if ($query[3]['error_no'] == $e->getCode())
						{
							continue;
						}
					}

					$error = '['.$e->getCode().'] '.$e->getMessage();
					break;
				}
			}

			// Agrego el resultado.
			if ($error === FALSE)
			{
				$lst[$k]['success'] = TRUE;
			}
			else
			{
				$lst[$k]['error'] = $error;
				$error_global = TRUE;
			}
		}

		return $error_global;
	}

	/**
	 * Visualización y edición de traducciones.
	 */
	public function action_traducciones($idioma)
	{
		// Cargo traducción activa.
		$l_activo = arr_get(configuracion_obtener(CONFIG_PATH.DS.'marifa.php'), 'language', '');

		// Cargo idioma.
		if ( ! empty($idioma))
		{
			// Verifico existencia.
			if ( ! preg_match('/^[a-z]{3}$/', $idioma))
			{
				add_flash_message(__('El idioma que quieres ver no existe.', FALSE));
				Request::redirect('/admin/sistema/traducciones/');
			}

			// Cargo listado de traducciones disponibles.
			$traducciones = $this->listado_traducciones();

			// Verifico se quiera editar una traducción existente.
			if ( ! in_array($idioma, $traducciones))
			{
				add_flash_message(__('El idioma que quieres ver no existe.', FALSE));
				Request::redirect('/admin/sistema/traducciones/');
			}

			// Cargo la vista.
			$vista = View::factory('/admin/sistema/editar_traduccion/');

			// Asigno idioma a la vista.
			$vista->assign('idioma', $idioma);

			// Asigno activo.
			$vista->assign('activo', $l_activo);

			// Obtengo listado de traducciones.
			$lang = configuracion_obtener(APP_BASE.DS.'traducciones'.DS.$idioma.'.php');

			// Lo asigno a la vista.
			$vista->assign('lang', $lang);
		}
		else
		{
			// Cargo la vista.
			$vista = View::factory('/admin/sistema/traducciones/');

			// Cargo listado de traducciones disponibles.
			$traducciones = $this->listado_traducciones();

			// Asigno activo.
			$vista->assign('activo', $l_activo);

			// Asigno a la vista.
			$vista->assign('traducciones', $traducciones);
		}


		// Asignamos el menú.
		$this->template->assign('master_bar', parent::base_menu('admin'));

		// Cargamos plantilla administración.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('sistema.traducciones'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Activo una traducción.
	 * @param  string $traduccion Traducción a activar.
	 */
	public function action_activar_traduccion($traduccion)
	{
		// Verificamos que sea válida.
		if ( ! preg_match('/^[a-z]{3}$/', $traduccion))
		{
			// Informo y vuelvo.
			add_flash_message(FLASH_ERROR, 'La traducción que deseas activar es incorrecta.');
			Request::redirect('/admin/sistema/traducciones/');
		}

		// Obtengo listado de traducciones.
		$traducciones = $this->listado_traducciones();

		// Verifico existencia.
		if ( ! in_array($traduccion, $traducciones))
		{
			// Informo y vuelvo.
			add_flash_message(FLASH_ERROR, 'La traducción que deseas activar es incorrecta.');
			Request::redirect('/admin/sistema/traducciones/');
		}

		// Cargo archivo de configuración.
		$config = Configuracion::factory(CONFIG_PATH.DS.'marifa.php');

		// Cargo traducción activa.
		$l_activo = arr_get($config, 'language', '');

		// Verifico que coincidan.
		if ($traduccion !== $l_activo)
		{
			// Actualizo el valor.
			$config['language'] = $traduccion;
			$config->save();
		}

		// Informo y vuelvo.
		add_flash_message(FLASH_SUCCESS, 'Se ha activado la traducción correctamente.');
		Request::redirect('/admin/sistema/traducciones/');
	}

	/**
	 * Desactivo la traducción actual. El resultado es utilizar las cadenas por defecto.
	 * @param  string $traduccion Traducción a desactivar.
	 */
	public function action_desactivar_traduccion($traduccion)
	{
		// Verificamos que sea válida.
		if ( ! preg_match('/^[a-z]{3}$/', $traduccion))
		{
			// Informo y vuelvo.
			add_flash_message(FLASH_ERROR, 'La traducción que deseas desactivar es incorrecta.');
			Request::redirect('/admin/sistema/traducciones/');
		}

		// Cargo archivo de configuración.
		$config = Configuracion::factory(CONFIG_PATH.DS.'marifa.php');

		// Cargo traducción activa.
		$l_activo = arr_get($config, 'language', '');

		// Verifico que coincidan.
		if ($traduccion !== $l_activo)
		{
			// Informo y vuelvo.
			add_flash_message(FLASH_ERROR, 'La traducción que deseas desactivar no se encuentra activa.');
			Request::redirect('/admin/sistema/traducciones/');
		}
		else
		{
			// Actualizo.
			$config['language'] = NULL;
			$config->save();

			// Informo y vuelvo.
			add_flash_message(FLASH_SUCCESS, 'Se ha desactivado la traducción correctamente.');
			Request::redirect('/admin/sistema/traducciones/');
		}
	}

	protected function listado_traducciones()
	{
		// Obtengo listado de archivos.
		$archivos = glob(APP_BASE.DS.'traducciones'.DS.'*.php');

		// Borro directorio y extensión.
		foreach ($archivos as $k => $v)
		{
			$archivos[$k] = substr($v, strlen(APP_BASE.DS.'traducciones'.DS), -4);
		}

		return $archivos;
	}
}
