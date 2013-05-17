<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li class="active">{@Correo@}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">{@Correo@}</h2>
	<form method="POST" class="form-inline pull-right" action="{#SITE_URL#}/admin/configuracion/test_mail">
		<input type="text" value="{$email}" name="email" id="email" class="input-large" placeholder="{@E-Mail@}" />
		<button type="submit" class="btn btn-primary" title="{@Enviar un correo para verificar la configuración.@}">{@Enviar correo de prueba@}</button>
	</form>
</div>
<!--<h2 class="title">{@Correo@}</h2>-->

<form method="POST" class="form-horizontal" action="">
	<fieldset>
		<legend>{@General@}</legend>

		<div class="control-group{if="$error_from_name"} error{/if}">
			<label class="control-label" for="from_name">{@Nombre@}</label>
			<div class="controls">
				<input type="text" name="from_name" id="from_name" value="{$from_name}" />
				<span class="help-block">{if="$error_from_name"}{$error_from_name}{else}{@Nombre de quien envía los correos. Es utilizado para la cabecera FROM.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_from_email"} error{/if}">
			<label class="control-label" for="from_email">{@Correo@}</label>
			<div class="controls">
				<input type="text" name="from_email" id="from_email" value="{$from_email}" />
				<span class="help-block">{if="$error_from_email"}{$error_from_email}{else}{@Correo de quien envía los correos. Es utilizado para la cabecera FROM.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_transport"} error{/if}">
			<label class="control-label" for="transport">{@Transporte@}</label>
			<div class="controls">
				<select id="transport" name="transport">
					{loop="$transports"}
					<option value="{$key}"{if="$key == $transport"} selected="selected"{/if}>{$value}</option>
					{/loop}
				</select>
				<span class="help-block">{if="$error_transport"}{$error_transport}{else}{@Forma de enviar los correos. Dependiendo el método elegido debe agregar configuraciones extra.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Cola de envio@}:</legend>

		<div class="control-group{if="$error_queue_use"} error{/if}">
			<label class="control-label" for="queue_use">{@Cola de correos@}</label>
			<div class="controls">
				<label class="checkbox"><input type="checkbox" name="queue_use" id="queue_use" value="1" {if="$queue_use"}checked="checked"{/if} /> Usar la cola de envios</label>
				<span class="help-block">{if="$error_queue_use"}{$error_queue_use}{else}{@La cola de envios produce que los correos no se envien de forma directa, sino que el envío lo realiza un cronjob.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_queue_limit"} error{/if}">
			<label class="control-label" for="queue_limit">{@Límite por ejecución@}</label>
			<div class="controls">
				<input type="text" name="queue_limit" id="queue_limit" value="{$queue_limit}" />
				<span class="help-block">{if="$error_queue_limit"}{$error_path}{else}{@Cantidad máxima de correos que puede enviar por ejecución del cronjob.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_queue_limit_hour"} error{/if}">
			<label class="control-label" for="queue_limit_hour">{@Límite por hora@}</label>
			<div class="controls">
				<input type="text" name="queue_limit_hour" id="queue_limit_hour" value="{$queue_limit_hour}" />
				<span class="help-block">{if="isset($error_path) && $error_path"}{$error_path}{else}{@Cantidad máxima de correos que puede enviar por hora.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_queue_limit_day"} error{/if}">
			<label class="control-label" for="queue_limit_day">{@Límite por día@}</label>
			<div class="controls">
				<input type="text" name="queue_limit_day" id="queue_limit_day" value="{$queue_limit_day}" />
				<span class="help-block">{if="$error_queue_limit_day"}{$error_queue_limit_day}{else}{@Cantidad máxima de correos que puede enviar por día.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Sendmail@}:</legend>

		<div class="control-group{if="$error_sendmail_command"} error{/if}">
			<label class="control-label" for="sendmail_command">{@Comando@}</label>
			<div class="controls">
				<input type="text" name="sendmail_command" id="sendmail_command" value="{$sendmail_command}" />
				<span class="help-block">{if="$error_sendmail_command"}{$error_sendmail_command}{else}{@Comando a ejecutar para el envio de correos con SendMail@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@SMTP@}:</legend>

		<div class="control-group{if="$error_smtp_host"} error{/if}">
			<label class="control-label" for="smtp_host">{@Host@}</label>
			<div class="controls">
				<input type="text" name="smtp_host" id="smtp_host" value="{$smtp_host}" />
				<span class="help-block">{if="$error_smtp_host"}{$error_smtp_host}{else}{@Dirección para conectar con el servidor SMTP@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_smtp_port"} error{/if}">
			<label class="control-label" for="smtp_port">{@Puerto@}</label>
			<div class="controls">
				<input type="text" name="smtp_port" id="smtp_port" value="{$smtp_port}" />
				<span class="help-block">{if="$error_smtp_port"}{$error_smtp_port}{else}{@Puerto para conectar con el servidor SMTP.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_smtp_encryption"} error{/if}">
			<label class="control-label" for="smtp_encryption">{@Encriptación@}</label>
			<div class="controls">
				<select id="smtp_encryption" name="smtp_encryption">
					<option value=""{if="$smtp_encryption == ''"} selected="selected"{/if}>Ninguna</option>
					<option value="ssl"{if="$smtp_encryption == 'ssl'"} selected="selected"{/if}>SSL</option>
					<option value="tls"{if="$smtp_encryption == 'tls'"} selected="selected"{/if}>TLS</option>
				</select>
				<span class="help-block">{if="$error_smtp_encryption"}{$error_smtp_encryption}{else}{@Encriptación para el envio de datos al servidor SMTP.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_smtp_username"} error{/if}">
			<label class="control-label" for="smtp_username">{@Usuario@}</label>
			<div class="controls">
				<input type="text" name="smtp_username" id="smtp_username" value="{$smtp_username}" />
				<span class="help-block">{if="$error_smtp_username"}{$error_smtp_username}{else}{@Usuario para iniciar sesión en el servidor SMTP.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_smtp_password"} error{/if}">
			<label class="control-label" for="smtp_password">{@Contraseña@}</label>
			<div class="controls">
				<input type="password" name="smtp_password" id="smtp_password" value="{$smtp_password}" />
				<span class="help-block">{if="$error_smtp_password"}{$error_smtp_password}{else}{@Contraseña para iniciar sesión en el servidor SMTP.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Actualizar@}</button>
	</div>
</form>