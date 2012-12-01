<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Detalles del baneo de {$usuario.nick}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Detalles del baneo de <a href="/perfil/index/{$usuario.nick}/">{$usuario.nick}</a></h2>
	<div class="btn-group pull-right">
		<a href="/admin/usuario/desbanear_usuario/{$usuario.id}" class="btn btn-success"><i class="icon-white icon-ok"></i> Desbanear</a>
		<a href="/admin/usuario/" class="btn btn-primary"><i class="icon-white icon-chevron-left"></i> Volver</a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>Moderador:</th>
		<td><a href="/perfil/index/{$moderador.nick}/">{$moderador.nick}</a></td>
	</tr>
	<tr>
		<th>Usuario:</th>
		<td><a href="/perfil/index/{$usuario.nick}/">{$usuario.nick}</a></td>
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