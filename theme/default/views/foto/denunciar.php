<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="">

			<div class="control-group{if="$error_motivo"} error{/if}">
				<label class="control-label" for="motivo">Motivo</label>
				<div class="controls">
					<select name="motivo" id="tipo">
						<option value="0" {if="$motivo == 0"}selected="selected"{/if}>Ya está publicada</option>
						<option value="1" {if="$motivo == 1"}selected="selected"{/if}>Se hace Spam</option>
						<option value="2" {if="$motivo == 2"}selected="selected"{/if}>La imagen está caída</option>
						<option value="3" {if="$motivo == 3"}selected="selected"{/if}>Es racista o irrespetuosa</option>
						<option value="4" {if="$motivo == 4"}selected="selected"{/if}>Contiene información personal</option>
						<option value="5" {if="$motivo == 5"}selected="selected"{/if}>Contiene pedofilia</option>
						<option value="6" {if="$motivo == 6"}selected="selected"{/if}>Es gore o asqueros</option>
						<option value="7" {if="$motivo == 7"}selected="selected"{/if}>Otra (especificar)</option>
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
				<button type="submit" name="submit" value="enviar" class="btn btn-large btn-primary">Crear</button> o <a href="{#SITE_URL#}/foto/ver/{$foto}">Volver</a>
			</div>
		</form>
	</div>
</div>