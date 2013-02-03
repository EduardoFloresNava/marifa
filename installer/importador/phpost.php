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
	 * Limpio base de datos.
	 */
	protected function import_clean()
	{
		// Borro las tablas indicadas.
		$tablas = array(
			'categoria',
			'foto',
			'foto_comentario',
			'foto_denuncia',
			'foto_favorito',
			'foto_voto',
			'medalla',
			'mensaje',
			'noticia',
			'post',
			'post_comentario',
			'post_comentario_voto',
			'post_compartido',
			'post_denuncia',
			'post_favorito',
			'post_moderado',
			'post_punto',
			'post_seguidor',
			'post_tag',
			'session',
			'shout',
			'shout_comentario',
			'shout_favorito',
			'shout_tag',
			'shout_usuario',
			'shout_voto',
			'suceso',
			'usuario',
			'usuario_aviso',
			'usuario_baneo',
			'usuario_bloqueo',
			'usuario_denuncia',
			'usuario_medalla',
			'usuario_nick',
			'usuario_perfil',
			'usuario_rango',
			'usuario_rango_permiso',
			'usuario_recuperacion',
			'usuario_seguidor',
			'usuario_suspension',
			'usuario_visita',
		);

		foreach ($tablas as $tabla)
		{
			$this->marifa_db->delete('DELETE FROM '.$tabla);
		}
	}

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
			list($id,) = $this->marifa_db->insert('INSERT INTO usuario_rango (id, nombre, color, imagen, orden, puntos, tipo, cantidad, puntos_dar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', $dt);
			unset($dt);

			// Listado de asociación de permisos.
			$p_assoc = array(
				'suad'     => array(
					Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS,
					Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER,
					Model_Usuario_Rango::PERMISO_USUARIO_BANEAR,
					Model_Usuario_Rango::PERMISO_USUARIO_ADVERTIR,
					Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR,
					Model_Usuario_Rango::PERMISO_POST_CREAR,
					Model_Usuario_Rango::PERMISO_POST_PUNTUAR,
					Model_Usuario_Rango::PERMISO_POST_ELIMINAR,
					Model_Usuario_Rango::PERMISO_POST_OCULTAR,
					Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS,
					Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO,
					Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER,
					Model_Usuario_Rango::PERMISO_POST_EDITAR,
					Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA,
					Model_Usuario_Rango::PERMISO_FOTO_CREAR,
					Model_Usuario_Rango::PERMISO_FOTO_VOTAR,
					Model_Usuario_Rango::PERMISO_FOTO_ELIMINAR,
					Model_Usuario_Rango::PERMISO_FOTO_OCULTAR,
					Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS,
					Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO,
					Model_Usuario_Rango::PERMISO_FOTO_EDITAR,
					Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA,
					Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO,
					Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO,
					Model_Usuario_Rango::PERMISO_SITIO_ACCESO_MANTENIMIENTO,
					Model_Usuario_Rango::PERMISO_SITIO_CONFIGURAR,
					Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO
				),
				'sumo'     => array(
					Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS,
					Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER,
					Model_Usuario_Rango::PERMISO_USUARIO_BANEAR,
					Model_Usuario_Rango::PERMISO_USUARIO_ADVERTIR,
					Model_Usuario_Rango::PERMISO_POST_CREAR,
					Model_Usuario_Rango::PERMISO_POST_PUNTUAR,
					Model_Usuario_Rango::PERMISO_POST_ELIMINAR,
					Model_Usuario_Rango::PERMISO_POST_OCULTAR,
					Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS,
					Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO,
					Model_Usuario_Rango::PERMISO_POST_FIJAR_PROMOVER,
					Model_Usuario_Rango::PERMISO_POST_EDITAR,
					Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA,
					Model_Usuario_Rango::PERMISO_FOTO_CREAR,
					Model_Usuario_Rango::PERMISO_FOTO_VOTAR,
					Model_Usuario_Rango::PERMISO_FOTO_ELIMINAR,
					Model_Usuario_Rango::PERMISO_FOTO_OCULTAR,
					Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS,
					Model_Usuario_Rango::PERMISO_FOTO_VER_DESAPROBADO,
					Model_Usuario_Rango::PERMISO_FOTO_EDITAR,
					Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA,
					Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_COMENTAR_CERRADO,
					Model_Usuario_Rango::PERMISO_COMENTARIO_VOTAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_ELIMINAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_OCULTAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_EDITAR,
					Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO
				),
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
				'movcud'   => Model_Usuario_Rango::PERMISO_USUARIO_ADMINISTRAR,
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
				$this->marifa_db->insert('INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (?, ?)', array($id, $permiso));
			}
		}
	}

	/**
	 * Importamos los usuarios.
	 */
	protected function import_usuarios()
	{
		// Obtenemos la lista de usuarios.
		$usuarios = $this->importador_db->query('SELECT user_id, user_name, user_password, user_email, user_rango, user_puntos, user_registro, user_lastlogin, user_lastactive, user_last_ip, user_activo, user_baneado FROM u_miembros')->set_fetch_type(Database_Query::FETCH_OBJ);

		foreach ($usuarios as $usuario)
		{
			// Arreglo con los datos.
			$usuario_data = array(
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
			try {
				$this->marifa_db->insert('INSERT INTO usuario (id, nick, password, email, rango, puntos, registro, lastlogin, lastactive, lastip, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array_values($usuario_data));
			}
			catch (Exception $e)
			{

			}
		}
		unset($usuarios, $usuario, $usuario_data);
	}

	/**
	 * Importo las suspensiones a los usuarios.
	 */
	protected function import_suspensiones()
	{
		// Obtengo el listado de suspensiones.
		$suspensiones = $this->importador_db->query('SELECT susp_id, user_id, susp_causa, susp_date, susp_termina, susp_mod FROM u_suspension')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo suspensiones a los usuarios.
		foreach ($suspensiones as $suspension)
		{
			// Arreglo con los datos.
			$suspension_data = array(
				'id' => (int) $suspension->susp_id,
				'usuario_id' => (int) $suspension->user_id,
				'moderador_id' => (int) $suspension->susp_mod,
				'motivo' => $suspension->susp_causa,
				'inicio' => date(BD_DATETIME, (int) $suspension->susp_date),
				'fin' => date(BD_DATETIME, (int) $suspension->susp_termina)
			);

			// Creo la suspensión.
			$this->marifa_db->insert('INSERT INTO usuario_suspension (id, usuario_id, moderador_id, motivo, inicio, fin) VALUES (?, ?, ?, ?, ?, ?)', array_values($suspension_data));
		}
		unset($suspensiones, $suspension, $suspension_data);
	}

	/**
	 * Importo los avisos enviados a los usuarios.
	 */
	protected function import_avisos()
	{
		// Obtengo listado de avisos.
		$avisos = $this->importador_db->query('SELECT av_id, user_id, av_subject, av_body, av_date, av_read FROM u_avisos')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo los avisos.
		foreach ($avisos as $aviso)
		{
			// Arreglo con los datos.
			$aviso_data = array(
				'id' => (int) $aviso->av_id,
				'usuario_id' => (int) $aviso->user_id,
				'moderador_id' => (int) $aviso->user_id,
				'asunto' => $aviso->av_subject,
				'contenido' => $aviso->av_body,
				'fecha' => date(BD_DATETIME, (int) $aviso->av_date),
				'estado' => $aviso->av_read == 0 ? Model_Usuario_Aviso::ESTADO_NUEVO : Model_Usuario_Aviso::ESTADO_VISTO
			);

			$this->marifa_db->insert('INSERT INTO usuario_aviso (id, usuario_id, moderador_id, asunto, contenido, fecha, estado) VALUES (?, ?, ?, ?, ?, ?, ?)', array_values($aviso_data));
		}
		unset($avisos, $aviso, $aviso_data);
	}

	/**
	 * Importo los bloqueos entre usuarios.
	 */
	protected function import_bloqueos()
	{
		// Obtengo listado de bloqueos.
		$bloqueos = $this->importador_db->query('SELECT b_user, b_auser FROM u_bloqueos')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo los bloqueos.
		foreach ($bloqueos as $bloqueo)
		{
			$this->marifa_db->insert('INSERT INTO usuario_bloqueo (usuario_id, bloqueado_id) VALUES (?, ?)', array( (int) $bloqueo->b_user, (int) $bloqueo->b_auser));
		}
		unset($bloqueos, $bloqueo);
	}

	/**
	 * Importo listado de seguidores entre usuarios y posts.
	 */
	protected function import_seguidores()
	{
		// 1: Usuario, 3 Post.

		// Obtengo listado de seguidores.
		$seguidores = $this->importador_db->query('SELECT f_user, f_id, f_date FROM u_follows WHERE f_type = 1')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo los seguidores.
		foreach ($seguidores as $seguidor)
		{
			$this->marifa_db->insert('INSERT INTO usuario_seguidor (usuario_id, seguidor_id, fecha) VALUES (?, ?, ?)', array( (int) $seguidor->f_user, (int) $seguidor->f_id, date(BD_DATETIME, (int) $seguidor->f_date)));
		}
		unset($seguidores, $seguidor);
	}

	/**
	 * Importo los mensajes entre los usuarios.
	 */
	protected function import_mensajes()
	{
		// Obtengo listado de mensajes.
		$mensajes = $this->importador_db->query('SELECT mp_id, mp_to, mp_from, mp_read_to, mp_del_to, mp_subject, mp_preview, mp_date FROM u_mensajes')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo los mensajes.
		foreach ($mensajes as $mensaje)
		{
			$mensaje_data = array(
				'id' => (int) $mensaje->mp_id,
				'emisor_id' => (int) $mensaje->mp_from,
				'receptor_id' => (int) $mensaje->mp_to,
				'estado' => $mensaje->mp_read_to == 0 ? Model_Mensaje::ESTADO_NUEVO : ($mensaje->mp_del_to == 0 ? Model_Mensaje::ESTADO_LEIDO : Model_Mensaje::ESTADO_ELIMINADO),
				'asunto' => $mensaje->mp_subject,
				'contenido' => $mensaje->mp_preview,
				'fecha' => date(BD_DATETIME, (int) $mensaje->mp_date),
				'padre_id' => NULL
			);

			$this->marifa_db->insert('INSERT INTO mensaje (id, emisor_id, receptor_id, estado, asunto, contenido, fecha, padre_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', array_values($mensaje_data));
		}
		unset($mensajes, $mensaje, $mensaje_data);

		// Obtengo listado de respuestas.
		$respuestas = $this->importador_db->query('SELECT mp_id, mr_from, mr_body, mr_date FROM u_respuestas')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Listado de últimos id de un MP.
		$lst_id = array();

		// Importo los mensajes.
		foreach ($respuestas as $respuesta)
		{
			// Obtengo el ID de quien será el padre.
			if (isset($lst_id[ (int) $respuesta->mp_id]))
			{
				$padre_id = $lst_id[ (int) $respuesta->mp_id];
			}
			else
			{
				$padre_id = (int) $respuesta->mp_id;
			}

			// Cargo el usuarios involucrados en el mensaje.
			list($from, $to, $subject) = $this->marifa_db->query('SELECT emisor_id, receptor_id, asunto FROM mensaje WHERE id = ?', $padre_id)->get_record(Database_Query::FETCH_NUM, array(Database_Query::FIELD_INT, Database_Query::FIELD_INT,Database_Query::FIELD_STRING));

			// Genero emisor-receptor correcto.
			if ($from != $respuesta->mr_from)
			{
				$to = $from;
				$from = (int) $respuesta->mr_from;
			}

			// Genero listado de información.
			$respuesta_data = array(
				'emisor_id' => $from,
				'receptor_id' => $to,
				'estado' => Model_Mensaje::ESTADO_LEIDO, // Se toman todas las respuestas como leidas.
				'asunto' => $subject,
				'contenido' => $respuesta->mr_body,
				'fecha' => date(BD_DATETIME, (int) $respuesta->mr_date),
				'padre_id' => $padre_id
			);

			// Creo la respuesta.
			list ($id, ) = $this->marifa_db->insert('INSERT INTO mensaje (emisor_id, receptor_id, estado, asunto, contenido, fecha, padre_id) VALUES (?, ?, ?, ?, ?, ?, ?)', array_values($respuesta_data));

			// Actualizo el padre.
			$lst_id[ (int) $respuesta->mp_id] = $id;
		}
		unset($respuestas, $respuesta, $respuesta_data, $lst_id);
	}

	/**
	 * Importo los datos del perfil del usuario.
	 */
	protected function import_perfil_usuario()
	{
		// Obtengo listado de datos de usuarios.
		$perfiles = $this->importador_db->query('SELECT user_id, user_dia, user_mes, user_ano, user_pais, user_estado, user_sexo, user_firma, p_nombre, p_avatar, p_mensaje, p_sitio, p_socials, p_gustos, p_estado, p_hijos, p_vivo, p_altura, p_peso, p_pelo, p_ojos, p_fisico, p_dieta, p_tengo, p_fumo, p_tomo, p_estudios, p_idiomas, p_profesion, p_empresa, p_sector, p_ingresos, p_int_prof, p_hab_prof, p_intereses, p_hobbies, p_tv, p_musica, p_deportes, p_libros, p_peliculas, p_comida, p_heroes, p_configs, p_total FROM u_perfil')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo los datos de los perfiles.
		foreach ($perfiles as $perfil)
		{
			// Cargo el modelo local.
			$model_perfil = new Model_Usuario_Perfil( (int) $perfil->user_id);

			// Origen.
			$model_perfil->origen = $perfil->user_pais.'.'.$perfil->user_estado;

			// Sexo.
			$model_perfil->sexo = $perfil->user_sexo == 1 ? 'm' : 'f';

			// Fecha de nacimiento.
			$model_perfil->nacimiento = $perfil->user_ano.'-'.$perfil->user_mes.'-'.$perfil->user_dia;

			// Nombre completo.
			$model_perfil->nombre = $perfil->p_nombre;

			// Mensaje Personal.
			$model_perfil->mensaje_personal = $perfil->p_mensaje;

			// Web.
			$model_perfil->web = $perfil->p_sitio;

			if ($perfil->p_socials !== NULL)
			{
				$social = @unserialize($perfil->p_socials);

				if (isset($social[0]))
				{
					// Facebook.
					$model_perfil->facebook = $social[0];
				}

				if (isset($social[1]))
				{
					// Twitter.
					$model_perfil->twitter = $social[1];
				}
				unset($social);
			}

			$gustos = unserialize($perfil->p_gustos);

			// Hacer amigos.
			$model_perfil->hacer_amigos = (bool) $gustos[0];

			// Conocer gente con mismos intereses.
			$model_perfil->conocer_gente_intereses = (bool) $gustos[1];

			// Conocer gente para hacer negocios.
			$model_perfil->conocer_gente_negocios = (bool) $gustos[2];

			// Encontrar pareja.
			$model_perfil->encontrar_pareja = (bool) $gustos[3];

			// De todo.
			$model_perfil->de_todo = (bool) $gustos[4];

			unset($gustos);

			// Estado civil.
			//$model_perfil->estado_civil =

			$fields = array(

			'estado_civil',
			'hijos',
			'vivo_con',

			'mi_altura',
			'mi_peso',

			'color_pelo',
			'color_ojos',
			'complexion',
			'mi_dieta',
			'fumo',
			'tomo_alcohol',

			'tatuajes',
			'piercings',

			'estudios',

			'idioma_espanol',
			'idioma_ingles',
			'idioma_portugues',
			'idioma_frances',
			'idioma_italiano',
			'idioma_aleman',
			'idioma_otro',

			'empresa',
			'profesion',

			'sector',

			'nivel_ingresos',

			'intereses_personales',
			'habilidades_profesionales',
			'mis_intereses',
			'hobbies',
			'series_tv_favoritas',
			'musica_favorita',
			'deportes_y_equipos_favoritos',
			'libros_favoritos',
			'peliculas_favoritas',
			'comida_favorita',
			'mis_heroes',
		);
		}
		unset($perfiles, $perfil);
	}

	/**
	 * Importamos las noticias.
	 */
	protected function import_noticias()
	{
		// Obtengo listado de noticias.
		$noticias = $this->importador_db->query('SELECT not_id, not_body, not_autor, not_date, not_active FROM w_noticias')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las noticias.
		foreach ($noticias as $noticia)
		{
			$noticia_data = array(
				'id' => (int) $noticia->not_id,
				'usuario_id' => (int) $noticia->not_autor,
				'contenido' => $noticia->not_body,
				'fecha' => date(BD_DATETIME, (int) $noticia->not_date),
				'estado' => (int) $noticia->not_active
			);

			$this->marifa_db->insert('INSERT INTO noticia (id, usuario_id, contenido, fecha, estado) VALUES (?, ?, ?, ?, ?)', array_values($noticia_data));
		}
		unset($noticias, $noticia, $noticia_data);
	}

	/**
	 * Importamos las medallas.
	 */
	protected function import_medallas()
	{
		// Obtengo listado de medallas.
		$medallas = $this->importador_db->query('SELECT medal_id, m_autor, m_title, m_description, m_image, m_cant, m_type, m_cond_user, m_cond_user_rango, m_cond_post, m_cond_foto, m_date, m_total FROM w_medallas')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las medallas.
		foreach ($medallas as $medalla)
		{
			$medalla_data = array(
				'id' => (int) $medalla->medal_id,
				'nombre' => $medalla->m_title,
				'descripcion' => $medalla->m_description,
				'imagen' => $medalla->m_image.'_32.png',
				'tipo' => (int) $medalla->m_type + 1,
				'condicion' => NULL,
				'cantidad' => (int) $medalla->m_cant
			);

			switch ($medalla->m_type)
			{
				case 0: // Usuario.
					switch ($medalla->m_cond_user)
					{
						case 1:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_PUNTOS;
							break;
						case 2:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_SEGUIDORES;
							break;
						case 3:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_SIGUIENDO;
							break;
						case 4:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_POSTS;
							break;
						case 5:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_COMENTARIOS_EN_FOTOS;
							break;
						case 6:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_POSTS;
							break;
						case 7:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_FOTOS;
							break;
						case 8:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_MEDALLAS;
							break;
						case 9:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_USUARIO_RANGO;
							break;
					}
					break;
				case 1: // Post.
					switch ($medalla->m_cond_post)
					{
						case 1:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_PUNTOS;
							break;
						case 2:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_SEGUIDORES;
							break;
						case 3:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_COMENTARIOS;
							break;
						case 4:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_FAVORITOS;
							break;
						case 5:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_DENUNCIAS;
							break;
						case 6:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_VISITAS;
							break;
						case 7:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_MEDALLAS;
							break;
						case 8:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_POST_VECES_COMPARTIDO;
							break;
					}
					break;
				case 2: // Foto.
					switch ($medalla->m_cond_foto)
					{
						case 1:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_FOTO_VOTOS_POSITIVOS;
							break;
						case 2:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_FOTO_VOTOS_POSITIVOS;
							break;
						case 3:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_FOTO_COMENTARIOS;
							break;
						case 4:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_FOTO_VISITAS;
							break;
						case 5:
							$medalla_data['condicion'] = Model_Medalla::CONDICION_FOTO_MEDALLAS;
							break;
					}
					break;
			}
			$this->marifa_db->insert('INSERT INTO medalla (id, nombre, descripcion, imagen, tipo, condicion, cantidad) VALUES (?, ?, ?, ?, ?, ?, ?)', array_values($medalla_data));
		}
		unset($medallas, $medalla, $medalla_data);

		// Obtengo listado de medallas a usuarios.
		$medallas_usuario = $this->importador_db->query('SELECT id, medal_id, medal_for, medal_date, medal_ip FROM w_medallas_assign')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las medallas a usuarios.
		foreach ($medallas_usuario as $medalla_usuario)
		{
			$medalla_usuario_data = array(
				'usuario_id' => (int) $medalla_usuario->medal_for,
				'medalla_id' => $medalla_usuario->medal_id,
				'fecha' => date(BD_DATETIME, (int) $medalla_usuario->medal_date),
				'objeto_id' => NULL
			);

			$this->marifa_db->insert('INSERT INTO usuario_medalla (usuario_id, medalla_id, fecha, objeto_id) VALUES (?, ?, ?, ?)', array_values($medalla_usuario_data));
		}
		unset($medalla_usuario, $medallas_usuario, $medalla_usuario_data);
	}

	/**
	 * Importamos las categorias.
	 */
	protected function import_categorias()
	{
		// Obtengo listado de categorias.
		$categorias = $this->importador_db->query('SELECT cid, c_nombre, c_img FROM p_categorias')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las fotos.
		foreach ($categorias as $categoria)
		{
			$categoria_data = array(
				'id' => (int) $categoria->cid,
				'nombre' => $categoria->c_nombre,
				'seo' => Model_Categoria::make_seo_s($categoria->c_nombre),
				'imagen' => $categoria->c_img
			);

			$this->marifa_db->insert('INSERT INTO categoria (id, nombre, seo, imagen) VALUES (?, ?, ?, ?)', array_values($categoria_data));
		}
		unset($categorias, $categoria, $categoria_data);
	}

	/**
	 * Importamos las fotos.
	 */
	protected function import_fotos()
	{
		// Obtengo la primera categoría para poner todas las fotos.
		$categoria_id = (int) $this->marifa_db->query('SELECT id FROM categoria LIMIT 1')->get_var();

		// Obtengo listado de fotos.
		$fotos = $this->importador_db->query('SELECT foto_id, f_title, f_date, f_description, f_url, f_user, f_closed, f_visitas, f_status, f_last, f_hits FROM f_fotos')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las fotos.
		foreach ($fotos as $foto)
		{
			$foto_data = array(
				'id' => (int) $foto->foto_id,
				'usuario_id' => (int) $foto->f_user,
				'creacion' => date(BD_DATETIME, (int) $foto->f_date),
				'titulo' => $foto->f_title,
				'descripcion' => $foto->f_description,
				'url' => $foto->f_url,
				'estado' => $foto->f_status == 0 ? Model_Foto::ESTADO_ACTIVA : ($foto->f_status == 1 ? Model_Foto::ESTADO_OCULTA : Model_Foto::ESTADO_BORRADA),
				'ultima_visita' => $foto->f_visitas == 0 ? NULL : date(BD_DATETIME, (int) $foto->f_last),
				'visitas' => $foto->f_visitas == 0 ? NULL : (int) $foto->f_hits,
				'categoria_id' => $categoria_id,
				'comentar' => (bool) $foto->f_closed
			);

			$this->marifa_db->insert('INSERT INTO foto (id, usuario_id, creacion, titulo, descripcion, url, estado, ultima_visita, visitas, categoria_id, comentar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array_values($foto_data));
		}
		unset($fotos, $foto, $foto_data, $catuegoria_id);

		// Obtengo listado de votos a fotos.
		$votos = $this->importador_db->query('SELECT v_foto_id, v_user, v_type FROM f_votos')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los voto.
		foreach ($votos as $voto)
		{
			$votos_data = array(
				'foto_id' => (int) $voto->v_foto_id,
				'usuario_id' => (int) $voto->v_user,
				'cantidad' => $voto->v_type == 0 ? -1 : 1
			);

			$this->marifa_db->insert('INSERT INTO foto_voto (foto_id, usuario_id, cantidad) VALUES (?, ?, ?)', array_values($votos_data));
		}
		unset($votos, $voto, $votos_data);

		// Obtengo listado de comentarios.
		$comentarios = $this->importador_db->query('SELECT cid, c_foto_id, c_user, c_date, c_body FROM f_comentarios')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los comentarios.
		foreach ($comentarios as $comentario)
		{
			$comentarios_data = array(
				'id' => (int) $comentario->cid,
				'foto_id' => (int) $comentario->c_foto_id,
				'usuario_id' => (int) $comentario->c_user,
				'comentario' => $comentario->c_body,
				'fecha' => date(BD_DATETIME, (int) $comentario->c_date),
				'estado' => 0
			);

			$this->marifa_db->insert('INSERT INTO foto_comentario (id, foto_id, usuario_id, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)', array_values($comentarios_data));
		}
		unset($comentarios, $comentario, $comentarios_data);
	}

	/**
	 * Importamos los posts.
	 */
	protected function import_posts()
	{
		// Obtengo listado de comentarios.
		$posts = $this->importador_db->query('SELECT post_id, post_user, post_category, post_title, post_body, post_date, post_hits, post_private, post_sponsored, post_sticky, post_comments, post_status, post_tags FROM p_posts')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los comentarios.
		foreach ($posts as $post)
		{
			$posts_data = array(
				'id' => (int) $post->post_id,
				'usuario_id' => (int) $post->post_user,
				'categoria_id' => (int) $post->post_category,
				'titulo' => $post->post_title,
				'contenido' => $post->post_body,
				'fecha' => date(BD_DATETIME, (int) $post->post_date),
				'vistas' => (int) $post->post_hits,
				'privado' => (bool) $post->post_private,
				'sponsored' => (bool) $post->post_sponsored,
				'sticky' => (bool) $post->post_sticky,
				'comentar' => ! (bool) $post->post_comments,
				'tags' => $post->post_tags
			);

			// Convierto los estados.
			switch ($post->post_status)
			{
				case 3:
					$posts_data['estado'] = Model_Post::ESTADO_OCULTO;
					break;
				case 2:
					$posts_data['estado'] = Model_Post::ESTADO_BORRADO; // Ver si es el estado correspondiente.
					break;
				case 1:
					$posts_data['estado'] = Model_Post::ESTADO_PENDIENTE;
					break;
				default:
					$posts_data['estado'] = Model_Post::ESTADO_ACTIVO;
			}

			// Inserto el post.
			$this->marifa_db->insert('INSERT INTO post (id, usuario_id, categoria_id, titulo, contenido, fecha, vistas, privado, sponsored, sticky, comentar, tags, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array_values($posts_data));

			// Inserto las etiquetas.
			$tags = array_map(create_function('$m', 'return trim(strtolower($m));'), explode(',', $post->post_tags));

			// Elimino repetidas.
			$tags = array_unique($tags);

			foreach ($tags as $tag)
			{
				try
				{
					$this->marifa_db->insert('INSERT INTO post_tag (post_id, nombre) VALUES (?, ?)', array( (int) $post->post_id, $tag));
				}
				catch (Database_Exception $e)
				{

				}
			}
		}
		unset($posts, $post, $posts_data);

		// Obtengo listado de favoritos.
		$favoritos = $this->importador_db->query('SELECT fav_user, fav_post_id FROM p_favoritos')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los favoritos.
		foreach ($favoritos as $favorito)
		{
			$this->marifa_db->insert('INSERT INTO post_favorito (usuario_id, post_id) VALUES (?, ?)', array( (int) $favorito->fav_user, (int) $favorito->fav_post_id));
		}
		unset($favoritos, $favorito);

		// Obtengo listado de puntos.
		$puntos = $this->importador_db->query('SELECT tid, tuser, cant FROM p_votos WHERE type = 1')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los puntos.
		foreach ($puntos as $punto)
		{
			$this->marifa_db->insert('INSERT INTO post_punto (post_id, usuario_id, cantidad) VALUES (?, ?, ?)', array( (int) $punto->tid, (int) $punto->tuser, (int) $punto->cant));
		}
		unset($puntos, $punto);

		// Obtengo listado de comentarios.
		$comentarios = $this->importador_db->query('SELECT cid, c_post_id, c_user, c_date, c_body, c_status FROM p_comentarios')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los comentarios.
		foreach ($comentarios as $comentario)
		{
			$comentarios_data = array(
				'id' => (int) $comentario->cid,
				'post_id' => (int) $comentario->c_post_id,
				'usuario_id' => (int) $comentario->c_user,
				'fecha' => date(BD_DATETIME, (int) $comentario->c_date),
				'contenido' => $comentario->c_body,
				'estado' => $comentario->c_status == 0 ? Model_Comentario::ESTADO_VISIBLE : Model_Comentario::ESTADO_OCULTO
			);

			$this->marifa_db->insert('INSERT INTO post_comentario (id, post_id, usuario_id, fecha, contenido, estado) VALUES (?, ?, ?, ?, ?, ?)', array_values($comentarios_data));
		}
		unset($comentarios, $comentario, $comentarios_data);

		// Obtengo listado de puntos.
		$puntos_comentarios = $this->importador_db->query('SELECT tid, tuser, cant FROM p_votos WHERE type = 2')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los puntos.
		foreach ($puntos_comentarios as $puntos_comentario)
		{
			$this->marifa_db->insert('INSERT INTO post_comentario_voto (post_comentario_id, usuario_id, cantidad) VALUES (?, ?, ?)', array( (int) $puntos_comentario->tid, (int) $puntos_comentario->tuser, 1));
		}
		unset($puntos_comentarios, $puntos_comentario);

		// Obtengo listado de seguidores.
		$seguidores = $this->importador_db->query('SELECT f_user, f_id FROM u_follows WHERE f_type = 3')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importo los seguidores.
		foreach ($seguidores as $seguidor)
		{
			$this->marifa_db->insert('INSERT INTO post_seguidor (usuario_id, post_id) VALUES (?, ?)', array( (int) $seguidor->f_user, (int) $seguidor->f_id));
		}
		unset($seguidores, $seguidor);

		// Obtengo listado de comentarios.
		$borradores = $this->importador_db->query('SELECT b_post_id FROM p_borradores')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los comentarios.
		foreach ($borradores as $borrador)
		{
			$this->marifa_db->update('UPDATE post SET estado = ? WHERE id = ?', array(Model_Post::ESTADO_BORRADOR, $borrador->b_post_id));
		}
		unset($borradores, $borrador);
	}

	/**
	 * Importamos las denuncias de contenido.
	 */
	protected function import_denuncias()
	{
		// Obtengo listado de denuncias.
		$denuncias = $this->importador_db->query('SELECT did, obj_id, d_user, d_razon, d_extra, d_total, d_type, d_date FROM w_denuncias')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las actividades.
		foreach ($denuncias as $denuncia)
		{
			switch ( (int) $denuncia->d_type)
			{
				case 1: // Post
					// Listado de datos a usar.
					$denuncia_data = array(
						'post_id' => (int) $denuncia->obj_id,
						'usuario_id' => (int) $denuncia->d_user,
						'motivo' => (int) $d_type + 1,
						'comentario' => $denuncia->d_extra,
						'fecha' => date(BD_DATETIME, (int) $denuncia->d_date),
						'estado' => Model_Post_Denuncia::ESTADO_PENDIENTE
					);

					// Inserto la denuncia.
					$this->marifa_db->insert('INSERT INTO post_denuncia (post_id, usuario_id, motivo, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)', array_values($denuncia_data));
					break;
				case 2: // Mensaje -> No implementado aún en Marifa.
					break;
				case 3: // Usuario.
					// Listado de datos a usar.
					$denuncia_data = array(
						'denunciado_id' => (int) $denuncia->obj_id,
						'usuario_id' => (int) $denuncia->d_user,
						'motivo' => (int) $d_type + 1,
						'comentario' => $denuncia->d_extra,
						'fecha' => date(BD_DATETIME, (int) $denuncia->d_date),
						'estado' => Model_Post_Denuncia::ESTADO_PENDIENTE
					);

					// Inserto la denuncia.
					$this->marifa_db->insert('INSERT INTO usuario_denuncia (denunciado_id, usuario_id, motivo, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)', array_values($denuncia_data));
					break;
				case 4: // Fotos

					// Listado de datos a usar.
					$denuncia_data = array(
						'foto_id' => (int) $denuncia->obj_id,
						'usuario_id' => (int) $denuncia->d_user,
						'motivo' => (int) $d_type + 1,
						'comentario' => $denuncia->d_extra,
						'fecha' => date(BD_DATETIME, (int) $denuncia->d_date),
						'estado' => Model_Post_Denuncia::ESTADO_PENDIENTE
					);

					// Inserto la denuncia.
					$this->marifa_db->insert('INSERT INTO foto_denuncia (foto_id, usuario_id, motivo, comentario, fecha, estado) VALUES (?, ?, ?, ?, ?, ?)', array_values($denuncia_data));
					break;
			}
		}
		unset($denuncias, $denuncia);
	}

	/**
	 * Importamos información del muro de los usuarios.
	 */
	protected function import_muro()
	{
		// Obtengo listado de publicaciones.
		$publicaciones = $this->importador_db->query('SELECT pub_id, p_user, p_user_pub, p_date, p_body, p_likes, p_type, p_ip FROM u_muro')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos las publicaciones.
		foreach ($publicaciones as $publicacion)
		{
			switch ( (int) $publicacion->p_type)
			{
				case 1: // Publicación.
					$this->marifa_db->insert('INSERT INTO shout (id, usuario_id, mensaje, tipo, valor, fecha) VALUES (?, ?, ?, ?, ?, ?)', array( (int) $publicacion->pub_id, (int) $publicacion->p_user_pub, $publicacion->p_body), Model_Shout::TIPO_TEXTO, NULL, date(BD_DATETIME, (int) $publicacion->p_date));
					break;
				case 2: // Foto.
					// Obtengo datos de la foto.
					$url = $this->importador_db->query('SELECT a_url FROM u_muro_adjuntos WHERE pub_id = ?', (int) $publicacion->pub_id)->get_var();

					$this->marifa_db->insert('INSERT INTO shout (id, usuario_id, mensaje, tipo, valor, fecha) VALUES (?, ?, ?, ?, ?, ?)', array( (int) $publicacion->pub_id, (int) $publicacion->p_user_pub, $publicacion->p_body, Model_Shout::TIPO_IMAGEN, $url, date(BD_DATETIME, (int) $publicacion->p_date)));
					break;
				case 3: // Enlace.
					// Obtengo datos del enlace.
					list($url, $title) = $this->importador_db->query('SELECT a_title, a_url FROM u_muro_adjuntos WHERE pub_id = ?', (int) $publicacion->pub_id)->get_record(Database_Query::FETCH_NUM);

					$this->marifa_db->insert('INSERT INTO shout (id, usuario_id, mensaje, tipo, valor, fecha) VALUES (?, ?, ?, ?, ?, ?)', array( (int) $publicacion->pub_id, (int) $publicacion->p_user_pub, $publicacion->p_body, Model_Shout::TIPO_ENLACE, serialize(array($url, $title)), date(BD_DATETIME, (int) $publicacion->p_date)));
					break;
				case 4: // Video.
					// Obtengo datos del video.
					$video = $this->importador_db->query('SELECT a_url FROM u_muro_adjuntos WHERE pub_id = ?', (int) $publicacion->pub_id)->get_var();

					$this->marifa_db->insert('INSERT INTO shout (id, usuario_id, mensaje, tipo, valor, fecha) VALUES (?, ?, ?, ?, ?, ?)', array( (int) $publicacion->pub_id, (int) $publicacion->p_user_pub, $publicacion->p_body, Model_Shout::TIPO_VIDEO, 'youtube:'.$video, date(BD_DATETIME, (int) $publicacion->p_date)));
					break;
			}
		}
		unset($publicaciones, $publicacion);

		// Obtengo listado de comentarios.
		$comentarios = $this->importador_db->query('SELECT cid, pub_id, c_user, c_date, c_body FROM u_muro_comentarios')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importamos los comentarios.
		foreach ($comentarios as $comentario)
		{
			$comentario_data = array(
				'id' => (int) $comentario->cid,
				'usuario_id' => (int) $comentario->c_user,
				'shout_id' => (int) $comentario->pub_id,
				'comentario' => $comentario->c_body,
				'fecha' => date(BD_DATETIME, (int) $comentario->c_date)
			);
			$this->marifa_db->update('INSERT INTO shout_comentario (id, usuario_id, shout_id, comentario, fecha) VALUES (?, ?, ?, ?, ?)', array_values($comentario_data));
		}
		unset($comentarios, $comentario, $comentario_data);

		// Obtengo listado de likes.
		$likes = $this->importador_db->query('SELECT user_id, obj_id FROM u_muro_likes WHERE obj_type = 1')->set_fetch_type(Database_Query::FETCH_OBJ);

		// Importar me-gusta.
		foreach ($likes as $like)
		{
			$this->marifa_db->update('INSERT INTO shout_voto (usuario_id, shout_id) VALUES (?, ?)', array( (int) $like->user_id, (int) $like->obj_id));
		}
		unset($likes, $like);
	}

	/**
	 * Agregamos un nuevo suceso para un usuario.
	 * @param int|array $usuario_id ID del usuario dueño del suceso o arreglo
	 * de dueños. De esta forma todos los usuarios involucrados tiene el mismo
	 * suceso.
	 * @param string $tipo Tipo de suceso.
	 * @param bool $notificar Si el suceso va a la barra de notificaciones o no.
	 * @param int $fecha Timestamp con la fecha del suceso.
	 * @param int $objeto_id ID del objeto del suceso.
	 * @param int $objeto_id2 ID secundario del objeto del suceso.
	 * @param int $objeto_id3 ID terciario del objeto del suceso.
	 */
	private function crear_suceso($usuario_id, $tipo, $notificar, $fecha, $objeto_id, $objeto_id2 = NULL, $objeto_id3 = NULL)
	{
		if (is_array($usuario_id))
		{
			// Eliminamos repetidos.
			$usuario_id = array_unique($usuario_id, SORT_NUMERIC);

			// Ejecutamos las consultas.
			$rst = array();
			foreach ($usuario_id as $id)
			{
				$rst[] = $this->crear_suceso($id, $tipo, $notificar, $fecha, $objeto_id, $objeto_id2 = NULL, $objeto_id3 = NULL);
			}
			return $rst;
		}
		else
		{
			$datos = array(
				'usuario_id' => $usuario_id,
				'objeto_id' => $objeto_id,
				'objeto_id1' => $objeto_id2,
				'objeto_id2' => $objeto_id3,
				'tipo' => $tipo,
				'notificar' => $notificar,
				'visto' => TRUE,
				'desplegado' => TRUE,
				'fecha' => date('Y/m/d H:i:s', $fecha)
			);

			return $this->marifa_db->insert('INSERT INTO suceso (usuario_id, objeto_id, objeto_id1, objeto_id2, tipo, notificar, visto, desplegado, fecha) VALUES (:usuario_id, :objeto_id, :objeto_id1, :objeto_id2, :tipo, :notificar, :visto, :desplegado, :fecha)', $datos);
		}
	}
}