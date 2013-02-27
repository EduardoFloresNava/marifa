<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario/rangos">Rangos</a> <span class="divider">/</span></li>
    <li class="active">Editar</li>
</ul>
<div class="header">
	<h2>Editar rango</h2>
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
			<span class="help-block">{if="$error_color"}{$error_color}{else}Color hexadecimal de 6 dígitos. Por ejemplo: 00FFE5{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_imagen"} error{/if}">
		<label class="control-label" for="imagen">Imagen</label>
		<div class="controls">
			<select name="imagen" id="imagen">
				{loop="$imagenes_rangos"}
				<option style="padding-left: 22px; background: transparent url({#THEME_URL#}/assets/img/rangos/{$value}) no-repeat 2px 0;" value="{$key}"{if="$key == $imagen"} selected="selected"{/if}>{$key}</option>
				{/loop}
			</select>
			<span class="help-block">{if="$error_imagen"}{$error_imagen}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_puntos"} error{/if}">
		<label class="control-label" for="puntos">Puntos por día</label>
		<div class="controls">
			<input type="text" value="{$puntos}" name="puntos" id="puntos" class="span10" />
			<span class="help-block">{if="$error_puntos"}{$error_puntos}{else}Cantidad de puntos que se le otorgan por día.{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_puntos_dar"} error{/if}">
		<label class="control-label" for="puntos_dar">Puntos por día</label>
		<div class="controls">
			<input type="text" value="{$puntos_dar}" name="puntos_dar" id="puntos_dar" class="span10" />
			<span class="help-block">{if="$error_puntos_dar"}{$error_puntos_dar}{else}Cantidad máxima de puntos a dar por post.{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_tipo"} error{/if}">
		<label class="control-label" for="tipo">Puntos por día</label>
		<div class="controls">
			<select name="tipo" id="tipo">
				<option value="0"{if="$tipo==0"} selected="selected"{/if}>Especial</option>
				<option value="1"{if="$tipo==1"} selected="selected"{/if}>Puntos</option>
				<option value="2"{if="$tipo==2"} selected="selected"{/if}>Posts</option>
				<option value="3"{if="$tipo==3"} selected="selected"{/if}>Fotos</option>
				<option value="4"{if="$tipo==4"} selected="selected"{/if}>Comentarios</option>
			</select>
			<span class="help-block">{if="$error_tipo"}{$error_tipo}{else}Tipo de rango. Especial implica que son rango a asignar manualmente, mientras que el resto son asignado automáticamente al cumplir los requisitos especificados. Solo necesario si el tipo no es especial.{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_cantidad"} error{/if}">
		<label class="control-label" for="cantidad">Puntos por día</label>
		<div class="controls">
			<input type="text" value="{$cantidad}" name="cantidad" id="cantidad" class="span10" />
			<span class="help-block">{if="$error_cantidad"}{$error_cantidad}{else}Cantidad de post/fotos/comentarios/puntos que debe tener para poder tener este rango.{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button> o <a href="{#SITE_URL#}/admin/usuario/rangos/">Volver</a>
	</div>
</form>