<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/configuracion/plugins">Plugins</a> <span class="divider">/</span></li>
    <li class="active">Importar plugin</li>
</ul>
<div class="header">
	<h2 class="title">Importar plugin</h2>
</div>
<form method="POST" enctype="multipart/form-data" class="form-horizontal" action="">

	<div class="control-group{if="$error_carga"} error{/if}">
		<label class="control-label" for="plugin">Plugin a importar</label>
		<div class="controls">
			<input type="file" name="plugin" id="plugin" />
			<span class="help-block">{if="$error_carga"}{$error_carga}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Importar</button> o <a href="/admin/configuracion/plugins">Volver</a>
	</div>
</form>