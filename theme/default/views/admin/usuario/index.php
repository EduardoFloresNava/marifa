<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Usuarios</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Usuarios</h2>
	<div class="pull-right btn-group">
		<a href="/admin/usuario/index/{$actual}/1/" class="btn btn-small btn-success{if="$tipo == 1"} active{/if}">Activos{if="$cantidad_activas > 0"} ({$cantidad_activas}){/if}</a>
		<a href="/admin/usuario/index/{$actual}/2/" class="btn btn-small btn-warning{if="$tipo == 2"} active{/if}">Suspendidos{if="$cantidad_suspendidas > 0"} ({$cantidad_suspendidas}){/if}</a>
		<a href="/admin/usuario/index/{$actual}/3/" class="btn btn-small btn-danger{if="$tipo == 3"} active{/if}">Baneados{if="$cantidad_baneadas > 0"} ({$cantidad_baneadas}){/if}</a>
		<a href="/admin/usuario/index/{$actual}/" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}">Todos{if="$cantidad_total > 0"} ({$cantidad_total}){/if}</a>
	</div>
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
			<td class="alert" colspan="5">&iexcl;No hay usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/admin/usuario/index/{$paginacion.first}/{$tipo}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/admin/usuario/index/{$paginacion.prev}/{$tipo}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/admin/usuario/index/{$value}/{$tipo}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/admin/usuario/index/{$paginacion.next}/{$tipo}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/admin/usuario/index/{$paginacion.last}/{$tipo}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}