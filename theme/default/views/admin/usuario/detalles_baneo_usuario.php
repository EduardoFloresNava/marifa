<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Detalles del baneo de {$usuario.nick}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Detalles del baneo de <a href="{#SITE_URL#}/@{$usuario.nick}/">{$usuario.nick}</a></h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/usuario/desbanear_usuario/{$usuario.id}" class="btn btn-success"><i class="icon-white icon-ok"></i> Desbanear</a>
		<a href="{#SITE_URL#}/admin/usuario/" class="btn btn-primary"><i class="icon-white icon-chevron-left"></i> Volver</a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>Moderador:</th>
		<td><a href="{#SITE_URL#}/@{$moderador.nick}/">{$moderador.nick}</a></td>
	</tr>
	<tr>
		<th>Usuario:</th>
		<td><a href="{#SITE_URL#}/@{$usuario.nick}/">{$usuario.nick}</a></td>
	</tr>
	<tr>
		<th>Fecha:</th>
		<td>{$baneo.fecha->fuzzy()} ({$baneo.fecha->format('d/m/Y H:i:s')})</td>
	</tr>
	<tr>
		<th>Motivo:</th>
		<td>{function="Decoda::procesar($baneo.razon)"}</td>
	</tr>
</table>