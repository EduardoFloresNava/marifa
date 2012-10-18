<?php
/**
 * perfil.php is part of Marifa.
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
 * Clase para el parseo de los sucesos del perfil del usuario.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Suceso_Perfil extends Suceso {

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
		return parent::obtener_listado($usuario, $pagina, $cantidad, $class);
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
	 * Suceso producido cuando se crea un nuevo post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_nuevo($suceso)
	{
		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['object_id']);
		$rst = $model_post->as_array();
		$rst['usuario'] = $model_post->usuario()->as_array();

		return $rst;
	}

	/**
	 * Suceso producido cuando se agrega a favoritos un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_seguir($suceso)
	{
		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['object_id']);

		$rst = array();
		$rst['post'] = $model_post->as_array();
		$rst['post']['usuario'] = $model_post->usuario()->as_array();
		unset($model_post);

		// Cargo datos del usuario que agregó el post a favoritos.
		$model_usuario = new Model_Usuario( (int) $suceso['object_id1']);
		$rst['usuario'] = $model_usuario->as_array();

		return $rst;
	}


/**
 * **post_seguir** (*post_id*, *usuario_id*): El usuario comienza a seguir un post.
 * * **post_id**: ID del post que se está por seguir.
 * * **usuario_id**: ID del usuario que comienza a seguir el post.

 * **post_puntuar** (*post_id*, *usuario_id*, *puntos*): Un usuario da puntos a un post.
 * * **post_id**: ID del post al cual se le dan puntos.
 * * **usuario_id**: ID del usuario que da los puntos.
 * * **puntos**: Cantidad de puntos que dio el usuario. Estos puntos se pueden calcular directamente de la entrada pero se pasan de igual forma.

 * **post_borrar** (*post_id*, *usuario_id*): Eliminamos un post.
 * * **post_id**: ID del post a eliminar.
 * * **usuario_id**: ID del usuario que elimina el post.

 * **post_papelera** (*post_id*, *usuario_id*): Enviamos un post a la papelera de posts.
 * * **post_id**: ID del post que se envia a la papelera.
 * * **usuario_id**: ID del usuario que envia el post a la papelera.

 * **post_restaurar** (*post_id*, *usuario_id*): Restauramos un post que se encuentra en la papelera.
 * * **post_id**: ID del post a restaurar.
 * * **usuario_id**: ID del usuario que restaura el post.

 * **post_publicar** (*post_id*, *usuario_id*): Publicamos un post que se encuentra pendiente o como borrador.
 * * **post_id**: ID del post a publicar.
 * * **usuario_id**: ID del usuario que publica el post.

### Comentarios en Posts:
 * **post_comentario_crear** (*comentario_id*): Creamos un comentario en un post.
 * * **comentario_id**: ID del comentario que se crea.

 * **post_comentario_voto** (*comentario_id*, *usuario_id*, *voto*): Votamos el comentario de un post.
 * * **comentario_id**: ID del comentario a votar.
 * * **usuario_id**: ID del usuario que realiza la votación.
 * * **voto**: Si el voto es positivo o negativo. 0 voto negativo, 1 voto positivo.

 * **post_comentario_ocultar** (*comentario_id*, *usuario_id*): El moderador/administrador oculta el comentario de un post.
 * * **comentario_id**: ID del comentario a ocultar.
 * * **usuario_id**: ID del usuario que realiza la acción de ocultar. Puede ser un moderador/administrador.

 * **post_comentario_mostrar** (*comentario_id*, *usuario_id*): El moderador/administrador muestra el comentario de un post.
 * * **comentario_id**: ID del comentario a mostrar.
 * * **usuario_id**: ID del usuario que realiza la acción de mostrar. Puede ser un moderador/administrador.

 * **post_comentario_borrar** (*comentario_id*, *usuario_id*): Se elimina un comentario.
 * * **comentario_id**: ID del comentario a eliminar.
 * * **usuario_id**: ID del usuario que elimina el comentario.

## Fotos:

 * **foto_votar** (*foto_id*, *usuario_id*, *tipo*): Votamos una foto.
 * * **foto_id**: ID de la foto a votar.
 * * **usuario_id**: ID del usuario que realiza la votación.
 * * **tipo**: Tipo de voto. 0 negativo, 1 positivo.

 * **foto_favorito** (*foto_id*, *usuario_id*): Agregamos una foto a favoritos.
 * * **foto_id**: ID de la foto que se agrega a favoritos.
 * * **usuario_id**: ID del usuario que agrega la foto a favoritos.

 * **foto_nueva** (*foto_id*): Creamos una nueva foto.
 * * **foto_id**: ID de la foto creada.

 * **foto_editar** (*foto_id*, *usuario_id*): Un usuario edita una foto. Puede ser el dueño o un administrador/moderador.
 * * **foto_id**: ID de la foto que fue editada.
 * * **usuario_id**: ID del usuario que edita la foto. Puede ser un administrador/moderador o el creador.

 * **foto_ocultar** (*foto_id*, *usuario_id*, *tipo*): Ocultamos/Mostramos una foto.
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

### Comentarios en Fotos:
 * **foto_comentario_crear** (*comentario_id*): Creamos un comentario en una foto.
 * * **comentario_id**: ID del comentario que se crea.

 * **foto_comentario_voto** (*comentario_id*, *usuario_id*, *voto*): Votamos el comentario de una foto.
 * * **comentario_id**: ID del comentario a votar.
 * * **usuario_id**: ID del usuario que realiza la votación.
 * * **voto**: Si el voto es positivo o negativo. 0 voto negativo, 1 voto positivo.

## Usuarios:
 * **usuario_nuevo** (*usuario_id*): Se crea una cuenta de usuario nueva.
 * * **usuario_id**: ID del usuario que ha sido creado.

 * **usuario_cambio_nick** (*usuario_id*, *nick_inicial_id*, *nick_final_id*): Un usuario cambia su nick.
 * * **usuario_id**: ID del usuario que cambia el nick.
 * * **nick_inicial_id**: ID del nick anterior del usuario.
 * * **nick_final_id**: ID del nick nuevo del usuario.

 * **usuario_suspender** (*suspension_id*): El usuario es suspendido.
 * * **suspension_id**: ID de la suspensión.

 * **usuario_fin_suspension** (*usuario_id*, [*moderador_id*]): Suspensión del usuario finalizada. Puede ser de forma automática (terminada la suspensión) o por acción de un usuario.
 * * **usuario_id**: ID del usuario del que se quitó(o finalizo) la suspensión.
 * * **moderador_id**: ID del moderador que termina la suspensión. En caso de ser NULO es porque finalizó el periodo de forma automática.

 * **usuario_baneo** (*baneo_id*): Un usuario es baneado.
 * * **baneo_id**: ID del baneo al usuario.

 * **usuario_fin_baneo** (*usuario_id*, *moderador_id*): Se cancela el baneo del usuario.
 * * **usuario_id:**: ID del usuario al que se le quita el baneo.
 * * **moderador_id**: ID del usuario (moderador o administrador) que ha quitado el baneo al usuario.
*/

}
