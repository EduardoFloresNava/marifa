<div class="row">
	<div class="span5">
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
					<label class="control-label" for="actual">Actual</label>
					<div class="controls">
						<input type="text" disabled="disabled" value="{$nick_actual}" name="actual" id="actual" />
					</div>
				</div>

				{if="count($nicks) < 3"}
					{if="$tiempo_cambio <=0"}
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

				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Actualizar nick</button>
				</div>
					{else}
				<div class="alert alert-info">
					Solo puedes cambiar tu nick cada 2 meses.
				</div>
					{/if}
				{else}
				<div class="alert alert-info">
					Alcanzaste el m&aacute;ximo de nick's que se tienen permitidos poseer.
				</div>
				{/if}
			</fieldset>
		</form>
	</div>
	<div class="span5">
		{if="count($nicks) > 0"}
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>Nick</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{loop="$nicks"}
				<tr>
					<td>{$value}</td>
					<td>
						<a href="/cuenta/eliminar_nick/{$value}" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i> Eliminar</a>
						<a href="/cuenta/utilizar_nick/{$value}" class="btn btn-mini btn-info"><i class="icon-white icon-ok"></i> Utilizar</a>
					</td>
				</tr>
				{/loop}
			</tbody>
		</table>
		{else}
		<div class="alert">No tienes otros nick's.</div>
		{/if}
	</div>
	<div class="span2">
		<img class="thumbnail" src="{function="Utils::get_gravatar($email, 150, 150)"}" />
	</div>
</div>