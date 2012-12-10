<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="" enctype="multipart/form-data">
			<fieldset>
				<legend>Editar foto</legend>

				<div class="control-group{if="$error_titulo"} error{/if}">
					<label class="control-label" for="titulo">T&iacute;tulo</label>
					<div class="controls">
						<input type="text" id="titulo" name="titulo" value="{$titulo}" class="span10" />
						<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
					</div>
				</div>

				<div class="control-group{if="$error_descripcion"} error{/if}">
					<label class="control-label" for="descripcion">Descripci&oacute;n</label>
					<div class="controls">
						{include="helper/bbcode_bar"}
						<textarea name="descripcion" id="descripcion" class="span10" data-preview="{#SITE_URL#}/foto/preview">{$descripcion}</textarea>
						<span class="help-block">{if="$error_descripcion"}{$error_descripcion}{/if}</span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Opciones</label>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" id="comentarios" name="comentarios" value="1"{if="$comentarios"} checked{/if}><strong>Cerrar comentarios</strong>
							<p> Si no quieres recibir comentarios en tu foto. </p>
						</label>

						<label class="checkbox">
							<input type="checkbox" id="visitantes" name="visitantes" value="1"{if="$visitantes"} checked{/if}><strong>&Uacute;ltimos visitantes</strong>
							<p>Se mostrar&aacute;n los &uacute;ltimos visitantes.</p>
						</label>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-large btn-primary">Editar</button> o <a href="{#SITE_URL#}/foto/ver/{$foto}">Volver</a>
				</div>

			</fieldset>
		</form>
	</div>
</div>