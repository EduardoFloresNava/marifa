<h2 class="title">Importar datos:</h2>
{if="!isset($terminado)"}
<div class="alert alert-warning"><strong>&iexcl;IMPORTANTE!</strong> Al realizarse la importación toda información existente será eliminada. Solo realize una importación si es una instalación limpia, en caso contrario puede generar perdida de información y/o fallas irreversibles en el script.</div>
<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

	<div class="control-group{if="$error_importador"} error{/if}">
		<label class="control-label" for="importador">Importador</label>
		<div class="controls">
			<select id="importador" name="importador">
				{loop="$importadores"}
				<option value="{$value}"{if="$value == $importador"} selected="selected"{/if}>{$value}</option>
				{/loop}
			</select>
			<span class="help-inline">{if="$error_importador"}{$error_importador}{/if}</span>
		</div>
	</div>

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

	<div class="form-actions">
		<button type="submit" name="method" value="import" class="btn btn-primary">Importar</button>
		<button type="submit" name="method" value="skip" class="btn btn-info">Omitir <i class="icon-white icon-arrow-right"></i></button>
	</div>
</form>
{else}<a class="btn btn-success" href="{function="Installer_Step::url_siguiente('importar')"}">Continuar</a>{/if}