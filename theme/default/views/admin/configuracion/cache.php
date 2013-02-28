<ul class="breadcrumb">
	<li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/configuracion/">{@Configuración@}</a> <span class="divider">/</span></li>
	<li class="active">{@Cache@}</li>
</ul>
<div class="header">
	<h2>{@Cache@}</h2>
</div>

{if="isset($error)"}<div class="alert alert-danger">{$error}</div>{/if}

<form method="POST" class="form-horizontal" action="">
	<fieldset>
		<legend>General</legend>

		<div class="control-group{if="$error_driver"} error{/if}">
			<label class="control-label" for="driver">{@Driver@}</label>
			<div class="controls">
				<select id="driver" name="driver">
					{loop="$drivers"}
					<option value="{$value}"{if="$value == $driver"} selected="selected"{/if}>{$value}</option>
					{/loop}
				</select>
				<span class="help-block">{if="$error_driver"}{$error_driver}{else}{@Drivers para cache. Se recomiendo APC o Memcached que poseen mejor rendimiento, en caso de no estar disponibles file una buena opción. Dummy desactiva la cache (no recomendado).@}{/if}</span>
			</div>
		</div>
	</fieldset>

	{if="in_array('file', $drivers)"}
	<fieldset>
		<legend>File</legend>

		<div class="control-group{if="isset($error_path) && $error_path"} error{/if}">
			<label class="control-label" for="path">{@Directorio@}</label>
			<div class="controls">
				<input type="text" name="path" id="path" value="{if="isset($path)"}{$path}{/if}" />
				<span class="help-block">{if="isset($error_path) && $error_path"}{$error_path}{else}{@Directorio donde colocar la cache. Por defecto es <code>APP_BASE/cache/file/</code>@}{/if}</span>
			</div>
		</div>
	</fieldset>
	{/if}

	{*if="in_array('memcached', $drivers)"*}
	<fieldset>
		<legend>Memcached</legend>

		<div class="control-group{if="isset($error_hostname) && $error_hostname"} error{/if}">
			<label class="control-label" for="hostname">{@Hostname@}</label>
			<div class="controls">
				<input type="text" name="hostname" id="hostname" value="{if="isset($hostname)"}{$hostname}{else}127.0.0.1{/if}" />
				{if="isset($error_hostname) && $error_hostname"}<span class="help-block">{$error_hostname}</span>{/if}
			</div>
		</div>

		<div class="control-group{if="isset($error_port) && $error_port"} error{/if}">
			<label class="control-label" for="port">{@Port@}</label>
			<div class="controls">
				<input type="text" name="port" id="port" value="{if="isset($port)"}{$port}{else}11211{/if}" />
				{if="isset($error_port) && $error_port"}<span class="help-block">{$error_port}</span>{/if}
			</div>
		</div>

		<div class="control-group{if="isset($error_weight) && $error_weight"} error{/if}">
			<label class="control-label" for="path">{@Weight@}</label>
			<div class="controls">
				<input type="text" name="weight" id="weight" value="{if="isset($weight)"}{$weight}{else}1{/if}" />
				{if="isset($error_weight) && $error_weight"}<span class="help-block">{$error_weight}</span>{/if}
			</div>
		</div>
	</fieldset>
	{*/if*}

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Actualizar@}</button>
	</div>
</form>