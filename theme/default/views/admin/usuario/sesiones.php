<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Sessiones</li>
</ul>
<div class="header">
	<h2>Sessiones de los usuarios</h2>
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
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.ip}</td>
			<td>{$value.expira->fuzzy()}</td>
			<td>
				<a href="/admin/usuario/terminar_session/{$value.id}" class="btn btn-mini btn-danger">Terminar Sessi&oacute;n</a>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay sesiones activas!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}