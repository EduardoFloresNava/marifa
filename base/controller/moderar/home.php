<?php
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
 * @package		Marifa\Base
 * @subpackage  Controller\Admin
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador de la portada de la moderación.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller\Moderar
 */
class Base_Controller_Moderar_Home extends Controller {

	/**
	 * Constructor de la clase. Verificamos permisos.
	 */
	public function before()
	{
		// Verifico esté logueado.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, 'Debes iniciar sessión para poder acceder a esta sección.');
			Request::redirect('/usuario/login', TRUE);
		}

		// Verifico si tiene algun permiso.
		if ( ! self::permisos_acceso())
		{
			add_flash_message(FLASH_ERROR, 'No tienes permisos para acceder a esa sección.');
			Request::redirect('/');
		}

		parent::before();
	}

	/**
	 * Verifica si los permisos de acceso son los requeridos para acceder a esta sección.
	 * @return bool
	 */
	public static function permisos_acceso()
	{
		// Verifico si tiene algun permiso.
		$permisos = array(
			Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS,
			Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS,
			Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS,
			Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER,
			Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA,
			Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA,
			Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO,
			Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO
		);

		return Usuario::permiso($permisos);
	}

	/**
	 * Cantidad de elementos que están esperando supervición.
	 * @return int
	 */
	public static function cantidad_pendiente()
	{
		$cantidad = 0;

		// TODO: REALIZAR CACHE PARA OPTIMIZAR.
		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS))
		{
			$cantidad += Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS))
		{
			$cantidad += Model_Foto_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS))
		{
			$cantidad += Model_Usuario_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			$cantidad += Model_Usuario_Suspension::cantidad();
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA))
		{
			$cantidad += Model_Post::s_cantidad(Model_Post::ESTADO_PAPELERA);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA))
		{
			$cantidad += Model_Foto::s_cantidad(Model_Foto::ESTADO_PAPELERA);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
		{
			$cantidad += Model_Post::s_cantidad(Model_Post::ESTADO_PENDIENTE) + Model_Post::s_cantidad(Model_Post::ESTADO_RECHAZADO);
		}

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
		{
			$cantidad +=  Model_Comentario::cantidad(Model_Comentario::ESTADO_OCULTO);
		}

		return $cantidad;
	}

	/**
	 * Submenu de la moderación.
	 * @param string $activo Sección actual.
	 * @return array
	 */
	public static function submenu($activo)
	{
		$listado = array();

		$listado['p_principal'] = array('caption' => 'Principal');
		$listado['index'] = array('link' => '/moderar/', 'caption' => 'Inicio', 'active' => FALSE);

		if (Usuario::permiso(array(Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS, Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS, Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS)))
		{
			$listado['p_denuncias'] = array('caption' => 'Denuncias');
			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DENUNCIAS))
			{
				$listado['denuncias_posts'] = array('link' => '/moderar/denuncias/posts/', 'caption' => 'Posts', 'active' => FALSE, 'cantidad' => Model_Post_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE));
			}

			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_DENUNCIAS))
			{
				$listado['denuncias_fotos'] = array('link' => '/moderar/denuncias/fotos/', 'caption' => 'Fotos', 'active' => FALSE, 'cantidad' => Model_Foto_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE));
			}

			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_VER_DENUNCIAS))
			{
				$listado['denuncias_usuarios'] = array('link' => '/moderar/denuncias/usuarios/', 'caption' => 'Usuarios', 'active' => FALSE, 'cantidad' => Model_Usuario_Denuncia::cantidad(Model_Post_Denuncia::ESTADO_PENDIENTE));
			}
		}

		$listado['p_gestion'] = array('caption' => 'Gestión');

		if (Usuario::permiso(Model_Usuario_Rango::PERMISO_USUARIO_SUSPENDER))
		{
			$listado['gestion_usuarios'] = array('link' => '/moderar/gestion/usuarios/', 'caption' => 'Usuarios', 'active' => FALSE, 'cantidad' => Model_Usuario_Suspension::cantidad());
		}
		// $listado['gestion_buscador'] = array('link' => '/moderar/gestion/buscador/', 'caption' => 'Buscador contenido', 'active' => FALSE);

		if (Usuario::permiso(array(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA, Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA)))
		{
			$listado['p_papelera'] = array('caption' => 'Papelera de reciclaje');

			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_PAPELERA))
			{
				$listado['papelera_posts'] = array('link' => '/moderar/papelera/posts/', 'caption' => 'Posts eliminados', 'active' => FALSE, 'cantidad' => Model_Post::s_cantidad(Model_Post::ESTADO_PAPELERA));
			}

			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_FOTO_VER_PAPELERA))
			{
				$listado['papelera_fotos'] = array('link' => '/moderar/papelera/fotos/', 'caption' => 'Fotos eleminadas', 'active' => FALSE, 'cantidad' => Model_Foto::s_cantidad(Model_Foto::ESTADO_PAPELERA));
			}
		}

		if (Usuario::permiso(array(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO, Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO)))
		{
			$listado['p_desaprobado'] = array('caption' => 'Contenido desaprobado');

			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_POST_VER_DESAPROBADO))
			{
				$listado['desaprobado_posts'] = array('link' => '/moderar/desaprobado/posts', 'caption' => 'Posts', 'active' => FALSE, 'cantidad' => Model_Post::s_cantidad(Model_Post::ESTADO_PENDIENTE) + Model_Post::s_cantidad(Model_Post::ESTADO_RECHAZADO));
			}

			if (Usuario::permiso(Model_Usuario_Rango::PERMISO_COMENTARIO_VER_DESAPROBADO))
			{
				$listado['desaprobado_comentarios'] = array('link' => '/moderar/desaprobado/comentarios/', 'caption' => 'Comentarios', 'active' => FALSE, 'cantidad' => Model_Comentario::cantidad(Model_Comentario::ESTADO_OCULTO));
			}
		}

		// Seteamos el color.
		foreach ($listado as $k => $v)
		{
			if (isset($v['cantidad']))
			{
				if ($listado[$k]['cantidad'] > 0)
				{
					$listado[$k]['tipo'] = 'important';
				}
				else
				{
					$listado[$k]['tipo'] = 'success';
				}
			}
		}

		if (isset($listado[$activo]))
		{
			$listado[$activo]['active'] = TRUE;
		}
		return $listado;
	}

	/**
	 * Portada de la moderación.
	 * Mostramos elementos relevantes y un resumen de los que sucede.
	 */
	public function action_index()
	{
		// Cargamos la portada.
		$portada = View::factory('moderar/home/index');

		// Listado de sucesos de moderacion.
		//TODO: IMPLEMENTAR.
		$portada->assign('sucesos', array());

		// Listado de denuncias.
		$rst = Database::get_instance()->query('SELECT type, id FROM ( SELECT \'post\' AS type, id, fecha FROM post_denuncia WHERE estado = 0 UNION SELECT \'foto\' AS type, id, fecha FROM foto_denuncia WHERE estado = 0 UNION SELECT \'usuario\' AS type, id, fecha FROM usuario_denuncia WHERE estado = 0 ) as A ORDER BY fecha DESC LIMIT 10');
		$rst->set_fetch_type(Database_Query::FETCH_ASSOC);

		$lst = array();
		foreach ($rst as $v)
		{
			switch ($v['type'])
			{
				case 'post':
					$denuncia = new Model_Post_Denuncia( (int) $v['id']);
					$obj = $denuncia->as_array();
					$obj['usuario'] = $denuncia->usuario()->as_array();
					$obj['tipo'] = 'post';
					$obj['post'] = $denuncia->post()->as_array();
					$obj['post']['categoria'] = $denuncia->post()->categoria()->as_array();
					$lst[] = $obj;
					break;
				case 'foto':
					$denuncia = new Model_Foto_Denuncia( (int) $v['id']);
					$obj = $denuncia->as_array();
					$obj['usuario'] = $denuncia->usuario()->as_array();
					$obj['tipo'] = 'foto';
					$obj['foto'] = $denuncia->foto()->as_array();
					$obj['foto']['categoria'] = $denuncia->foto()->categoria()->as_array();
					$lst[] = $obj;
					break;
				case 'usuario':
					$denuncia = new Model_Usuario_Denuncia( (int) $v['id']);
					$obj = $denuncia->as_array();
					$obj['usuario'] = $denuncia->usuario()->as_array();
					$obj['tipo'] = 'usuario';
					$obj['denunciado'] = $denuncia->denunciado()->as_array();
					$lst[] = $obj;
					break;
			}
		}
		$portada->assign('denuncias', $lst);
		unset($lst, $rst);

		// Listado de contenido desaprobado.
		$rst = Database::get_instance()->query('SELECT * FROM ( SELECT \'post\' AS type, id, fecha FROM post WHERE estado = 3 UNION SELECT \'post_comentario\' AS type, id, fecha FROM post_comentario WHERE estado = 1 UNION SELECT \'foto_comentario\' AS type, id, fecha FROM foto_comentario WHERE estado = 1 ) AS A ORDER BY fecha DESC LIMIT 10');
		$rst->set_fetch_type(Database_Query::FETCH_ASSOC);

		$lista = array();
		foreach ($rst as $v)
		{
			switch ($v['type'])
			{
				case 'post':
					$post = new Model_Post( (int) $v['id']);
					$obj = $post->as_array();
					$obj['usuario'] = $post->usuario()->as_array();
					$obj['tipo'] = 'post';
					$lista[] = $obj;
					break;
				case 'post_comentario':
					$post = new Model_Post_Comentario( (int) $v['id']);
					$obj = $post->as_array();
					$obj['post'] = $post->post()->as_array();
					$obj['usuario'] = $post->usuario()->as_array();
					$obj['tipo'] = 'post_comentario';
					$lista[] = $obj;
					break;
				case 'foto_comentario':
					$post = new Model_Foto_Comentario( (int) $v['id']);
					$obj = $post->as_array();
					$obj['foto'] = $post->foto()->as_array();
					$obj['usuario'] = $post->usuario()->as_array();
					$obj['tipo'] = 'foto_comentario';
					$lista[] = $obj;
					break;
			}
		}
		$portada->assign('contenido', $lista);
		unset($lista);

		// Seteamos el menu.
		$this->template->assign('master_bar', parent::base_menu('moderar'));

		// Cargamos plantilla moderacion.
		$admin_template = View::factory('moderar/template');
		$admin_template->assign('contenido', $portada->parse());
		unset($portada);
		$admin_template->assign('top_bar', self::submenu('index'));

		// Asignamos la vista a la plantilla base.
		$this->template->assign('contenido', $admin_template->parse());
	}

	/**
	 * Previsualización de bbcode.
	 */
	public function action_preview()
	{
		// Obtengo el contenido y evitamos XSS.
		$contenido = isset($_POST['contenido']) ? htmlentities($_POST['contenido'], ENT_NOQUOTES, 'UTF-8') : '';

		// Evito salida por template.
		$this->template = NULL;

		// Proceso contenido.
		die(Decoda::procesar($contenido));
	}
}
