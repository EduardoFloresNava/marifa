<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="" enctype="multipart/form-data">
			<fieldset>
				<legend>Editar foto</legend>

				<div class="control-group{if="$error_titulo"} error{/if}">
					<label class="control-label" for="titulo">Título</label>
					<div class="controls">
						<input type="text" id="titulo" name="titulo" value="{$titulo}" class="span10" />
						<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
					</div>
				</div>

				<div class="control-group{if="$error_descripcion"} error{/if}">
					<label class="control-label" for="descripcion">Descripción</label>
					<div class="controls">
						{include="helper/bbcode_bar"}
						<textarea name="descripcion" id="descripcion" class="span10" data-preview="{#SITE_URL#}/foto/preview">{$descripcion}</textarea>
						<span class="help-block">{if="$error_descripcion"}{$error_descripcion}{/if}</span>
					</div>
				</div>

				<div class="control-group{if="$error_categoria"} error{/if}">
					<label class="control-label" for="categoria">Categoría</label>
					<div class="controls">
						<select class="span12" name="categoria" id="categoria" size="10">
							{loop="$categorias"}
							<option style="padding: 3px 0 3px 22px; background: transparent url({#THEME_URL#}/assets/img/categoria/{$value.imagen}) no-repeat 2px center;" value="{$value.seo}"{if="$categoria == $value.seo"}selected="selected"{/if}>{$value.nombre|htmlentities:ENT_NOQUOTES}</option>{/loop}
						</select>
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
							<input type="checkbox" id="visitantes" name="visitantes" value="1"{if="$visitantes"} checked{/if}><strong>últimos visitantes</strong>
							<p>Se mostrarán los últimos visitantes.</p>
						</label>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-large btn-primary">Editar</button> o <a href="{#SITE_URL#}/foto/{$foto.categoria.seo}/{$foto.id}/{$foto.titulo|Texto::make_seo}.html">Volver</a>
				</div>

			</fieldset>
		</form>
	</div>
</div>