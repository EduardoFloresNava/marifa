<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/medallas">Medallas</a> <span class="divider">/</span></li>
    <li class="active">Usuarios en {$medalla.nombre}</li>
</ul>
<div class="header">
	<h2>Usuarios en {$medalla.nombre}</h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Nick</th>
			<th>E-Mail</th>
			<th>Ultima vez activo</th>
			<th>Estado</th>
		</tr>
	</thead>
	<tbody>
		{loop="$usuarios"}
		<tr>
			<td><a href="/perfil/index/{$value.nick}">{$value.nick}</a></td>
			<td>{$value.email}</td>
			<td>{$value.lastactive->fuzzy()}</td>
			<td><span class="label label-{if="$value.estado == 0"}info">PENDIENTE{elseif="$value.estado == 1"}success">ACTIVO{elseif="$value.estado == 2"}warning">SUSPENDIDO{elseif="$value.estado == 3"}important">BANEADO{/if}</span></td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="4">&iexcl;No hay usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}