<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Correo</li>
</ul>
<h2 class="title">Correo</h2>
{if=" ! isset($configuracion) || ! is_array($configuracion)"}
<div class="alert alert-info">
	Aún no se encuentra configurado el envío de correos. Para configurarlo copie el archivo <code>/config/email.example.php</code> a <code>/config/email.php</code> y rellene con sus configuraciones.
</div>
{else}
<div class="alert alert-info">
	Para cambiar las configuraciones del correo, edite el archivo <code>/config/email.php</code>. En <code>/config/email.example.php</code> puede encontrar uno documentado sobre las distintas opciones.
</div>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th colspan="2">Configuración del correo</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Transporte:</th>
			<td>{if="isset($configuracion.transport) && in_array($configuracion.transport, array('mail', 'sendmail', 'smtp'))"}<code>{$configuracion.transport|strtoupper}</code>{else}<span class="badge badge-important">INCORRECTO</span>{/if}</td>
		</tr>
		{if="isset($configuracion.transport)"}
			{if="$configuracion.transport == 'sendmail'"}
				{if="isset($configuracion.parametros) && is_array($configuracion.parametros) && isset($configuracion.parametros['command'])"}
		<tr>
			<th>Comando:</th>
			<td><code>{$configuracion.parametros.command}</code></td>
		</tr>
				{else}
		<tr>
			<th>Comando:</th>
			<td><code>/usr/sbin/sendmail</code></td>
		</tr>
				{/if}
			{/if}{if="$configuracion.transport == 'smtp'"}
				{if="isset($configuracion.parametros) && is_array($configuracion.parametros)"}
					{if="isset($configuracion.parametros.host)"}
		<tr>
			<th>Host:</th>
			<td><code>{$configuracion.parametros.host}</code></td>
		</tr>
					{/if}
					{if="isset($configuracion.parametros.port)"}
		<tr>
			<th>Port:</th>
			<td><code>{$configuracion.parametros.port}</code></td>
		</tr>
					{/if}
					{if="isset($configuracion.parametros.encryption)"}
		<tr>
			<th>Encriptación:</th>
			<td><code>{$configuracion.parametros.encryption}</code></td>
		</tr>
					{/if}
					{if="isset($configuracion.parametros.username)"}
		<tr>
			<th>Usuario:</th>
			<td><code>{$configuracion.parametros.username}</code></td>
		</tr>
					{/if}
					{if="isset($configuracion.parametros.password)"}
		<tr>
			<th>Contraseña:</th>
			<td><code>{$configuracion.parametros.password}</code></td>
		</tr>
					{/if}
				{/if}
			{/if}
		{/if}
		{if="isset($configuracion.from) && is_array($configuracion.from)"}
		<tr>
			<th>De:</th>
			<td>{if="isset($configuracion.from.usuario) && isset($configuracion.from.email)"}
				<code>
					{if="isset($configuracion.from.usuario)"}{$configuracion.from.usuario}{else}<span class="badge badge-important">SIN ESPECIFICAR</span>{/if}
					&lt;{if="isset($configuracion.from.email)"}{$configuracion.from.email}{else}<span class="badge badge-important">SIN ESPECIFICAR</span>{/if}&gt;
				</code>
				{else}
				<span class="badge badge-important">SIN ESPECIFICAR</span>
				{/if}
			</td>
		</tr>
		{else}
		<tr>
			<th>De:</th>
			<td><span class="badge badge-important">SIN ESPECIFICAR</span></td>
		</tr>
		{/if}
	</tbody>
</table>
<h3 class="title">Envío de correo de prueba</h3>
<form method="POST" class="form-horizontal" action="{#SITE_URL#}/admin/configuracion/test_mail">

	<div class="control-group">
		<label class="control-label" for="nombre">Dirección de correo:</label>
		<div class="controls">
			<input type="text" value="{$email}" name="email" id="email" class="input-large" />
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Enviar</button>
	</div>
</form>
{/if}