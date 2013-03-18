<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido">{@Contenido@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/paginas">{@Paginas@}</a> <span class="divider">/</span></li>
    <li class="active">{@Nueva página@}</li>
</ul>
<div class="header">
	<h2>{@Nueva página@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_titulo"} error{/if}">
		<label class="control-label" for="titulo">{@Título@}</label>
		<div class="controls">
			<input type="text" name="titulo" id="titulo" class="span10" value="{$titulo}" />
			<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_menu"} error{/if}">
		<label class="control-label" for="menu">{@Menú@}</label>
		<div class="controls">
			<select name="menu" id="menu">
				<option value="0" {if="$menu == 0"} selected="selected"{/if}>{@Superior@}</option>
				<option value="1" {if="$menu == 1"} selected="selected"{/if}>{@Pie@}</option>
				<option value="2" {if="$menu == 2"} selected="selected"{/if}>{@Ambos@}</option>
			</select>
			<span class="help-block">{if="$error_menu"}{$error_menu}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_estado"} error{/if}">
		<label class="control-label" for="estado">{@Estado@}</label>
		<div class="controls">
			<select name="estado" id="estado">
				<option value="0" {if="$estado == 0"} selected="selected"{/if}>{@Oculto@}</option>
				<option value="1" {if="$estado == 1"} selected="selected"{/if}>{@Visible@}</option>
			</select>
			<span class="help-block">{if="$error_estado"}{$error_estado}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_contenido"} error{/if}">
		<label class="control-label" for="contenido">{@Contenido@}</label>
		<div class="controls">
			<textarea name="contenido" id="contenido" class="span10">{$contenido}</textarea>
			<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Crear@}</button> {@o@} <a href="{#SITE_URL#}/admin/contenido/paginas">{@Volver@}</a>
	</div>
</form>