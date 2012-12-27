<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Sesiones</li>
</ul>
<div class="header">
	<h2>Sesiones de los usuarios</h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>ID</th>
			<th>Usuario</th>
			<th>IP</th>
			<th>Expira</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$sesiones"}
		<tr>
			<td><code>{$value.id|strtoupper}</code></td>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.ip}</td>
			<td>{$value.expira->fuzzy()}</td>
			<td>
				<a href="{#SITE_URL#}/admin/usuario/terminar_session/{$value.id}" class="btn btn-mini btn-danger">Terminar Sesión</a>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">!No hay sesiones activas!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}