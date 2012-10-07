<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Sessiones</li>
</ul>
<div class="header">
	<h2>Sessiones de los usuarios</h2>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
{if="isset($error)"}<div class="alert">{$error}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
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
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/admin/usuario/sessiones/{$paginacion.first}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/admin/usuario/sessiones/{$paginacion.prev}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/admin/usuario/sessiones/{$value}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/admin/usuario/sessiones/{$paginacion.next}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/admin/usuario/sessiones/{$paginacion.last}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}