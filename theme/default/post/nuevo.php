<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="">

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

			<div class="control-group{if="$error_contenido"} error{/if}">
				<label class="control-label" for="titulo">Contenido</label>
				<div class="controls">
					<textarea name="contenido" id="contenido" class="span10">{$contenido}</textarea>
					<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<h3>Categor&iacute;a</h3>
					<select class="span12" name="categoria" id="categoria" size="10">
						{loop="$categorias"}
						<option value="{$value.seo}"{if="$categoria == $value.seo"}selected="selected"{/if}>{$value.nombre|htmlentities:ENT_NOQUOTES}</option>{/loop}
					</select>
				</div>

				<div class="span6">
					<h3>Opciones</h3>

					<label class="checkbox">
						<input type="checkbox" id="privado" name="privado" value="1"><strong>S&oacute;lo usuarios registrados</strong>
						<p>Tu post ser&aacute; visto s&oacute;lo por los usuarios que est&eacute;n registrados.</p>
					</label>
					<!--
					<label class="checkbox">
						<input type="checkbox" id="comentar" name="comentar" value="1"><strong>Cerrar Comentarios</strong>
						<p>Si tu post es pol&eacute;mico ser&iacute;a mejor que cierres los comentarios.</p>
					</label>
					<label class="checkbox">
						<input type="checkbox" id="visitas" name="visitas" value="1"><strong>Mostrar visitantes recientes</strong>
						<p>Tu post mostrar&aacute; los &uacute;ltimos visitantes que ha tenido.</p>
					</label>-->
					<label class="checkbox">
						<input type="checkbox" id="sponsored" name="sponsored" value="1"><strong>Patrocinado</strong>
						<p>Resalta este post entre los dem&aacute;s.</p>
					</label>
					<label class="checkbox"">
						<input type="checkbox" id="sticky" name="sticky" value="1"><strong>Sticky</strong>
						<p>Colocar a este post fijo en la home.</p>
					</label>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-large btn-info">Guardar como borrador</button>
				<button type="submit" class="btn btn-large btn-primary">Crear</button>
			</div>
		</form>
	</div>
</div>