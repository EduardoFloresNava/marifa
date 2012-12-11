<div class="row">
	<div class="span12">
		<h2 class="title">Nuevo post:</h2>
		<form method="POST" class="form-horizontal" action="">

			<div class="control-group{if="$error_titulo"} error{/if}">
				<label class="control-label" for="titulo">T&iacute;tulo</label>
				<div class="controls">
					<input type="text" id="titulo" name="titulo" value="{$titulo}" class="input-xxlarge" />
					<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_contenido"} error{/if}">
				<label class="control-label" for="titulo">Contenido</label>
				<div class="controls">
					{include="helper/bbcode_bar"}
					<textarea name="contenido" id="contenido" data-preview="{#SITE_URL#}/post/preview" class="input-xxlarge">{$contenido}</textarea>
					<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_tags"} error{/if}">
				<label class="control-label" for="tags">Etiquetas</label>
				<div class="controls">
					<input type="text" id="tags" name="tags" value="{$tags}" class="input-xxlarge" />
					<span class="help-block">{if="$error_tags"}{$error_tags}{else}Listado de etiquetas separadas por ','. Las etiquetas deben ser alphanum&eacute;ricas y contener espacios.{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_captcha"} error{/if}">
				<label class="control-label" for="captcha">CAPTCHA</label>
				<div class="controls">
					<input type="text" id="captcha" name="captcha" value="{$captcha}" />
					<span class="help-block">{if="$error_captcha"}El c&oacute;digo ingresado no es correcto.{else}Ingresa el c&oacute;digo que aparece a continuaci&oacute;n.{/if}</span>
					<img src="/home/captcha" style="display: block;" />
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<h3 class="title">Categor&iacute;a</h3>
					{if="$error_categoria"}<div class="alert alert-danger">{$error_categoria}</div>{/if}
					<select class="span12" name="categoria" id="categoria" size="10">
						{loop="$categorias"}
						<option value="{$value.seo}"{if="$categoria == $value.seo"} selected="selected"{/if}>{$value.nombre|htmlentities:ENT_NOQUOTES}</option>{/loop}
					</select>
				</div>

				<div class="span6">
					<h3 class="title">Opciones</h3>

					<label class="checkbox">
						<input type="checkbox" id="privado" name="privado" value="1"{if="$privado"} checked{/if}><strong>S&oacute;lo usuarios registrados</strong>
						<p>Tu post ser&aacute; visto s&oacute;lo por los usuarios que est&eacute;n registrados.</p>
					</label>
					<label class="checkbox">
						<input type="checkbox" id="comentar" name="comentar" value="1"{if="$comentar"} checked{/if}><strong>Comentarios cerrados</strong>
						<p>No se permiten comentarios en el post.</p>
					</label>
					{if="$permisos_especiales"}<label class="checkbox">
						<input type="checkbox" id="patrocinado" name="patrocinado" value="1"{if="$patrocinado"} checked{/if}><strong>Patrocinado</strong>
						<p>Resalta este post entre los dem&aacute;s.</p>
					</label>
					<label class="checkbox">
						<input type="checkbox" id="sticky" name="sticky" value="1"{if="$sticky"} checked{/if}><strong>Sticky</strong>
						<p>Colocar a este post fijo en la portada.</p>
					</label>{/if}
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" name="submit" value="borrador" class="btn btn-large btn-info">Guardar como borrador</button>
				<button type="submit" name="submit" value="enviar" class="btn btn-large btn-primary">Crear</button>
			</div>
		</form>
	</div>
</div>