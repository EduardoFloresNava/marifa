<div class="row">
	<div class="span12">
		<form class="form-horizontal" action="" method="POST">
			<fieldset>
				<legend>Cambiar contrase&ntilde;a</legend>

				{if="isset($error)"}
				<div class="alert">
					<a class="close" data-dismiss="alert">×</a>
					<strong>Error: </strong>{$error}
				</div>
				{/if}
				{if="isset($success)"}
				<div class="alert alert-success">
					<a class="close" data-dismiss="alert">×</a>
					<strong>Felicitaciones: </strong>{$success}
				</div>
				{/if}

				<div class="control-group{if="isset($error_current)"} error{/if}">
					<label class="control-label" for="current">Actual</label>
					<div class="controls">
						<input type="password" id="current" name="current" />
					</div>
				</div>

				<div class="control-group{if="isset($error_password) || isset($error_c_password)"} error{/if}">
					<label class="control-label" for="password">Nueva contrase&ntilde;a</label>
					<div class="controls">
						<p><input type="password" id="password" name="password" /></p>
						<p><input type="password" id="cpassword" name="cpassword" /></p>
					</div>
				</div>

				<button type="submit" class="btn btn-primary">Cambiar</button>

			</fieldset>
		</form>
	</div>
</div>