<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/rangos">Rangos</a> <span class="divider">/</span></li>
    <li class="active">Nuevo</li>
</ul>
<div class="header">
	<h2>Nuevo rango</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_nombre"} error{/if}">
		<label class="control-label" for="nombre">Nombre</label>
		<div class="controls">
			<input type="text" value="{$nombre}" name="nombre" id="nombre" class="span10" />
			<span class="help-block">{if="$error_nombre"}{$error_nombre}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_color"} error{/if}">
		<label class="control-label" for="color">Color</label>
		<div class="controls">
			<input type="text" value="{$color}" name="color" id="color" class="span10" />
			<span class="help-block">{if="$error_color"}{$error_color}{else}Color hexadecimal de 6 d&iacute;gitos. Por ejemplo: 00FFE5{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_imagen"} error{/if}">
		<label class="control-label" for="imagen">Imagen</label>
		<div class="controls">
			<select name="imagen" id="imagen">
				{loop="$imagenes_rangos"}
				<option style="padding-left: 30px; background: transparent url({#THEME_URL#}/assets/img/rangos/{$value}) no-repeat 0 5px;" value="{$value}"{if="$value == $imagen"} selected="selected"{/if}>{$value}</option>
				{/loop}
			</select>
			<span class="help-block">{if="$error_imagen"}{$error_imagen}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_puntos"} error{/if}">
		<label class="control-label" for="puntos">Puntos por d&iacute;a</label>
		<div class="controls">
			<input type="text" value="{$puntos}" name="puntos" id="puntos" class="span10" />
			<span class="help-block">{if="$error_puntos"}{$error_puntos}{else}Cantidad de puntos que se le otorgan por d&iacute;a.{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Crear</button>
	</div>
</form>