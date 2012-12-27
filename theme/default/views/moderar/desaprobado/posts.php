<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/desaprobado/">Contenido desaprobado</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Posts</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/moderar/desaprobado/posts/{$actual}/1" class="btn btn-small btn-danger{if="$tipo == 1"} active{/if}"><i class="icon-white icon-remove-circle"></i> Pendientes{if="$cantidad_pendientes > 0"} ({$cantidad_pendientes}){/if}</a>
		<a href="{#SITE_URL#}/moderar/desaprobado/posts/{$actual}/0" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}"><i class="icon-white icon-time"></i> Pendientes y rechazados{if="$cantidad_total > 0"} ({$cantidad_total}){/if}</a>
		<a href="{#SITE_URL#}/moderar/desaprobado/posts/{$actual}/2" class="btn btn-small btn-success{if="$tipo == 2"} active{/if}"><i class="icon-white icon-ok-circle"></i> Rechazados{if="$cantidad_rechazados > 0"} ({$cantidad_rechazados}){/if}</a>
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
			<td><a href="{#SITE_URL#}/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="{#SITE_URL#}/post/index/{$value.id}">{$value.titulo}</a></td>
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
					<a href="{#SITE_URL#}/post/index/{$value.id}" class="btn btn-mini btn-info show-tooltip" title="Ver post"><i class="icon-white icon-eye-close"></i></a>
					{if="$value.estado != 2"}<a href="{#SITE_URL#}/post/editar/{$value.id}" class="btn btn-mini btn-primary show-tooltip" title="Editar post"><i class="icon-white icon-pencil"></i></a>{/if}
					<a href="{#SITE_URL#}/moderar/desaprobado/aprobar_post/{$value.id}/1" class="btn btn-mini btn-success show-tooltip" title="Aprobar post"><i class="icon-white icon-hand-up"></i></a>
					{if="$value.estado == 3"}<a href="{#SITE_URL#}/moderar/desaprobado/aprobar_post/{$value.id}/-1" class="btn btn-mini btn-warning show-tooltip" title="Rechazar post"><i class="icon-white icon-hand-down"></i></a>{/if}
					{if="$value.estado != 2"}<a href="{#SITE_URL#}/moderar/desaprobado/borrar_post/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar post"><i class="icon-white icon-remove"></i></a>{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">!No hay denuncias a los posts!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}