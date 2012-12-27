{if="isset($origen) || count($general) > 0 || count($vida_personal) > 0 || count($idioma) > 0 || count($datos_profesionales) > 0 || count($como_es) > 0 || count($habitos_personales) > 0 || count($intereses_y_preferencias) > 0"}
<h3 class="title">{@Información de@} {$usuario.nick}:</h3>
{if="count($general) > 0 || isset($origen)"}
<table class="table table-striped table-bordered">
	<tbody>
		{if="isset($origen)"}<tr>
			<th>{@País@}</th>
			<td>
				{$origen}
			</td>
		</tr>{/if}
		{if="isset($general.nombre)"}<tr>
			<th>{@Nombre Completo@}</th>
			<td>
				{$general.nombre}
			</td>
		</tr>{/if}
		{if="isset($general.web)"}<tr>
			<th>{@Sitio Web@}</th>
			<td>
				{$general.web}
			</td>
		</tr>{/if}
		{if="isset($general.twitter)"}<tr>
			<th>{@Twitter@}</th>
			<td>
				<a href="http://www.twitter.com/{$general.twitter}">{$general.twitter}</a>
			</td>
		</tr>{/if}
		{if="isset($general.facebook)"}<tr>
			<th>{@Facebook@}</th>
			<td>
				<a href="http://www.twitter.com/{$general.facebook}">{$general.facebook}</a>
			</td>
		</tr>{/if}
		{if="isset($general.estudios)"}<tr>
			<th>{@Estudios@}</th>
			<td>
				{if="$general.estudios == 'sin_estudios'"}{@Sin Estudios@}{/if}
				{if="$general.estudios == 'primario_en_curso'"}{@Primario en curso@}{/if}
				{if="$general.estudios == 'primario_completo'"}{@Primario completo@}{/if}
				{if="$general.estudios == 'secundario_en_curso'"}{@Secundario en curso@}{/if}
				{if="$general.estudios == 'secundario_completo'"}{@Secundario completo@}{/if}
				{if="$general.estudios == 'terciario_en_curso'"}{@Terciario en curso@}{/if}
				{if="$general.estudios == 'terciario_completo'"}{@Terciario completo@}{/if}
				{if="$general.estudios == 'universitario_en_curso'"}{@Universitario en curso@}{/if}
				{if="$general.estudios == 'universitario_completo'"}{@Universitario completo@}{/if}
				{if="$general.estudios == 'post_grado_en_curso'"}{@Post-grado en curso@}{/if}
				{if="$general.estudios == 'post_grado_completo'"}{@Post-grado completo@}{/if}
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{if="count($vida_personal) > 0"}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th colspan="2">Vida personal</th>
		</tr>
	</thead>
	<tbody>
		{if="isset($vida_personal.hacer_amigos) || isset($vida_personal.conocer_gente_intereses) || isset($vida_personal.conocer_gente_negocios) || isset($vida_personal.encontrar_pareja) || isset($vida_personal.de_todo)"}<tr>
			<th>{@Le gustaría@}</th>
			<td>
				<ul>
					{if="isset($vida_personal.hacer_amigos)"}<li>Hacer amigos</li>{/if}
					{if="isset($vida_personal.conocer_gente_intereses)"}<li>Conocer gente con mis intereses</li>{/if}
					{if="isset($vida_personal.conocer_gente_negocios)"}<li>Conocer gente para negocios</li>{/if}
					{if="isset($vida_personal.encontrar_pareja)"}<li>Encontrar pareja</li>{/if}
					{if="isset($vida_personal.de_todo)"}<li>De todo</li>{/if}
				</ul>
			</td>
		</tr>{/if}
		{if="isset($vida_personal.estado_civil)"}<tr>
			<th>{@Estado civil@}</th>
			<td>
				{if="$vida_personal.estado_civil == 'soltero'"}{@Soltero/a@}{/if}
				{if="$vida_personal.estado_civil == 'novio'"}{@Con novio/a@}{/if}
				{if="$vida_personal.estado_civil == 'casado'"}{@Casado/a@}{/if}
				{if="$vida_personal.estado_civil == 'divorciado'"}{@Divorciado/a@}{/if}
				{if="$vida_personal.estado_civil == 'viudo'"}{@Viudo/a@}{/if}
				{if="$vida_personal.estado_civil == 'en_algo'"}{@En algo...@}{/if}
			</td>
		</tr>{/if}
		{if="isset($vida_personal.hijos)"}<tr>
			<th>{@Hijos@}</th>
			<td>
				{if="$vida_personal.hijos == 'no_tengo'"}{@No tengo@}{/if}
				{if="$vida_personal.hijos == 'algun_dia'"}{@Algún día@}{/if}
				{if="$vida_personal.hijos == 'no_son_lo_mio'"}{@No son lo mío@}{/if}
				{if="$vida_personal.hijos == 'tengo_vivo_con_ellos'"}{@Tengo, vivo con ellos@}{/if}
				{if="$vida_personal.hijos == 'tengo_no_vivo_con_ellos'"}{@Tengo, no vivo con ellos@}{/if}
			</td>
		</tr>{/if}
		{if="isset($vida_personal.vivo_con)"}<tr>
			<th>{@Vive con@}</th>
			<td>
				{if="$vida_personal.vivo_con == 'solo'"}{@Sólo@}{/if}
				{if="$vida_personal.vivo_con == 'mis_padres'"}{@Con mis padres@}{/if}
				{if="$vida_personal.vivo_con == 'mi_pareja'"}{@Con mi pareja@}{/if}
				{if="$vida_personal.vivo_con == 'con_amigos'"}{@Con amigos@}{/if}
				{if="$vida_personal.vivo_con == 'otro'"}{@Otro@}{/if}
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{if="count($idioma) > 0"}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th colspan="2">{@Idiomas@}</th>
		</tr>
	</thead>
	<tbody>
		{if="isset($idioma.idioma_espanol)"}<tr>
			<th>{@Español@}</th>
			<td>
				{if="$idioma.idioma_espanol == ''"}{@Sin Respuesta@}{/if}
				{if="$idioma.idioma_espanol == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_espanol == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_espanol == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_espanol == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_espanol == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($idioma.idioma_ingles)"}<tr>
			<th>{@Inglés@}</th>
			<td>
				{if="$idioma.idioma_ingles == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_ingles == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_ingles == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_ingles == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_ingles == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($idioma.idioma_portugues)"}<tr>
			<th>{@Portugués@}</th>
			<td>
				{if="$idioma.idioma_portugues == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_portugues == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_portugues == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_portugues == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_portugues == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($idioma.idioma_frances)"}<tr>
			<th>{@Francés@}</th>
			<td>
				{if="$idioma.idioma_frances == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_frances == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_frances == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_frances == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_frances == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($idioma.idioma_italiano)"}<tr>
			<th>{@Italiano@}</th>
			<td>
				{if="$idioma.idioma_italiano == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_italiano == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_italiano == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_italiano == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_italiano == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($idioma.idioma_aleman)"}<tr>
			<th>{@Alemán@}</th>
			<td>
				{if="$idioma.idioma_aleman == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_aleman == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_aleman == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_aleman == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_aleman == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($idioma.idioma_otro)"}<tr>
			<th>{@Otro@}</th>
			<td>
				{if="$idioma.idioma_otro == 'sin_conocimiento'"}{@Sin conocimiento@}{/if}
				{if="$idioma.idioma_otro == 'basico'"}{@Básico@}{/if}
				{if="$idioma.idioma_otro == 'intermedio'"}{@Intermedio@}{/if}
				{if="$idioma.idioma_otro == 'fluido'"}{@Fluido@}{/if}
				{if="$idioma.idioma_otro == 'nativo'"}{@Nativo@}{/if}
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{if="count($datos_profesionales) > 0"}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th colspan="2">Datos profesionales</th>
		</tr>
	</thead>
	<tbody>
		{if="isset($datos_profesionales.profesion)"}<tr>
			<th>{@Profesión@}</th>
			<td>
				{$datos_profesionales.profesion}
			</td>
		</tr>{/if}
		{if="isset($datos_profesionales.empresa)"}<tr>
			<th>{@Empresa@}</th>
			<td>
				{$datos_profesionales.empresa}
			</td>
		</tr>{/if}
		{if="isset($datos_profesionales.sector)"}<tr>
			<th>{@Profesión@}</th>
			<td>
				{if="$datos_profesionales.sector == 'abastecimiento'"}{@Abastecimiento@}{/if}
				{if="$datos_profesionales.sector == 'administracion'"}{@Administración@}{/if}
				{if="$datos_profesionales.sector == 'apoderado_aduanal'"}{@Apoderado Aduanal@}{/if}
				{if="$datos_profesionales.sector == 'asesoria_en_comercio_exterior'"}{@Asesoría en Comercio Exterior@}{/if}
				{if="$datos_profesionales.sector == 'asesoria_legal_internacional'"}{@Asesoría Legal Internacional@}{/if}
				{if="$datos_profesionales.sector == 'asistente_de_trafico'"}{@Asistente de Tráfico@}{/if}
				{if="$datos_profesionales.sector == 'auditoria'"}{@Auditoría@}{/if}
				{if="$datos_profesionales.sector == 'calidad'"}{@Calidad@}{/if}
				{if="$datos_profesionales.sector == 'call_center'"}{@Call Center@}{/if}
				{if="$datos_profesionales.sector == 'capacitacion_comercio_exterior'"}{@Capacitación Comercio Exterior@}{/if}
				{if="$datos_profesionales.sector == 'comercial'"}{@Comercial@}{/if}
				{if="$datos_profesionales.sector == 'comercio_exterior'"}{@Comercio Exterior@}{/if}
				{if="$datos_profesionales.sector == 'compras'"}{@Compras@}{/if}
				{if="$datos_profesionales.sector == 'compras_internacionalesimportacion'"}{@Compras Internacionales/Importación@}{/if}
				{if="$datos_profesionales.sector == 'comunicacion_social'"}{@Comunicación Social@}{/if}
				{if="$datos_profesionales.sector == 'comunicaciones_externas'"}{@Comunicaciones Externas@}{/if}
				{if="$datos_profesionales.sector == 'comunicaciones_internas'"}{@Comunicaciones Internas@}{/if}
				{if="$datos_profesionales.sector == 'consultoria'"}{@Consultoría@}{/if}
				{if="$datos_profesionales.sector == 'consultorias_comercio_exterior'"}{@Consultorías Comercio Exterior@}{/if}
				{if="$datos_profesionales.sector == 'contabilidad'"}{@Contabilidad@}{/if}
				{if="$datos_profesionales.sector == 'control_de_gestion'"}{@Control de Gestión@}{/if}
				{if="$datos_profesionales.sector == 'creatividad'"}{@Creatividad@}{/if}
				{if="$datos_profesionales.sector == 'diseno'"}{@Diseño@}{/if}
				{if="$datos_profesionales.sector == 'distribucion'"}{@Distribución@}{/if}
				{if="$datos_profesionales.sector == 'ecommerce'"}{@E-commerce@}{/if}
				{if="$datos_profesionales.sector == 'educacion'"}{@Educación@}{/if}
				{if="$datos_profesionales.sector == 'finanzas'"}{@Finanzas@}{/if}
				{if="$datos_profesionales.sector == 'finanzas_internacionales'"}{@Finanzas Internacionales@}{/if}
				{if="$datos_profesionales.sector == 'gerencia_direccion_general'"}{@Gerencia / Dirección General@}{/if}
				{if="$datos_profesionales.sector == 'impuestos'"}{@Impuestos@}{/if}
				{if="$datos_profesionales.sector == 'ingenieria'"}{@Ingeniería@}{/if}
				{if="$datos_profesionales.sector == 'internet'"}{@Internet@}{/if}
				{if="$datos_profesionales.sector == 'investigacion_y_desarrollo'"}{@Investigación y Desarrollo@}{/if}
				{if="$datos_profesionales.sector == 'jovenes_profesionales'"}{@Jóvenes Profesionales@}{/if}
				{if="$datos_profesionales.sector == 'legal'"}{@Legal@}{/if}
				{if="$datos_profesionales.sector == 'logistica'"}{@Logística@}{/if}
				{if="$datos_profesionales.sector == 'mantenimiento'"}{@Mantenimiento@}{/if}
				{if="$datos_profesionales.sector == 'marketing'"}{@Marketing@}{/if}
				{if="$datos_profesionales.sector == 'medio_ambiente'"}{@Medio Ambiente@}{/if}
				{if="$datos_profesionales.sector == 'mercadotecnia_internacional'"}{@Mercadotecnia Internacional@}{/if}
				{if="$datos_profesionales.sector == 'multimedia'"}{@Multimedia@}{/if}
				{if="$datos_profesionales.sector == 'otra'"}{@Otra@}{/if}
				{if="$datos_profesionales.sector == 'pasantias'"}{@Pasantías@}{/if}
				{if="$datos_profesionales.sector == 'periodismo'"}{@Periodismo@}{/if}
				{if="$datos_profesionales.sector == 'planeamiento'"}{@Planeamiento@}{/if}
				{if="$datos_profesionales.sector == 'produccion'"}{@Producción@}{/if}
				{if="$datos_profesionales.sector == 'produccion_e_ingenieria'"}{@Producción e Ingeniería@}{/if}
				{if="$datos_profesionales.sector == 'recursos_humanos'"}{@Recursos Humanos@}{/if}
				{if="$datos_profesionales.sector == 'relaciones_institucionales_publicas'"}{@Relaciones Institucionales / Públicas@}{/if}
				{if="$datos_profesionales.sector == 'salud'"}{@Salud@}{/if}
				{if="$datos_profesionales.sector == 'seguridad_industrial'"}{@Seguridad Industrial@}{/if}
				{if="$datos_profesionales.sector == 'servicios'"}{@Servicios@}{/if}
				{if="$datos_profesionales.sector == 'soporte_tecnico'"}{@Soporte Técnico@}{/if}
				{if="$datos_profesionales.sector == 'tecnologia'"}{@Tecnología@}{/if}
				{if="$datos_profesionales.sector == 'tecnologias_de_la_informacion'"}{@Tecnologías de la Información@}{/if}
				{if="$datos_profesionales.sector == 'telecomunicaciones'"}{@Telecomunicaciones@}{/if}
				{if="$datos_profesionales.sector == 'telemarketing'"}{@Telemarketing@}{/if}
				{if="$datos_profesionales.sector == 'traduccion'"}{@Traducción@}{/if}
				{if="$datos_profesionales.sector == 'transporte'"}{@Transporte@}{/if}
				{if="$datos_profesionales.sector == 'ventas'"}{@Ventas@}{/if}
				{if="$datos_profesionales.sector == 'ventas_internacionalesexportacion'"}{@Ventas Internacionales/Exportación@}{/if}
			</td>
		</tr>{/if}
		{if="isset($datos_profesionales.nivel_ingresos)"}<tr>
			<th>{@Nivel de ingresos@}</th>
			<td>
				{if="$datos_profesionales.nivel_ingresos == 'sin_ingresos'"}{@Sin ingresos@}{/if}
				{if="$datos_profesionales.nivel_ingresos == 'bajos'"}{@Bajos@}{/if}
				{if="$datos_profesionales.nivel_ingresos == 'intermedios'"}{@Intermedios@}{/if}
				{if="$datos_profesionales.nivel_ingresos == 'altos'"}{@Altos@}{/if}
			</td>
		</tr>{/if}
		{if="isset($datos_profesionales.intereses_personales)"}<tr>
			<th>{@Intereses personales@}</th>
			<td>
				{$datos_profesionales.intereses_personales}
			</td>
		</tr>{/if}
		{if="isset($datos_profesionales.habilidades_profesionales)"}<tr>
			<th>{@Habilidades profesionales@}</th>
			<td>
				{$datos_profesionales.habilidades_profesionales}
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{if="count($como_es) > 0"}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th colspan="2">{@¿Cómo es?@}</th>
		</tr>
	</thead>
	<tbody>
		{if="isset($como_es.mi_altura)"}<tr>
			<th>{@Mide@}</th>
			<td>
				{$como_es.mi_altura} cm
			</td>
		</tr>{/if}
		{if="isset($como_es.mi_peso)"}<tr>
			<th>{@Pesa@}</th>
			<td>
				{$como_es.mi_peso} kg
			</td>
		</tr>{/if}
		{if="isset($como_es.color_pelo)"}<tr>
			<th>{@Su color de pelo es@}</th>
			<td>
				{if="$como_es.color_pelo == 'negro'"}{@Negro@}{/if}
				{if="$como_es.color_pelo == 'castano_oscuro'"}{@Castaño oscuro@}{/if}
				{if="$como_es.color_pelo == 'castano_claro'"}{@Castaño claro@}{/if}
				{if="$como_es.color_pelo == 'rubio'"}{@Rubio@}{/if}
				{if="$como_es.color_pelo == 'pelirrojo'"}{@Pelirrojo@}{/if}
				{if="$como_es.color_pelo == 'gris'"}{@Gris@}{/if}
				{if="$como_es.color_pelo == 'verde'"}{@Verde@}{/if}
				{if="$como_es.color_pelo == 'naranja'"}{@Naranja@}{/if}
				{if="$como_es.color_pelo == 'morado'"}{@Morado@}{/if}
				{if="$como_es.color_pelo == 'azul'"}{@Azul@}{/if}
				{if="$como_es.color_pelo == 'canoso'"}{@Canoso@}{/if}
				{if="$como_es.color_pelo == 'tenido'"}{@Teñido@}{/if}
				{if="$como_es.color_pelo == 'rapado'"}{@Rapado@}{/if}
				{if="$como_es.color_pelo == 'calvo'"}{@Calvo@}{/if}
			</td>
		</tr>{/if}
		{if="isset($como_es.color_ojos)"}<tr>
			<th>{@Su color de ojos es@}</th>
			<td>
				{if="$como_es.color_ojos == 'negros'"}{@Negros@}{/if}
				{if="$como_es.color_ojos == 'marrones'"}{@Marrones@}{/if}
				{if="$como_es.color_ojos == 'celestes'"}{@Celestes@}{/if}
				{if="$como_es.color_ojos == 'verdes'"}{@Verdes@}{/if}
				{if="$como_es.color_ojos == 'grises'"}{@Grises@}{/if}
			</td>
		</tr>{/if}
		{if="isset($como_es.complexion)"}<tr>
			<th>{@Su físico es@}</th>
			<td>
				{if="$como_es.complexion == 'delgado'"}{@Delgado/a@}{/if}
				{if="$como_es.complexion == 'atletico'"}{@Atlético@}{/if}
				{if="$como_es.complexion == 'normal'"}{@Normal@}{/if}
				{if="$como_es.complexion == 'kilos_mas'"}{@Algunos kilos de más@}{/if}
				{if="$como_es.complexion == 'corpulento'"}{@Corpulento/a@}{/if}
			</td>
		</tr>{/if}
		{if="isset($como_es.tatuajes)"}<tr>
			<th></th>
			<td>
				Tiene tatuajes
			</td>
		</tr>{/if}
		{if="isset($como_es.piercings)"}<tr>
			<th></th>
			<td>
				Tiene piercings
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{if="count($habitos_personales) > 0"}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th colspan="2">{@Hábitos personales@}</th>
		</tr>
	</thead>
	<tbody>
		{if="isset($habitos_personales.mi_dieta)"}<tr>
			<th>{@Mi dieta es@}</th>
			<td>
				{if="$habitos_personales.mi_dieta == 'vegetariana'"}{@Vegetariana@}{/if}
				{if="$habitos_personales.mi_dieta == 'lacto_vegetariana'"}{@Lacto Vegetariana@}{/if}
				{if="$habitos_personales.mi_dieta == 'organica'"}{@Orgánica@}{/if}
				{if="$habitos_personales.mi_dieta == 'de_todo'"}{@De todo@}{/if}
				{if="$habitos_personales.mi_dieta == 'comida_basura'"}{@Comida basura@}{/if}
			</td>
		</tr>{/if}
		{if="isset($habitos_personales.fumo)"}<tr>
			<th>{@Fumo@}</th>
			<td>
				{if="$habitos_personales.fumo == 'no'"}{@No@}{/if}
				{if="$habitos_personales.fumo == 'casualemente'"}{@Casualmente@}{/if}
				{if="$habitos_personales.fumo == 'socialmente'"}{@Socialmente@}{/if}
				{if="$habitos_personales.fumo == 'regularmente'"}{@Regularmente@}{/if}
				{if="$habitos_personales.fumo == 'mucho'"}{@Mucho@}{/if}
			</td>
		</tr>{/if}
		{if="isset($habitos_personales.tomo_alcohol)"}<tr>
			<th>{@Tomo alcohol@}</th>
			<td>
				{if="$habitos_personales.tomo_alcohol == 'no'"}{@No@}{/if}
				{if="$habitos_personales.tomo_alcohol == 'casualmente'"}{@Casualmente@}{/if}
				{if="$habitos_personales.tomo_alcohol == 'socialmente'"}{@Socialmente@}{/if}
				{if="$habitos_personales.tomo_alcohol == 'regularmente'"}{@Regularmente@}{/if}
				{if="$habitos_personales.tomo_alcohol == 'mucho'"}{@Mucho@}{/if}
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{if="count($intereses_y_preferencias) > 0"}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th colspan="2">Intereses y preferencias</th>
		</tr>
	</thead>
	<tbody>
		{if="isset($intereses_y_preferencias.mis_intereses)"}<tr>
			<th>{@Intereses@}</th>
			<td>
				{$intereses_y_preferencias.mis_intereses}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.hobbies)"}<tr>
			<th>{@Hobbies@}</th>
			<td>
				{$intereses_y_preferencias.hobbies}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.series_tv_favoritas)"}<tr>
			<th>{@Series de TV favoritas@}</th>
			<td>
				{$intereses_y_preferencias.series_tv_favoritas}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.musica_favorita)"}<tr>
			<th>{@Música favorita@}</th>
			<td>
				{$intereses_y_preferencias.musica_favorita}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.deportes_y_equipos_favoritos)"}<tr>
			<th>{@Deportes y equipos favoritos@}</th>
			<td>
				{$intereses_y_preferencias.deportes_y_equipos_favoritos}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.libros_favoritos)"}<tr>
			<th>{@Libros favoritos@}</th>
			<td>
				{$intereses_y_preferencias.libros_favoritos}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.peliculas_favoritas)"}<tr>
			<th>{@Películas favoritas@}</th>
			<td>
				{$intereses_y_preferencias.peliculas_favoritas}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.comida_favorita)"}<tr>
			<th>{@Comida favorita@}</th>
			<td>
				{$intereses_y_preferencias.comida_favorita}
			</td>
		</tr>{/if}
		{if="isset($intereses_y_preferencias.mis_heroes)"}<tr>
			<th>{@Mis héroes son@}</th>
			<td>
				{$intereses_y_preferencias.mis_heroes}
			</td>
		</tr>{/if}
	</tbody>
</table>{/if}
{else}
<div class="alert"><strong>{$usuario.nick}</strong> {@aún no ha completado su perfil. Cuando lo haga podrás saber más sobre él.@}</div>
{/if}