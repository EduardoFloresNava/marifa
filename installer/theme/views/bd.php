<h2 class="title">Conección a la base de datos:</h2>
<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

	{if="isset($error)"}<div class="alert">{$error}</div>{/if}

	<div class="control-group{if="$error_driver"} error{/if}">
		<label class="control-label" for="driver">Driver</label>
		<div class="controls">
			<select id="driver" name="driver">
				{loop="$drivers"}
				<option value="{$key}"{if="$key == $driver"} selected="selected"{/if}>{$value}</option>
				{/loop}
			</select>
			<span class="help-inline">{if="$error_driver"}{$error_driver}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_host"} error{/if}">
		<label class="control-label" for="host">Host/DSN</label>
		<div class="controls">
			<input type="text" name="host" id="host" value="{$host}" />
			<span class="help-inline">{if="$error_host"}{$error_host}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_db_name"} error{/if}">
		<label class="control-label" for="db_name">Base de datos</label>
		<div class="controls">
			<input type="text" name="db_name" id="db_name" value="{$db_name}" />
			<span class="help-inline">{if="$error_db_name"}{$error_db_name}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_usuario"} error{/if}">
		<label class="control-label" for="usuario">Usuario</label>
		<div class="controls">
			<input type="text" name="usuario" id="usuario" value="{$usuario}" />
			<span class="help-inline">{if="$error_usuario"}{$error_usuario}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_password"} error{/if}">
		<label class="control-label" for="password">Contrase&ntilde;a</label>
		<div class="controls">
			<input type="password" name="password" id="password" />
			<span class="help-inline">{if="$error_password"}{$error_password}{/if}</span>
		</div>
	</div>

	<input type="submit" class="btn btn-large btn-primary" value="Conectar" />
</form>