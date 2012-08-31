<div class="row">
	<div class="span12">
		<form class="form-horizontal" action="" method="POST">
			<fieldset>
				<legend>Cambiar Nick</legend>

				{if="isset($error_nick)"}
				<div class="alert">
					<a class="close" data-dismiss="alert">×</a>
					<strong>Error: </strong>{$error_nick}
				</div>
				{/if}
				{if="isset($error_password)"}
				<div class="alert">
					<a class="close" data-dismiss="alert">×</a>
					<strong>Error: </strong>{$error_password}
				</div>
				{/if}
				{if="isset($success)"}
				<div class="alert alert-success">
					<a class="close" data-dismiss="alert">×</a>
					<strong>Felicitaciones: </strong>{$success}
				</div>
				{/if}

				<div class="control-group">
					<label class="control-label">Actual</label>
					<div class="controls">
						<span>{$nick_actual}</span>
					</div>
				</div>

				<div class="control-group{if="isset($error_nick)"} error{/if}">
					<label class="control-label" for="nick">Nuevo</label>
					<div class="controls">
						<input type="text" id="nick" name="nick" value="{$nick}" />
					</div>
				</div>

				<div class="control-group{if="isset($error_password)"} error{/if}">
					<label class="control-label" for="password">Contrase&ntilde;a</label>
					<div class="controls">
						<input type="password" id="password" name="password" value="" />
					</div>
				</div>

				<button type="submit" class="btn btn-primary">Actualizar nick</button>

			</fieldset>
		</form>
	</div>
</div>