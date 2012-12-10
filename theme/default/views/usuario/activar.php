<form class="form-horizontal" action="{#SITE_URL#}/usuario/pedir_activacion" method="POST">
	<fieldset>
		<legend>Pedir correo de activaci&oacute;n</legend>

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
			<a href="{#SITE_URL#}/usuario/login/">&iquest;Iniciar sesi&oacute;n?</a>
			<a href="{#SITE_URL#}/usuario/register/">&iquest;Necesitas una cuenta?</a>
			<a href="{#SITE_URL#}/usuario/recovery/">&iquest;Perdi&oacute; su contrase&ntilde;a?</a>
		</div>

	</fieldset>
</form>