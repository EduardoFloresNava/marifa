<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="" enctype="multipart/form-data">
			<fieldset>
				<legend>Nueva foto</legend>

			<div class="control-group{if="$error_titulo"} error{/if}">
				<label class="control-label" for="titulo">Título</label>
				<div class="controls">
					<input type="text" id="titulo" name="titulo" value="{$titulo}" class="input-xxlarge" />
					<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_url"} error{/if}">
				<label class="control-label" for="url">URL</label>
				<div class="controls">
					<input type="text" id="url" name="url" value="{$url}" class="input-large" />
					o
					<input type="file" id="img" name="img" class="input-xxlarge" />
					<span class="help-block">{if="$error_url"}{$error_url}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_descripcion"} error{/if}">
				<label class="control-label" for="descripcion">Descripción</label>
				<div class="controls">
					{include="helper/bbcode_bar"}
					<textarea name="descripcion" id="descripcion" data-preview="{#SITE_URL#}/foto/preview" class="input-xxlarge">{$descripcion}</textarea>
					<span class="help-block">{if="$error_descripcion"}{$error_descripcion}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_captcha"} error{/if}">
				<label class="control-label" for="captcha">CAPTCHA</label>
				<div class="controls">
					<input type="text" id="captcha" name="captcha" value="{$captcha}" />
					<span class="help-block">{if="$error_captcha"}{$error_captcha}{else}Ingresa el código que aparece a continuación.{/if}</span>
					<img src="{#SITE_URL#}/home/captcha" style="display: block;" />
				</div>
			</div>

			<div class="control-group{if="$error_categoria"} error{/if}">
				<label class="control-label" for="categoria">Categoría</label>
				<div class="controls">
					<select class="input-xxlarge" name="categoria" id="categoria" size="10">
						{loop="$categorias"}
						<option style="padding: 3px 0 3px 22px; background: transparent url({#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.imagen, 'small')"}) no-repeat 2px center;" value="{$value.seo}"{if="$categoria == $value.seo"}selected="selected"{/if}>{$value.nombre|htmlentities:ENT_NOQUOTES}</option>{/loop}
					</select>
					<span class="help-block">{if="$error_categoria"}{$error_categoria}{/if}</span>
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
				<button type="submit" class="btn btn-large btn-primary">Agregar</button>
			</div>

			</fieldset>
		</form>
	</div>
</div>