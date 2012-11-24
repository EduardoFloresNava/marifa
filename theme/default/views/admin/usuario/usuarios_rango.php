<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/rangos">Rangos</a> <span class="divider">/</span></li>
    <li class="active">Usuarios en {$rango.nombre}</li>
</ul>
<div class="header">
	<h2>Usuarios en {$rango.nombre}</h2>
</div>
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
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						{if="$value.estado == 0"}<a href="/admin/usuario/activar_usuario/{$value.id}" class="btn btn-mini btn-success">Activar cuenta</a>{/if}
						{if="$value.estado == 0 || $value.estado == 1"}<a href="/admin/usuario/suspender_usuario/{$value.id}" class="btn btn-mini btn-info">Suspender</a>{/if}
						{if="$value.estado == 2"}<a href="/admin/usuario/quitar_suspension_usuario/{$value.id}" class="btn btn-mini btn-info">Quitar suspensión</a>{/if}
						{if="$value.estado == 1"}<a href="/admin/usuario/advertir_usuario/{$value.id}" class="btn btn-mini btn-warning">Advertir</a>{/if}
						{if="$value.estado == 0 || $value.estado == 1 || $value.estado == 2"}<a href="/admin/usuario/banear_usuario/{$value.id}" class="btn btn-mini btn-danger">Banear</a>{/if}
						{if="$value.estado == 3"}<a href="/admin/usuario/desbanear_usuario/{$value.id}" class="btn btn-mini btn-danger">Desbanear</a>{/if}
					</div>
					<div class="btn-group">
						<a href="/admin/usuario/cambiar_rango/{$value.id}" class="btn btn-mini btn-primary">Cambiar rango</a>
						<button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
							{$uid_aux=$value.id}
							{$rango_aux=$value.rango}
							{loop="$rangos"}
							{if="$rango_aux !== $value.id"}<li><a href="/admin/usuario/cambiar_rango/{$uid_aux}/{$value.id}/">{$value.nombre}</a></li>{/if}
							{/loop}
						</ul>
					</div>
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
{$paginacion}