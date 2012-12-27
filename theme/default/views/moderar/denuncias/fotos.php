<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/">Denuncias</a> <span class="divider">/</span></li>
    <li class="active">Fotos</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Fotos</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/moderar/denuncias/fotos/{$actual}/1" class="btn btn-small btn-danger{if="$tipo == 1"} active{/if}"><i class="icon-white icon-remove-circle"></i> Rechazadas{if="$cantidad_rechazados > 0"} ({$cantidad_rechazados}){/if}</a>
		<a href="{#SITE_URL#}/moderar/denuncias/fotos/{$actual}/0" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}"><i class="icon-white icon-time"></i> Pendientes{if="$cantidad_pendientes > 0"} ({$cantidad_pendientes}){/if}</a>
		<a href="{#SITE_URL#}/moderar/denuncias/fotos/{$actual}/2" class="btn btn-small btn-success{if="$tipo == 2"} active{/if}"><i class="icon-white icon-ok-circle"></i> Aprobadas{if="$cantidad_aprobados > 0"} ({$cantidad_aprobados}){/if}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Denunciante</th>
			<th>Foto</th>
			<th>Fecha</th>
			<th>Motivo</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$denuncias"}
		<tr>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="{#SITE_URL#}/foto/{$value.foto.categoria.seo}/{$value.foto.id}/{$value.foto.titulo|Texto::make_seo}.html">{$value.foto.titulo}</a></td>
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
					<a href="{#SITE_URL#}/moderar/denuncias/detalle_foto/{$value.id}" class="btn btn-mini show-tooltip" title="Detalles"><i class="icon icon-file"></i></a>
					{if="$value.estado == 0"}<a href="/moderar/denuncias/cerrar_denuncia_foto/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Rechazar denuncia"><i class="icon-white icon-trash"></i></a>
					<a href="{#SITE_URL#}/moderar/denuncias/cerrar_denuncia_foto/{$value.id}/1" class="btn btn-mini btn-success show-tooltip" title="Aceptar denuncia"><i class="icon-white icon-ok"></i></a>{/if}
					{if="$value.foto.estado != 3"}<a href="/foto/editar/{$value.foto_id}" class="btn btn-mini btn-primary show-tooltip" title="Editar foto"><i class="icon-white icon-pencil"></i></a>
					<a href="{#SITE_URL#}/moderar/denuncias/borrar_foto/{$value.foto_id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar foto"><i class="icon-white icon-remove"></i></a>{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">!No hay denuncias a las fotos!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}