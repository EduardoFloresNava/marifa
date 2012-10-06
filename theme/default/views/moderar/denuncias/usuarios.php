<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/denuncias/">Denuncias</a> <span class="divider">/</span></li>
    <li class="active">Usuarios</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Usuarios</h2>
	<div class="btn-group pull-right">
		<a href="/moderar/denuncias/usuarios/{$actual}/1" class="btn btn-small btn-danger{if="$tipo == 1"} active{/if}"><i class="icon-white icon-remove-circle"></i> Rechazadas{if="$cantidad_rechazados > 0"} ({$cantidad_rechazados}){/if}</a>
		<a href="/moderar/denuncias/usuarios/{$actual}/0" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}"><i class="icon-white icon-time"></i> Pendientes{if="$cantidad_pendientes > 0"} ({$cantidad_pendientes}){/if}</a>
		<a href="/moderar/denuncias/usuarios/{$actual}/2" class="btn btn-small btn-success{if="$tipo == 2"} active{/if}"><i class="icon-white icon-ok-circle"></i> Aprobadas{if="$cantidad_aprobados > 0"} ({$cantidad_aprobados}){/if}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Denunciante</th>
			<th>Denunciado</th>
			<th>Fecha</th>
			<th>Motivo</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$denuncias"}
		<tr>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="/perfil/index/{$value.denunciado.id}">{$value.denunciado.nick}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				{if="$value.motivo == 0"}
				<span class="label">EXISTE</span>
				{elseif="$value.motivo == 1"}
				<span class="label">SPAM</span>
				{elseif="$value.motivo == 2"}
				<span class="label">CAIDA</span>
				{elseif="$value.motivo == 3"}
				<span class="label">IRRESPETUOSA</span>
				{elseif="$value.motivo == 4"}
				<span class="label">INFORMACION PERSONAL</span>
				{elseif="$value.motivo == 5"}
				<span class="label">PEDOFILIA</span>
				{elseif="$value.motivo == 6"}
				<span class="label">ASQUEROSA</span>
				{elseif="$value.motivo == 7"}
				<span class="label">PERSONALIZADA</span>
				{else}
				<span class="label label-important">TIPO SIN DEFINIR</span>
				{/if}
			</td>
			<td style="text-align: center;">
				<div class="btn-group">
					<a href="/moderar/denuncias/detalle_usuario/{$value.id}" class="btn btn-mini" rel="tooltip" title="Detalles"><i class="icon icon-file"></i></a>
					<a href="/perfil/index/{$value.denunciado.nick}" class="btn btn-mini btn-info" rel="tooltip" title="Ver usuario"><i class="icon-white icon-eye-open"></i></a>
					{if="$value.estado == 0"}<a href="/moderar/denuncias/cerrar_denuncia_usuario/{$value.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Rechazar denuncia"><i class="icon-white icon-trash"></i></a>
					<a href="/moderar/denuncias/cerrar_denuncia_usuario/{$value.id}/1" class="btn btn-mini btn-success" rel="tooltip" title="Aceptar denuncia"><i class="icon-white icon-ok"></i></a>{/if}
					<a href="/moderar/denuncias/advertir_usuario/{$value.denunciado.id}" class="btn btn-mini btn-warning" rel="tooltip" title="Enviar advertencia"><i class="icon-white icon-warning-sign"></i></a>
					{if="$value.denunciado.estado !== 2"}<a href="/moderar/denuncias/suspender_usuario/{$value.denunciado.id}" class="btn btn-mini btn-warning" rel="tooltip" title="Suspender usuario"><i class="icon-white icon-ban-circle"></i></a>{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay denuncias a usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/moderar/denuncias/usuarios/{$paginacion.first}/{$tipo}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/moderar/denuncias/usuarios/{$paginacion.prev}/{$tipo}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/moderar/denuncias/usuarios/{$value}/{$tipo}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/moderar/denuncias/usuarios/{$paginacion.next}/{$tipo}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/moderar/denuncias/usuarios/{$paginacion.last}/{$tipo}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}