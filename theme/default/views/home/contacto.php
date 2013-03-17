<h1 class="title">{@Contacto@}</h1>
<form method="POST" class="form-horizontal" action="">
	<div class="control-group{if="$error_nombre"} error{/if}">
		<label class="control-label" for="nombre">{@Nombre o e-mail:@}</label>
		<div class="controls">
			<input type="text" name="nombre" id="nombre" value="{$nombre}" />
			<span class="help-inline">{if="$error_nombre"}{$error_nombre}{else}{@Nombre de quien solicita contacto. Puede ser también un correo electrónico.@}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_asunto"} error{/if}">
		<label class="control-label" for="asunto">{@Asunto:@}</label>
		<div class="controls">
			<input type="text" name="asunto" id="asunto" value="{$asunto}" />
			<span class="help-inline">{if="$error_asunto"}{$error_asunto}{else}{@Asunto por el que envia el mensaje.@}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_mensaje"} error{/if}">
		<label class="control-label" for="mensaje">{@Mensaje:@}</label>
		<div class="controls">
			<textarea name="mensaje" id="mensaje">{$mensaje}</textarea>
			{if="$error_mensaje"}<span class="help-inline">{$error_mensaje}</span>{/if}
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Enviar@}</button>
	</div>
</form>