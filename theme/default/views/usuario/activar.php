<form class="form-horizontal" action="{#SITE_URL#}/usuario/pedir_activacion" method="POST">
	<fieldset>
		<legend>Pedir correo de activación</legend>

		<div class="control-group{if="$error_email"} error{/if}">
			<label class="control-label" for="email">E-Mail</label>
			<div class="controls">
				<input type="text" class="input-xlarge" id="email" name="email" value="{$email}" />
				<span class="help-inline">{if="$error_email"}{$error_email}{else}E-Mail de la cuenta a activar.{/if}</span>
			</div>
		</div>

		<div class="form-actions">
			<button class="btn btn-primary">Pedir correo</button>
			o
			<a href="{#SITE_URL#}/usuario/login/">¿Iniciar sesión?</a>
			<a href="{#SITE_URL#}/usuario/register/">¿Necesitas una cuenta?</a>
			<a href="{#SITE_URL#}/usuario/recovery/">¿Perdió su contraseña?</a>
		</div>

	</fieldset>
</form>