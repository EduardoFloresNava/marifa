<?php
/**
 * phpost.php is part of Marifa.
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
 * @package		Marifa\Installer
 * @subpackage  Importador
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase base para los importadores.
 *
 * @author     Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @since      Versión 0.2.RC4
 * @package    Marifa\Installer
 * @subpackage Importador
 */
class Installer_Importador_Phpost extends Installer_Importador {

	/**
	 * Importamos las configuraciones del sitio.
	 */
	protected function import_config()
	{
		// Obtengo elementos.
		$data = $this->importador_db->query('SELECT titulo, slogan, c_reg_active, c_reg_activate, c_reg_rango FROM w_configuracion LIMIT 1')->get_record(Database_Query::FETCH_OBJ);

		// Modelo de configuraciones.
		$model_config = new Model_Configuracion;

		// Importo nombre del sitio.
		$model_config->nombre = $data->titulo;

		// Descripción del sitio.
		$model_config->descripcion = $data->slogan;

		// Estado del registro.
		$model_config->registro = (bool) $data->c_reg_active;

		// Tipo de activación de las cuentas.
		$model_config->activacion_usuario = $data->c_reg_activate == 0 ? 2 : 1;

		// Rango por defecto.
		$model_config->rango_defecto = (int) $data->c_reg_rango;
	}

	/**
	 * Importamos los rangos.
	 */
	protected function import_rangos()
	{
		// Borro los rangos del sistema.
		$this->marifa_db->delete('DELETE FROM usuario_rango_permiso');
		$this->marifa_db->delete('DELETE FROM usuario_rango');

		// Obtengo listado de rangos.
		$rangos = $this->importador_db->query('SELECT rango_id, r_name, r_color, r_image, r_cant, r_allows, r_type FROM	u_rangos')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Máximo orden.
		$maximo = 1;

		foreach ($rangos as $rango)
		{
			// Obtengo información serializada.
			$rango->r_allows = unserialize($rango->r_allows);

			// Genero listado de información del rango.
			$dt = array();
			$dt[] = (int) $rango->rango_id; // ID.
			$dt[] = $rango->r_name; // Nombre.
			$dt[] = hexdec( (int) $rango->r_color); // Color.
			$dt[] = $rango->r_image; // Imagen.
			$dt[] = $maximo++; // Orden.
			$dt[] = (int) $rango->r_allows['gopfd']; // Puntos por día.
			$dt[] = (int) $rango->r_type; // Tipo de rango.
			$dt[] = $rango->r_type == 0 ? NULL : (int) $rango->r_cant; // Cantidad para el cambio de rango.
			$dt[] = (int) $rango->r_allows['gopfp']; // Puntos por post.

			// Insertamos el rango.
			list($id,) = $this->db->insert('INSERT INTO usuario_rango (id, nombre, color, imagen, orden, puntos, tipo, cantidad, puntos_dar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $dt);
			unset($dt);

			// Listado de asociación de permisos.
			$p_assoc = array(
				//'suad'     => SUPERADMIN
				//'sumo'     => SUPERMODERADOR
				'godp'     => Model_Usuario_Rango::PERMISO_POST_PUNTUAR,
				'gopp'     => Model_Usuario_Rango::PERMISO_POST_CREAR,
				'gopcp'    => Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR,
				'govpp'    => Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR,
				'govpn'    => Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR,
				//'goepc'   => Model_Usuario_Rango:: EDITAR COMENTARIO PROPIO
				//'godpc'   => Model_Usuario_Rango:: ELIMINAR COMENTARIO PROPIO
				'gopf'     => Model_Usuario_Rango::PERMISO_FOTO_CREAR,
				'gopcf'    => Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR,
				'gorpap'   => Model_Usuario_Rango::PERMISO_USUARIO_REVISAR_CONTENIDO,
				'govwm'    => Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO,
				'moacp'    => array(Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS, Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS),
				'modcu'    => Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS,
				'mocdf'    => Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS,
				'moadp'    => Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS,
				//'moadm'    => Model_Usuario_Rango::PERMISO_ Podrán aceptar reportes de mensajes.
				//'mocdm'    => Model_Usuario_Rango::PERMISO_ Podrán rechazar reportes de mensajes.
				'movub'    => Model_Usuario_Rango::PERMISO_USUARIO_BANEAR,
				//'moub'     => Model_Usuario_Rango::PERMISO_ Podrán usar el buscador de contenidos.
				'morp'     => Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA,
				'morf'     => Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA,
				'mocp'     => Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO,
				'mocc'     => Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO,
				'most'     => Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER,
				'moayaca'  => Model_Usuario_Rango::PERMISO_POST_OCULTAR,
				'movcud'   => Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRA,
				'movcus'   => Model_Usuario_Rango::PERMISO_USUARIO_BANEAR,
				'mosu'     => Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER,
				'modu'     => Model_Usuario_Rango::PERMISO_USUARIO_BANEAR,
				'moep'     => Model_Usuario_Rango::PERMISO_POST_ELIMINAR,
				'moedpo'   => Model_Usuario_Rango::PERMISO_POST_EDITAR,
				'moop'     => Model_Usuario_Rango::PERMISO_POST_OCULTAR,
				'mocepc'   => Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO,
				'moedcopo' => Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR,
				'moaydcp'  => Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR,
				'moecp'    => Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR,
				'moef'     => Model_Usuario_Rango::PERMISO_FOTO_ELIMINAR,
				'moedfo'   => Model_Usuario_Rango::PERMISO_FOTO_EDITAR,
				'moecf'    => Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR,
				//'moepm'    => Model_Usuario_Rango::PERMISO_ Podrán eliminar publicaciones en muros de otros usuarios
				//'moecm'    => Model_Usuario_Rango::PERMISO_ Eliminar Comentarios de Muros de otros usuarios
			);

			$permisos_agregar = array();

			// Proceso los permisos.
			foreach ($p_assoc as $k => $v)
			{
				// Verifico si existe.
				if (isset($rango->r_allows[$k]) && $rango->r_allows[$k] == 'on')
				{
					// Agrego permisos al arreglo.
					if ( ! is_array($v))
					{
						$v = array($v);
					}

					foreach ($v as $vv)
					{
						$permisos_agregar[] = $vv;
					}
				}
			}

			// Elimino repetidos.
			$permisos_agregar = array_unique($permisos_agregar);

			// Inserto los permisos.
			foreach ($permisos_agregar as $permiso)
			{
				// Agrego permiso.
				$this->db->insert('INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (?, ?)', array($id, $permiso));
			}
		}
	}

	/**
	 * Importamos los usuarios.
	 */
	public function import_usuarios()
	{
		// Obtenemos la lista de usuarios.
		$usuarios = $this->importador_db->query('SELECT user_id, user_name, user_password, user_email, user_rango, user_puntos, user_registro, user_lastlogin, user_lastactive, user_last_ip, user_activo, user_baneado FROM u_miembros')->set_fetch_type(Database_Query::FETCH_OBJ);

		foreach ($usuarios as $usuario)
		{
			// Arreglo con los datos.
			$user_data = array(
				'id'         => (int) $usuario->user_id,
				'nick'       => $usuario->user_name,
				'password'   => $usuario->user_password,
				'email'      => $usuario->user_email,
				'rango'      => (int) $usuario->user_rango,
				'puntos'     => (int) $usuario->user_puntos,
				'registro'   => date(BD_DATETIME, (int) $usuario->user_registro),
				'lastlogin'  => date(BD_DATETIME, (int) $usuario->user_lastlogin),
				'lastactive' => date(BD_DATETIME, (int) $usuario->user_lastactive),
				'lastip'     => $usuario->user_last_ip == NULL ? NULL : ip2long($usuario->user_last_ip),
				'estado'     => $usuario->user_activo == 0 ? Model_Usuario::ESTADO_PENDIENTE : ($usuario->user_baneado == 1 ? Model_Usuario::ESTADO_SUSPENDIDA : Model_Usuario::ESTADO_ACTIVA)
			);

			// Creo el usuario.
			
		}
	}
}