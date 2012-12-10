<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Banear</li>
</ul>
<div class="header">
	<h2>Banear a <a href="{#SITE_URL#}/perfil/index/{$usuario.nick}">{$usuario.nick}</a></h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_razon"} error{/if}">
		<label class="control-label" for="razon">Raz&oacute;n</label>
		<div class="controls">
			{include="helper/bbcode_bar"}
			<textarea name="razon" id="razon" data-preview="{#SITE_URL#}/admin/usuario/preview" class="span10">{$razon}</textarea>
			<span class="help-block">{if="$error_razon"}{$error_razon}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Banear</button> o <a href="{#SITE_URL#}/admin/usuario">Volver</a>
	</div>
</form>