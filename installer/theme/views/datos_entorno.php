<h2 class="title">Datos del entorno</h2>
<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

	<div class="control-group{if="$error_cookie_secret"} error{/if}">
		<label class="control-label" for="cookie_secret">Clave para encriptar las cookies</label>
		<div class="controls">
			<input type="text" name="cookie_secret" id="cookie_secret" class="input-xxlarge" value="{$config.cookie_secret}" />
			<a href="#" id="make_random_cookie_secret" class="btn btn-success"><i class="icon-white icon-random"></i></a>
			<span class="help-inline">{if="$error_cookie_secret"}{$error_cookie_secret}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_language"} error{/if}">
		<label class="control-label" for="language">Idioma del sistema</label>
		<div class="controls">
			<input type="text" name="language" id="language" class="input-xxlarge" value="{$config.language}" />
			<span class="help-inline">{if="$error_language"}{$error_language}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_default_timezone"} error{/if}">
		<label class="control-label" for="default_timezone">Zona horaria del sistema</label>
		<div class="controls">
			<select id="default_timezone" name="default_timezone">
			{loop="$tz_list"}
				<option value="{$key}"{if="$value == $config.default_timezone"} selected{/if}>{$value}</option>
			{/loop}
			</select>
			<span class="help-inline">{if="$error_default_timezone"}{$error_default_timezone}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Guardar</button>
	</div>

</form>