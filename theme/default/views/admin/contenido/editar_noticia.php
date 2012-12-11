<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido/noticias">Contenido</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/noticias">Noticias</a> <span class="divider">/</span></li>
    <li class="active">Nueva</li>
</ul>
<div class="header">
	<h2>Editar noticia #{$noticia}</h2>
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

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Editar</button> o <a href="{#SITE_URL#}/admin/contenido/noticias">Volver</a>
	</div>
</form>