<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Suspender</li>
</ul>
<div class="header">
	<h2>Suspender a <a href="{#SITE_URL#}/perfil/index/{$usuario.nick}">{$usuario.nick}</a></h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_motivo"} error{/if}">
		<label class="control-label" for="motivo">Motivo</label>
		<div class="controls">
			{include="helper/bbcode_bar"}
			<textarea name="motivo" id="motivo" data-preview="{#SITE_URL#}/admin/usuario/preview" class="span10">{$motivo}</textarea>
			<span class="help-block">{if="$error_motivo"}{$error_motivo}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_fin"} error{/if}">
		<label class="control-label" for="fin">Finalización</label>
		<div class="controls">
			<input type="text" value="{$fin}" name="fin" id="fin" class="span10" />
			<span class="help-block">{if="$error_fin"}{$error_fin}{else}Fecha de terminación. El formato es el de strtotime.{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Crear</button> o <a href="{#SITE_URL#}/admin/usuario">Volver</a>
	</div>
</form>