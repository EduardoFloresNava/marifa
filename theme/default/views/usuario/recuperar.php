<form class="form-horizontal" action="" method="POST">
	<fieldset>
		<legend>Recuperar credenciales de acceso:</legend>

		<div class="control-group{if="$error_email"} error{/if}">
			<label class="control-label" for="email">E-Mail o Usuario</label>
			<div class="controls">
				<input type="text" class="input-xlarge" id="email" name="email" value="{$email}" />
				<span class="help-inline">{if="$error_email"}{$error_email}{else}Tu nick actual o tu E-Mail. Si has cambiado tu nick, debes colocar el &uacute;ltimo.{/if}</span>
			</div>
		</div>
		<div class="form-actions">
			<button class="btn btn-primary">Recuperar</button>
			o
			<a href="{#SITE_URL#}/usuario/login/">&iquest;Tienes una cuenta?</a>
			<a href="{#SITE_URL#}/usuario/register/">&iquest;Necesitas una cuenta?</a>
		</div>

	</fieldset>
</form>