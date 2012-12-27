<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="">
			<h2 class="title">Denunciar a {$usuario}</h2>

			<div class="control-group{if="$error_motivo"} error{/if}">
				<label class="control-label" for="motivo">Motivo</label>
				<div class="controls">
					<select name="motivo" id="tipo">
						<option value="0" {if="$motivo == 0"}selected="selected"{/if}>Perfil falso/clon</option>
						<option value="1" {if="$motivo == 1"}selected="selected"{/if}>Usuario insultante y agresivo</option>
						<option value="2" {if="$motivo == 2"}selected="selected"{/if}>Publicaciones inapropiadas</option>
						<option value="3" {if="$motivo == 3"}selected="selected"{/if}>Foto del perfil inapropiada</option>
						<option value="4" {if="$motivo == 4"}selected="selected"{/if}>Publicidad no deseada (SPAM)</option>
						<option value="5" {if="$motivo == 12"}selected="selected"{/if}>Otra (especificar)</option>
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
				<button type="submit" name="submit" value="enviar" class="btn btn-large btn-primary">Denunciar</button> o <a href="{#SITE_URL#}/@{$usuario}">Volver</a>
			</div>
		</form>
	</div>
</div>