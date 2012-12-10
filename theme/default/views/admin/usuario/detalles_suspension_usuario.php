<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Detalles de la suspensión de {$usuario.nick}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Detalles de la suspensión de <a href="/perfil/index/{$usuario.nick}/">{$usuario.nick}</a></h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/usuario/banear_usuario/{$usuario.id}" class="btn btn-danger"><i class="icon-white icon-ban-circle"></i> Banear</a>
		<a href="{#SITE_URL#}/admin/usuario/quitar_suspension_usuario/{$usuario.id}" class="btn btn-success"><i class="icon-white icon-ok"></i> Quitar suspensión</a>
		<a href="{#SITE_URL#}/admin/usuario/" class="btn btn-primary"><i class="icon-white icon-chevron-left"></i> Volver</a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>Moderador:</th>
		<td><a href="{#SITE_URL#}/perfil/index/{$moderador.nick}/">{$moderador.nick}</a></td>
	</tr>
	<tr>
		<th>Usuario:</th>
		<td><a href="{#SITE_URL#}/perfil/index/{$usuario.nick}/">{$usuario.nick}</a></td>
	</tr>
	<tr>
		<th>Suspendido:</th>
		<td>{$suspension.inicio->fuzzy()} ({$suspension.inicio->format('d/m/Y H:i:s')})</td>
	</tr>
	<tr>
		<th>Termina:</th>
		<td>{$suspension.fin->fuzzy()} ({$suspension.fin->format('d/m/Y H:i:s')})</td>
	</tr>
	<tr>
		<th>Restan:</th>
		<td>{$restante|secs_to_h}</td>
	</tr>
	<tr>
		<th>Motivo:</th>
		<td>{function="Decoda::procesar($suspension.motivo)"}</td>
	</tr>
</table>