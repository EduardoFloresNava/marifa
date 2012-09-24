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
		parent::__construct();
		//TODO: verificar permisos para la accion.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/usuario/login');
		}
	}

	/**
	 * Listado de temas.
	 */
	public function action_temas()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/temas');

		// Noticia Flash.
		if (Session::is_set('tema_correcta'))
		{
			$vista->assign('success', Session::get_flash('tema_correcta'));
		}
		if (Session::is_set('tema_error'))
		{
			$vista->assign('error', Session::get_flash('tema_error'));
		}

		// Cargo tema previsualizado y el actual.
		if (Session::is_set('preview-theme'))
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
			$a = Configuraciones::obtener(APP_BASE.DS.VIEW_PATH.$v.DS.'theme.php');
			$a['key'] = $v;
			$a['nombre'] = isset($a['nombre']) ? $a['nombre'] : $v;
			$a['descripcion'] = isset($a['descripcion']) ? $a['descripcion'] : 'Sin descripción';
			$themes[$k] = $a;
		}
		$vista->assign('temas', $themes);
		unset($themes);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

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
			Session::set('tema_error', 'El tema seleccionado para previsualizar es incorrecto.');
			Request::redirect('/admin/configuracion/temas');
		}

		// Verifico no sea actual.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			Session::set('tema_error', 'El tema seleccionado para previsualizar es el actual.');
			Request::redirect('/admin/configuracion/temas');
		}

		// Activo el tema.
		Session::set('preview-theme', $tema);
		Session::set('tema_correcta', 'El tema se a colocado para previsualizar correctamente');
		Request::redirect('/admin/configuracion/temas');
	}

	/**
	 * Terminamos la visualización temporal del tema al usuario.
	 */
	public function action_terminar_preview_tema()
	{
		// Verificamos si existe la previsualizacion.
		if (Session::is_set('preview-theme'))
		{
			// Quitamos la vista previa.
			Session::un_set('preview-theme');

			Session::set('tema_correcta', 'Vista previa terminada correctamente.');
		}
		else
		{
			Session::set('tema_error', 'No hay vista previa para deshabilitar.');
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
			Session::set('tema_error', 'El tema ya es el predeterminado.');
			Request::redirect('/admin/configuracion/temas');
		}

		// Verificamos exista.
		if ( ! in_array($tema, Theme::lista()))
		{
			Session::set('tema_error', 'El tema seleccionado para setear como predeterminado es incorrecto.');
			Request::redirect('/admin/configuracion/temas');
		}

		// Borro preview.
		if (Session::is_set('preview-theme'))
		{
			Session::un_set('preview-theme');
		}

		// Activo tema.
		Theme::setear_tema($tema);

		Session::set('tema_correcta', 'El tema se ha seteado como predeterminado correctamente.');
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
			Session::set('tema_error', 'El tema seleccionado para eliminar es incorrecto.');
			Request::redirect('/admin/configuracion/temas');
		}

		// Verifico no sea el actual ni el de previsualizacion.
		if ($tema == Theme::actual(TRUE) || $tema == Theme::actual())
		{
			Session::set('tema_error', 'El tema no se puede borrar por estar en uso.');
			Request::redirect('/admin/configuracion/temas');
		}

		// Lo eliminamos.
		Update_Utils::unlink(APP_BASE.DS.VIEW_PATH.$tema);

		// Refrescamos la cache.
		Theme::lista(TRUE);

		Session::set('tema_correcta', 'El tema se ha eliminado correctamente.');
		Request::redirect('/admin/configuracion/temas');
	}

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
					$vista->assign('error_carga', 'No se pudo descomprimir el archivo. Compruebe que sea correcteo.');
				}
				else
				{
					// Verifico que sea correcto.
					if (is_dir($tmp_dir) && file_exists($tmp_dir.DS.'theme.php') && file_exists($tmp_dir.DS.'views') && $tmp_dir.DS.'assets')
					{
						// Cargo configuraciones.
						$data = Configuraciones::obtener($tmp_dir.DS.'theme.php');

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
					Session::set('tema_correcta', 'El tema se instaló correctamente.');

					// Redireccionamos.
					Request::redirect('/admin/configuracion/temas');
				}
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_temas'));

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
		$pagina = (int) $pagina > 0 ? (int) $pagina : 1;

		// Cantidad de elementos por pagina.
		$cantidad_por_pagina = 20;

		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/noticias');

		// Noticia Flash.
		if (Session::is_set('noticia_correcta'))
		{
			$vista->assign('success', Session::get_flash('noticia_correcta'));
		}

		// Modelo de noticias.
		$model_noticias = new Model_Noticia;

		// Cargamos el listado de noticias.
		$lst = $model_noticias->listado($pagina, $cantidad_por_pagina);

		// Paginación.
		$total = $model_noticias->total();
		$paginador = new Paginator($total, $cantidad_por_pagina);
		$vista->assign('actual', $pagina);
		$vista->assign('total', $total);
		$vista->assign('cpp', $cantidad_por_pagina);
		$vista->assign('paginacion', $paginador->paginate($pagina));

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
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	public function action_nueva_noticia()
	{
		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/nueva_noticia');

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
			$visible = isset($_POST['visible']) ? $_POST['visible'] == 1 : FALSE;

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
				$id = $model_noticia->nuevo(Session::get('usuario_id'), $contenido, $visible ? Model_Noticia::ESTADO_VISIBLE : Model_Noticia::ESTADO_OCULTO);

				//TODO: agregar suceso de administracion.

				// Seteo FLASH message.
				Session::set('noticia_correcta', 'La noticia se creó correctamente');

				// Redireccionamos.
				Request::redirect('/admin/configuracion/noticias');
			}
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Activamos o desactivamos una noticia.
	 * @param int $id
	 * @param int $estado
	 */
	public function action_estado_noticia($id, $estado)
	{
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia( (int) $id);
		if ($model_noticia->existe())
		{
			$estado = (bool) $estado;
			if ($estado)
			{
				$model_noticia->activar();
				Session::set('noticia_correcta', 'Se habilitó correctamente la noticia #'. (int) $id);
			}
			else
			{
				$model_noticia->desactivar();
				Session::set('noticia_correcta', 'Se ocultó correctamente la noticia #'. (int) $id);
			}
		}
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Desctivamos todas las noticias.
	 */
	public function action_ocultar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->desactivar_todas();
		Session::set('noticia_correcta', 'Se han ocultado correctamente todas las noticias.');
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Activamos o desactivamos una noticia.
	 * @param int $id
	 * @param int $estado
	 */
	public function action_borrar_noticia($id)
	{
		// Cargamos el modelo de noticia.
		$model_noticia = new Model_Noticia( (int) $id);
		if ($model_noticia->existe())
		{
			// Borramos la noticia.
			$model_noticia->eliminar();
			Session::set('noticia_correcta', 'Se borró correctamente la noticia #'. (int) $id);
		}
		Request::redirect('/admin/configuracion/noticias');
	}

	/**
	 * Borramos todas las noticias.
	 */
	public function action_limpiar_noticias()
	{
		$model_noticia = new Model_Noticia;
		$model_noticia->eliminar_todas();
		Session::set('noticia_correcta', 'Se han borrado correctamente todas las noticias.');
		Request::redirect('/admin/configuracion/noticias');
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
			Request::redirect('/admin/configuracion/noticias');
		}

		// Cargamos la vista.
		$vista = View::factory('admin/configuracion/editar_noticia');

		// Valores por defecto y errores.
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
				$vista->assign('success', 'Contenido actualizado correctamente');
			}
			unset($contenido_clean);
		}

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu_login('admin'));

		// Cargamos plantilla administracion.
		$admin_template = View::factory('admin/template');
		$admin_template->assign('contenido', $vista->parse());
		unset($portada);
		$admin_template->assign('top_bar', Controller_Admin_Home::submenu('configuracion_noticias'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

}
