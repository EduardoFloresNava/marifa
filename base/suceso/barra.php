<?php
/**
 * barra.php is part of Marifa.
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
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para el parseo de los sucesos de la barra de usuario.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Suceso_Barra extends Suceso {

	/**
	 * Obtenemos el listado de sucesos a procesar.
	 * @param int $usuario ID del usuario dueño de los sucesos.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @param string $class Clase para procesar. No debe ser pasado, solo es a fines de compatibilidad de herencias estáticas.
	 * @return array
	 */
	public static function obtener_listado($usuario, $pagina, $cantidad = 20, $class = __CLASS__)
	{
		// Obtenemos la lista de sucesos que puede procesar.
		$rc = new ReflectionClass(substr($class, 5));
		$ms = $rc->getMethods(ReflectionMethod::IS_STATIC);

		$methods = array();
		foreach ($ms as $method)
		{
			if (substr($method->name, 0, 7) == 'suceso_')
			{
				$methods[] = substr($method->name, 7);
			}
		}
		unset($rc, $ms);

		// Cargamos el listado de sucesos.
		$model_suceso = new Model_Suceso;
		return $model_suceso->listado($usuario, $pagina, $cantidad, $methods, TRUE, FALSE);
	}

	/**
	 * Obtenemos el listado de sucesos a procesar incluyendo los vistos.
	 * @param int $usuario ID del usuario dueño de los sucesos.
	 * @param int $pagina Número de página a mostrar.
	 * @param int $cantidad Cantidad de elementos por página.
	 * @return array
	 */
	public static function obtener_listado_completo($usuario, $pagina, $cantidad = 20)
	{
		$class = __CLASS__;

		// Obtenemos la lista de sucesos que puede procesar.
		$rc = new ReflectionClass(substr($class, 5));
		$ms = $rc->getMethods(ReflectionMethod::IS_STATIC);

		$methods = array();
		foreach ($ms as $method)
		{
			if (substr($method->name, 0, 7) == 'suceso_')
			{
				$methods[] = substr($method->name, 7);
			}
		}
		unset($rc, $ms);

		// Cargamos el listado de sucesos.
		$model_suceso = new Model_Suceso;
		return $model_suceso->listado($usuario, $pagina, $cantidad, $methods, TRUE);
	}

	/**
	 * Obtenemos los datos para visualizar un suceso.
	 * @param array|Model_Suceso $informacion Información de un suceso.
	 * @param string $class Clase para procesar. No debe ser pasado, solo es a fines de compatibilidad de herencias estáticas.
	 * @return array
	 */
	public static function procesar($informacion, $class = __CLASS__)
	{
		return parent::procesar($informacion, $class);
	}

	/**
	 * Obtenemos la cantidad de sucesos que hay disponibles.
	 * @param int $usuario ID del usuario dueño de los posts.
	 * @param string $class Nombre de la clase. No debe ser pasado, solo es a fines de compatibilidad de herencias estáticas.
	 */
	public static function cantidad($usuario, $class = __CLASS__)
	{
		// Obtenemos la lista de sucesos que puede procesar.
		$rc = new ReflectionClass(substr($class, 5));
		$ms = $rc->getMethods(ReflectionMethod::IS_STATIC);

		$methods = array();
		foreach ($ms as $method)
		{
			if (substr($method->name, 0, 7) == 'suceso_')
			{
				$methods[] = substr($method->name, 7);
			}
		}
		unset($rc, $ms);

		// Obtenemos la cantidad.
		$model_suceso = new Model_Suceso;
		return $model_suceso->cantidad($usuario, $methods, TRUE, FALSE);
	}

	/**
	 * Obtenemos la cantidad de sucesos que hay disponibles incluyendo los vistos.
	 * @param int $usuario ID del usuario dueño de los posts.
	 */
	public static function cantidad_completa($usuario)
	{
		$class = __CLASS__;
		// Obtenemos la lista de sucesos que puede procesar.
		$rc = new ReflectionClass(substr($class, 5));
		$ms = $rc->getMethods(ReflectionMethod::IS_STATIC);

		$methods = array();
		foreach ($ms as $method)
		{
			if (substr($method->name, 0, 7) == 'suceso_')
			{
				$methods[] = substr($method->name, 7);
			}
		}
		unset($rc, $ms);

		// Obtenemos la cantidad.
		$model_suceso = new Model_Suceso;
		return $model_suceso->cantidad($usuario, $methods, TRUE);
	}

	/**
	 * Suceso producido cuando se edita un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_editado($suceso)
	{
		// Verifico si yo edité el post.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo editor.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'editor' => $model_usuario->as_array());
	}

	/**
	 * Un usuario ha agregado un post como favorito.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_favorito($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien lo agregó como favorito.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'favorito' => $model_usuario->as_array());
	}

	/**
	 * Un usuario comienza a seguir un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_seguir($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien lo sigue.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'seguidor' => $model_usuario->as_array());
	}

	/**
	 * Un usuario da puntos a un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_puntuar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien da los puntos.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'puntua' => $model_usuario->as_array(), 'puntos' => (int) $suceso['objeto_id2']);
	}

	/**
	 * Un usuario fija un post en la portada.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_fijar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien fija el post.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'fija' => $model_usuario->as_array(), 'tipo' => (bool) $suceso['objeto_id2']);
	}

	/**
	 * Un usuario patrocina un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_patrocinar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien patrocina el post.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'patrocina' => $model_usuario->as_array(), 'tipo' => (bool) $suceso['objeto_id2']);
	}

/**
 * * **post_ocultar** (*post_id*, *usuario_id*, *tipo*): Ocultamos/mostramos un post.
 * * **post_id**: ID del post a ocultar/mostrar.
 * * **usuario_id**: ID del usuario que realiza la acción de ocultar/mostrar el post.
 * * **tipo**: Tipo de acción. 0 ocultar, 1 mostrar.
 *
 * **post_aprobar** (*post_id*, *usuario_id*, *tipo*): Aprobamos/Rechazamos un post.
 * * **post_id**: ID del post a aprobar/rechazar.
 * * **usuario_id**: ID del usuario que realiza la acción de aprobar/rechazar el post.
 * * **tipo**: Tipo de acción. 0 rechazar, 1 aprobar.
 *
 * **post_borrar** (*post_id*, *usuario_id*): Eliminamos un post.
 * * **post_id**: ID del post a eliminar.
 * * **usuario_id**: ID del usuario que elimina el post.
 *
 * **post_papelera** (*post_id*, *usuario_id*): Enviamos un post a la papelera de posts.
 * * **post_id**: ID del post que se envia a la papelera.
 * * **usuario_id**: ID del usuario que envia el post a la papelera.
 *
 * **post_restaurar** (*post_id*, *usuario_id*): Restauramos un post que se encuentra en la papelera.
 * * **post_id**: ID del post a restaurar.
 * * **usuario_id**: ID del usuario que restaura el post.
 */

	/**
	 * Suceso producido cuando se publica un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_publicar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien publica el post.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'publica' => $model_usuario->as_array());
	}

/**
 *
 * **post_denuncia_crear** (*denuncia_id*): Se denuncia un post. Implica la creación que luego debe ser verificada por un moderador/administrador para tomar una acción.
 * * **denuncia_id**: ID de la denuncia creada.

 * **post_denuncia_aceptar** (*denuncia_id*, *usuario_id*): Aceptamos una denuncia.
 * * **denuncia_id**: ID de la denuncia a aceptar.
 * * **usuario_id**: ID del usuario que acepta la denuncia.

 * **post_denuncia_rechazar** (*denuncia_id*, *usuario_id*): Rechazamos una denuncia.
 * * **denuncia_id**: ID de la denuncia a rechazar.
 * * **usuario_id**: ID del usuario que rechaza la denuncia.
 *
 */

	/**
	 * Suceso producido cuando se publica un comentario en un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_comentario_crear($suceso)
	{
		// Cargo el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);

		// Verifico no sea yo.
		if (Usuario::$usuario_id == $model_comentario->usuario_id)
		{
			return NULL;
		}

		// Post donde se crea el comentario.
		$model_post = $model_comentario->post();

		// Usuario que crea el post.
		$model_usuario = $model_comentario->usuario();

		return array('comentario' => $model_comentario->as_array(), 'post' => $model_post->as_array(), 'post_usuario' => $model_post->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se vota el comentario de un usuario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_comentario_voto($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);

		// Cargo el usuario que vota.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		// Cargo el voto.
		$voto = (bool) $suceso['objeto_id2'];

		return array('post' => $model_comentario->post()->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array(), 'usuario' => $model_usuario->as_array(), 'voto' => $voto);
	}

/**
 *  * **post_comentario_ocultar** (*comentario_id*, *usuario_id*): El moderador/administrador oculta el comentario de un post.
 * * **comentario_id**: ID del comentario a ocultar.
 * * **usuario_id**: ID del usuario que realiza la acción de ocultar. Puede ser un moderador/administrador.

 * **post_comentario_mostrar** (*comentario_id*, *usuario_id*): El moderador/administrador muestra el comentario de un post.
 * * **comentario_id**: ID del comentario a mostrar.
 * * **usuario_id**: ID del usuario que realiza la acción de mostrar. Puede ser un moderador/administrador.

 * **post_comentario_borrar** (*comentario_id*, *usuario_id*): Se elimina un comentario.
 * * **comentario_id**: ID del comentario a eliminar.
 * * **usuario_id**: ID del usuario que elimina el comentario.
 */

	/**
	 * Suceso producido cuando se edita el comentario de un usuario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_comentario_editar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);

		// Cargo el usuario que edita.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_comentario->post()->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se vota una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_votar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo la foto.
		$model_foto = new Model_Foto( (int) $suceso['objeto_id']);

		// Cargo quien vota.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		// Tipo de voto.
		$voto = (bool) $suceso['objeto_id2'];

		return array('foto' => $model_foto->as_array(), 'foto_usuario' => $model_foto->usuario()->as_array(), 'usuario' => $model_usuario->as_array(), 'voto' => $voto);
	}

	/**
	 * Suceso producido cuando se agrega a favoritos una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_favorito($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo la foto.
		$model_foto = new Model_Foto( (int) $suceso['objeto_id']);

		// Cargo quien agrega a favoritos.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('foto' => $model_foto->as_array(), 'foto_usuario' => $model_foto->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se agrega a favoritos una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_editar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo la foto.
		$model_foto = new Model_Foto( (int) $suceso['objeto_id']);

		// Cargo quien edita la foto.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('foto' => $model_foto->as_array(), 'usuario' => $model_foto->usuario()->as_array(), 'editor' => $model_usuario->as_array());
	}

/**
 * * **foto_ocultar** (*foto_id*, *usuario_id*, *tipo*): Ocultamos/Mostramos una foto.
 * * **foto_id**: ID de la foto a ocultar/mostrar.
 * * **usuario_id**: ID del usuario que oculta/muestra la foto.
 * * **tipo**: Tipo de modificación. 0 oculta, 1 muestra.

 * **foto_borrar** (*foto_id*, *usuario_id*): Eliminamos una foto.
 * * **foto_id**: ID de la foto a eliminar.
 * * **usuario_id**: ID del usuario que elimina la foto.

 * **foto_papelera** (*foto_id*, *usuario_id*): Enviamos una foto a la papelera de fotos.
 * * **foto_id**: ID de la foto que se envia a la papelera.
 * * **usuario_id**: ID del usuario que envia la foto a la papelera.

 * **foto_restaurar** (*foto_id*, *usuario_id*): Restauramos una foto que se encuentra en la papelera.
 * * **foto_id**: ID de la foto a restaurar.
 * * **usuario_id**: ID del usuario que restaura la foto.
 */

/**
 * * **foto_denuncia_crear** (*denuncia_id*): Se denuncia una foto. Implica la creación que luego debe ser verificada por un moderador/administrador para tomar una acción.
 * * **denuncia_id**: ID de la denuncia creada.

 * **foto_denuncia_aceptar** (*denuncia_id*, *usuario_id*): Aceptamos una denuncia.
 * * **denuncia_id**: ID de la denuncia a aceptar.
 * * **usuario_id**: ID del usuario que acepta la denuncia.

 * **foto_denuncia_rechazar** (*denuncia_id*, *usuario_id*): Rechazamos una denuncia.
 * * **denuncia_id**: ID de la denuncia a rechazar.
 * * **usuario_id**: ID del usuario que rechaza la denuncia.
 */

	/**
	 * Suceso producido cuando se publica un comentario en una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_comentario_crear($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo el comentario.
		$model_comentario = new Model_Foto_Comentario( (int) $suceso['objeto_id']);

		// Foto en donde se crea el comentario.
		$model_foto = $model_comentario->foto();

		// Usuario que crea el foto.
		$model_usuario = $model_comentario->usuario();

		return array('comentario' => $model_comentario->as_array(), 'foto' => $model_foto->as_array(), 'foto_usuario' => $model_foto->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

/**
 * * **foto_comentario_ocultar** (*comentario_id*, *usuario_id*): El moderador/administrador oculta el comentario de  una foto.
 * * **comentario_id**: ID del comentario a ocultar.
 * * **usuario_id**: ID del usuario que realiza la acción de ocultar. Puede ser un moderador/administrador.

 * **foto_comentario_mostrar** (*comentario_id*, *usuario_id*): El moderador/administrador muestra el comentario de  una foto.
 * * **comentario_id**: ID del comentario a mostrar.
 * * **usuario_id**: ID del usuario que realiza la acción de mostrar. Puede ser un moderador/administrador.

 * **foto_comentario_borrar** (*comentario_id*, *usuario_id*): Se elimina un comentario.
 * * **comentario_id**: ID del comentario a eliminar.
 * * **usuario_id**: ID del usuario que elimina el comentario.
 */

	/**
	 * Suceso producido cuando se edita el comentario de un usuario en una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_comentario_editar($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id1'] == Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo el comentario.
		$model_comentario = new Model_Foto_Comentario( (int) $suceso['objeto_id']);

		// Cargo el usuario que edita.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('foto' => $model_comentario->foto()->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

/**
 * * **usuario_bloqueo** (*usuario_id*, *bloqueado_id*, *tipo*): El usuario *usuario_id* bloquea/desbloquea al usuario *bloqueado_id* para acceder a su perfil y eventos.
 * * **usuario_id**: ID del usuario que bloquea/desbloquea al otro.
 * * **bloquedo_id**: ID del usuario que es bloqueado/desbloqueado.
 * * **tipo**: Tipo de acción: 0 bloquea, 1 desbloquea.
 */

	/**
	 * Suceso producido cuando el usuario cambia de rango.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_cambio_rango($suceso)
	{
		// Verifico si fui yo.
		if ($suceso['objeto_id'] != Usuario::$usuario_id)
		{
			return NULL;
		}

		// Cargo datos del usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id']);

		// Cargo el rango nuevo.
		$model_rango = new Model_Usuario_Rango( (int) $suceso['objeto_id1']);

		// Cargo el moderador.
		$model_moderador = new Model_Usuario( (int) $suceso['objeto_id2']);

		return array('usuario' => $model_usuario->as_array(), 'rango' => $model_rango->as_array(), 'moderador' => $model_moderador->as_array());
	}

/**
 * * **usuario_suspender** (*suspension_id*): El usuario es suspendido.
 * * **suspension_id**: ID de la suspensión.

 * **usuario_fin_suspension** (*usuario_id*, [*moderador_id*]): Suspensión del usuario finalizada. Puede ser de forma automática (terminada la suspensión) o por acción de un usuario.
 * * **usuario_id**: ID del usuario del que se quitó(o finalizo) la suspensión.
 * * **moderador_id**: ID del moderador que termina la suspensión. En caso de ser NULO es porque finalizó el periodo de forma automática.

 * **usuario_baneo** (*baneo_id*): Un usuario es baneado.
 * * **baneo_id**: ID del baneo al usuario.

 * **usuario_fin_baneo** (*usuario_id*, *moderador_id*): Se cancela el baneo del usuario.
 * * **usuario_id:**: ID del usuario al que se le quita el baneo.
 * * **moderador_id**: ID del usuario (moderador o administrador) que ha quitado el baneo al usuario.

 * **usuario_advertir** (*advertencia_id*): Se envía una advertencia al usuario.
 * * **advertencia_id*: ID de la advertencia que se ha creado.
 */

	/**
	 * Suceso producido cuando el usuario cambia de rango.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_seguir($suceso)
	{
		// Cargo datos del usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id']);

		// Cargo datos del seguidor.
		$model_seguidor = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('usuario' => $model_usuario->as_array(), 'seguidor' => $model_seguidor->as_array());
	}

/**
 * * **usuario_denuncia_crear** (*denuncia_id*): Se denuncia un usuario. Implica la creación que luego debe ser verificada por un moderador/administrador para tomar una acción.
 * * **denuncia_id**: ID de la denuncia creada.

 * **usuario_denuncia_aceptar** (*denuncia_id*, *usuario_id*): Aceptamos una denuncia.
 * * **denuncia_id**: ID de la denuncia a aceptar.
 * * **usuario_id**: ID del usuario que acepta la denuncia.

 * **usuario_denuncia_rechazar** (*denuncia_id*, *usuario_id*): Rechazamos una denuncia.
 * * **denuncia_id**: ID de la denuncia a rechazar.
 * * **usuario_id**: ID del usuario que rechaza la denuncia.
 */

}