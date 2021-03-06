<h2 class="title">Configuración del sitio</h2>
<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
	<fieldset>
		<legend>Cuenta de usuario <small>Cuenta para administrar el sitio.</small></legend>
		<div class="control-group{if="$error_usuario"} error{/if}">
			<label class="control-label" for="usuario">Usuario</label>
			<div class="controls">
				<input type="text" name="usuario" id="usuario" class="input-large" value="{$usuario}" placeholder="Usuario" />
				<span class="help-inline">{if="$error_usuario"}{$error_usuario}{else}Nick de la cuenta para el administrador.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_email"} error{/if}">
			<label class="control-label" for="email">E-Mail</label>
			<div class="controls">
				<input type="text" name="email" id="email" class="input-large" value="{$email}" placeholder="E-Mail" />
				<span class="help-inline">{if="$error_email"}{$error_email}{else}Email de la cuenta de administrador.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_password"} error{/if}">
			<label class="control-label" for="password">Contraseña</label>
			<div class="controls">
				<input type="password" name="password" id="descripcion" class="input-large" />
				<span class="help-inline">{if="$error_password"}{$error_password}{else}Contraseña de la cuenta de administración{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_cpassword"} error{/if}">
			<label class="control-label" for="cpassword">Repetir contraseña</label>
			<div class="controls">
				<input type="password" name="cpassword" id="cpassword" class="input-large" />
				<span class="help-inline">{if="$error_cpassword"}{$error_cpassword}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Verificación</legend>
		<div class="control-group{if="$error_usuario"} error{/if}">
			<label class="control-label" for="bd_password">Contraseña</label>
			<div class="controls">
				<input type="password" name="bd_password" id="bd_password" class="input-large" value="{$bd_password}" />
				<span class="help-inline">{if="$error_bd_password"}{$error_bd_password}{else}Contraseña de conección a la base de datos. Necesaria por seguridad.{/if}</span>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Guardar</button>
	</div>

</form>