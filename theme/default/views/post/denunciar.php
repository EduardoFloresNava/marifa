<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="">

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}

			<div class="control-group{if="$error_motivo"} error{/if}">
				<label class="control-label" for="motivo">Motivo</label>
				<div class="controls">
					<select name="motivo" id="tipo">
						<option value="0" {if="$motivo == 0"}selected="selected"{/if}>Re-post</option>
						<option value="1" {if="$motivo == 1"}selected="selected"{/if}>spam</option>
						<option value="2" {if="$motivo == 2"}selected="selected"{/if}>Contiene links muertos</option>
						<option value="3" {if="$motivo == 3"}selected="selected"{/if}>Racista o irrespetuoso</option>
						<option value="4" {if="$motivo == 4"}selected="selected"{/if}>Contiene información personal</option>
						<option value="5" {if="$motivo == 5"}selected="selected"{/if}>Titulo en mayúsculas</option>
						<option value="6" {if="$motivo == 6"}selected="selected"{/if}>Contiene pedofilia</option>
						<option value="7" {if="$motivo == 7"}selected="selected"{/if}>Gore o asqueroso</option>
						<option value="8" {if="$motivo == 8"}selected="selected"{/if}>Fuente incorrecta</option>
						<option value="9" {if="$motivo == 9"}selected="selected"{/if}>Contenido pobre o crap</option>
						<option value="10" {if="$motivo == 10"}selected="selected"{/if}>No es un foro</option>
						<option value="11" {if="$motivo == 11"}selected="selected"{/if}>No cumple el protocolo</option>
						<option value="12" {if="$motivo == 12"}selected="selected"{/if}>Otra (especificar)</option>
					</select>
					<span class="help-block">{if="$error_motivo"}{$error_motivo}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_comentario"} error{/if}">
				<label class="control-label" for="comentario">Motivo</label>
				<div class="controls">
					<textarea id="comentario" name="comentario" class="span10">{$comentario}</textarea>
					<span class="help-block">{if="$error_comentario"}{$error_comentario}{/if}</span>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" name="submit" value="enviar" class="btn btn-large btn-primary">Crear</button> o <a href="/post/index/{$post}">Volver</a>
			</div>
		</form>
	</div>
</div>