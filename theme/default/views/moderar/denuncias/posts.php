<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/denuncias/">Denuncias</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Posts</h2>
	<div class="btn-group pull-right">
		<a href="/moderar/denuncias/posts/{$actual}/1" class="btn btn-small btn-danger{if="$tipo == 1"} active{/if}"><i class="icon-white icon-remove-circle"></i> Rechazados{if="$cantidad_rechazados > 0"} ({$cantidad_rechazados}){/if}</a>
		<a href="/moderar/denuncias/posts/{$actual}/0" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}"><i class="icon-white icon-time"></i> Pendientes{if="$cantidad_pendientes > 0"} ({$cantidad_pendientes}){/if}</a>
		<a href="/moderar/denuncias/posts/{$actual}/2" class="btn btn-small btn-success{if="$tipo == 2"} active{/if}"><i class="icon-white icon-ok-circle"></i> Aprobados{if="$cantidad_aprobados > 0"} ({$cantidad_aprobados}){/if}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Denunciante</th>
			<th>Post</th>
			<th>Fecha</th>
			<th>Motivo</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$denuncias"}
		<tr>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="/foto/index/{$value.post.id}">{$value.post.titulo}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				{if="$value.motivo == 0"}
				<span class="label">RE-POST</span>
				{elseif="$value.motivo == 1"}
				<span class="label">SPAM</span>
				{elseif="$value.motivo == 2"}
				<span class="label">Links muertos</span>
				{elseif="$value.motivo == 3"}
				<span class="label">Irrespetuoso</span>
				{elseif="$value.motivo == 4"}
				<span class="label">Información personal</span>
				{elseif="$value.motivo == 5"}
				<span class="label">Titulo mayúscula</span>
				{elseif="$value.motivo == 6"}
				<span class="label">Pedofilia</span>
				{elseif="$value.motivo == 7"}
				<span class="label">Asqueroso</span>
				{elseif="$value.motivo == 8"}
				<span class="label">Fuente</span>
				{elseif="$value.motivo == 9"}
				<span class="label">Pobre</span>
				{elseif="$value.motivo == 10"}
				<span class="label">Foro</span>
				{elseif="$value.motivo == 11"}
				<span class="label">Protocolo</span>
				{elseif="$value.motivo == 12"}
				<span class="label">Personalizada</span>
				{else}
				<span class="label label-important">TIPO SIN DEFINIR</span>
				{/if}
			</td>
			<td style="text-align: center;">
				<div class="btn-group">
					<a href="/moderar/denuncias/detalle_post/{$value.id}" class="btn btn-mini" rel="tooltip" title="Detalles"><i class="icon icon-file"></i></a>
					<a href="/post/index/{$value.post.id}" class="btn btn-mini btn-info" rel="tooltip" title="Ver post"><i class="icon-white icon-eye-close"></i></a>
					{if="$value.estado == 0"}<a href="/moderar/denuncias/cerrar_denuncia_post/{$value.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Rechazar denuncia"><i class="icon-white icon-trash"></i></a>
					<a href="/moderar/denuncias/cerrar_denuncia_post/{$value.id}/1" class="btn btn-mini btn-success" rel="tooltip" title="Aceptar denuncia"><i class="icon-white icon-ok"></i></a>{/if}
					{if="$value.post.estado != 2"}<a href="/post/editar/{$value.post.id}" class="btn btn-mini btn-primary" rel="tooltip" title="Editar post"><i class="icon-white icon-pencil"></i></a>
					<a href="/moderar/denuncias/borrar_post/{$value.post.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Borrar post"><i class="icon-white icon-remove"></i></a>{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay denuncias a los posts!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/moderar/denuncias/posts/{$paginacion.first}/{$tipo}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/moderar/denuncias/posts/{$paginacion.prev}/{$tipo}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/moderar/denuncias/posts/{$value}/{$tipo}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/moderar/denuncias/posts/{$paginacion.next}/{$tipo}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/moderar/denuncias/posts/{$paginacion.last}/{$tipo}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}