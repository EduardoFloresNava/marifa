<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Usuarios</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Usuarios</h2>
	<div class="pull-right btn-group">
		<a href="/admin/usuario/index/{$actual}/1/" class="btn btn-small btn-success{if="$tipo == 1"} active{/if}">Activos{if="$cantidad_activas > 0"} ({$cantidad_activas}){/if}</a>
		<a href="/admin/usuario/index/{$actual}/4/" class="btn btn-small btn-info{if="$tipo == 4"} active{/if}">Pendientes{if="$cantidad_pendientes > 0"} ({$cantidad_pendientes}){/if}</a>
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
			<th>Rango</th>
			<th>Ultima vez activo</th>
			<th>Estado</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$usuarios"}
		<tr>
			<td><a href="/perfil/index/{$value.nick}">{$value.nick}</a></td>
			<td><img src="{#THEME_URL#}/assets/img/rangos/{$value.rango.imagen}" /> <strong style="color: #{function="sprintf('%06s', dechex($value.rango.color))"};">{$value.rango.nombre}</strong></td>
			<td>{if="$value.lastactive == NULL"}<span class="label">NUNCA</span>{else}{$value.lastactive->fuzzy()}{/if}</td>
			<td>
				<span class="label label-{if="$value.estado == 0"}info">PENDIENTE{elseif="$value.estado == 1"}success">ACTIVO{elseif="$value.estado == 2"}warning show-tooltip" title="Restante: {$value.restante|secs_to_h}">SUSPENDIDO{elseif="$value.estado == 3"}important">BANEADO{/if}</span>
				{if="$value.avisos !== 0"}<a href="/admin/usuario/advertencias_usuario/{$value.id}"><span class="label">{$value.avisos} advertencia{if="$value.avisos > 1"}s{/if}</span></a>{/if}
			</td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						{if="$value.estado == 0"}
						<a href="/admin/usuario/activar_usuario/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Activar cuenta"><i class="icon-white icon-ok"></i></a>
						<a href="/admin/usuario/suspender_usuario/{$value.id}" class="btn btn-mini btn-warning show-tooltip" title="Suspender"><i class="icon-white icon-warning-sign"></i></a>
						<a href="/admin/usuario/banear_usuario/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Banear"><i class="icon-white icon-ban-circle"></i></a>
						{elseif="$value.estado == 1"}
						<a href="/admin/usuario/suspender_usuario/{$value.id}" class="btn btn-mini btn-warning show-tooltip" title="Suspender"><i class="icon-white icon-warning-sign"></i></a>
						<a href="/admin/usuario/advertir_usuario/{$value.id}" class="btn btn-mini btn-inverse show-tooltip" title="Advertir"><i class="icon-white icon-bell"></i></a>
						<a href="/admin/usuario/banear_usuario/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Banear"><i class="icon-white icon-ban-circle"></i></a>
						{elseif="$value.estado == 2"}
						<a href="/admin/usuario/quitar_suspension_usuario/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Terminar suspensión"><i class="icon-white icon-ok"></i></a>
						<a href="/admin/usuario/detalles_suspension_usuario/{$value.id}" class="btn btn-mini btn-info show-tooltip" title="Detalles de la suspensión"><i class="icon-white icon-info-sign"></i></a>
						<a href="/admin/usuario/banear_usuario/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Banear"><i class="icon-white icon-ban-circle"></i></a>
						{elseif="$value.estado == 3"}
						<a href="/admin/usuario/desbanear_usuario/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Desbanear"><i class="icon-white icon-ok"></i></a>
						<a href="/admin/usuario/detalles_baneo_usuario/{$value.id}" class="btn btn-mini btn-info show-tooltip" title="Detalles baneo"><i class="icon-white icon-info-sign"></i></a>
						{/if}
					</div>
					<div class="btn-group">
						<a href="/admin/usuario/cambiar_rango/{$value.id}" class="btn btn-mini btn-primary">Cambiar rango</a>
						<button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
							{$uid_aux=$value.id}
							{$rango_aux=$value.rango_id}
							{loop="$rangos"}
							{if="$rango_aux !== $value.id"}<li><a href="/admin/usuario/cambiar_rango/{$uid_aux}/{$value.id}/"><img src="{#THEME_URL#}/assets/img/rangos/{$value.imagen}" /> {$value.nombre}</a></li>{/if}
							{/loop}
						</ul>
					</div>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="6">&iexcl;No hay usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}