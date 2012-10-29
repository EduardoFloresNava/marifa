<form class="form-horizontal" action="" method="POST">
	<fieldset>
		<legend>Restaurar contrase&ntilde;a:</legend>

		<div class="control-group{if="$error_password"} error{/if}">
			<label class="control-label" for="password">Nueva clave</label>
			<div class="controls">
				<input type="password" class="input-xlarge" id="password" name="password" value="" />
				<span class="help-inline">{if="$error_password"}{$error_password}{else}Tu nueva clave de acceso.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_cpassword"} error{/if}">
			<label class="control-label" for="cpassword">Verificar nueva clave</label>
			<div class="controls">
				<input type="password" class="input-xlarge" id="cpassword" name="cpassword" value="" />
				<span class="help-inline">{if="$error_cpassword"}{$error_cpassword}{else}Vuelva a ingresar la nueva clave de acceso.{/if}</span>
			</div>
		</div>

		<div class="form-actions">
			<button class="btn btn-primary">Cambiar clave</button>
			o
			<a href="/usuario/login/">&iquest;Tienes una cuenta?</a>
			<a href="/usuario/register/">&iquest;Necesitas una cuenta?</a>
		</div>

	</fieldset>
</form>