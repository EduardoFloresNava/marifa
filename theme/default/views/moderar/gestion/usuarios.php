<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/gestion/">Gestión</a> <span class="divider">/</span></li>
    <li class="active">Usuarios</li>
</ul>
<div class="header">
	<h2>Usuarios suspendidos</h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Usuario</th>
			<th>Causa</th>
			<th>Suspendido</th>
			<th>Termina</th>
			<th>Moderador</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$suspensiones"}
		<tr>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.motivo}</td>
			<td>{$value.inicio->fuzzy()}</td>
			<td>{$value.fin->fuzzy()}</td>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>
				<a href="/moderar/gestion/terminar_suspension/{$value.usuario_id}" class="btn btn-mini btn-success"><i class="icon-white icon-remove"></i> Terminar suspensión</a>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="6">&iexcl;No hay suspensiones a usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}