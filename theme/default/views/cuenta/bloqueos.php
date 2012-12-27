<h2 class="title">Bloqueos:</h2>
{if="count($bloqueos) > 0"}
<ul class="thumbnails">
	{loop="$bloqueos"}
	<li class="span2">
		<a href="{#SITE_URL#}/perfil/index/{$value.nick}" class="thumbnail user-icon">
			<img src="{function="Utils::get_gravatar($value.email, 160, 160)"}" />
			<h4 class="nick">{$value.nick}</h4>
		</a>
	</li>
	{/loop}
</ul>
{else}
<div class="alert">No estás bloqueando ningún usuario aún.</div>
{/if}
<form class="form-horizontal" action="" method="POST">
	<fieldset>
		<legend>Bloquear usuario:</legend>

		<div class="control-group{if="$error_usuario"} error{/if}">
			<label class="control-label" for="usuario">Usuario</label>
			<div class="controls">
				<input type="text" id="usuario" name="usuario" value="{$usuario}" />
				<div class="help-inline">{if="$error_usuario"}{$error_usuario}{else}Nick del usuario a bloquear.{/if}</div>
			</div>
		</div>

		<div class="form-actions">
			<input type="submit" value="Bloquear" class="btn btn-large btn-primary" />
		</div>
	</fieldset>
</form>