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
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador de la portada.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Foto extends Controller {

	/**
	 * Listado de pestañas de la foto.
	 * @param int $activo Pestaña seleccionada.
	 * @param bool $login Menu para usuario logueado o no.
	 */
	protected function submenu($activo, $login = FALSE)
	{
		$lst = array();
		$lst['index'] = array('link' => '/foto/', 'caption' => 'Fotos', 'active' => $activo == 'index');
		if ($login)
		{
			$lst['nuevo'] = array('link' => '/foto/nueva', 'caption' => 'Agregar Foto', 'active' => $activo == 'nuevo');
			$lst['mis_fotos'] = array('link' => '/foto/mis_fotos', 'caption' => 'Mis Fotos', 'active' => $activo == 'mis_fotos');
		}
		return $lst;
	}

	/**
	 * Mostramos listado de fotos.
	 */
	public function action_index()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Fotos');

		// Cargamos la vista.
		$view = View::factory('foto/index');

		// Cargamos el listado de fotos.
		$model_fotos = new Model_Foto;
		$fotos = $model_fotos->obtener_ultimas();

		// Procesamos información relevante.
		foreach ($fotos as $key => $value)
		{
			$d = $value->as_array();
			$d['descripcion'] = Decoda::procesar($d['descripcion']);
			$d['categoria'] = $value->categoria()->as_array();
			$d['votos'] = $value->votos();
			$d['favoritos'] = $value->favoritos();
			$d['usuario'] = $value->usuario()->as_array();

			// Acciones.
			if (Session::is_set('usuario_id'))
			{
				if ( (int) Session::get('usuario_id') == $value->usuario_id)
				{
					$d['favorito'] = TRUE;
					$d['voto'] = TRUE;
				}
				else
				{
					$d['favorito'] = $value->es_favorito( (int) Session::is_set('usuario_id'));
					$d['voto'] = $value->ya_voto( (int) Session::is_set('usuario_id'));
				}
			}
			else
			{
				$d['favorito'] = TRUE;
				$d['voto'] = TRUE;
			}
			$fotos[$key] = $d;
		}

		$view->assign('fotos', $fotos);
		unset($fotos);

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login('fotos'));
		$this->template->assign('top_bar', $this->submenu('index', Session::is_set('usuario_id')));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Mostramos listado de fotos del usuario conectado
	 */
	public function action_mis_fotos()
	{
		// Verificamos si esta conectado.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/foto/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Mis Fotos');

		// Cargamos la vista.
		$view = View::factory('foto/index');

		// Cargamos el listado de fotos.
		$model_fotos = new Model_Foto;
		$fotos = $model_fotos->obtener_ultimas_usuario( (int) Session::get('usuario_id'));

		// Procesamos información relevante.
		foreach ($fotos as $key => $value)
		{
			$d = $value->as_array();
			$d['descripcion'] = Decoda::procesar($d['descripcion']);
			$d['categoria'] = $value->categoria()->as_array();
			$d['votos'] = $value->votos();
			$d['favoritos'] = $value->favoritos();
			$d['usuario'] = $value->usuario()->as_array();

			// Acciones. Como son nuestras fotos no hacen falta acciones.
			$d['favorito'] = TRUE;
			$d['voto'] = TRUE;

			$fotos[$key] = $d;
		}

		$view->assign('fotos', $fotos);
		unset($fotos);

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login('fotos'));
		$this->template->assign('top_bar', $this->submenu('mis_fotos', Session::is_set('usuario_id')));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Mostramos una foto.
	 * @param int $foto ID de la foto.
	 */
	public function action_ver($foto)
	{
		// Convertimos la foto a ID.
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			Request::redirect('/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Foto - '.$model_foto->as_object()->titulo);

		// Cargamos la vista.
		$view = View::factory('foto/ver');

		// Mi id.
		$view->assign('me', Session::get('usuario_id'));

		// Información del usuario dueño del post.
		$u_data = $model_foto->usuario()->as_array();
		$u_data['seguidores'] = $model_foto->usuario()->cantidad_seguidores();
		$u_data['posts'] = $model_foto->usuario()->cantidad_posts();
		$u_data['comentarios'] = $model_foto->usuario()->cantidad_comentarios();
		$u_data['puntos'] = $model_foto->usuario()->cantidad_puntos();
		$view->assign('usuario', $u_data);
		unset($u_data);

		// Información de la foto.
		$ft = $model_foto->as_array();
		$ft['descripcion'] = Decoda::procesar($ft['descripcion']);
		$ft['votos'] = (int) $model_foto->votos();
		$ft['favoritos'] = (int) $model_foto->favoritos();
		$view->assign('foto', $ft);
		unset($ft);

		if ( ! Session::is_set('usuario_id') || $model_foto->as_object()->usuario_id == Session::get('usuario_id'))
		{
			$view->assign('es_favorito', TRUE);
			$view->assign('ya_vote', TRUE);
		}
		else
		{
			// Computamos la visita si es necesario.
			if ($model_foto->visitas !== NULL)
			{
				$model_foto->agregar_visita();
			}

			$view->assign('es_favorito', $model_foto->es_favorito( (int) Session::get('usuario_id')));
			$view->assign('ya_vote', $model_foto->ya_voto( (int) Session::get('usuario_id')));
		}

		// Verifico si soporta comentarios.
		$view->assign('puedo_comentar', $model_foto->soporta_comentarios());

		// Comentarios del post.
		$cmts = $model_foto->comentarios();
		$l_cmt = array();
		foreach ($cmts as $cmt)
		{
			$cl_cmt = $cmt->as_array();
			$cl_cmt['usuario'] = $cmt->usuario()->as_array();
			$l_cmt[] = $cl_cmt;
		}
		$view->assign('comentarios', $l_cmt);
		unset($l_cmt, $cmts);

		$view->assign('comentario_content', isset($_POST['comentario']) ? $_POST['comentario'] : NULL);
		$view->assign('comentario_error', Session::get_flash('post_comentario_error'));
		$view->assign('comentario_success', Session::get_flash('post_comentario_success'));
		$view->assign('success', Session::get_flash('success'));


		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login('fotos'));
		$this->template->assign('top_bar', $this->submenu('index', Session::is_set('usuario_id')));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Votamos una foto.
	 * @param int $foto ID de la foto.
	 * @param int $voto 1 para positivo, -1 para negativo.
	 */
	public function action_votar($foto, $voto)
	{
		// Obtenemos el voto.
		$voto = $voto == 1;

		// Cargamos el comentario.
		$model_foto = new Model_Foto( (int) $foto);

		// Verificamos existencia.
		if ( ! is_array($model_foto->as_array()))
		{
			Request::redirect('/');
		}

		// Cargamos usuario.
		$usuario_id = (int) Session::get('usuario_id');

		// Verificamos autor.
		if ($model_foto->usuario_id != $usuario_id)
		{
			// Verificamos puntuación.
			if ( ! $model_foto->ya_voto($usuario_id))
			{
				Session::set('success', 'El voto fue guardado correctamente.');
				$model_foto->votar($usuario_id, $voto);

				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array($usuario_id, $model_foto->usuario_id), 'voto_foto', $usuario_id, $foto);
			}
		}
		Request::redirect('/foto/ver/'.$model_foto->foto_id);
	}

	/**
	 * Agregamos la foto como favorita.
	 * @param int $foto ID de la foto.
	 */
	public function action_favorito($foto)
	{
		// Convertimos el post a ID.
		$foto = (int) $foto;

		// Cargamos el post.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			Request::redirect('/');
		}

		// Verifica autor.
		if ($model_foto->usuario_id != Session::get('usuario_id'))
		{
			// Verificamos el voto.
			if ( ! $model_foto->es_favorito( (int) Session::get('usuario_id')))
			{
				Session::set('success', 'Foto agregada a favoritos correctamente.');
				$model_foto->agregar_favorito( (int) Session::get('usuario_id'));

				$model_suceso = new Model_Suceso;
				$model_suceso->crear(array( (int) Session::get('usuario_id'), $model_foto->usuario_id), 'favorito_foto', (int) Session::get('usuario_id'), $foto);
			}
		}
		Request::redirect('/foto/ver/'.$foto);
	}

	/**
	 * Agregamos un comentario en la foto.
	 * @param int $foto ID de la foto donde comentar.
	 */
	public function action_comentar($foto)
	{
		// Verificamos el método de envio.
		if (Request::method() != 'POST')
		{
			Request::redirect('/foto/ver/'.$foto);
		}

		// Convertimos el foto a ID.
		$foto = (int) $foto;

		// Cargamos la foto.
		$model_foto = new Model_Foto($foto);

		// Verificamos exista.
		if ( ! is_array($model_foto->as_array()))
		{
			Request::redirect('/');
		}

		// Verifico se pueda comentar.
		if ( ! $model_foto->soporta_comentarios())
		{
			Session::set('post_comentario_error', 'No se puede comentar la foto porque están cerrados.');
			Request::redirect('/foto/ver/'.$foto);
		}

		// Obtenemos el comentario.
		$comentario = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

		// Verificamos el formato.
		$comentario_clean = preg_replace('/\[.*\]/', '', $comentario);
		if ( ! isset($comentario_clean{20}) || isset($comentario{400}))
		{
			Session::set('post_comentario_error', 'El comentario debe tener entre 20 y 400 caracteres.');

			// Evitamos la visualización de la plantilla.
			$this->template = NULL;

			Dispatcher::call('/foto/ver/'.$foto, TRUE);
		}
		else
		{
			// Evitamos XSS.
			$comentario = htmlentities($comentario, ENT_NOQUOTES, 'UTF-8');

			// Insertamos el comentario.
			$id = $model_foto->comentar( (int) Session::get('usuario_id'), $comentario);

			$model_suceso = new Model_Suceso;
			$model_suceso->crear(array( (int) Session::get('usuario_id'), $model_foto->usuario_id), 'comentario_foto', $id);

			Session::set('post_comentario_success', 'El comentario se ha realizado correctamente.');

			Request::redirect('/foto/ver/'.$foto);
		}
	}

	/**
	 * Agregamos una nueva foto.
	 */
	public function action_nueva()
	{
		// Verificamos usuario conectado.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Nueva foto');

		// Cargamos la vista.
		$view = View::factory('foto/nueva');

		// Cargo el listado de categorias.
		$model_categorias = new Model_Categoria;
		$categorias = $model_categorias->lista();

		$view->assign('categorias', $categorias);

		// Elementos por defecto.
		foreach (array('titulo', 'url', 'descripcion', 'comentarios', 'visitantes', 'categoria', 'error_titulo', 'error_url', 'error_descripcion', 'error_categoria') as $k)
		{
			$view->assign($k, '');
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login('fotos'));
		$this->template->assign('top_bar', $this->submenu('nuevo', TRUE));

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Obtenemos los datos y seteamos valores.
			foreach (array('titulo', 'url', 'descripcion', 'categoria') as $k)
			{
				$$k = isset($_POST[$k]) ? $_POST[$k] : '';
				$view->assign($k, $$k);
			}

			// Obtenemos los checkbox.
			$visitantes = isset($_POST['visitantes']) ? ($_POST['visitantes'] == 1) : FALSE;
			$view->assign('visitantes', $visitantes);

			$comentarios = isset($_POST['comentarios']) ? ($_POST['comentarios'] == 1) : FALSE;
			$view->assign('comentarios', $comentarios);

			// Verificamos el titulo.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $titulo))
			{
				$view->assign('error_titulo', 'El formato del título no es correcto.');
				$error = TRUE;
			}

			// Verificamos quitando BBCODE.
			$descripcion_clean = preg_replace('/\[([^\[\]]+)\]/', '', $descripcion);

			// Verificamos la descripcion.
			if ( ! isset($descripcion_clean{20}) || isset($descripcion{600}))
			{
				$view->assign('error_descripcion', 'La descripción debe tener entre 20 y 600 caractéres.');
				$error = TRUE;
			}
			unset($contenido_clean);

			// Verificamos la URL.
			if ( ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/Di', $url))
			{
				// Verifico IMG.
				if ( ! isset($_FILES['img']) || $_FILES['img']['error'] == UPLOAD_ERR_NO_FILE)
				{
					$view->assign('error_url', 'La dirección de la URL no es válida.');
					$error = TRUE;
				}
			}

			// Verifico la categoria.
			if ( ! $model_categorias->existe_seo($categoria))
			{
				$view->assign('error_categoria', 'La categoria seleccionada es incorrecta.');
				$error = TRUE;
			}


			// Proceso de verificación de método de carga de la imagen.
			if ( ! $error)
			{
				if ( ! isset($_FILES['img']) || $_FILES['img']['error'] == UPLOAD_ERR_NO_FILE)
				{
					$upload = new Upload_Imagen;
					try {
						$rst = $upload->from_url($url);

						if ($rst)
						{
							$url = $rst;
						}
						else
						{
							$view->assign('error_url', 'Se produjo un error al cargar la imagen.');
							$error = TRUE;
						}
					}
					catch (Exception $e)
					{
						$view->assign('error_url', $e->getMessage());
						$error = TRUE;
					}
				}
				else
				{
					// Verifico la imagen.
					$upload = new Upload_Imagen;
					try {
						$rst = $upload->procesar_imagen('img');

						if ($rst)
						{
							$url = $rst;
						}
						else
						{
							$view->assign('error_url', 'Se produjo un error al cargar la imagen.');
							$error = TRUE;
						}
					}
					catch (Exception $e)
					{
						$view->assign('error_url', $e->getMessage());
						$error = TRUE;
					}
				}
			}

			// Procedemos a crear la imagen.
			if ( ! $error)
			{
				// Evitamos XSS.
				$descripcion = htmlentities($descripcion, ENT_NOQUOTES, 'UTF-8');

				// Formateamos los campos.
				$titulo = trim(preg_replace('/\s+/', ' ', $titulo));

				// Obtengo el ID de la categoria.
				$model_categorias->load_by_seo($categoria);

				$model_foto = new Model_Foto;
				$foto_id = $model_foto->crear( (int) Session::get('usuario_id'), $titulo, $descripcion, $url, $model_categorias->id, $visitantes, $comentarios);

				if ($foto_id > 0)
				{
					$model_suceso = new Model_Suceso;
					$model_suceso->crear( (int) Session::get('usuario_id'), 'nueva_foto', $model_foto->id);

					Request::redirect('/foto/ver/'.$model_foto->id);
				}
				else
				{
					$view->assign('error', 'Se produjo un error cuando se creaba la foto. Reintente.');
				}
			}
		}

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

}
