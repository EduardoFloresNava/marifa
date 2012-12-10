<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/configuracion/plugins">Plugins</a> <span class="divider">/</span></li>
    <li class="active">Importar plugin</li>
</ul>
<div class="header">
	<h2>Importar plugin</h2>
</div>
<div class="alert alert-info">
	<i class="icon icon-info-sign"></i> Si lo desea puede directamente colocar el plugin en <code>{$plugin_dir}</code>.
</div>
<form method="POST" enctype="multipart/form-data" class="form-horizontal" action="">

	<div class="control-group{if="$error_carga"} error{/if}">
		<label class="control-label" for="plugin">Plugin a importar</label>
		<div class="controls">
			<input type="file" name="plugin" id="plugin" />
			<span class="help-block">{if="$error_carga"}{$error_carga}{else}Compresiones disponibles: {function="implode(', ', $compresores)"}.{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Importar</button> o <a href="{#SITE_URL#}/admin/configuracion/plugins">Volver</a>
	</div>
</form>