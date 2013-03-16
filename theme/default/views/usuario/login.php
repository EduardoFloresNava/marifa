<div class="row-fluid">
	<div class="span12">
		<form class="form-horizontal" id="login" action="" method="POST">
			<fieldset>
				<legend>Inicio de Sesión</legend>

				<div class="control-group{if="$error_nick"} error{/if}">
					<label class="control-label" for="nick">E-Mail o Usuario</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="nick" name="nick" value="{$nick}" />
						<span class="help-inline">Tu nick actual o tu E-Mail. Si has cambiado tu nick, debes colocar el último.</span>
					</div>
				</div>

				<div class="control-group{if="$error_password"} error{/if}">
					<label class="control-label" for="password">Contraseña</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="password" name="password" />
						<span class="help-inline">La contraseña de acceso a tu cuenta.</span>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary" type="submit">Ingresar</button>
					o
					<a href="{#SITE_URL#}/usuario/register/">¿Necesitas una cuenta?</a>
					<a href="{#SITE_URL#}/usuario/recuperar/">¿Perdió su contraseña?</a>
				</div>

			</fieldset>
		</form>
	</div>
</div>