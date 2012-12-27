<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Advertir</li>
</ul>
<div class="header">
	<h2>Advertir a <a href="{#SITE_URL#}/perfil/index/{$usuario.nick}">{$usuario.nick}</a></h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_asunto"} error{/if}">
		<label class="control-label" for="asunto">Asunto</label>
		<div class="controls">
			<input type="text" value="{$asunto}" name="asunto" id="asunto" class="span10" />
			<span class="help-block">{if="$error_asunto"}{$error_asunto}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_contenido"} error{/if}">
		<label class="control-label" for="titulo">Contenido</label>
		<div class="controls">
			{include="helper/bbcode_bar"}
			<textarea name="contenido" id="contenido" class="span10" data-preview="{#SITE_URL#}/admin/usuario/preview" placeholder="Mensaje...">{$contenido}</textarea>
			<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Crear</button> o <a href="{#SITE_URL#}/admin/usuario">Volver</a>
	</div>
</form>