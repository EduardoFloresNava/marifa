<div id="login-modal-form" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="login-modal-form-title" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="login-modal-form-title">{@Inicio de sesión@}</h3>
	</div>
	<div class="modal-body">
		<form class="form-horizontal" id="login" action="" method="POST">
			<div class="control-group">
				<label class="control-label" for="login-modal-form-nick">E-Mail o Usuario</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="login-modal-form-nick" name="login-modal-form-nick" title="{@Tu nick actual o tu E-Mail. Si has cambiado tu nick, debes colocar el último.@}" data-placement="right" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="login-modal-form-password">Contraseña</label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="login-modal-form-password" name="login-modal-form-password" title="{@La contraseña de acceso a tu cuenta.@}" data-placement="right" />
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" type="submit">{@Ingresar@}</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">{@Cerrar@}</button>
	</div>
</div>