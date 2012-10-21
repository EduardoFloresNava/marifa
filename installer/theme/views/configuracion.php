<h2 class="title">Configuraci&oacute;n del sitio</h2>
<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

	{if="isset($error)"}<div class="alert">{$error}</div>{/if}

	<fieldset>
		<legend>Datos del sitio</legend>
		<div class="control-group{if="$error_nombre"} error{/if}">
			<label class="control-label" for="nombre">Nombre de la comunidad</label>
			<div class="controls">
				<input type="text" name="nombre" id="nombre" class="input-xxlarge" value="{$nombre}" placeholder="Marifa" />
				<span class="help-inline">{if="$error_nombre"}{$error_nombre}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_descripcion"} error{/if}">
			<label class="control-label" for="descripcion">Frase de la comunidad</label>
			<div class="controls">
				<input type="text" name="descripcion" id="descripcion" class="input-xxlarge" value="{$descripcion}" placeholder="Tu comunidad de forma simple" />
				<span class="help-inline">{if="$error_descripcion"}{$error_descripcion}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Cuenta de usuario</legend>
		<div class="control-group{if="$error_usuario"} error{/if}">
			<label class="control-label" for="usuario">Usuario</label>
			<div class="controls">
				<input type="text" name="usuario" id="usuario" class="input-xxlarge" value="{$usuario}" placeholder="Usuario" />
				<span class="help-inline">{if="$error_usuario"}{$error_usuario}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_email"} error{/if}">
			<label class="control-label" for="email">E-Mail</label>
			<div class="controls">
				<input type="text" name="email" id="email" class="input-xxlarge" value="{$email}" placeholder="E-Mail" />
				<span class="help-inline">{if="$error_email"}{$error_email}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_password"} error{/if}">
			<label class="control-label" for="password">Contrase&ntilde;a</label>
			<div class="controls">
				<input type="password" name="password" id="descripcion" class="input-xxlarge" />
				<span class="help-inline">{if="$error_password"}{$error_password}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_cpassword"} error{/if}">
			<label class="control-label" for="cpassword">Repetir contrase&ntilde;a</label>
			<div class="controls">
				<input type="password" name="cpassword" id="cpassword" class="input-xxlarge" />
				<span class="help-inline">{if="$error_cpassword"}{$error_cpassword}{/if}</span>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Guardar</button>
	</div>

</form>