<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido">{@Contenido@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/noticias">{@Noticias@}</a> <span class="divider">/</span></li>
    <li class="active">{@Nuevo boletín@}</li>
</ul>
<div class="header">
	<h2>{@Nuevo boletín@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_titulo"} error{/if}">
		<label class="control-label" for="titulo">{@Título@}</label>
		<div class="controls">
			<input type="text" name="titulo" id="titulo" value="{$titulo}" />
			<span class="help-block">{if="$error_titulo"}{$error_titulo}{else}{@Título del boletín.@}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_contenido"} error{/if}">
		<label class="control-label" for="titulo">{@Contenido@}</label>
		<div class="controls">
			{include="helper/bbcode_bar"}
			<textarea name="contenido" id="contenido" data-preview="{#SITE_URL#}/admin/contenido/preview" class="span10">{$contenido}</textarea>
			<span class="help-block">{if="$error_contenido"}{$error_contenido}{else}{@Contenido del boletín.@}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_grupos"} error{/if}">
		<label class="control-label" for="titulo">{@Grupos@}</label>
		<div class="controls">
			{loop="$grupos_disponibles"}
			<label class="checkbox block">
				<input type="checkbox" name="grupos[]" id="grupos[]" value="{$key}"{if="in_array($key, $grupos)"} checked{/if}>
				{$value}
			</label>
			{/loop}
			<span class="help-block">{if="$error_grupos"}{$error_grupos}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Enviar@}</button> o <a href="{#SITE_URL#}/admin/contenido/noticias">{@Volver@}</a>
	</div>
</form>