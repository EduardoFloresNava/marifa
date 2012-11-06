<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/contenido">Contenido</a> <span class="divider">/</span></li>
    <li><a href="/admin/contenido/posts">Posts</a> <span class="divider">/</span></li>
    <li class="active">Eliminar</li>
</ul>
<div class="header">
	<h2 class="title">Eliminar post</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	{if="isset($success)"}<div class="alert alert-success">{$success}</div>{/if}

	<div class="control-group{if="$error_tipo"} error{/if}">
		<label class="control-label" for="tipo">Motivo</label>
		<div class="controls">
			<select name="tipo" id="tipo">
				<option value="0" {if="$tipo == 0"}selected="selected"{/if}>Re-post</option>
				<option value="1" {if="$tipo == 1"}selected="selected"{/if}>spam</option>
				<option value="2" {if="$tipo == 2"}selected="selected"{/if}>Contiene links muertos</option>
				<option value="3" {if="$tipo == 3"}selected="selected"{/if}>Racista o irrespetuoso</option>
				<option value="4" {if="$tipo == 4"}selected="selected"{/if}>Contiene información personal</option>
				<option value="5" {if="$tipo == 5"}selected="selected"{/if}>Titulo en mayúsculas</option>
				<option value="6" {if="$tipo == 6"}selected="selected"{/if}>Contiene pedofilia</option>
				<option value="7" {if="$tipo == 7"}selected="selected"{/if}>Gore o asqueroso</option>
				<option value="8" {if="$tipo == 8"}selected="selected"{/if}>Fuente incorrecta</option>
				<option value="9" {if="$tipo == 9"}selected="selected"{/if}>Contenido pobre o crap</option>
				<option value="10" {if="$tipo == 10"}selected="selected"{/if}>No es un foro</option>
				<option value="11" {if="$tipo == 11"}selected="selected"{/if}>No cumple el protocolo</option>
				<option value="12" {if="$tipo == 12"}selected="selected"{/if}>Otra (especificar)</option>
			</select>
			<span class="help-block">{if="$error_tipo"}{$error_tipo}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_razon"} error{/if}">
		<label class="control-label" for="razon">Razón</label>
		<div class="controls">
			<input type="text" value="{$razon}" name="razon" id="razon" class="span10" />
			<span class="help-block">{if="$error_razon"}{$error_razon}{else}Razón para el caso de un motivo personalizado.{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_borrador"} error{/if}">
		<label class="control-label" for="borrador">Borrador</label>
		<div class="controls">
			<label>
				<input type="checkbox" value="1" name="borrador" id="borrador" {if="$borrador"}selected{/if} />
				Enviar como borrador del usuario para que pueda corregirlo y postearlo.
			</label>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Borrar</button> o <a href="/admin/contenido/noticias">Volver</a>
	</div>
</form>