<div class="row">
	<div class="span10">
		<form class="form-horizontal" method="POST" action="">

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}
			{if="isset($success)"}
			<div class="alert alert-success">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Felicitaciones: </strong>{$success}
			</div>
			{/if}

			<fieldset>
				<legend>M&aacute;s sobre mi</legend>

				<div class="control-group{if="$estado_nombre == -1"} error{elseif="$estado_nombre == 1"} success{/if}">
					<label class="control-label" for="nombre">Nombre Completo</label>
					<div class="controls">
						<input type="text" id="nick" name="nombre" value="{$nombre}" />
					</div>
				</div>

				<div class="control-group{if="$estado_mensaje_personal == -1"} error{elseif="$estado_mensaje_personal == 1"} success{/if}">
					<label class="control-label" for="mensaje_personal">Mensaje Personal</label>
					<div class="controls">
						<textarea id="mensaje_personal" name="mensaje_personal">{$mensaje_personal}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_web == -1"} error{elseif="$estado_web == 1"} success{/if}">
					<label class="control-label" for="web">Sitio Web</label>
					<div class="controls">
						<input type="text" id="web" name="web" value="{$web}" />
					</div>
				</div>

				<div class="control-group{if="$estado_facebook == -1"} error{elseif="$estado_facebook == 1"} success{/if}">
					<label class="control-label">Redes sociales</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">facebook.com/</span>
							<input class="span2" id="facebook" type="text" value="{$facebook}" name="facebook">
						</div>
					</div>
				</div>

				<div class="control-group{if="$estado_twitter == -1"} error{elseif="$estado_twitter == 1"} success{/if}">
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on">twitter.com/</span>
							<input class="span2" id="twitter" type="text" value="{$twitter}" name="twitter">
						</div>
					</div>
				</div>

				<div class="control-group{if="$estado_hacer_amigos == -1"} error{elseif="$estado_hacer_amigos == 1"} success{/if}">
					<label class="control-label">Me gustar&iacute;a</label>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="hacer_amigos" value="1"{if="$hacer_amigos"} checked="checked"{/if}>
							Hacer amigos
						</label>
					</div>
				</div>
				<div class="control-group{if="$estado_conocer_gente_intereses == -1"} error{elseif="$estado_conocer_gente_intereses == 1"} success{/if}">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="conocer_gente_intereses" value="1"{if="$conocer_gente_intereses"} checked="checked"{/if}>
							Conocer gente con mis intereses
						</label>
					</div>
				</div>
				<div class="control-group{if="$estado_conocer_gente_negocios == -1"} error{elseif="$estado_conocer_gente_negocios == 1"} success{/if}">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="conocer_gente_negocios" value="1"{if="$conocer_gente_negocios"} checked="checked"{/if}>
							Conocer gente para negocios
						</label>
					</div>
				</div>
				<div class="control-group{if="$estado_encontrar_pareja == -1"} error{elseif="$estado_encontrar_pareja == 1"} success{/if}">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="encontrar_pareja" value="1"{if="$encontrar_pareja"} checked="checked"{/if}>
							Encontrar pareja
						</label>
					</div>
				</div>
				<div class="control-group{if="$estado_de_todo == -1"} error{elseif="$estado_de_todo == 1"} success{/if}">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="de_todo" value="1"{if="$de_todo"} checked="checked"{/if}>
							De todo
						</label>
					</div>
				</div>

				<div class="control-group{if="$estado_estado_civil == -1"} error{elseif="$estado_estado_civil == 1"} success{/if}">
					<label class="control-label" for="estado_civil">Estado civil</label>
					<div class="controls">
						<select name="estado_civil" id="estado_civil">
							<option value=""{if="$estado_civil == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="soltero"{if="$estado_civil == 'soltero'"} selected="selected"{/if}>Soltero/a</option>
							<option value="novio"{if="$estado_civil == 'novio'"} selected="selected"{/if}>Con novio/a</option>
							<option value="casado"{if="$estado_civil == 'casado'"} selected="selected"{/if}>Casado/a</option>
							<option value="divorciado"{if="$estado_civil == 'divorciado'"} selected="selected"{/if}>Divorciado/a</option>
							<option value="viudo"{if="$estado_civil == 'viudo'"} selected="selected"{/if}>Viudo/a</option>
							<option value="en_algo"{if="$estado_civil == 'en_algo'"} selected="selected"{/if}>En algo...</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_hijos == -1"} error{elseif="$estado_hijos == 1"} success{/if}">
					<label class="control-label" for="hijos">Hijos</label>
					<div class="controls">
						<select name="hijos" id="hijos">
							<option value=""{if="$hijos == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="no_tengo"{if="$hijos == 'no_tengo'"} selected="selected"{/if}>No tengo</option>
							<option value="algun_dia"{if="$hijos == 'algun_dia'"} selected="selected"{/if}>Algún día</option>
							<option value="no_son_lo_mio"{if="$hijos == 'no_son_lo_mio'"} selected="selected"{/if}>No son lo mío</option>
							<option value="tengo_vivo_con_ellos"{if="$hijos == 'tengo_vivo_con_ellos'"} selected="selected"{/if}>Tengo, vivo con ellos</option>
							<option value="tengo_no_vivo_con_ellos"{if="$hijos == 'tengo_no_vivo_con_ellos'"} selected="selected"{/if}>Tengo, no vivo con ellos</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_vivo_con == -1"} error{elseif="$estado_vivo_con == 1"} success{/if}">
					<label class="control-label" for="vivo_con">Vivo con</label>
					<div class="controls">
						<select name="vivo_con" id="vivo_con">
							<option value=""{if="$vivo_con == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="solo"{if="$vivo_con == 'solo'"} selected="selected"{/if}>Sólo</option>
							<option value="mis_padres"{if="$vivo_con == 'mis_padres'"} selected="selected"{/if}>Con mis padres</option>
							<option value="mi_pareja"{if="$vivo_con == 'mi_pareja'"} selected="selected"{/if}>Con mi pareja</option>
							<option value="con_amigos"{if="$vivo_con == 'con_amigos'"} selected="selected"{/if}>Con amigos</option>
							<option value="otro"{if="$vivo_con == 'otro'"} selected="selected"{/if}>Otro</option>
						</select>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary" type="submit">Guardar</button>
				</div>
			</fieldset>

			<fieldset>
				<legend>Como soy</legend>

				<div class="control-group{if="$estado_mi_altura == -1"} error{elseif="$estado_mi_altura == 1"} success{/if}">
					<label class="control-label" for="mi_altura">Mi altura</label>
					<div class="controls">
						<div class="input-append">
							<input class="span2" id="mi_altura" name="mi_altura" value="{$mi_altura}" type="text">
							<span class="add-on">cm</span>
						</div>
					</div>
				</div>

				<div class="control-group{if="$estado_mi_peso == -1"} error{elseif="$estado_mi_peso == 1"} success{/if}">
					<label class="control-label" for="mi_peso">Mi peso</label>
					<div class="controls">
						<div class="input-append">
							<input class="span2" id="mi_peso" name="mi_peso" value="{$mi_peso}" type="text">
							<span class="add-on">kg</span>
						</div>
					</div>
				</div>

				<div class="control-group{if="$estado_color_pelo == -1"} error{elseif="$estado_color_pelo == 1"} success{/if}">
					<label class="control-label" for="color_pelo">Color de pelo</label>
					<div class="controls">
						<select name="color_pelo" id="color_pelo">
							<option value=""{if="$color_pelo == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="negro"{if="$color_pelo == 'negro'"} selected="selected"{/if}>Negro</option>
							<option value="castano_oscuro"{if="$color_pelo == 'castano_oscuro'"} selected="selected"{/if}>Casta&ntilde;o oscuro</option>
							<option value="castano_claro"{if="$color_pelo == 'castano_claro'"} selected="selected"{/if}>Casta&ntilde;o claro</option>
							<option value="rubio"{if="$color_pelo == 'rubio'"} selected="selected"{/if}>Rubio</option>
							<option value="pelirrojo"{if="$color_pelo == 'pelirrojo'"} selected="selected"{/if}>Pelirrojo</option>
							<option value="gris"{if="$color_pelo == 'gris'"} selected="selected"{/if}>Gris</option>
							<option value="verde"{if="$color_pelo == 'verde'"} selected="selected"{/if}>Verde</option>
							<option value="naranja"{if="$color_pelo == 'naranja'"} selected="selected"{/if}>Naranja</option>
							<option value="morado"{if="$color_pelo == 'morado'"} selected="selected"{/if}>Morado</option>
							<option value="azul"{if="$color_pelo == 'azul'"} selected="selected"{/if}>Azul</option>
							<option value="canoso"{if="$color_pelo == 'canoso'"} selected="selected"{/if}>Canoso</option>
							<option value="tenido"{if="$color_pelo == 'tenido'"} selected="selected"{/if}>Te&ntilde;ido</option>
							<option value="rapado"{if="$color_pelo == 'rapado'"} selected="selected"{/if}>Rapado</option>
							<option value="calvo"{if="$color_pelo == 'calvo'"} selected="selected"{/if}>Calvo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_color_ojos == -1"} error{elseif="$estado_color_ojos == 1"} success{/if}">
					<label class="control-label" for="color_ojos">Color de ojos</label>
					<div class="controls">
						<select name="color_ojos" id="color_ojos">
							<option value=""{if="$color_ojos == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="negros"{if="$color_ojos == 'negros'"} selected="selected"{/if}>Negros</option>
							<option value="marrones"{if="$color_ojos == 'marrones'"} selected="selected"{/if}>Marrones</option>
							<option value="celestes"{if="$color_ojos == 'celestes'"} selected="selected"{/if}>Celestes</option>
							<option value="verdes"{if="$color_ojos == 'verdes'"} selected="selected"{/if}>Verdes</option>
							<option value="grises"{if="$color_ojos == 'grises'"} selected="selected"{/if}>Grises</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_complexion == -1"} error{elseif="$estado_complexion == 1"} success{/if}">
					<label class="control-label" for="complexion">Complexi&oacute;n</label>
					<div class="controls">
						<select name="complexion" id="complexion">
							<option value=""{if="$complexion == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="delgado"{if="$complexion == 'delgado'"} selected="selected"{/if}>Delgado/a</option>
							<option value="atletico"{if="$complexion == 'atletico'"} selected="selected"{/if}>Atl&eacute;tico</option>
							<option value="normal"{if="$complexion == 'normal'"} selected="selected"{/if}>Normal</option>
							<option value="kilos_mas"{if="$complexion == 'kilos_mas'"} selected="selected"{/if}>Algunos kilos de m&aacute;s</option>
							<option value="corpulento"{if="$complexion == 'corpulento'"} selected="selected"{/if}>Corpulento/a</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_mi_dieta == -1"} error{elseif="$estado_mi_dieta == 1"} success{/if}">
					<label class="control-label" for="mi_dieta">Mi dieta es</label>
					<div class="controls">
						<select name="mi_dieta" id="mi_dieta">
							<option value=""{if="$mi_dieta == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="vegetariana"{if="$mi_dieta == 'vegetariana'"} selected="selected"{/if}>Vegetariana</option>
							<option value="lacto_vegetariana"{if="$mi_dieta == 'lacto_vegetariana'"} selected="selected"{/if}>Lacto Vegetariana</option>
							<option value="organica"{if="$mi_dieta == 'organica'"} selected="selected"{/if}>Org&aacute;nica</option>
							<option value="de_todo"{if="$mi_dieta == 'de_todo'"} selected="selected"{/if}>De todo</option>
							<option value="comida_basura"{if="$mi_dieta == 'comida_basura'"} selected="selected"{/if}>Comida basura</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_tatuajes == -1"} error{elseif="$estado_tatuajes == 1"} success{/if}">
					<label class="control-label" for="tatuajes">Tengo</label>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="tatuajes" id="tatuajes" value="1"{if="$tatuajes"} checked="checked"{/if}>Tatuajes
						</label>
					</div>
				</div>
				<div class="control-group{if="$estado_piercings == -1"} error{elseif="$estado_piercings == 1"} success{/if}">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="piercings" id="piercings" value="1"{if="$piercings"} checked="checked"{/if}>Piercings
						</label>
					</div>
				</div>

				<div class="control-group{if="$estado_fumo == -1"} error{elseif="$estado_fumo == 1"} success{/if}">
					<label class="control-label" for="fumo">Fumo</label>
					<div class="controls">
						<select name="fumo" value="fumo">
							<option value=""{if="$fumo == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="no"{if="$fumo == 'no'"} selected="selected"{/if}>No</option>
							<option value="casualemente"{if="$fumo == 'casualemente'"} selected="selected"{/if}>Casualmente</option>
							<option value="socialmente"{if="$fumo == 'socialmente'"} selected="selected"{/if}>Socialmente</option>
							<option value="regularmente"{if="$fumo == 'regularmente'"} selected="selected"{/if}>Regularmente</option>
							<option value="mucho"{if="$fumo == 'mucho'"} selected="selected"{/if}>Mucho</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_tomo_alcohol == -1"} error{elseif="$estado_tomo_alcohol == 1"} success{/if}">
					<label class="control-label" for="tomo_alcohol">Tomo alcohol</label>
					<div class="controls">
						<select name="tomo_alcohol" id="tomo_alcohol">
							<option value=""{if="$tomo_alcohol == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="no"{if="$tomo_alcohol == 'no'"} selected="selected"{/if}>No</option>
							<option value="casualmente"{if="$tomo_alcohol == 'casualmente'"} selected="selected"{/if}>Casualmente</option>
							<option value="socialmente"{if="$tomo_alcohol == 'socialmente'"} selected="selected"{/if}>Socialmente</option>
							<option value="regularmente"{if="$tomo_alcohol == 'regularmente'"} selected="selected"{/if}>Regularmente</option>
							<option value="mucho"{if="$tomo_alcohol == 'mucho'"} selected="selected"{/if}>Mucho</option>
						</select>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary" type="submit">Guardar</button>
				</div>
			</fieldset>

			<fieldset>
				<legend>Formaci&oacute;n y trabajo</legend>

				<div class="control-group{if="$estado_estudios == -1"} error{elseif="$estado_estudios == 1"} success{/if}">
					<label class="control-label" for="estudios">Estudios</label>
					<div class="controls">
						<select name="estudios" id="estudios">
							<option value=""{if="$estudios == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_estudios"{if="$estudios == 'sin_estudios'"} selected="selected"{/if}>Sin Estudios</option>
							<option value="primario_en_curso"{if="$estudios == 'primario_en_curso'"} selected="selected"{/if}>Primario en curso</option>
							<option value="primario_completo"{if="$estudios == 'primario_completo'"} selected="selected"{/if}>Primario completo</option>
							<option value="secundario_en_curso"{if="$estudios == 'secundario_en_curso'"} selected="selected"{/if}>Secundario en curso</option>
							<option value="secundario_completo"{if="$estudios == 'secundario_completo'"} selected="selected"{/if}>Secundario completo</option>
							<option value="terciario_en_curso"{if="$estudios == 'terciario_en_curso'"} selected="selected"{/if}>Terciario en curso</option>
							<option value="terciario_completo"{if="$estudios == 'terciario_completo'"} selected="selected"{/if}>Terciario completo</option>
							<option value="universitario_en_curso"{if="$estudios == 'universitario_en_curso'"} selected="selected"{/if}>Universitario en curso</option>
							<option value="universitario_completo"{if="$estudios == 'universitario_completo'"} selected="selected"{/if}>Universitario completo</option>
							<option value="post_grado_en_curso"{if="$estudios == 'post_grado_en_curso'"} selected="selected"{/if}>Post-grado en curso</option>
							<option value="post_grado_completo"{if="$estudios == 'post_grado_completo'"} selected="selected"{/if}>Post-grado completo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_espanol == -1"} error{elseif="$estado_idioma_espanol == 1"} success{/if}">
					<label class="control-label" for="idioma_espanol">Espa&ntilde;ol</label>
					<div class="controls">
						<select name="idioma_espanol" id="idioma_espanol">
							<option value=""{if="$idioma_espanol == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_espanol == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_espanol == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_espanol == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_espanol == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_espanol == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_ingles == -1"} error{elseif="$estado_idioma_ingles == 1"} success{/if}">
					<label class="control-label" for="idioma_ingles">Ingl&eacute;s</label>
					<div class="controls">
						<select name="idioma_ingles" id="idioma_ingles">
							<option value=""{if="$idioma_ingles == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_ingles == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_ingles == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_ingles == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_ingles == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_ingles == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_portugues == -1"} error{elseif="$estado_idioma_portugues == 1"} success{/if}">
					<label class="control-label" for="idioma_portugues">Portugu&eacute;s</label>
					<div class="controls">
						<select name="idioma_portugues" id="idioma_portugues">
							<option value=""{if="$idioma_portugues == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_portugues == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_portugues == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_portugues == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_portugues == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_portugues == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_frances == -1"} error{elseif="$estado_idioma_frances == 1"} success{/if}">
					<label class="control-label" for="idioma_frances">Franc&eacute;s</label>
					<div class="controls">
						<select name="idioma_frances" id="idioma_frances">
							<option value=""{if="$idioma_frances == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_frances == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_frances == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_frances == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_frances == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_frances == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_italiano == -1"} error{elseif="$estado_idioma_italiano == 1"} success{/if}">
					<label class="control-label" for="idioma_italiano">Italiano</label>
					<div class="controls">
						<select name="idioma_italiano" id="idioma_italiano">
							<option value=""{if="$idioma_italiano == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_italiano == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_italiano == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_italiano == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_italiano == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_italiano == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_aleman == -1"} error{elseif="$estado_idioma_aleman == 1"} success{/if}">
					<label class="control-label" for="idioma_aleman">Alem&aacute;n</label>
					<div class="controls">
						<select name="idioma_aleman" id="idioma_aleman">
							<option value=""{if="$idioma_aleman == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_aleman == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_aleman == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_aleman == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_aleman == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_aleman == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_idioma_otro == -1"} error{elseif="$estado_idioma_otro == 1"} success{/if}">
					<label class="control-label" for="idioma_otro">Otro</label>
					<div class="controls">
						<select name="idioma_otro" id="idioma_otro">
							<option value=""{if="$idioma_otro == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_conocimiento"{if="$idioma_otro == 'sin_conocimiento'"} selected="selected"{/if}>Sin conocimiento</option>
							<option value="basico"{if="$idioma_otro == 'basico'"} selected="selected"{/if}>Básico</option>
							<option value="intermedio"{if="$idioma_otro == 'intermedio'"} selected="selected"{/if}>Intermedio</option>
							<option value="fluido"{if="$idioma_otro == 'fluido'"} selected="selected"{/if}>Fluido</option>
							<option value="nativo"{if="$idioma_otro == 'nativo'"} selected="selected"{/if}>Nativo</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_profesion == -1"} error{elseif="$estado_profesion == 1"} success{/if}">
					<label class="control-label" for="profesion">Profesi&oacute;n</label>
					<div class="controls">
						<input type="text" id="profesion" name="profesion" value="{$profesion}" />
					</div>
				</div>

				<div class="control-group{if="$estado_empresa == -1"} error{elseif="$estado_empresa == 1"} success{/if}">
					<label class="control-label" for="empresa">Empresa</label>
					<div class="controls">
						<input type="text" id="empresa" name="empresa" value="{$empresa}" />
					</div>
				</div>

				<div class="control-group{if="$estado_sector == -1"} error{elseif="$estado_sector == 1"} success{/if}">
					<label class="control-label" for="sector">Sector</label>
					<div class="controls">
						<select name="sector" id="sector">
							<option value=""{if="$sector == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="abastecimiento"{if="$sector == 'abastecimiento'"} selected="selected"{/if}>Abastecimiento</option>
							<option value="administracion"{if="$sector == 'administracion'"} selected="selected"{/if}>Administraci&oacute;n</option>
							<option value="apoderado_aduanal"{if="$sector == 'apoderado_aduanal'"} selected="selected"{/if}>Apoderado Aduanal</option>
							<option value="asesoria_en_comercio_exterior"{if="$sector == 'asesoria_en_comercio_exterior'"} selected="selected"{/if}>Asesor&iacute;a en Comercio Exterior</option>
							<option value="asesoria_legal_internacional"{if="$sector == 'asesoria_legal_internacional'"} selected="selected"{/if}>Asesor&iacute;a Legal Internacional</option>
							<option value="asistente_de_trafico"{if="$sector == 'asistente_de_trafico'"} selected="selected"{/if}>Asistente de Tr&aacute;fico</option>
							<option value="auditoria"{if="$sector == 'auditoria'"} selected="selected"{/if}>Auditor&iacute;a</option>
							<option value="calidad"{if="$sector == 'calidad'"} selected="selected"{/if}>Calidad</option>
							<option value="call_center"{if="$sector == 'call_center'"} selected="selected"{/if}>Call Center</option>
							<option value="capacitacion_comercio_exterior"{if="$sector == 'capacitacion_comercio_exterior'"} selected="selected"{/if}>Capacitaci&oacute;n Comercio Exterior</option>
							<option value="comercial"{if="$sector == 'comercial'"} selected="selected"{/if}>Comercial</option>
							<option value="comercio_exterior"{if="$sector == 'comercio_exterior'"} selected="selected"{/if}>Comercio Exterior</option>
							<option value="compras"{if="$sector == 'compras'"} selected="selected"{/if}>Compras</option>
							<option value="compras_internacionalesimportacion"{if="$sector == 'compras_internacionalesimportacion'"} selected="selected"{/if}>Compras Internacionales/Importaci&oacute;n</option>
							<option value="comunicacion_social"{if="$sector == 'comunicacion_social'"} selected="selected"{/if}>Comunicaci&oacute;n Social</option>
							<option value="comunicaciones_externas"{if="$sector == 'comunicaciones_externas'"} selected="selected"{/if}>Comunicaciones Externas</option>
							<option value="comunicaciones_internas"{if="$sector == 'comunicaciones_internas'"} selected="selected"{/if}>Comunicaciones Internas</option>
							<option value="consultoria"{if="$sector == 'consultoria'"} selected="selected"{/if}>Consultor&iacute;a</option>
							<option value="consultorias_comercio_exterior"{if="$sector == 'consultorias_comercio_exterior'"} selected="selected"{/if}>Consultor&iacute;as Comercio Exterior</option>
							<option value="contabilidad"{if="$sector == 'contabilidad'"} selected="selected"{/if}>Contabilidad</option>
							<option value="control_de_gestion"{if="$sector == 'control_de_gestion'"} selected="selected"{/if}>Control de Gesti&oacute;n</option>
							<option value="creatividad"{if="$sector == 'creatividad'"} selected="selected"{/if}>Creatividad</option>
							<option value="diseno"{if="$sector == 'diseno'"} selected="selected"{/if}>Dise&ntilde;o</option>
							<option value="distribucion"{if="$sector == 'distribucion'"} selected="selected"{/if}>Distribuci&oacute;n</option>
							<option value="ecommerce"{if="$sector == 'ecommerce'"} selected="selected"{/if}>E-commerce</option>
							<option value="educacion"{if="$sector == 'educacion'"} selected="selected"{/if}>Educaci&oacute;n</option>
							<option value="finanzas"{if="$sector == 'finanzas'"} selected="selected"{/if}>Finanzas</option>
							<option value="finanzas_internacionales"{if="$sector == 'finanzas_internacionales'"} selected="selected"{/if}>Finanzas Internacionales</option>
							<option value="gerencia_direccion_general"{if="$sector == 'gerencia_direccion_general'"} selected="selected"{/if}>Gerencia / Direcci&oacute;n General</option>
							<option value="impuestos"{if="$sector == 'impuestos'"} selected="selected"{/if}>Impuestos</option>
							<option value="ingenieria"{if="$sector == 'ingenieria'"} selected="selected"{/if}>Ingenier&iacute;a</option>
							<option value="internet"{if="$sector == 'internet'"} selected="selected"{/if}>Internet</option>
							<option value="investigacion_y_desarrollo"{if="$sector == 'investigacion_y_desarrollo'"} selected="selected"{/if}>Investigaci&oacute;n y Desarrollo</option>
							<option value="jovenes_profesionales"{if="$sector == 'jovenes_profesionales'"} selected="selected"{/if}>J&oacute;venes Profesionales</option>
							<option value="legal"{if="$sector == 'legal'"} selected="selected"{/if}>Legal</option>
							<option value="logistica"{if="$sector == 'logistica'"} selected="selected"{/if}>Log&iacute;stica</option>
							<option value="mantenimiento"{if="$sector == 'mantenimiento'"} selected="selected"{/if}>Mantenimiento</option>
							<option value="marketing"{if="$sector == 'marketing'"} selected="selected"{/if}>Marketing</option>
							<option value="medio_ambiente"{if="$sector == 'medio_ambiente'"} selected="selected"{/if}>Medio Ambiente</option>
							<option value="mercadotecnia_internacional"{if="$sector == 'mercadotecnia_internacional'"} selected="selected"{/if}>Mercadotecnia Internacional</option>
							<option value="multimedia"{if="$sector == 'multimedia'"} selected="selected"{/if}>Multimedia</option>
							<option value="otra"{if="$sector == 'otra'"} selected="selected"{/if}>Otra</option>
							<option value="pasantias"{if="$sector == 'pasantias'"} selected="selected"{/if}>Pasant&iacute;as</option>
							<option value="periodismo"{if="$sector == 'periodismo'"} selected="selected"{/if}>Periodismo</option>
							<option value="planeamiento"{if="$sector == 'planeamiento'"} selected="selected"{/if}>Planeamiento</option>
							<option value="produccion"{if="$sector == 'produccion'"} selected="selected"{/if}>Producci&oacute;n</option>
							<option value="produccion_e_ingenieria"{if="$sector == 'produccion_e_ingenieria'"} selected="selected"{/if}>Producci&oacute;n e Ingenier&iacute;a</option>
							<option value="recursos_humanos"{if="$sector == 'recursos_humanos'"} selected="selected"{/if}>Recursos Humanos</option>
							<option value="relaciones_institucionales_publicas"{if="$sector == 'relaciones_institucionales_publicas'"} selected="selected"{/if}>Relaciones Institucionales / P&uacute;blicas</option>
							<option value="salud"{if="$sector == 'salud'"} selected="selected"{/if}>Salud</option>
							<option value="seguridad_industrial"{if="$sector == 'seguridad_industrial'"} selected="selected"{/if}>Seguridad Industrial</option>
							<option value="servicios"{if="$sector == 'servicios'"} selected="selected"{/if}>Servicios</option>
							<option value="soporte_tecnico"{if="$sector == 'soporte_tecnico'"} selected="selected"{/if}>Soporte T&eacute;cnico</option>
							<option value="tecnologia"{if="$sector == 'tecnologia'"} selected="selected"{/if}>Tecnolog&iacute;a</option>
							<option value="tecnologias_de_la_informacion"{if="$sector == 'tecnologias_de_la_informacion'"} selected="selected"{/if}>Tecnolog&iacute;as de la Informaci&oacute;n</option>
							<option value="telecomunicaciones"{if="$sector == 'telecomunicaciones'"} selected="selected"{/if}>Telecomunicaciones</option>
							<option value="telemarketing"{if="$sector == 'telemarketing'"} selected="selected"{/if}>Telemarketing</option>
							<option value="traduccion"{if="$sector == 'traduccion'"} selected="selected"{/if}>Traducci&oacute;n</option>
							<option value="transporte"{if="$sector == 'transporte'"} selected="selected"{/if}>Transporte</option>
							<option value="ventas"{if="$sector == 'ventas'"} selected="selected"{/if}>Ventas</option>
							<option value="ventas_internacionalesexportacion"{if="$sector == 'ventas_internacionalesexportacion'"} selected="selected"{/if}>Ventas Internacionales/Exportaci&oacute;n</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_nivel_ingresos == -1"} error{elseif="$estado_nivel_ingresos == 1"} success{/if}">
					<label class="control-label" for="nivel_ingresos">Nivel de ingresos</label>
					<div class="controls">
						<select name="nivel_ingresos" id="nivel_ingresos">
							<option value=""{if="$nivel_ingresos == ''"} selected="selected"{/if}>Sin Respuesta</option>
							<option value="sin_ingresos"{if="$nivel_ingresos == 'sin_ingresos'"} selected="selected"{/if}>Sin ingresos</option>
							<option value="bajos"{if="$nivel_ingresos == 'bajos'"} selected="selected"{/if}>Bajos</option>
							<option value="intermedios"{if="$nivel_ingresos == 'intermedios'"} selected="selected"{/if}>Intermedios</option>
							<option value="altos"{if="$nivel_ingresos == 'altos'"} selected="selected"{/if}>Altos</option>
						</select>
					</div>
				</div>

				<div class="control-group{if="$estado_intereses_personales == -1"} error{elseif="$estado_intereses_personales == 1"} success{/if}">
					<label class="control-label" for="intereses_personales">Intereses personales</label>
					<div class="controls">
						<textarea id="intereses_personales" name="intereses_personales">{$intereses_personales}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_habilidades_profesionales == -1"} error{elseif="$estado_habilidades_profesionales == 1"} success{/if}">
					<label class="control-label" for="habilidades_profesionales">Habilidades profesionales</label>
					<div class="controls">
						<textarea id="habilidades_profesionales" name="habilidades_profesionales">{$habilidades_profesionales}</textarea>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary" type="submit">Guardar</button>
				</div>
			</fieldset>

			<fieldset>
				<legend>Intereses y preferencias</legend>

				<div class="control-group{if="$estado_mis_intereses == -1"} error{elseif="$estado_mis_intereses == 1"} success{/if}">
					<label class="control-label" for="mis_intereses">Mis intereses</label>
					<div class="controls">
						<textarea id="mis_intereses" name="mis_intereses">{$mis_intereses}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_hobbies == -1"} error{elseif="$estado_hobbies == 1"} success{/if}">
					<label class="control-label" for="hobbies">Hobbies</label>
					<div class="controls">
						<textarea id="hobbies" name="hobbies">{$hobbies}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_series_tv_favoritas == -1"} error{elseif="$estado_series_tv_favoritas == 1"} success{/if}">
					<label class="control-label" for="series_tv_favoritas">Series de TV favoritas:</label>
					<div class="controls">
						<textarea id="series_tv_favoritas" name="series_tv_favoritas">{$series_tv_favoritas}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_musica_favorita == -1"} error{elseif="$estado_musica_favorita == 1"} success{/if}">
					<label class="control-label" for="musica_favorita">Música favorita</label>
					<div class="controls">
						<textarea id="musica_favorita" name="musica_favorita">{$musica_favorita}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_deportes_y_equipos_favoritos == -1"} error{elseif="$estado_deportes_y_equipos_favoritos == 1"} success{/if}">
					<label class="control-label" for="deportes_y_equipos_favoritos">Deportes y equipos favoritos</label>
					<div class="controls">
						<textarea id="deportes_y_equipos_favoritos" name="deportes_y_equipos_favoritos">{$deportes_y_equipos_favoritos}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_libros_favoritos == -1"} error{elseif="$estado_libros_favoritos == 1"} success{/if}">
					<label class="control-label" for="libros_favoritos">Libros favoritos</label>
					<div class="controls">
						<textarea id="libros_favoritos" name="libros_favoritos">{$libros_favoritos}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_peliculas_favoritas == -1"} error{elseif="$estado_peliculas_favoritas == 1"} success{/if}">
					<label class="control-label" for="peliculas_favoritas">Películas favoritas</label>
					<div class="controls">
						<textarea id="peliculas_favoritas" name="peliculas_favoritas">{$peliculas_favoritas}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_comida_favorita == -1"} error{elseif="$estado_comida_favorita == 1"} success{/if}">
					<label class="control-label" for="comida_favorita">Comida favorita</label>
					<div class="controls">
						<textarea id="comida_favorita" name="comida_favorita">{$comida_favorita}</textarea>
					</div>
				</div>

				<div class="control-group{if="$estado_mis_heroes == -1"} error{elseif="$estado_mis_heroes == 1"} success{/if}">
					<label class="control-label" for="mis_heroes">Mis héroes son</label>
					<div class="controls">
						<textarea id="mis_heroes" name="mis_heroes">{$mis_heroes}</textarea>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary" type="submit">Guardar</button>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="span2">
		<img class="thumbnail" src="{function="Utils::get_gravatar($email, 150, 150)"}" />
	</div>
</div>