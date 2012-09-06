<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="">
			<fieldset>
				<legend>Nueva foto</legend>

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}

			<div class="control-group{if="$error_titulo"} error{/if}">
				<label class="control-label" for="titulo">T&iacute;tulo</label>
				<div class="controls">
					<input type="text" id="titulo" name="titulo" value="{$titulo}" class="span10" />
					<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_url"} error{/if}">
				<label class="control-label" for="url">URL</label>
				<div class="controls">
					<input type="text" id="url" name="url" value="{$url}" class="span10" />
					<span class="help-block">{if="$error_url"}{$error_url}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_descripcion"} error{/if}">
				<label class="control-label" for="descripcion">Descripci&oacute;n</label>
				<div class="controls">
					<textarea name="descripcion" id="descripcion" class="span10">{$descripcion}</textarea>
					<span class="help-block">{if="$error_descripcion"}{$error_descripcion}{/if}</span>
				</div>
			</div>

			<div class="control-group">
				<lable class="control-label">Opciones</lable>
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" id="comentarios" name="comentarios" value="1"{if="$comentarios"} selected="selected"{/if}><strong>Cerrar comentarios</strong>
						<p> Si no quieres recibir comentarios en tu foto. </p>
					</label>

					<label class="checkbox">
						<input type="checkbox" id="visitantes" name="visitantes" value="1"{if="$visitantes"} selected="selected"{/if}><strong>&Uacute;ltimos visitantes</strong>
						<p>Se mostrar&aacute;n los &uacute;ltimos visitantes.</p>
					</label>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-large btn-primary">Agregar</button>
			</div>

			</fieldset>
		</form>
	</div>
</div>