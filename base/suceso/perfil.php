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
		if ($usuario == Usuario::$usuario_id)
		{
			// Genero listado de usuarios a partir del actual y de quien sigue.
			$usuarios = Database::get_instance()->query('SELECT usuario_id FROM usuario_seguidor WHERE seguidor_id = ?', $usuario)->get_pairs(Database_Query::FIELD_INT);
			$usuarios[] = $usuario;
		}
		else
		{
			$usuarios = $usuario;
		}

		return parent::obtener_listado($usuarios, $pagina, $cantidad, $class);
	}

	/**
	 * Obtenemos la cantidad de sucesos que hay disponibles.
	 * @param int $usuario ID del usuario dueño de los posts.
	 * @param string $class Nombre de la clase. No debe ser pasado, solo es a fines de compatibilidad de herencias estáticas.
	 */
	public static function cantidad($usuario, $class = __CLASS__)
	{
		if ($usuario == Usuario::$usuario_id)
		{
			// Genero listado de usuarios a partir del actual y de quien sigue.
			$usuarios = Database::get_instance()->query('SELECT usuario_id FROM usuario_seguidor WHERE seguidor_id = ?', $usuario)->get_pairs(Database_Query::FIELD_INT);
			$usuarios[] = $usuario;
		}
		else
		{
			$usuarios = $usuario;
		}

		return parent::cantidad($usuarios, $class);
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
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array());
	}

	/**
	 * Suceso producido cuando se edita un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_editado($suceso)
	{
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
		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien patrocina el post.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'patrocina' => $model_usuario->as_array(), 'tipo' => (bool) $suceso['objeto_id2']);
	}

	/**
	 * Suceso producido cuando se publica un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_publicar($suceso)
	{
		// Cargo datos del post.
		$model_post = new Model_Post( (int) $suceso['objeto_id']);

		// Cargo datos de quien publica el post.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_post->as_array(), 'usuario' => $model_post->usuario()->as_array(), 'publica' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se publica un comentario en un post.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_comentario_crear($suceso)
	{
		// Cargo el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);

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
		// Cargo el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);

		// Cargo el usuario que vota.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		// Cargo el voto.
		$voto = (bool) $suceso['objeto_id2'];

		return array('post' => $model_comentario->post()->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array(), 'usuario' => $model_usuario->as_array(), 'voto' => $voto);
	}

	/**
	 * Suceso producido cuando se edita el comentario de un usuario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_post_comentario_editar($suceso)
	{
		// Cargo el comentario.
		$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);

		// Cargo el usuario que edita.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('post' => $model_comentario->post()->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se crea una nueva foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_nueva($suceso)
	{
		// Cargo la foto.
		$model_foto = new Model_Foto( (int) $suceso['objeto_id']);

		return array('foto' => $model_foto->as_array(), 'usuario' => $model_foto->usuario()->as_array());
	}

	/**
	 * Suceso producido cuando se vota una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_votar($suceso)
	{
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
		// Cargo la foto.
		$model_foto = new Model_Foto( (int) $suceso['objeto_id']);

		// Cargo quien edita la foto.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('foto' => $model_foto->as_array(), 'usuario' => $model_foto->usuario()->as_array(), 'editor' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se publica un comentario en una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_comentario_crear($suceso)
	{
		// Cargo el comentario.
		$model_comentario = new Model_Foto_Comentario( (int) $suceso['objeto_id']);

		// Foto en donde se crea el comentario.
		$model_foto = $model_comentario->foto();

		// Usuario que crea el foto.
		$model_usuario = $model_comentario->usuario();

		return array('comentario' => $model_comentario->as_array(), 'foto' => $model_foto->as_array(), 'foto_usuario' => $model_foto->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se edita el comentario de un usuario en una foto.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_foto_comentario_editar($suceso)
	{
		// Cargo el comentario.
		$model_comentario = new Model_Foto_Comentario( (int) $suceso['objeto_id']);

		// Cargo el usuario que edita.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('foto' => $model_comentario->foto()->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array(), 'usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando se crea un usuario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_nuevo($suceso)
	{
		// Cargo datos del usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id']);

		return array('usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando el usuario cambia su nick.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_cambio_nick($suceso)
	{
		// Cargo datos del usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id']);

		//TODO: Cargar los nicks.

		return array('usuario' => $model_usuario->as_array());
	}

	/**
	 * Suceso producido cuando el usuario cambia de rango.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_cambio_rango($suceso)
	{
		// Cargo datos del usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id']);

		// Cargo el rango nuevo.
		$model_rango = new Model_Usuario_Rango( (int) $suceso['objeto_id1']);

		// Cargo el moderador.
		if ($suceso['objeto_id2'] !== NULL)
		{
			$model_moderador = new Model_Usuario( (int) $suceso['objeto_id2']);
			$moderador = $model_moderador->as_array();
			unset($model_moderador);
		}
		else
		{
			$moderador = NULL;
		}

		return array('usuario' => $model_usuario->as_array(), 'rango' => $model_rango->as_array(), 'moderador' => $moderador);
	}

	/**
	 * Suceso producido cuando el usuario sigue a otro.
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
	 * Suceso producido cuando el usuario deja de seguir a otro.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_fin_seguir($suceso)
	{
		// Cargo datos del usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id']);

		// Cargo datos del seguidor.
		$model_seguidor = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('usuario' => $model_usuario->as_array(), 'seguidor' => $model_seguidor->as_array());
	}

	/**
	 * Suceso producido cuando un usuario gana una medalla.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_nueva_medalla($suceso)
	{
		// Cargo usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['usuario_id']);

		// Cargo medalla.
		$model_medalla = new Model_Medalla( (int) $suceso['objeto_id']);

		return array('usuario' => $model_usuario->as_array(), 'medalla' => $model_medalla->as_array());
	}

	/**
	 * Suceso producido cuando se realiza una cita de un comentario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_comentario_citado($suceso)
	{
		// Cargo usuario.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		// Cargo comentario.
		if ($suceso['objeto_id2'] == 1)
		{
			$model_comentario = new Model_Post_Comentario( (int) $suceso['objeto_id']);
		}
		else
		{
			$model_comentario = new Model_Foto_Comentario( (int) $suceso['objeto_id']);
		}

		return array('usuario' => $model_usuario->as_array(), 'comentario' => $model_comentario->as_array(), 'comentario_usuario' => $model_comentario->usuario()->as_array());
	}

	/**
	 * Suceso producido por un shout creado por un usuario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_shout($suceso)
	{
		// Cargo shout.
		$model_shout = new Model_Shout( (int) $suceso['objeto_id']);
		$shout = $model_shout->as_array();

		// Proceso BBCode.
		$decoda = new Decoda($shout['mensaje']);
		$decoda->addFilter(new TagFilter());
		$decoda->addFilter(new UserFilter());
		$shout['mensaje_bbcode'] = $decoda->parse(FALSE);

		// Proceso valor si es tipo especial.
		if ($model_shout->tipo == Model_Shout::TIPO_VIDEO)
		{
			// Obtengo clase de video.
			$shout['valor'] = explode(':', $model_shout->valor);
		}
		elseif($model_shout->tipo == Model_Shout::TIPO_ENLACE)
		{
			$shout['valor'] = unserialize($shout['valor']);
		}

		// Campos extra.
		$shout['usuario'] = $model_shout->usuario()->as_array();
		$shout['votos'] = $model_shout->cantidad_votos();
		$shout['comentario'] = $model_shout->cantidad_comentarios();
		$shout['favoritos'] = $model_shout->cantidad_favoritos();
		$shout['compartido'] = $model_shout->cantidad_compartido();

		// Cargo usuario a quien se publica.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('usuario' => $model_usuario->as_array(), 'shout' => $shout);
	}

	/**
	 * Suceso producido por un shout compartido por un usuario.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	protected static function suceso_usuario_shout_compartir($suceso)
	{
		// Cargo shout.
		$model_shout = new Model_Shout( (int) $suceso['objeto_id']);
		$shout = $model_shout->as_array();

		// Campos extra.
		$shout['usuario'] = $model_shout->usuario()->as_array();
		$shout['votos'] = $model_shout->cantidad_votos();
		$shout['comentario'] = $model_shout->cantidad_comentarios();
		$shout['favoritos'] = $model_shout->cantidad_favoritos();
		$shout['compartido'] = $model_shout->cantidad_compartido();

		// Cargo datos de quien comparte.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		// Cargo usuario a través del cual se comparte.
		$model_usuario_comparte = new Model_Usuario( (int) $suceso['objeto_id2']);

		return array('usuario' => $model_usuario->as_array(), 'usuario_comparte' => $model_usuario_comparte->as_array(), 'shout' => $shout);
	}

	/**
	 * Suceso producido por un shout agregado/quitado de los favoritos.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	public static function suceso_usuario_shout_favorito($suceso)
	{
		// Cargo shout.
		$model_shout = new Model_Shout( (int) $suceso['objeto_id']);
		$shout = $model_shout->as_array();

		// Información extendida del shout.
		$shout['usuario'] = $model_shout->usuario()->as_array();

		// Cargo quien agregó a favoritos el shout.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('usuario' => $model_usuario->as_array(), 'agregar' => (bool) $suceso['objeto_id2'], 'shout' => $shout);
	}

	/**
	 * Suceso producido por un shout votado.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	public static function suceso_usuario_shout_voto($suceso)
	{
		// Cargo shout.
		$model_shout = new Model_Shout( (int) $suceso['objeto_id']);
		$shout = $model_shout->as_array();

		// Información extendida del shout.
		$shout['usuario'] = $model_shout->usuario()->as_array();

		// Cargo quien votó el shout.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('usuario' => $model_usuario->as_array(), 'voto' => (bool) $suceso['objeto_id2'], 'shout' => $shout);
	}

	/**
	 * Suceso producido por un comentario en un shout.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	public static function suceso_usuario_shout_comentario($suceso)
	{
		// Cargo shout.
		$model_shout = new Model_Shout( (int) $suceso['objeto_id']);
		$shout = $model_shout->as_array();

		// Información extendida del shout.
		$shout['usuario'] = $model_shout->usuario()->as_array();

		// Cargo quien comentó el shout.
		$model_usuario = new Model_Usuario( (int) $suceso['objeto_id1']);

		return array('usuario' => $model_usuario->as_array(), 'comentario_id' => (int) $suceso['objeto_id2'], 'shout' => $shout);
	}

	/**
	 * Suceso producido cuando se cita a un usuario en un shout.
	 * @param array $suceso Datos del suceso.
	 * @return array
	 */
	public static function suceso_usuario_shout_cita($suceso)
	{
		// Cargo shout.
		$model_shout = new Model_Shout( (int) $suceso['objeto_id']);
		$shout = $model_shout->as_array();

		// Información extendida del shout.
		$shout['usuario'] = $model_shout->usuario()->as_array();

		return array('shout' => $shout);
	}
}
