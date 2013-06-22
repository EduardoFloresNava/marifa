<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">{@Moderación@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/">{@Gestión@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/usuarios/">{@Censuras@}</a> <span class="divider">/</span></li>
    <li class="active">{@Nueva censura@}</li>
</ul>
<div class="header">
	<h2>{@Verificar censuras@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_entrada"} error{/if}">
		<label class="control-label" for="entrada">{@Entrada@}</label>
		<div class="controls">
			<textarea name="entrada" id="entrada">{$entrada}</textarea>
			<span class="help-block">{if="$error_entrada"}{$error_entrada}{else}{@Texto donde probar las censuras.@}{/if}</span>
		</div>
	</div>

	{if="isset($salida)"}<div class="control-group">
		<label class="control-label" for="salida">{@Salida de depuración@}</label>
		<div class="controls">
			<div class="well">{$salida_debug}</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="salida">{@Salida final@}</label>
		<div class="controls">
			<div class="well">{$salida}</div>
		</div>
	</div>{/if}

	<div class="control-group">
		<label class="control-label">{@Censuras a probar@}</label>
		<div class="controls">
			<ul>
				{loop="$censuras"}<li>#{$value->id} <code>{$value->valor}</code> <i class="icon icon-arrow-right"></i> <code>{$value->censura}</code> <span class="label">{if="$value->tipo == 0"}{@TEXTO@}{elseif="$value->tipo == 1"}{@PALABRA@}{else}{@REGEX@}{/if}</label></li>
				{/loop}
			</ul>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">{@Verificar@}</button> {@o@} <a href="{#SITE_URL#}/moderar/gestion/censuras/">{@Volver@}</a>
	</div>
</form>