<?php
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
 * @package		Marifa\Base
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador para las opciones del perfil del usuario.
 *
 * @since      Versión 0.1
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Cuenta extends Controller {

	/**
	 * Verifico los permisos.
	 */
	public function before()
	{
		// Verificamos permisos.
		if ( ! Usuario::is_login())
		{
			add_flash_message(FLASH_ERROR, __('Debes iniciar sesión para editar tu cuenta.', FALSE));
			Request::redirect('/usuario/login');
		}

		// Llamamos al constructor padre.
		parent::before();
	}

	/**
	 * Listado de pestañas del perfil.
	 * @param int $activo Pestaña seleccionada.
	 */
	protected function submenu($activo)
	{
		// Creo el menu.
		$menu = new Menu('cuenta_menu');

		// Agrego los elementos.
		$menu->element_set(__('Cuenta', FALSE), '/cuenta/', 'index');
		$menu->element_set(__('Perfil', FALSE), '/cuenta/perfil/', 'perfil');
		$menu->element_set(__('Bloqueos', FALSE), '/cuenta/bloqueados/', 'bloqueados');
		$menu->element_set(__('Contraseña', FALSE), '/cuenta/password/', 'password');
		$menu->element_set(__('Nicks', FALSE), '/cuenta/nick/', 'nick');
		$menu->element_set(__('Avisos', FALSE), '/cuenta/avisos/', 'avisos', NULL, Usuario::usuario()->cantidad_avisos(Model_Usuario_Aviso::ESTADO_NUEVO));

		// Devuelvo el menú procesado.
		return $menu->as_array($activo);
	}

	/**
	 * Datos principales de la cuenta.
	 */
	public function action_index()
	{
		// Asignamos el título.
		$this->template->assign('title', __('Cuenta', FALSE));

		// Cargamos la vista.
		$view = View::factory('cuenta/index');

		// Cargamos el usuario.
		$model_usuario = Usuario::usuario();
		$model_usuario->perfil()->load_list(array('origen', 'sexo', 'nacimiento'));

		// Asignamos los datos actuales.
		$view->assign('error', array());
		$view->assign('email', $model_usuario->email);
		$view->assign('estado_email', 0);
		$view->assign('origen', Utils::prop($model_usuario->perfil(), 'origen'));
		$view->assign('estado_origen', 0);
		$view->assign('sexo', Utils::prop($model_usuario->perfil(), 'sexo'));
		$view->assign('estado_sexo', 0);
		$view->assign('nacimiento', explode('-', Utils::prop($model_usuario->perfil(), 'nacimiento')));
		$view->assign('estado_nacimiento', 0);

		// Listado de países.
		$lista_pais = configuracion_obtener(CONFIG_PATH.DS.'geonames.'.FILE_EXT);
		$view->assign('paices', $lista_pais);

		if (Request::method() == 'POST')
		{
			$errors = array();

			// Verificamos el e-mail.
			if (isset($_POST['email']) && ! empty($_POST['email']))
			{
				$view->assign('email', trim($_POST['email']));

				// Verificamos el formato de e-mail.
				if ( ! preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/D', $_POST['email']))
				{
					$errors[] = __('La dirección de E-Mail es inválida.', FALSE);
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
							$errors[] = __('Ya existe un usuario con ese E-Mail.', FALSE);
							$view->assign('estado_email', -1);
						}
						else
						{
							// Actualizo la casilla de correo.
							//TODO: pedir validación de la misma.
							$model_usuario->cambiar_email($m);

							$view->assign('email', $m);
							$view->assign('success', __('Datos actualizados correctamente.', FALSE));
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
					$errors[] = __('El sexo seleccionado no es correcto.', FALSE);
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
						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_sexo', 1);
					}
				}
			}

			// Verificamos la fecha de nacimiento.
			if ((isset($_POST['dia']) && ! empty($_POST['dia'])) && (isset($_POST['mes']) && ! empty($_POST['mes'])) && (isset($_POST['ano']) && ! empty($_POST['ano'])))
			{
				// Obtenemos los parámetros.
				$ano = (int) $_POST['ano'];
				$mes = (int) $_POST['mes'];
				$dia = (int) $_POST['dia'];

				$error = FALSE;

				// Verificamos los rangos.
				if ($dia < 1 || $dia > 31)
				{
					$errors[] = __('El día de nacimiento es incorrecto.', FALSE);
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ($mes < 1 || $mes > 12)
				{
					$errors[] = __('El mes de nacimiento es incorrecto.', FALSE);
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ($ano < 1900 || $dia > date('Y'))
				{
					$errors[] = __('El año de nacimiento es incorrecto.', FALSE);
					$view->assign('estado_nacimiento', -1);
					$error = TRUE;
				}

				if ( ! $error)
				{
					// Validamos la fecha.
					if ( ! checkdate($mes, $dia, $ano))
					{
						$errors[] = __('La fecha de nacimiento es incorrecta', FALSE);
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
							$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						}
					}
				}
			}

			// Verificamos el país.
			if (isset($_POST['origen']) && ! empty($_POST['origen']))
			{
				// Obtenemos el país y el estado.
				list($pais, $estado) = explode('.', trim(strtoupper($_POST['origen'])));

				if ( ! isset($lista_pais[$pais]))
				{
					$errors[] = __('El lugar de origen es incorrecto.', FALSE);
					$view->assign('estado_origen', -1);
				}
				else
				{
					if ( ! isset($lista_pais[$pais][1][$estado]))
					{
						$errors[] = __('El lugar de origen es incorrecto.', FALSE);
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
							$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						}
					}
				}
			}

			$view->assign('error', $errors);
		}

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('index'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Información sobre el usuario.
	 */
	public function action_perfil()
	{
		// Asignamos el título.
		$this->template->assign('title', __('Cuenta - Perfil', FALSE));

		// Cargamos la vista.
		$view = View::factory('cuenta/perfil');

		// Cargamos el usuario.
		$model_usuario = Usuario::usuario();

		$view->assign('email', $model_usuario->email);

		// Asignamos los datos actuales.
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

		$model_usuario->perfil()->load_list($fields);

		foreach ($fields as $value)
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
				if ( ! preg_match('/^[a-zA-Z0-9 ]{4,60}$/D', $_POST['nombre']))
				{
					$errors[] = __('El nombre seleccionado no es correcto.', FALSE);
					$view->assign('estado_nombre', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['nombre']) != Utils::prop($model_usuario->perfil(), 'nombre', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->nombre = trim($_POST['nombre']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
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
				if ( ! preg_match('/^[a-zA-Z0-9\.,:\'"\s]{6,400}$/D', $_POST['mensaje_personal']))
				{
					$errors[] = __('El mensaje personal seleccionado no es correcto.', FALSE);
					$view->assign('estado_mensaje_personal', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['mensaje_personal']) != Utils::prop($model_usuario->perfil(), 'mensaje_personal', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->mensaje_personal = trim($_POST['mensaje_personal']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_mensaje_personal', 1);
					}
				}
			}

			// Verificamos la web.
			if (isset($_POST['web']) && ! empty($_POST['web']))
			{
				$view->assign('web', trim($_POST['web']));
				// Verificamos el formato.
				if ( ! preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $_POST['web']))
				{
					$errors[] = __('El sitio personal seleccionado no es correcto.', FALSE);
					$view->assign('estado_web', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['web']) != Utils::prop($model_usuario->perfil(), 'web', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->web = trim($_POST['web']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_web', 1);
					}
				}
			}

			// Verificamos la URL de facebook.
			if (isset($_POST['facebook']) && ! empty($_POST['facebook']))
			{
				$view->assign('facebook', trim($_POST['facebook']));
				// Verificamos el formato.
				if ( ! preg_match('/^[a-z\d.]{5,}$/Di', $_POST['facebook']))
				{
					$errors[] = __('La URL de facebook no es correcta.', FALSE);
					$view->assign('estado_facebook', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['facebook']) != Utils::prop($model_usuario->perfil(), 'facebook', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->facebook = trim($_POST['facebook']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_facebook', 1);
					}
				}
			}

			// Verificamos la URL de facebook.
			if (isset($_POST['twitter']) && ! empty($_POST['twitter']))
			{
				$view->assign('twitter', trim($_POST['twitter']));
				// Verificamos el formato.
				if ( ! preg_match('/^[a-z\d.]{5,}$/Di', $_POST['twitter']))
				{
					$errors[] = __('La URL de twitter no es correcta.', FALSE);
					$view->assign('estado_twitter', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['twitter']) != Utils::prop($model_usuario->perfil(), 'twitter', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->twitter = trim($_POST['twitter']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_twitter', 1);
					}
				}
			}

			// Validación de checkboxes
			$me_gustaria_listado = array(
				'hacer_amigos',
				'conocer_gente_intereses',
				'conocer_gente_negocios',
				'encontrar_pareja',
				'de_todo',
				'tatuajes',
				'piercings',
			);
			foreach ($me_gustaria_listado as $key)
			{
				$checked = isset($_POST[$key]);

				if (Utils::prop($model_usuario->perfil(), $key, NULL) != $checked)
				{
					// Actualizo nombre.
					$model_usuario->perfil()->$key = $checked;

					$view->assign('success', __('Datos actualizados correctamente.', FALSE));
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
					$errors[] = __('La altura ingresada no es correcta.', FALSE);
					$view->assign('estado_mi_altura', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if ($altura != Utils::prop($model_usuario->perfil(), 'mi_altura', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->mi_altura = $altura;

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
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
					$errors[] = __('El peso ingresado no es correcto.', FALSE);
					$view->assign('estado_mi_peso', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if ($peso != Utils::prop($model_usuario->perfil(), 'mi_peso', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->mi_peso = $peso;

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_mi_peso', 1);
					}
				}
			}

			// Validaciones de select's.
			$select_list = array(
				'estado_civil' => array(__('El estado civil es incorrecto.', FALSE), array('soltero', 'novio', 'casado', 'divorciado', 'viudo', 'en_algo')),
				'hijos' => array(__('La información sobre sus hijos es incorrecta.', FALSE), array('no_tengo', 'algun_dia', 'no_son_lo_mio', 'tengo_vivo_con_ellos', 'tengo_no_vivo_con_ellos')),
				'vivo_con' => array(__('La información sobre quien vive es incorrecta.', FALSE), array('solo', 'mis_padres', 'mi_pareja', 'con_amigos', 'otro')),
				'color_pelo' => array(__('El color de pelo provisto es incorrecto.', FALSE), array('negro', 'castano_oscuro', 'castano_claro', 'rubio', 'pelirrojo', 'gris', 'verde', 'naranja', 'morado', 'azul', 'canoso', 'tenido', 'rapado', 'calvo')),
				'color_ojos' => array(__('El color de ojos provisto es incorrecto.', FALSE), array('negros', 'marrones', 'celestes', 'verdes', 'grises')),
				'complexion' => array(__('La complexión provista es incorrecta.', FALSE), array('delgado', 'atletico', 'normal', 'kilos_mas', 'corpulento')),
				'mi_dieta' => array(__('La dieta provista es incompleta.', FALSE), array('vegetariana', 'lacto_vegetariana', 'organica', 'de_todo', 'comida_basura')),
				'fumo' => array(__('La información sobre cuanto fuma es incorrecta.', FALSE), array('no', 'casualmente', 'socialmente', 'regularmente', 'mucho')),
				'tomo_alcohol' => array(__('La información sobre su consumo de alcohol es incorrecta.', FALSE), array('no', 'casualmente', 'socialmente', 'regularmente', 'mucho')),

				'estudios' => array(__('La información sobre sus estudios en incorrecta.', FALSE), array('sin_estudios', 'primario_en_curso', 'primario_completo', 'secundario_en_curso', 'secundario_completo', 'terciario_en_curso', 'terciario_completo', 'universitario_en_curso', 'universitario_completo', 'post_grado_en_curso', 'post_grado_completo')),
				'idioma_espanol' => array(__('La información sobre el idioma español es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_ingles' => array(__('La información sobre el idioma inglés es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_portugues' => array(__('La información sobre el idioma portugués es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_frances' => array(__('La información sobre el idioma francés es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_italiano' => array(__('La información sobre el idioma italiano es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_aleman' => array(__('La información sobre el idioma alemán es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),
				'idioma_otro' => array(__('La información sobre el idioma otro es incorrecta.', FALSE), array('sin_conocimiento', 'basico', 'intermedio', 'fluido', 'nativo')),

				'sector' => array(__('El sector seleccionado es incorrecto.', FALSE), array('sin_respuesta', 'abastecimiento', 'administracion', 'apoderado_aduanal', 'asesoria_en_comercio_exterior', 'asesoria_legal_internacional', 'asistente_de_trafico', 'auditoria', 'calidad', 'call_center', 'capacitacion_comercio_exterior', 'comercial', 'comercio_exterior', 'compras', 'compras_internacionalesimportacion', 'comunicacion_social', 'comunicaciones_externas', 'comunicaciones_internas', 'consultoria', 'consultorias_comercio_exterior', 'contabilidad', 'control_de_gestion', 'creatividad', 'diseno', 'distribucion', 'ecommerce', 'educacion', 'finanzas', 'finanzas_internacionales', 'gerencia_direccion_general', 'impuestos', 'ingenieria', 'internet', 'investigacion_y_desarrollo', 'jovenes_profesionales', 'legal', 'logistica', 'mantenimiento', 'marketing', 'medio_ambiente', 'mercadotecnia_internacional', 'multimedia', 'otra', 'pasantias', 'periodismo', 'planeamiento', 'produccion', 'produccion_e_ingenieria', 'recursos_humanos', 'relaciones_institucionales_publicas', 'salud', 'seguridad_industrial', 'servicios', 'soporte_tecnico', 'tecnologia', 'tecnologias_de_la_informacion', 'telecomunicaciones', 'telemarketing', 'traduccion', 'transporte', 'ventas', 'ventas_internacionalesexportacion')),

				'nivel_ingresos' => array(__('El nivel de ingresos seleccionado no es correcto.', FALSE), array('sin_ingresos', 'bajos', 'intermedios', 'altos')),
			);
			foreach ($select_list as $key => $datos)
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

							$view->assign('success', __('Datos actualizados correctamente.', FALSE));
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
				if ( ! preg_match('/^[a-zA-Z0-9 ]{4,60}$/D', $_POST['profesion']))
				{
					$errors[] = __('La profesión seleccionada no es correcta.', FALSE);
					$view->assign('estado_profesion', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['profesion']) != Utils::prop($model_usuario->perfil(), 'profesion', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->profesion = trim($_POST['profesion']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
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
				if ( ! preg_match('/^[a-zA-Z0-9 ]{4,60}$/D', $_POST['empresa']))
				{
					$errors[] = __('La empresa seleccionada no es correcta.', FALSE);
					$view->assign('estado_empresa', -1);
				}
				else
				{
					// Verifico no sea el actual.
					if (trim($_POST['empresa']) != Utils::prop($model_usuario->perfil(), 'empresa', NULL))
					{
						// Actualizo nombre.
						$model_usuario->perfil()->empresa = trim($_POST['empresa']);

						$view->assign('success', __('Datos actualizados correctamente.', FALSE));
						$view->assign('estado_empresa', 1);
					}
				}
			}

			// Descripciones de gustos y demás de textareas.
			$textareas = array(
				'intereses_personales' => __('Los intereses personales son incorrectos.', FALSE),
				'habilidades_profesionales' => __('Las habilidades profesionales son incorrectas.', FALSE),
				'mis_intereses' => __('Los intereses son incorrectos.', FALSE),
				'hobbies' => __('Los hobbies son incorrectos.', FALSE),
				'series_tv_favoritas' => __('Las series de TV favoritas son incorrectas.', FALSE),
				'musica_favorita' => __('La música favorita es incorrecta.', FALSE),
				'deportes_y_equipos_favoritos' => __('Los deportes y equipos favoritos son incorrectos.', FALSE),
				'libros_favoritos' => __('Los libros favoritos son incorrectos.', FALSE),
				'peliculas_favoritas' => __('Las películas favoritas son incorrectas.', FALSE),
				'comida_favorita' => __('La comida favorita es incorrecta.', FALSE),
				'mis_heroes' => __('Los héroes son incorrectos.', FALSE)
			);
			foreach ($textareas as $key => $value)
			{
				if (isset($_POST[$key]) && ! empty($_POST[$key]))
				{
					$view->assign($key, trim($_POST[$key]));
					// Verificamos el formato.
					if ( ! preg_match('/^[a-z0-9\.,:\'"\s]{6,400}$/Di', $_POST[$key]))
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

							$view->assign('success', __('Datos actualizados correctamente.', FALSE));
							$view->assign('estado_'.$key, 1);
						}
					}
				}
			}
			unset($textareas);

			$view->assign('error', $errors);
		}

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('perfil'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Bloqueos a los usuarios.
	 */
	public function action_bloqueados()
	{
		// Asignamos el título.
		$this->template->assign('title', __('Cuenta - Bloqueos', FALSE));

		// Cargamos la vista.
		$view = View::factory('cuenta/bloqueos');

		// Asigno parámetros.
		$view->assign('email', Usuario::usuario()->email);
		$view->assign('usuario', '');
		$view->assign('error_usuario', FALSE);

		if (Request::method() == 'POST')
		{
			// Obtengo el usuario.
			$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';

			// Asigno a la vista.
			$view->assign('usuario', $usuario);

			$error = FALSE;

			// Verifico el nick
			if ( ! preg_match('/^[a-zA-Z0-9]{4,16}$/D', $usuario))
			{
				$view->assign('error_usuario', __('El usuario ingresado no es válido.', FALSE));
				$error = TRUE;
			}

			if ( ! $error)
			{
				// Verifico exista.
				$model_usuario = new Model_Usuario;
				if ( ! $model_usuario->exists_nick($usuario))
				{
					$view->assign('error_usuario', __('El usuario ingresado no es válido.', FALSE));
					$error = TRUE;
				}
				else
				{
					$model_usuario->load_by_nick($usuario);
				}
			}

			if ( ! $error)
			{
				// Verifico no sea uno mismo.
				if ($model_usuario->id === Usuario::$usuario_id)
				{
					$view->assign('error_usuario', __('El usuario ingresado no es válido.', FALSE));
					$error = TRUE;
				}
			}

			if ( ! $error)
			{
				// Verifico estado.
				if ($model_usuario->estado !== Model_Usuario::ESTADO_ACTIVA)
				{
					$view->assign('error_usuario', __('El usuario ingresado no es válido.', FALSE));
					$error = TRUE;
				}
			}

			if ( ! $error)
			{
				// Verifico no se encuentre bloqueado.
				if (Usuario::usuario()->esta_bloqueado($model_usuario->id))
				{
					$view->assign('error_usuario', __('El usuario ingresado no es válido.', FALSE));
					$error = TRUE;
				}
			}

			if ( ! $error)
			{
				// Bloqueo el usuario.
				Usuario::usuario()->bloquear($model_usuario->id);

				// Envío el suceso.
				$model_suceso = new Model_Suceso;
				if (Usuario::$usuario_id != $model_usuario->id)
				{
					$model_suceso->crear($model_usuario->id, 'usuario_bloqueo', TRUE, Usuario::$usuario_id, $model_usuario->id, 0);
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_bloqueo', FALSE, Usuario::$usuario_id, $model_usuario->id, 0);
				}
				else
				{
					$model_suceso->crear($model_usuario->id, 'usuario_bloqueo', FALSE, Usuario::$usuario_id, $model_usuario->id, 0);
				}

				// Envío notificación.
				add_flash_message(FLASH_SUCCESS, __('El usuario fue bloqueado correctamente.', FALSE));
				Request::redirect('/cuenta/bloqueados');
			}
		}

		// Cargo los bloqueos.
		$bloqueos = Usuario::usuario()->bloqueos();

		foreach ($bloqueos as $k => $v)
		{
			$bloqueos[$k] = $v->as_array();
		}
		$view->assign('bloqueos', $bloqueos);

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('bloqueados'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Administración de la contraseña de acceso a la cuenta.
	 */
	public function action_password()
	{
		// Asignamos el título.
		$this->template->assign('title', __('Cuenta - Contraseña', FALSE));

		// Cargamos la vista.
		$view = View::factory('cuenta/password');

		// Cargamos el usuario actual.
		$model_usuario = Usuario::usuario();
		$view->assign('email', $model_usuario->email);

		// Valores por defecto.
		$view->assign('error', NULL);
		$view->assign('error_actual', NULL);
		$view->assign('error_password', NULL);
		$view->assign('error_c_password', NULL);

		if (Request::method() == 'POST')
		{
			// Verificamos que estén los datos.
			if (( ! isset($_POST['current']) || empty($_POST['current'])) ||
				( ! isset($_POST['password']) || empty($_POST['password'])) ||
				( ! isset($_POST['cpassword']) || empty($_POST['cpassword'])))
			{
				if ( ! isset($_POST['current']) || empty($_POST['current']))
				{
					$view->assign('error', __('Debe rellenar todos los datos.', FALSE));
					$view->assign('error_current', TRUE);
				}

				if ( ! isset($_POST['password']) || empty($_POST['password']))
				{
					$view->assign('error', __('Debe rellenar todos los datos.', FALSE));
					$view->assign('error_password', TRUE);
				}

				if ( ! isset($_POST['cpassword']) || empty($_POST['cpassword']))
				{
					$view->assign('error', __('Debe rellenar todos los datos.', FALSE));
					$view->assign('error_cpassword', TRUE);
				}
			}
			else
			{
				// Comprobamos el formato
				if ( ! preg_match('/^[a-zA-Z0-9]{6,20}$/D', $_POST['password']) || $_POST['password'] != $_POST['cpassword'])
				{
					if ($_POST['password'] != $_POST['cpassword'])
					{
						$view->assign('error', __('Las contraseñas ingresadas no coinciden.', FALSE));
						$view->assign('error_password', TRUE);
					}
					else
					{
						$view->assign('error', __('La contraseña debe tener entre 6 y 20 caracteres alphanumericos.', FALSE));
						$view->assign('error_password', TRUE);
					}
				}
				else
				{
					// Verificamos la contraseña.
					$enc = new Phpass(8, FALSE);

					if ( ! $enc->check_password($_POST['current'], $model_usuario->password))
					{
						$view->assign('error', __('La contraseña es incorrecta.', FALSE));
						$view->assign('error_current', TRUE);
					}
					else
					{
						// Actualizo la contraseña.
						$model_usuario->actualizar_contrasena(trim($_POST['password']));
						$view->assign('success', __('La contraseña se ha actualizado correctamente.', FALSE));
					}
				}
			}
		}

		// Menú.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('password'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Administración de los nicks del usuario.
	 */
	public function action_nick()
	{
		// Asignamos el título.
		$this->template->assign('title', __('Cuenta - Nick', FALSE));

		// Cargamos la vista.
		$view = View::factory('cuenta/nick');

		// Cargamos el usuario actual.
		$model_usuario = Usuario::usuario();

		$view->assign('email', $model_usuario->email);

		// Valores por defecto.
		$view->assign('nick_actual', $model_usuario->nick);
		$view->assign('nick', '');
		$view->assign('error_nick', NULL);
		$view->assign('error_password', NULL);

		// Listado de nick's reservados.
		$nicks_reservados = $model_usuario->nicks();

		//TODO: Mantener nick's para evitar borrar y poder cambiar.
		//TODO: Mantener nicks por un tiempo limitado.

		// Calculo cuanto hace que cambio su nick.
		if (count($nicks_reservados) !== 0)
		{
			$fecha_cambio = Usuario::usuario()->ultimo_cambio_nick()->format('U');
		}
		else
		{
			// Obtengo fecha de registro.
			$fecha_cambio = Usuario::usuario()->registro->format('U');
		}

		// 5184000 === 2 meses.
		$view->assign('tiempo_cambio', $fecha_cambio + 5184000 - date('U'));

		if (Request::method() == 'POST')
		{
			if (( ! isset($_POST['nick']) || empty($_POST['nick'])) || ( ! isset($_POST['password']) || empty($_POST['password'])))
			{
				// Verificamos los datos
				if ( ! isset($_POST['nick']) || empty($_POST['nick']))
				{
					$view->assign('error_nick', __('Debe ingresar un nuevo nick.', FALSE));
				}
				else
				{
					$view->assign('nick', $_POST['nick']);
				}

				if ( ! isset($_POST['password']) || empty($_POST['password']))
				{
					$view->assign('error_password', __('Debe ingresar su contraseña para validar el cambio.', FALSE));
				}
			}
			else
			{
				$nick = $_POST['nick'];
				$password = $_POST['password'];

				$view->assign('nick', $nick);

				// Verifico longitud Nick.
				if ( ! preg_match('/^[a-zA-Z0-9]{4,20}$/D', $nick))
				{
					$view->assign('error_nick', __('El nick debe tener entre 4 y 20 caracteres alphanuméricos.', FALSE));
				}
				else
				{
					// Verifico la contraseña.
					$enc = new Phpass(8, FALSE);

					if ( ! $enc->check_password($password, $model_usuario->password))
					{
						$view->assign('error_password', __('La contraseña es incorrecta.', FALSE));
					}
					else
					{
						// Cargo usuarios bloqueados.
						$nicks_bloqueados = unserialize(Utils::configuracion()->get_default('usuarios_bloqueados', 'a:0:{}'));

						// Verifico que no exista el nick.
						if (in_array($nick, $nicks_bloqueados) || $model_usuario->exists_nick($nick))
						{
							$view->assign('error_nick', __('El nick no está disponible.', FALSE));
						}
						else
						{
							// Verifico la cantidad de nick's.
							if (count($nicks_reservados) >= 3)
							{
								$view->assign('error_nick', __('Has superado el máximo de nick\'s utilizados.', FALSE));
							}
							else
							{
								// Verifico tiempo de cambio.
								if ($fecha_cambio + 5184000 - date('U') <= 0)
								{
									// Actualizamos.
									$model_usuario->cambiar_nick($nick);

									// Recargo los nick's reservados.
									$nicks_reservados = $model_usuario->nicks();

									// Envio el suceso.
									$model_suceso = new Model_Suceso;
									//TODO: Implementar campos alphanumericos.
									$model_suceso->crear(Usuario::$usuario_id, 'usuario_cambio_nick', Usuario::$usuario_id);

									// Informamos resultado.
									$view->assign('success', __('El nick se ha actualizado correctamente.', FALSE));
									$view->assign('nick', '');
									$view->assign('nick_actual', $nick);
								}
								else
								{
									$view->assign('error_nick', __('Solo puedes cambiar tu nick cada 2 meses.', FALSE));
								}
							}
						}
					}
				}
			}
		}

		// Cargo listado de nicks.
		$view->assign('nicks', $nicks_reservados);

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('nick'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Liberamos el nick del usuario.
	 * @param string $nick Nick a liberar
	 */
	public function action_eliminar_nick($nick)
	{
		// Verifico el formato.
		if ( ! preg_match('/^[a-zA-Z0-9]{4,20}$/D', $nick))
		{
			add_flash_message(FLASH_ERROR, __('El nick que desea liberar no es correcto.', FALSE));
			Request::redirect('/cuenta/nick');
		}

		// Verifico si es del usuario.
		if ( ! in_array($nick, Usuario::usuario()->nicks()))
		{
			add_flash_message(FLASH_ERROR, __('El nick que desea liberar no es correcto.', FALSE));
			Request::redirect('/cuenta/nick');
		}

		// Elimino el nick.
		Usuario::usuario()->eliminar_nick($nick);

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('El nick se ha liberado correctamente.', FALSE));
		Request::redirect('/cuenta/nick');
	}

	/**
	 * Seleccionamos un nick a utilizar de nuestra lista.
	 * @param string $nick Nick que vamos a utilizar.
	 */
	public function action_utilizar_nick($nick)
	{
		// Verifico el formato.
		if ( ! preg_match('/^[a-zA-Z0-9]{4,20}$/D', $nick))
		{
			add_flash_message(FLASH_ERROR, __('El nick que desea utilizar no es correcto.', FALSE));
			Request::redirect('/cuenta/nick');
		}

		// Verifico si es del usuario.
		if ( ! in_array($nick, Usuario::usuario()->nicks()))
		{
			add_flash_message(FLASH_ERROR, __('El nick que desea utilizar no es correcto.', FALSE));
			Request::redirect('/cuenta/nick');
		}

		// Elimino el nick y lo asigno nuevamente.
		Usuario::usuario()->eliminar_nick($nick);
		Usuario::usuario()->cambiar_nick($nick);

		// Envio el suceso.
		$model_suceso = new Model_Suceso;
		//TODO: Implementar campos alphanumericos.
		$model_suceso->crear(Usuario::$usuario_id, 'usuario_cambio_nick', Usuario::$usuario_id);

		// Informo el resultado.
		add_flash_message(FLASH_SUCCESS, __('El nick se ha actualizado correctamente.', FALSE));
		Request::redirect('/cuenta/nick');
	}

	/**
	 * Visualización de los avisos que tiene el usuario.
	 */
	public function action_avisos()
	{
		// Asignamos el título.
		$this->template->assign('title', __('Cuenta - Avisos', FALSE));

		// Cargamos la vista.
		$view = View::factory('cuenta/avisos');

		// Cargamos el usuario actual.
		$model_usuario = Usuario::usuario();

		// Cargo listado de avisos
		$avisos = $model_usuario->avisos(array(Model_Usuario_Aviso::ESTADO_NUEVO, Model_Usuario_Aviso::ESTADO_VISTO));

		// Proceso para obtener información.
		$lst = array();
		foreach ($avisos as $v)
		{
			$a = $v->as_array();
			$a['moderador'] = $v->moderador()->as_array();
			$lst[] = $a;
		}
		// Listado de advertencias.
		$view->assign('advertencias', $lst);
		unset($lst);

		// Información del usuario del que se ven las advertencias.
		$view->assign('usuario', $model_usuario->as_array());

		// Menu.
		$this->template->assign('master_bar', parent::base_menu('inicio'));
		$this->template->assign('top_bar', $this->submenu('avisos'));

		// Asignamos la vista.
		$this->template->assign('contenido', $view->parse());
	}

	/**
	 * Marcamos un aviso como leído.
	 * @param int $id ID del aviso.
	 */
	public function action_aviso_leido($id)
	{
		$id = (int) $id;

		// Cargo aviso.
		$model_aviso = new Model_Usuario_Aviso($id);

		// Verifico existencia.
		if ( ! $model_aviso->existe())
		{
			add_flash_message(FLASH_ERROR, __('El aviso que deseas marcar como visto no se encuentra disponible.', FALSE));
			Request::redirect('/cuenta/avisos');
		}

		// Verifico sea del usuario.
		if ($model_aviso->usuario_id !== Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El aviso que deseas marcar como visto no se encuentra disponible.', FALSE));
			Request::redirect('/cuenta/avisos');
		}

		// Verifico el estado.
		if ($model_aviso->estado !== Model_Usuario_Aviso::ESTADO_NUEVO)
		{
			add_flash_message(FLASH_ERROR, __('El aviso que deseas marcar como visto no se encuentra disponible.', FALSE));
			Request::redirect('/cuenta/avisos');
		}

		// Marco como leído.
		$model_aviso->actualizar_campo('estado', Model_Usuario_Aviso::ESTADO_VISTO);

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, __('Aviso marcado como visto correctamente.', FALSE));
		Request::redirect('/cuenta/avisos');
	}

	/**
	 * Ocultamos un aviso.
	 * @param int $id ID del aviso.
	 */
	public function action_borrar_aviso($id)
	{
		$id = (int) $id;

		// Cargo aviso.
		$model_aviso = new Model_Usuario_Aviso($id);

		// Verifico existencia.
		if ( ! $model_aviso->existe())
		{
			add_flash_message(FLASH_ERROR, __('El aviso que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/cuenta/avisos');
		}

		// Verifico sea del usuario.
		if ($model_aviso->usuario_id !== Usuario::$usuario_id)
		{
			add_flash_message(FLASH_ERROR, __('El aviso que deseas borrar no se encuentra disponible.', FALSE));
			Request::redirect('/cuenta/avisos');
		}

		// Marco como oculto.
		$model_aviso->actualizar_campo('estado', Model_Usuario_Aviso::ESTADO_OCULTO);

		// Informo resultado.
		add_flash_message(FLASH_SUCCESS, __('Aviso borrado correctamente.', FALSE));
		Request::redirect('/cuenta/avisos');
	}

}
