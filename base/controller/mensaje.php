<?php
/**
 * mensaje.php is part of Marifa.
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
 * Controlador de mensajeria entre usuarios.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Mensaje extends Controller {

	public function __construct()
	{
		// Solo usuarios conectados.
		if ( ! Usuario::is_login())
		{
			Request::redirect('/usuario/login', TRUE);
		}

		parent::__construct();
	}

	protected function submenu($activo)
	{
		return array(
			'index' => array('link' => '/mensaje/', 'caption' => 'Bandeja entrada', 'active' => $activo == 'index'),
			'enviados' => array('link' => '/mensaje/enviados', 'caption' => 'Bandeja salida', 'active' => $activo == 'enviados'),
			'nuevo' => array('link' => '/mensaje/nuevo', 'caption' => 'Enviar', 'active' => $activo == 'nuevo'),
		);
	}

	public function action_index()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Mensajes - Bandeja de entrada');

		// Cargamos la vista.
		$view = View::factory('mensaje/index');

		// Cargamos el listado de mensajes.
		$model_mensajes = new Model_Mensaje;
		$recibidos = $model_mensajes->recibidos(Usuario::$usuario_id);

		// Procesamos información relevante.
		foreach ($recibidos as $key => $value)
		{
			$d = $value->as_array();
			$d['emisor'] = $value->emisor()->as_array();
			$d['receptor'] = $value->receptor()->as_array();

			$recibidos[$key] = $d;
		}

		$view->assign('recibidos', $recibidos);
		unset($recibidos);

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('index'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_enviados()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Mensajes - Bandeja de salida');

		// Cargamos la vista.
		$view = View::factory('mensaje/enviados');

		// Cargamos el listado de mensajes.
		$model_mensajes = new Model_Mensaje;
		$enviados = $model_mensajes->enviados(Usuario::$usuario_id);

		// Procesamos información relevante.
		foreach ($enviados as $key => $value)
		{
			$d = $value->as_array();
			$d['emisor'] = $value->emisor()->as_array();
			$d['receptor'] = $value->receptor()->as_array();

			$enviados[$key] = $d;
		}

		$view->assign('enviados', $enviados);
		unset($recibidos);

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('enviados'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_nuevo($tipo, $mensaje_id)
	{
		// Verificamos si es reenvio o respuesta.
		// 1 - Responder.
		// 2 - Reenviar.
		if ($tipo == 1 || $tipo == 2)
		{
			// Cargamos el mensaje padre.
			$model_padre = new Model_Mensaje( (int) $mensaje_id);

			if (is_array($model_padre->as_array()))
			{
				if ($model_padre->receptor_id == Usuario::$usuario_id)
				{
					$padre = $model_padre;
				}
			}
			unset($model_padre);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Mensajes - Enviar mensaje');

		// Cargamos la vista.
		$view = View::factory('mensaje/nuevo');

		// Informamos tipo y mensaje_id a la vista.
		if (isset($padre))
		{
			$view->assign('tipo', $tipo);
			$view->assign('mensaje_id', (int) $mensaje_id);
		}

		// Elementos por defecto.
		foreach (array('para', 'asunto', 'contenido', 'error_para', 'error_asunto', 'error_contenido') as $k)
		{
			$view->assign($k, '');
		}

		// Obtenemos los datos y seteamos valores.
		foreach (array('para', 'asunto', 'contenido') as $k)
		{
			$$k = isset($_POST[$k]) ? $_POST[$k] : '';
			$view->assign($k, $$k);
		}

		// Por defecto segun tipo de llamada.
		if (isset($padre))
		{
			if ($tipo == 1)
			{
				$para = $padre->emisor()->nick;
				$view->assign('para', $para);
				$view->assign('asunto', 'RE: '.$padre->asunto);
			}
			else
			{
				$view->assign('asunto', $padre->asunto);
				$view->assign('contenido', $padre->contenido);
			}
		}

		if (Request::method() == 'POST')
		{
			$error = FALSE;

			// Verificamos el asunto.
			if ( ! preg_match('/^[a-zA-Z0-9áéíóú\-,\.:\s]{6,60}$/D', $asunto))
			{
				$view->assign('error_asunto', 'El formato del asunto no es correcto.');
				$error = TRUE;
			}

			// Verificamos lista de usuarios.
			if ( ! preg_match('/^(([a-zA-Z0-9áéíóúAÉÍÓÚÑñ ]{4,16})(,(\s)?)?){1,}$/D', $para))
			{
				$view->assign('error_para', 'No introdujo una lista de usuarios válida.');
				$error = TRUE;
			}
			else
			{
				if (isset($padre) && $tipo == 1)
				{
					$usuarios = array($padre->emisor());
				}
				else
				{
					// Verificamos cada uno de los usuarios.
					$u_list = explode(',', $para);

					$model_usuario = new Model_Usuario;

					$usuarios = array();
					foreach ($u_list as $u)
					{
						$u = trim($u);
						if ($model_usuario->exists_nick($u))
						{
							$model_usuario->load_by_nick($u);
							if ($model_usuario->id == $_SESSION['usuario_id'])
							{
								$view->assign('error_para', "No puedes enviarte mensaje a ti mismo.");
								$error = TRUE;
								break;
							}
							$usuarios[$u] = $model_usuario;
							$model_usuario = new Model_Usuario;
						}
						else
						{
							$view->assign('error_para', "El usuario '$u' no es válida.");
							$error = TRUE;
						}
					}
					unset($model_usuario);
				}
			}

			// Verificamos el contenido.
			$contenido_clean = preg_replace('/\[([^\[\]]+)\]/', '', $contenido);
			if ( ! isset($contenido_clean{20}) || isset($contenido{600}))
			{
				$view->assign('error_contenido', 'El mensaje debe tener entre 20 y 600 caractéres.');
				$error = TRUE;
			}
			unset($contenido_clean);

			// Procedemos a crear el mensaje.
			if ( ! $error)
			{
				// Evitamos XSS.
				$contenido = htmlentities($contenido, ENT_NOQUOTES, 'UTF-8');

				// Formateamos los campos.
				$asunto = trim(preg_replace('/\s+/', ' ', $asunto));

				$errors = array();

				foreach ($usuarios as $u)
				{
					$model_mensaje = new Model_Mensaje;

					if (isset($padre) && $tipo == 1)
					{
						$padre->actualizar_estado(Model_Mensaje::ESTADO_RESPONDIDO);
						$mensaje_id = $model_mensaje->enviar(Usuario::$usuario_id, $u->id, $asunto, $contenido, $padre->id);
					}
					else
					{
						if (isset($padre) && $tipo == 2)
						{
							$padre->actualizar_estado(Model_Mensaje::ESTADO_REENVIADO);
						}
						$mensaje_id = $model_mensaje->enviar(Usuario::$usuario_id, $u->id, $asunto, $contenido);
					}

					if ($mensaje_id > 0)
					{
						$model_suceso = new Model_Suceso;
						$model_suceso->crear(array(Usuario::$usuario_id, $u->id), 'nuevo_mensaje', $mensaje_id);
					}
					else
					{
						$errors[] = "Se produjo un error cuando se creaba enviaba el mensaje a '{$u->nick}'. Reintente.";
					}
				}

				if (count($errors) == 0)
				{
					$view->assign('success', 'Mensajes enviados correctamente.');
				}
				else
				{
					$view->assign('error', $errors);
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_ver($mensaje)
	{
		// Forzamos entero.
		$mensaje = (int) $mensaje;

		// Verificamos exista el mensaje.
		$model_mensaje = new Model_Mensaje($mensaje);

		if ( ! is_array($model_mensaje->as_array()))
		{
			Request::redirect('/mensaje/');
		}

		// Verificamos sea el receptor.
		if (Usuario::$usuario_id != $model_mensaje->receptor_id)
		{
			Request::redirect('/mensaje/');
		}

		// Seteamos como leido.
		if ($model_mensaje->estado == Model_Mensaje::ESTADO_NUEVO)
		{
			$model_mensaje->actualizar_estado(Model_Mensaje::ESTADO_LEIDO);
		}

		// Asignamos el título.
		$this->template->assign('title', 'Mensajes - '.$model_mensaje->asunto);

		// Cargamos la vista.
		$view = View::factory('mensaje/ver');

		// Información general del mensaje.
		$aux = $model_mensaje->as_array();

		// Proceso el contenido.
		$aux['contenido'] = Decoda::procesar($aux['contenido']);

		$aux['emisor'] = $model_mensaje->emisor()->as_array();
		$aux['receptor'] = $model_mensaje->receptor()->as_array();
		$view->assign('mensaje', $aux);
		unset($aux);

		// Listado de mensajes hijos.
		// $view->assign('hijos', $this->listado_conversacion($model_mensaje->padre_id));

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_noleido($mensaje)
	{
		// Forzamos entero.
		$mensaje = (int) $mensaje;

		// Verificamos exista el mensaje.
		$model_mensaje = new Model_Mensaje($mensaje);

		if ( ! is_array($model_mensaje->as_array()))
		{
			Request::redirect('/mensaje/');
		}

		// Verificamos sea el receptor.
		if (Usuario::$usuario_id != $model_mensaje->receptor_id)
		{
			Request::redirect('/mensaje/');
		}

		// Seteamos como leido.
		if ($model_mensaje->estado == Model_Mensaje::ESTADO_LEIDO)
		{
			$model_mensaje->actualizar_estado(Model_Mensaje::ESTADO_NUEVO);
		}

		Request::redirect('/mensaje/');
	}

	/**
	 * Vemos un mensaje enviado.
	 * @param int $mensaje ID del mensaje a ver
	 */
	public function action_enviado($mensaje)
	{
		// Forzamos entero.
		$mensaje = (int) $mensaje;

		// Verificamos exista el mensaje.
		$model_mensaje = new Model_Mensaje($mensaje);

		if ( ! is_array($model_mensaje->as_array()))
		{
			Request::redirect('/mensaje/');
		}

		// Verificamos sea el emisor.
		if (Usuario::$usuario_id != $model_mensaje->emisor_id)
		{
			Request::redirect('/mensaje/');
		}

		// Asignamos el título.
		$this->template->assign('title', 'Mensajes - '.$model_mensaje->asunto);

		// Cargamos la vista.
		$view = View::factory('mensaje/enviado');

		// Información general del mensaje.
		$aux = $model_mensaje->as_array();

		// Proceso el contenido.
		$aux['contenido'] = Decoda::procesar($aux['contenido']);

		// Agrego usuarios.
		$aux['emisor'] = $model_mensaje->emisor()->as_array();
		$aux['receptor'] = $model_mensaje->receptor()->as_array();
		$view->assign('mensaje', $aux);
		unset($aux);

		// Listado de mensajes hijos.
		// $view->assign('hijos', $this->listado_conversacion($model_mensaje->padre_id));

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('nuevo'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Armamos un arreglo con el listado de comentarios.
	 * @param int $mensaje_id ID del mensaje padre a la lista a cargar.
	 * @param int $cantidad Cantidad de mensajes a cargar.
	 * @return array
	 */
	protected function listado_conversacion($mensaje_id, $cantidad = 10)
	{
		// Arreglo donde guardar los posts.
		$rst = array();

		// Ultimo mensaje padre.
		if ($mensaje_id != NULL)
		{
			$modelo_padre = new Model_Mensaje($mensaje_id);
		}

		// Buscamos todos los mensajes.
		while (count($rst) < $cantidad)
		{
			if ( ! is_object($modelo_padre))
			{
				// No existe por lo cual salimos.
				break;
			}

			// Obtenemos la información.
			$data = $modelo_padre->as_array();
			$data['emisor'] = $modelo_padre->emisor()->as_array();
			$data['receptor'] = $modelo_padre->receptor()->as_array();

			// Cargamos el proximo post.
			$modelo_padre = $modelo_padre->padre();

			// Agregamos elemento.
			$rst[] = $data;
		}

		return $rst;
	}

}