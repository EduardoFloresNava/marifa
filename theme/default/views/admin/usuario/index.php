<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Usuarios</li>
</ul>
<div class="header">
	<h2>Usuarios</h2>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
{if="isset($error)"}<div class="alert">{$error}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Nick</th>
			<th>E-Mail</th>
			<th>Ultima vez activo</th>
			<th>Estado</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$usuarios"}
		<tr>
			<td>{$value.nick}</td>
			<td>{$value.email}</td>
			<td>{$value.lastactive->fuzzy()}</td>
			<td><span class="label label-{if="$value.estado == 0"}info">PENDIENTE{elseif="$value.estado == 1"}success">ACTIVO{elseif="$value.estado == 2"}warning">SUSPENDIDO{elseif="$value.estado == 3"}important">BANEADO{/if}</span></td>
			<td style="text-align: center;">
				<div class="btn-group">
					{if="$value.estado == 0 || $value.estado == 1"}<a href="/admin/usuario/suspender_usuario/{$value.id}" class="btn btn-mini btn-info">Suspender</a>{/if}
					{if="$value.estado == 2"}<a href="/admin/usuario/quitar_suspension_usuario/{$value.id}" class="btn btn-mini btn-info">Quitar suspensión</a>{/if}
					{if="$value.estado == 1"}<a href="/admin/usuario/advertir_usuario/{$value.id}" class="btn btn-mini btn-warning">Advertir</a>{/if}
					{if="$value.estado == 0 || $value.estado == 1 || $value.estado == 2"}<a href="/admin/usuario/banear_usuario/{$value.id}" class="btn btn-mini btn-danger">Banear</a>{/if}
					{if="$value.estado == 3"}<a href="/admin/usuario/desbanear_usuario/{$value.id}" class="btn btn-mini btn-danger">Desbanear</a>{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="4">&iexcl;No hay usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/admin/usuario/index/{$paginacion.first}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/admin/usuario/index/{$paginacion.prev}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/admin/usuario/index/{$value}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/admin/usuario/index/{$paginacion.next}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/admin/usuario/index/{$paginacion.last}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}