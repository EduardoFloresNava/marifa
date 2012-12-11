<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido">Contenido</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido/categorias">Categor&iacute;as</a> <span class="divider">/</span></li>
    <li class="active">Editar</li>
</ul>
<div class="header">
	<h2>Editar categor&iacute;a</h2>
</div>
<form method="POST" class="form-horizontal" action="">
	<div class="control-group{if="$error_nombre"} error{/if}">
		<label class="control-label" for="nombre">Nombre</label>
		<div class="controls">
			<input type="text" value="{$nombre}" name="nombre" id="nombre" class="span10" />
			<span class="help-block">{if="$error_nombre"}{$error_nombre}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_imagen"} error{/if}">
		<label class="control-label" for="imagen">Imagen</label>
		<div class="controls">
			<select name="imagen" id="imagen">
				{loop="$imagenes_categorias"}
				<option value="{$value}"{if="$value == $imagen"} selected="selected"{/if}>{$value}</option>
				{/loop}
			</select>
			<span class="help-block">{if="$error_imagen"}{$error_imagen}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
	</div>
</form>