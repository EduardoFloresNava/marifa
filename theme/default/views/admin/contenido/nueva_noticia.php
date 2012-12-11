<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido">Contenido</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/noticias">Noticias</a> <span class="divider">/</span></li>
    <li class="active">Nueva</li>
</ul>
<div class="header">
	<h2>Nueva noticia</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_contenido"} error{/if}">
		<label class="control-label" for="titulo">Contenido</label>
		<div class="controls">
			{include="helper/bbcode_bar"}
			<textarea name="contenido" id="contenido" data-preview="{#SITE_URL#}/admin/contenido/preview" class="span10">{$contenido}</textarea>
			<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_visible"} error{/if}">
		<label class="control-label" for="visible">Visibilidad inicial</label>
		<div class="controls">
			<label class="radio inline">
				<input type="radio" name="visible" id="visible" value="1"{if="$visible"} checked{/if}>
				Visible
			</label>
			<label class="radio inline">
				<input type="radio" name="visible" id="visible" value="0"{if="!$visible"} checked{/if}>
				Oculto
			</label>
			<span class="help-block">La visibilidad que va a tener por defecto. Si se pone visible instantaneamente ser&aacute; mostrado a los usuarios.</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Crear</button> o <a href="{#SITE_URL#}/admin/contenido/noticias">Volver</a>
	</div>
</form>