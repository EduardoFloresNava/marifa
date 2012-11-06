<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/configuracion/temas">Temas</a> <span class="divider">/</span></li>
    <li class="active">Instalar tema</li>
</ul>
<div class="header">
	<h2 class="title">Instalar tema</h2>
</div>
<form method="POST" enctype="multipart/form-data" class="form-horizontal" action="">

	<div class="control-group{if="$error_carga"} error{/if}">
		<label class="control-label" for="theme">Tema a instalar</label>
		<div class="controls">
			<input type="file" name="theme" id="theme" />
			<span class="help-block">{if="$error_carga"}{$error_carga}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Instalar</button> o <a href="/admin/configuracion/temas">Volver</a>
	</div>
</form>