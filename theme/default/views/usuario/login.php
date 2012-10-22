<div class="row-fluid">
	<div class="span12">
		<form class="form-horizontal" action="/usuario/login" method="POST">
			<fieldset>
				<legend>Inicio de Sessi&oacute;n</legend>

				{if="isset($error)"}
				<div class="alert alert-danger">
					<a class="close" data-dismiss="alert">×</a>
					<strong>Error: </strong>{$error}
				</div>
				{/if}

				<div class="control-group{if="$error_nick"} error{/if}">
					<label class="control-label" for="nick">E-Mail o Usuario</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="nick" name="nick" value="{$nick}" />
						<span class="help-inline">Tu nick actual o tu E-Mail. Si has cambiado tu nick, debes colocar el último.</span>
					</div>
				</div>

				<div class="control-group{if="$error_password"} error{/if}">
					<label class="control-label" for="password">Contrase&ntilde;a</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="password" name="password" />
						<span class="help-inline">La contraseña de acceso a tu cuenta.</span>
					</div>
				</div>

				<div class="form-actions">
					<button class="btn btn-primary">Ingresar</button>
					o
					<a href="/usuario/register">&iquest;Necesitas una cuenta?</a>
					<a href="/usuario/recovery">&iquest;Perdio su contrase&ntilde;a?</a>
				</div>

			</fieldset>
		</form>
	</div>
</div>