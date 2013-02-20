<ul class="breadcrumb">
	<li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/configuracion/">Configuración</a> <span class="divider">/</span></li>
	<li class="active">Base de Datos</li>
</ul>
<div class="header">
	<h2>Base de datos</h2>
</div>
<div class="alert alert-danger">
	<strong>¡IMPORTANTE!</strong> La modificación de los siguientes parámetros puede generar que el sitio deje de funcionar.<br />
	En caso de que esto pase, puede editar manualmente el archivo <code>/config/database.php</code> usando como base <code>/config/database.example.php</code>.
</div>

{if="$error_permisos"}<div class="alert">Las configuraciones no pueden ser modificadas debido a que no se tienen permisos de escritura en <code>/config/database.php</code></diV>{/if}
{if="isset($error)"}<div class="alert alert-danger">{$error}</div>{/if}

<form method="POST" class="form-horizontal" action="">
	<div class="control-group{if="$error_driver"} error{/if}">
		<label class="control-label" for="driver">Driver</label>
		<div class="controls">
			<select id="driver" name="driver">
				{loop="$drivers"}
				<option value="{$key}"{if="$key == $driver"} selected="selected"{/if}>{$value}</option>
				{/loop}
			</select>
			<span class="help-inline">{if="$error_driver"}{$error_driver}{else}Driver de base de datos. Se recomienda MySQLi por rendimiento y seguridad. Si no está disponible utilice PDO y MySQL debe ser la última instancia (se prefiere MySQL a PDO solo para desarrollo).{/if}</span>
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
		<label class="control-label" for="password">Contraseña</label>
		<div class="controls">
			<input type="password" name="password" id="password" value="{$password}" />
			<span class="help-inline">{if="$error_password"}{$error_password}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
	</div>
</form>