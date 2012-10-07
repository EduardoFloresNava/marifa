<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/desaprobado/">Contenido desaprobado</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Posts</h2>
	<div class="btn-group pull-right">
		<a href="/moderar/desaprobado/posts/{$actual}/1" class="btn btn-small btn-danger{if="$tipo == 1"} active{/if}"><i class="icon-white icon-remove-circle"></i> Pendientes{if="$cantidad_pendientes > 0"} ({$cantidad_pendientes}){/if}</a>
		<a href="/moderar/desaprobado/posts/{$actual}/0" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}"><i class="icon-white icon-time"></i> Pendientes y rechazados{if="$cantidad_total > 0"} ({$cantidad_total}){/if}</a>
		<a href="/moderar/desaprobado/posts/{$actual}/2" class="btn btn-small btn-success{if="$tipo == 2"} active{/if}"><i class="icon-white icon-ok-circle"></i> Rechazados{if="$cantidad_rechazados > 0"} ({$cantidad_rechazados}){/if}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Autor</th>
			<th>Título</th>
			<th>Fecha</th>
			<th>Estado</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$posts"}
		<tr>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="/post/index/{$value.id}">{$value.titulo}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				{if="$value.estado == 3"}
				<span class="label label-info">PENDIENTE</span>
				{elseif="$value.estado == 5"}
				<span class="label label-important">RECHAZADO</span>
				{else}
				<span class="label label-important">TIPO SIN DEFINIR</span>
				{/if}
			</td>
			<td>
				<div class="btn-group">
					<a href="/post/index/{$value.id}" class="btn btn-mini btn-info" rel="tooltip" title="Ver post"><i class="icon-white icon-eye-close"></i></a>
					{if="$value.estado != 2"}<a href="/post/editar/{$value.id}" class="btn btn-mini btn-primary" rel="tooltip" title="Editar post"><i class="icon-white icon-pencil"></i></a>{/if}
					<a href="/moderar/desaprobado/aprobar_post/{$value.id}/1" class="btn btn-mini btn-success" rel="tooltip" title="Aprobar post"><i class="icon-white icon-hand-up"></i></a>
					{if="$value.estado == 3"}<a href="/moderar/desaprobado/aprobar_post/{$value.id}/-1" class="btn btn-mini btn-warning" rel="tooltip" title="Rechazar post"><i class="icon-white icon-hand-down"></i></a>{/if}
					{if="$value.estado != 2"}<a href="/moderar/desaprobado/borrar_post/{$value.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Borrar post"><i class="icon-white icon-remove"></i></a>{/if}
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
		{if="$paginacion.first != $actual"}<li><a href="/moderar/desaprobado/posts/{$paginacion.first}/{$tipo}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/moderar/desaprobado/posts/{$paginacion.prev}/{$tipo}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/moderar/desaprobado/posts/{$value}/{$tipo}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/moderar/desaprobado/posts/{$paginacion.next}/{$tipo}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/moderar/desaprobado/posts/{$paginacion.last}/{$tipo}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}