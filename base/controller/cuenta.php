<?php defined('APP_BASE') or die('No direct access allowed.');
/**
 * cuenta.php is part of Marifa.
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
 * Controlador para las opciones del perfil del usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa/Base
 * @subpackage Controller
 */
class Base_Controller_Cuenta extends Controller {

	/**
	 * Constructor de la clase.
	 */
	public function __construct()
	{
		// Verificamos permisos.
		if ( ! Session::is_set('usuario_id'))
		{
			Request::redirect('/usuario/login');
		}

		// Llamamos al constructor padre.
		parent::__construct();
	}

	/**
	 * Listado de pestañas del perfil.
	 * @param int $action Pestaña seleccionada.
	 */
	protected function submenu($activo)
	{
		return array(
			'index' => array('link' => '/cuenta', 'caption' => 'Cuenta', 'active' => $activo == 'index'),
			'perfil' => array('link' => '/cuenta/perfil', 'caption' => 'Perfil', 'active' => $activo == 'perfil'),
			'bloqueados' => array('link' => '/cuenta/bloqueados', 'caption' => 'Bloqueos', 'active' =>  $activo == 'bloqueados'),
			'password' => array('link' => '/cuenta/password', 'caption' => 'Contrase&ntilde;a', 'active' =>  $activo == 'password'),
			'nick' => array('link' => '/cuenta/nick', 'caption' => 'Nicks', 'active' =>  $activo == 'nick'),
		);
	}

	public function action_index()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta');

		// Cargamos la vista.
		$view = View::factory('cuenta/index');

		// Cargamos el usuario.
		$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));

		// Seteamos los datos actuales.
		$view->assign('error', array());
		$view->assign('email', $model_usuario->email);
		$view->assign('estado_email', 0);
		$view->assign('origen', Utils::prop($model_usuario->perfil(), 'origen'));
		$view->assign('estado_origen', 0);
		$view->assign('sexo', Utils::prop($model_usuario->perfil(), 'sexo'));
		$view->assign('estado_sexo', 0);
		$view->assign('nacimiento', explode('-', Utils::prop($model_usuario->perfil(), 'nacimiento')));
		$view->assign('estado_nacimiento', 0);

		// Listado de paises.
		$lista_pais = Configuraciones::obtener(CONFIG_PATH.DS.'geonames.'.FILE_EXT);
		$view->assign('paices', $lista_pais);

		if (Request::method() == 'POST')
		{
			$errors = array();

			// Verificamos el e-mail.
			if (isset($_POST['email']) && ! empty($_POST['email']))
			{
				$view->assign('email', trim($_POST['email']));

				// Verificamos el formato de e-mail.
				if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST['email']))
				{
					$errors[] = 'La direcci&oacute;n de email es inv&oacute;lida.';
					$view->assign('estado_email', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['email']) != $model_usuario->email)
					{
						$m = trim($_POST['email']);

						// Verifico no exista un usuario con ese email.
						if ($model_usuario->exists_email($m))
						{
							$errors[] = 'Ya existe un usuario con ese E-Mail.';
							$view->assign('estado_email', -1);
						}
						else
						{
							// Actualizo la casilla de correo.
							//TODO: pedir validación de la misma.
							$model_usuario->cambiar_email($m);

							$view->assign('email', $m);
							$view->assign('success', 'Datos actualizados correctamente.');
							$view->assign('estado_email', 1);
						}
						unset($m);
					}
				}
			}

			// Verificamos el sexo.
			if (isset($_POST['sexo']) && ! empty($_POST['sexo']))
			{
				$view->assign('sexo', trim($_POST['sexo']));

				// Verificamos el valor.
				if ($_POST['sexo'] != 'f' && $_POST['sexo'] != 'm')
				{
					$errors[] = 'El sexo seleccionado no es correcto.';
					$view->assign('estado_sexo', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['sexo']) != Utils::prop($model_usuario->perfil(), 'sexo', NULL))
					{
						// Actualizo sexo.
						$model_usuario->perfil()->sexo = trim($_POST['sexo']);

						$view->assign('sexo', trim($_POST['sexo']));
						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_sexo', 1);
					}
				}
			}

			// Verificamos la fecha de nacimiento.
			if ((isset($_POST['dia']) && ! empty($_POST['dia']) ) && ( isset($_POST['mes']) && ! empty($_POST['mes']) ) && ( isset($_POST['ano']) && ! empty($_POST['ano']) ))
			{
				// Obtenemos los parámetros.
				$ano = (int) $_POST['ano'];
				$mes = (int) $_POST['mes'];
				$dia = (int) $_POST['dia'];

				$error = FALSE;

				// Verificamos los rangos.
				if ($dia < 1 || $dia > 31)
				{
					$errors[] = 'El día de nacimiento es incorrecto.';
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ($mes < 1 || $mes > 12)
				{
					$errors[] = 'El mes de nacimiento es incorrecto.';
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ($ano < 1900 || $dia > date('Y'))
				{
					$errors[] = 'El año de nacimiento es incorrecto.';
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ( ! $error)
				{
					// Validamos la fecha.
					if ( ! checkdate($mes, $dia, $ano))
					{
						$errors[] = 'La fecha de nacimiento es incorrecta';
						$view->assign('estado_nacimiento', -1);
					}
					else
					{
						// Creamos la fecha.
						$fecha = $_POST['ano'].'-'.$_POST['mes'].'-'.$_POST['dia'];

						// Verificamos con la actual.
						if (Utils::prop($model_usuario->perfil(), 'nacimiento', NULL) != $fecha)
						{
							$model_usuario->perfil()->nacimiento = $fecha;
							$view->assign('nacimiento', explode('-', $fecha));
							$view->assign('estado_nacimiento', 1);
							$view->assign('success', 'Datos actualizados correctamente.');
						}
					}
				}
			}

			// Verificamos el pais.
			if (isset($_POST['origen']) && ! empty($_POST['origen']))
			{
				// Obtenemos el pais y el estado.
				list($pais, $estado) = explode('.', trim(strtoupper($_POST['origen'])));

				if ( ! isset($lista_pais[$pais]))
				{
					$errors[] = 'El lugar de origen es incorrecto.';
					$view->assign('estado_origen', -1);
				}
				else
				{
					if ( ! isset($lista_pais[$pais][1][$estado]))
					{
						$errors[] = 'El lugar de origen es incorrecto.';
						$view->assign('estado_origen', -1);
					}
					else
					{
						// Verificamos sea distinto al actual.
						if (Utils::prop($model_usuario->perfil(), 'origen', NULL) != $pais.'.'.$estado)
						{
							$model_usuario->perfil()->origen = $pais.'.'.$estado;
							$view->assign('origen', $pais.'.'.$estado);
							$view->assign('estado_origen', 1);
							$view->assign('success', 'Datos actualizados correctamente.');
						}
					}
				}
			}


			$view->assign('error', $errors);
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('index'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_perfil()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Perfil');

		// Cargamos la vista.
		$view = View::factory('cuenta/perfil');

		// Cargamos el usuario.
		$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));

		$view->assign('email', $model_usuario->email);

		// Seteamos los datos actuales.
		$view->assign('error', array());

		$fields = array(
			'nombre',
			'mensaje_personal',
			'web',
			'facebook',
			'twitter',

			'hacer_amigos',
			'conocer_gente_intereses',
			'conocer_gente_negocios',
			'encontrar_pareja',
			'de_todo',

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

		foreach($fields as $value)
		{
			$view->assign($value, Utils::prop($model_usuario->perfil(), $value));
			$view->assign('estado_'.$value, 0);
		}

		if (Request::method() == 'POST')
		{
			$errors = array();

			// Verificamos el nombre.
			if (isset($_POST['nombre']) && ! empty($_POST['nombre']))
			{
				$view->assign('nombre', trim($_POST['nombre']));
				// Verificamos el formato.
				//TODO: Caracteres extra.
				if ( ! preg_match('/^[a-zA-Z0-9 ]{4,60}$/', $_POST['nombre']))
				{
					$errors[] = 'El nombre seleccionado no es correcto.';
					$view->assign('estado_nombre', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['nombre']) != Utils::prop($model_usuario->perfil(), 'nombre', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->nombre = trim($_POST['nombre']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_nombre', 1);
					}
				}
			}

			// Verificamos el mensaje personal.
			if (isset($_POST['mensaje_personal']) && ! empty($_POST['mensaje_personal']))
			{
				$view->assign('mensaje_personal', trim($_POST['mensaje_personal']));
				// Verificamos el formato.
				//TODO: Caracteres extra.
				if ( ! preg_match('/^[a-zA-Z0-9\.,:\'"\s]{6,400}$/', $_POST['mensaje_personal']))
				{
					$errors[] = 'El mensaje personal seleccionado no es correcto.';
					$view->assign('estado_mensaje_personal', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['mensaje_personal']) != Utils::prop($model_usuario->perfil(), 'mensaje_personal', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->mensaje_personal = trim($_POST['mensaje_personal']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_mensaje_personal', 1);
					}
				}
			}

			// Verificamos la web.
			if (isset($_POST['web']) && ! empty($_POST['web']))
			{
				$view->assign('web', trim($_POST['web']));
				// Verificamos el formato.
				if ( ! preg_match("/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $_POST['web']))
				{
					$errors[] = 'El sitio personal seleccionado no es correcto.';
					$view->assign('estado_web', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['web']) != Utils::prop($model_usuario->perfil(), 'web', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->web = trim($_POST['web']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_web', 1);
					}
				}
			}

			// Verificamos la URL de facebook.
			if (isset($_POST['facebook']) && ! empty($_POST['facebook']))
			{
				$view->assign('facebook', trim($_POST['facebook']));
				// Verificamos el formato.
				if ( ! preg_match('/^[a-z\d.]{5,}$/i', $_POST['facebook']))
				{
					$errors[] = 'La URL de facebook no es correcta.';
					$view->assign('estado_facebook', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['facebook']) != Utils::prop($model_usuario->perfil(), 'facebook', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->facebook = trim($_POST['facebook']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_facebook', 1);
					}
				}
			}

			// Verificamos la URL de facebook.
			if (isset($_POST['twitter']) && ! empty($_POST['twitter']))
			{
				$view->assign('twitter', trim($_POST['twitter']));
				// Verificamos el formato.
				if ( ! preg_match('/^[a-z\d.]{5,}$/i', $_POST['twitter']))
				{
					$errors[] = 'La URL de twitter no es correcta.';
					$view->assign('estado_twitter', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['twitter']) != Utils::prop($model_usuario->perfil(), 'twitter', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->twitter = trim($_POST['twitter']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_twitter', 1);
					}
				}
			}

			// Validacion de checkboxes
			$me_gustaria_listado = array(
				'hacer_amigos',
				'conocer_gente_intereses',
				'conocer_gente_negocios',
				'encontrar_pareja',
				'de_todo',
				'tatuajes',
				'piercings',
			);
			foreach($me_gustaria_listado as $key)
			{
				$checked = isset($_POST[$key]);

				if (Utils::prop($model_usuario->perfil(), $key, NULL) != $checked)
				{
					// Actualizo nombre.
					$model_usuario->perfil()->$key = $checked;

					$view->assign('success', 'Datos actualizados correctamente.');
					$view->assign($key, $checked);
					$view->assign('estado_'.$key, 1);
				}
			}
			unset($me_gustaria_listado);

			// Altura.
			if (isset($_POST['mi_altura']) && ! empty($_POST['mi_altura']))
			{
				$altura = (int) $_POST['mi_altura'];
				$view->assign('mi_altura', $altura);
				// Verificamos el formato.
				if ($altura <= 0 || $altura > 500)
				{
					$errors[] = 'La altura ingresada no es correcta.';
					$view->assign('estado_mi_altura', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if ($altura != Utils::prop($model_usuario->perfil(), 'mi_altura', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->mi_altura = $altura;

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_mi_altura', 1);
					}
				}
			}

			// Peso.
			if (isset($_POST['mi_peso']) && ! empty($_POST['mi_peso']))
			{
				$peso = (float) $_POST['mi_peso'];
				$view->assign('mi_peso', $peso);
				// Verificamos el formato.
				if ($peso <= 0 || $peso > 1000)
				{
					$errors[] = 'El peso ingresado no es correcto.';
					$view->assign('estado_mi_peso', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if ($peso != Utils::prop($model_usuario->perfil(), 'mi_peso', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->mi_peso = $peso;

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_mi_peso', 1);
					}
				}
			}

			// Validaciones de select's.
			$select_list = array(
				'estado_civil' => array('El estado civil es incorrecto.', array('soltero', 'novio', 'casado', 'divorciado', 'viudo', 'en_algo')),
				'hijos' => array('La información sobre sus hijos es incorrecta.', array('no_tengo', 'algun_dia', 'no_son_lo_mio', 'tengo_vivo_con_ellos', 'tengo_no_vivo_con_ellos')),
				'vivo_con' => array('La información sobre quien vive es incorrecta.', array('solo', 'mis_padres', 'mi_pareja', 'con_amigos', 'otro')),
				'color_pelo' => array('El color de pelo provisto es incorrecto.', array('negro', 'castano_oscuro', 'castano_claro', 'rubio', 'pelirrojo', 'gris', 'verde', 'naranja', 'morado', 'azul', 'canoso', 'tenido', 'rapado', 'calvo')),
				'color_ojos' => array('El color de ojos provisto es incorrecto.', array('negros', 'marrones', 'celestes', 'verdes', 'grises')),
				'complexion' => array('La complexión provista es incorrecta.', array('delgado', 'atletico', 'normal', 'kilos_mas', 'corpulento')),
				'mi_dieta' => array('La dieta provista es incompleta.', array('vegetariana', 'lacto_vegetariana', 'organica', 'de_todo', 'comida_basura')),
				'fumo' => array('La información sobre cuanto fuma es incorrecta.', array('no', 'casualmente', 'socialmente', 'regularmente', 'mucho')),
				'tomo_alcohol' => array('La información sobre su consumo de alcohol es incorrecta.', array('no', 'casualmente', 'socialmente', 'regularmente', 'mucho')),

				'estudios' => array('La información sobre sus estudios en incorrecta.', array('sin_estudios', 'primario_en_curso', 'primario_completo', 'secundario_en_curso', 'secundario_completo', 'terciario_en_curso', 'terciario_completo', 'universitario_en_curso', 'universitario_completo', 'post_grado_en_curso', 'post_grado_completo')),
				'idioma_espanol' => array('La información sobre el idioma español es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_ingles' => array('La información sobre el idioma inglés es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_portugues' => array('La información sobre el idioma portugués es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_frances' => array('La información sobre el idioma francés es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_italiano' => array('La información sobre el idioma italiano es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_aleman' => array('La información sobre el idioma alemán es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_otro' => array('La información sobre el idioma otro es incorrecta.', array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),

				'sector' => array('El sector seleccionado es incorrecto.', array('sin_respuesta', 'abastecimiento', 'administracion', 'apoderado_aduanal', 'asesoria_en_comercio_exterior', 'asesoria_legal_internacional', 'asistente_de_trafico', 'auditoria', 'calidad', 'call_center', 'capacitacion_comercio_exterior', 'comercial', 'comercio_exterior', 'compras', 'compras_internacionalesimportacion', 'comunicacion_social', 'comunicaciones_externas', 'comunicaciones_internas', 'consultoria', 'consultorias_comercio_exterior', 'contabilidad', 'control_de_gestion', 'creatividad', 'diseno', 'distribucion', 'ecommerce', 'educacion', 'finanzas', 'finanzas_internacionales', 'gerencia_direccion_general', 'impuestos', 'ingenieria', 'internet', 'investigacion_y_desarrollo', 'jovenes_profesionales', 'legal', 'logistica', 'mantenimiento', 'marketing', 'medio_ambiente', 'mercadotecnia_internacional', 'multimedia', 'otra', 'pasantias', 'periodismo', 'planeamiento', 'produccion', 'produccion_e_ingenieria', 'recursos_humanos', 'relaciones_institucionales_publicas', 'salud', 'seguridad_industrial', 'servicios', 'soporte_tecnico', 'tecnologia', 'tecnologias_de_la_informacion', 'telecomunicaciones', 'telemarketing', 'traduccion', 'transporte', 'ventas', 'ventas_internacionalesexportacion')),

				'nivel_ingresos' => array('El nivel de ingresos seleccionado no es correcto.', array('sin_ingresos', 'bajos', 'intermedios', 'altos')),
			);
			foreach($select_list as $key => $datos)
			{
				if (isset($_POST[$key]) && ! empty($_POST[$key]))
				{
					$view->assign($key, trim($_POST[$key]));
					// Verificamos el formato.
					if ( ! in_array(trim($_POST[$key]), $datos[1]))
					{
						$errors[] = $datos[0];
						$view->assign('estado_'.$key, -1);
					}
					else
					{
						// Verifico no sea el actual.
						if (trim($_POST[$key]) != Utils::prop($model_usuario->perfil(), $key, NULL))
						{
							// Actualizo nombre.
							$model_usuario->perfil()->$key = trim($_POST[$key]);

							$view->assign('success', 'Datos actualizados correctamente.');
							$view->assign('estado_'.$key, 1);
						}
					}
				}
			}

			// Verificamos la profesión.
			if (isset($_POST['profesion']) && ! empty($_POST['profesion']))
			{
				$view->assign('profesion', trim($_POST['profesion']));
				// Verificamos el formato.
				//TODO: Caracteres extra.
				if ( ! preg_match('/^[a-zA-Z0-9 ]{4,60}$/', $_POST['profesion']))
				{
					$errors[] = 'La profesión seleccionada no es correcta.';
					$view->assign('estado_profesion', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['profesion']) != Utils::prop($model_usuario->perfil(), 'profesion', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->profesion = trim($_POST['profesion']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_profesion', 1);
					}
				}
			}

			// Verificamos la empresa.
			if (isset($_POST['empresa']) && ! empty($_POST['empresa']))
			{
				$view->assign('empresa', trim($_POST['empresa']));
				// Verificamos el formato.
				//TODO: Caracteres extra.
				if ( ! preg_match('/^[a-zA-Z0-9 ]{4,60}$/', $_POST['empresa']))
				{
					$errors[] = 'La empresa seleccionada no es correcta.';
					$view->assign('estado_empresa', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['empresa']) != Utils::prop($model_usuario->perfil(), 'empresa', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->empresa = trim($_POST['empresa']);

						$view->assign('success', 'Datos actualizados correctamente.');
						$view->assign('estado_empresa', 1);
					}
				}
			}

			// Descripciones de gustos y demás de textareas.
			$textareas = array(
				'intereses_personales' => 'Los intereses personales son incorrectos.',
				'habilidades_profesionales' => 'Las habilidades profesionales son incorrectas.',
				'mis_intereses' => 'Los intereses son incorrectos.',
				'hobbies' => 'Los hobbies son incorrectos.',
				'series_tv_favoritas' => 'Las series de TV favoritas son incorrectas.',
				'musica_favorita' => 'La música favorita es incorrecta.',
				'deportes_y_equipos_favoritos' => 'Los deportes y equipos favoritos son incorrectos.',
				'libros_favoritos' => 'Los libros favoritos son incorrectos.',
				'peliculas_favoritas' => 'Las peliculas favoritas son incorrectas.',
				'comida_favorita' => 'La comida favorita es incorrecta.',
				'mis_heroes' => 'Los heroes son incorrectos.'
			);
			foreach ($textareas as $key => $value)
			{
				if (isset($_POST[$key]) && ! empty($_POST[$key]))
				{
					$view->assign($key, trim($_POST[$key]));
					// Verificamos el formato.
					if ( ! preg_match('/^[a-z0-9\.,:\'"\s]{6,400}$/i', $_POST[$key]))
					{
						$errors[] = $value;
						$view->assign('estado_'.$key, -1);
					}
					else
					{
						// Verifico no sea el actual.
						if (trim($_POST[$key]) != Utils::prop($model_usuario->perfil(), $key, NULL))
						{
							// Actualizo nombre.
							$model_usuario->perfil()->$key = trim($_POST[$key]);

							$view->assign('success', 'Datos actualizados correctamente.');
							$view->assign('estado_'.$key, 1);
						}
					}
				}
			}
			unset($textareas);

			$view->assign('error', $errors);
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('perfil'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_bloqueados()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Bloqueos');

		// Cargamos la vista.
		$view = View::factory('cuenta/bloqueos');

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('bloqueados'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_password()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Contrase&ntilde;a');

		// Cargamos la vista.
		$view = View::factory('cuenta/password');

		// Cargamos el usuario actual.
		$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));
		$view->assign('email', $model_usuario->email);

		// Valores por defecto.
		$view->assign('error', NULL);
		$view->assign('error_actual', NULL);
		$view->assign('error_password', NULL);
		$view->assign('error_c_password', NULL);

		if (Request::method() == 'POST')
		{
			// Verificamos que estén los datos.
			if (
				( ! isset($_POST['current']) || empty($_POST['current'])) ||
				( ! isset($_POST['password']) || empty($_POST['password'])) ||
				( ! isset($_POST['cpassword']) || empty($_POST['cpassword']))
			   )
			{
				if ( ! isset($_POST['current']) || empty($_POST['current']))
				{
					$view->assign('error', 'Debe rellenar todos los datos.');
					$view->assign('error_current', TRUE);
				}

				if ( ! isset($_POST['password']) || empty($_POST['password']))
				{
					$view->assign('error', 'Debe rellenar todos los datos.');
					$view->assign('error_password', TRUE);
				}

				if ( ! isset($_POST['cpassword']) || empty($_POST['cpassword']))
				{
					$view->assign('error', 'Debe rellenar todos los datos.');
					$view->assign('error_cpassword', TRUE);
				}
			}
			else
			{
				// Comprobamos el formato
				if ( ! preg_match('/^[a-zA-Z0-9]{6,20}$/', $_POST['password']) || $_POST['password'] != $_POST['cpassword'])
				{
					if ($_POST['password'] != $_POST['cpassword'])
					{
						$view->assign('error', 'Las contrase&ntilde;as ingresadas no coinciden.');
						$view->assign('error_password', TRUE);
					}
					else
					{
						$view->assign('error', 'La contrase&ntilde;a debe tener entre 6 y 20 caracteres alphanumericos.');
						$view->assign('error_password', TRUE);
					}
				}
				else
				{
					// Verificamos la contraseña.
					$enc = new Phpass(8, FALSE);

					if ( ! $enc->CheckPassword($_POST['current'], $model_usuario->password))
					{
						$view->assign('error', 'La contrase&ntilde;a es incorrecta.');
						$view->assign('error_current', TRUE);
					}
					else
					{
						// Actualizo la caontraseña.
						$model_usuario->actualizar_contrasena(trim($_POST['password']));
						$view->assign('success', 'La contrase&ntilde;a se ha actualizado correctamente.');
					}
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('password'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	public function action_nick()
	{
		// Asignamos el título.
		$this->template->assign('title', 'Cuenta - Nick');

		// Cargamos la vista.
		$view = View::factory('cuenta/nick');

		// Cargamos el usuario actual.
		$model_usuario = new Model_Usuario((int) Session::get('usuario_id'));

		$view->assign('email', $model_usuario->email);

		// Valores por defecto.
		$view->assign('nick_actual', $model_usuario->nick);
		$view->assign('nick', '');
		$view->assign('error_nick', NULL);
		$view->assign('error_password', NULL);

		//TODO: Listado de nicks para elegir uno anterior.

		if (Request::method() == 'POST')
		{
			if ( ( ! isset($_POST['nick']) || empty($_POST['nick']) ) || ( ! isset($_POST['password']) || empty($_POST['password'])))
			{
				// Verificamos los datos
				if ( ! isset($_POST['nick']) || empty($_POST['nick']))
				{
					$view->assign('error_nick', 'Debe ingresar un nuevo nick.');
				}
				else
				{
					$view->assign('nick', $_POST['nick']);
				}

				if ( ! isset($_POST['password']) || empty($_POST['password']))
				{
					$view->assign('error_password', 'Debe ingresar su contrase&ntilde;a para validar el cambio.');
				}
			}
			else
			{
				$nick = $_POST['nick'];
				$password = $_POST['password'];

				$view->assign('nick', $nick);

				// Verifico longitud Nick.
				if ( ! preg_match('/^[a-zA-Z0-9]{4,20}$/', $nick))
				{
					$view->assign('error_nick', 'El nick debe tener entre 4 y 20 caracteres alphanum&eacute;ricos.');
				}
				else
				{
					// Verifico la contraseña.
					$enc = new Phpass(8, FALSE);

					if ( ! $enc->CheckPassword($password, $model_usuario->password))
					{
						$view->assign('error_password', 'La contrase&ntilde;a es incorrecta.');
					}
					else
					{
						// Verifico que no exista el nick.
						if ($model_usuario->exists_nick($nick))
						{
							$view->assign('error_nick', 'El nick no est&aacute; disponible.');
						}
						else
						{
							// Actualizamos.
							$model_usuario->cambiar_nick($nick);

							$view->assign('success', 'El nick se ha actualizado correctamente.');
							$view->assign('nick', '');
							$view->assign('nick_actual', $nick);
						}
					}
				}
			}
		}

		// Menu.
		$this->template->assign('master_bar', parent::base_menu_login());
		$this->template->assign('top_bar', $this->submenu('nick'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

}
