<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * home.php is part of Marifa.
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
 * @package		Marifa/Base
 * @subpackage  Controller
 */

/**
 * Controlador de la portada.
 *
 * @since      Versión 0.1
 * @package    Marifa/Base
 * @subpackage Controller
 */
class Base_Controller_Home extends Controller {

	/**
	 * Submenu de la portada.
	 * @param string $selected Elemento seleccionado.
	 * @return array
	 */
	protected function submenu_logout($selected = NULL)
	{
		return array(
		   'inicio' => array('link' => '/', 'caption' => 'Inicio', 'active' => $selected == 'inicio'),
		   'buscador' => array('link' => '/paquetes', 'caption' => 'Paquetes', 'active' => $selected == 'buscador'),
	   );
	}

	/**
	 * Submenu de la portada.
	 * @param string $selected Elemento seleccionado.
	 * @return array
	 */
	protected function submenu_login($selected = NULL)
	{
		return array(
			'inicio' => array('link' => '/', 'caption' => 'Inicio', 'active' => $selected == 'inicio'),
			'buscador' => array('link' => '/paquetes', 'caption' => 'Paquetes', 'active' => $selected == 'buscador'),
			'nuevo' => array('link' => '/post/nuevo', 'caption' => 'Agregar Post', 'active' => $selected == 'nuevo'),
	   );
	}

	/**
	 * Portada del sitio.
	 */
	public function action_index()
	{
		// Cargamos la portada.
		$portada = View::factory('home/index');

		// Acciones para menu offline.
		if ( ! Session::is_set('usuario_id'))
		{
			// Seteamos menu offline.
			$this->template->assign('master_bar', parent::base_menu_logout('posts'));

			$this->template->assign('top_bar', $this->submenu_logout('inicio'));
		}
		else
		{
			// Seteamos menu offline.
			$this->template->assign('master_bar', parent::base_menu_login('posts'));

			$this->template->assign('top_bar', $this->submenu_login('inicio'));
		}

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $portada->parse());
	}

	/**
	 * Prueba descarga de un plugin.
	 */
	public function action_install()
	{
		Dispatcher::call(''); // Prueba del uso de memoria.

		// Nombre del plugin.
		$p_nombre = "Test Plugin";

		// Borramos el plugin.
		//if (file_exists(Plugin_Manager::nombre_as_path($p_nombre)))
		//{
		//	Update_Utils::unlink(Plugin_Manager::nombre_as_path($p_nombre));
		//}

		// Objeto manejador de plugins.
		$pkg_manager = Plugin_Manager::getInstance();

		// Verificamos su existencia
		$o_plugin = $pkg_manager->get(Plugin_Manager::make_name($p_nombre));

		if ($o_plugin === NULL)
		{
			// Realizamos la instalación.

			// Cargamos el actualizador.
			$o_updater = new Update_Updater;

			// Descargamos el paquete e instalamos el paquete. Se usa 1 para mostrar actualizaciones.
			if ($o_updater->install_package(Update_Utils::make_hash($p_nombre), 1))
			{
				// Actualizamos la cache.
				$pkg_manager->regenerar_lista();

				// Cargamos el paquete.
				$o_plugin = new Plugin_Plugin($p_nombre);

				// Realizamos la actualizacion.
				$o_plugin->install();

				// Activamos el paquete.
				$pkg_manager->set_state(Plugin_Manager::make_name($p_nombre), TRUE, TRUE);

				echo "Instalación existosa";
			}
			else
			{
				echo "Problema al realizar la instalación";
			}
		}
		else
		{
			// Buscamos actualizaciones.
			$upd_id = $o_plugin->check_updates();

			if ($upd_id === FALSE)
			{
				echo "No hay actualizaciones";
			}
			else
			{
				// Instalamos la actualizacion.

				// Desactivo el plugin.
				if ($o_plugin->info()->estado)
				{
					$o_plugin->remove();
				}

				// Directorio del plugin.
				$orig_path = Plugin_Manager::nombre_as_path($p_nombre);
				$tmp_path = rtrim($orig_path, '/').'.bkp';

				// Realizamos una copia.
				Update_Utils::copyr($orig_path, $tmp_path);

				// Borramos el original.
				Update_Utils::unlink($orig_path);

				// Cargamos el actualizador.
				$o_updater = new Update_Updater;

				// Descargamos el paquete e instalamos el paquete.
				if ( ! $o_updater->install_package(Update_Utils::make_hash($p_nombre), $upd_id))
				{
					// Recuperamos el original.
					Update_Utils::copyr($tmp_path, $orig_path);

					echo "No se pudo actualizar a la versión $upd_id.";
				}
				else
				{
					echo "Actualización a la versión $upd_id exitosa.";
				}

				// Realizamos la instalación.
				$o_plugin->install();

				// Borramos la copia.
				Update_Utils::unlink($tmp_path);
			}
		}

		if ( ! Request::is_cli())
		{
			echo "<br />";
		}
	}

}
