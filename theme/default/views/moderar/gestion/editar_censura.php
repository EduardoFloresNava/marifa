<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">{@Moderación@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/">{@Gestión@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/usuarios/">{@Censuras@}</a> <span class="divider">/</span></li>
    <li class="active">{@Editar censura@}</li>
</ul>
<div class="header">
	<h2>{@Editar censura@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_valor"} error{/if}">
		<label class="control-label" for="valor">{@Valor@}</label>
		<div class="controls">
			<input type="text" value="{$valor}" name="valor" id="valor" />
			<span class="help-block">{if="$error_valor"}{$error_valor}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_tipo"} error{/if}">
		<label class="control-label" for="tipo">{@Tipo@}</label>
		<div class="controls">
			<select id="tipo" name="tipo">
				<option value="0" {if="$tipo == 0"}selected="selected"{/if}>{@Texto@}</option>
				<option value="1" {if="$tipo == 1"}selected="selected"{/if}>{@Palabra@}</option>
				<option value="2" {if="$tipo == 2"}selected="selected"{/if}>{@RegEx@}</option>
			</select>
			<span class="help-block">{if="$error_tipo"}{$error_tipo}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_censura"} error{/if}">
		<label class="control-label" for="censura">{@Censura@}</label>
		<div class="controls">
			<input type="text" value="{$censura}" name="censura" id="censura" />
			<span class="help-block">{if="$error_censura"}{$error_censura}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_estado"} error{/if}">
		<label class="control-label" for="estado">{@Estado@}</label>
		<div class="controls">
			<label class="checkbox">
				<input type="checkbox" value="1" name="estado" id="estado" {if="$estado"}checked{/if} /> {@Activo@}
			</label>
			<span class="help-block">{if="$error_estado"}{$error_estado}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Editar@}</button> {@o@} <a href="{#SITE_URL#}/moderar/gestion/censuras/">{@Volver@}</a>
	</div>
</form>