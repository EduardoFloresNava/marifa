<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header">
	<h2>Posts</h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Titulo</th>
			<th>Autor</th>
			<th>Creado</th>
			<th>Estado</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$posts"}
		<tr>
			<td><a href="/post/index/{$value.id}">{$value.titulo}</a></td>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				{if="$value.estado == 0"}
				<span class="label label-success">ACTIVO</span>
				{elseif="$value.estado == 1"}
				<span class="label label-info">BORRADOR</span>
				{elseif="$value.estado == 2"}
				<span class="label label-important">BORRADO</span>
				{elseif="$value.estado == 3"}
				<span class="label label-inverse">PENDIENTE</span>
				{elseif="$value.estado == 4"}
				<span class="label label-warning">OCULTO</span>
				{elseif="$value.estado == 5"}
				<span class="label label-warning">RECHAZADO</span>
				{elseif="$value.estado == 6"}
				<span class="label label-inverse">PAPELERA</span>
				{else}
				ESTADO INDEFINIDO
				{/if}
			</td>
			<td style="text-align: center;">
				<div class="btn-group">
					{if="$value.estado == 0"}
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/5" class="btn btn-mini btn-warning" title="Rechazar el post" rel="tooltip"><i class="icon-white icon-hand-down"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/4" class="btn btn-mini btn-inverse" title="Ocultar el post" rel="tooltip"><i class="icon-white icon-eye-close"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/6" class="btn btn-mini btn-danger" title="Enviar a la papelera" rel="tooltip"><i class="icon-white icon-trash"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 1"}
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 2"}
					<!--<a href="" class="btn btn-danger">Rechazar</a> ENVIAR COMO BORRADOR-->
					{elseif="$value.estado == 3"}
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Aprobar" rel="tooltip"><i class="icon-white icon-hand-up"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/5" class="btn btn-mini btn-warning" title="Rechazar el post" rel="tooltip"><i class="icon-white icon-hand-down"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 4"}
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Mostrar el post" rel="tooltip"><i class="icon-white icon-eye-open"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 5"}
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Aprobar" rel="tooltip"><i class="icon-white icon-hand-up"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 6"}
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Restaurar el post" rel="tooltip"><i class="icon-white icon-refresh"></i></a>
					<a href="/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{else}
					ESTADO INDEFINIDO
					{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay Posts!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/admin/contenido/posts/{$paginacion.first}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/admin/contenido/posts/{$paginacion.prev}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/admin/contenido/posts/{$value}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/admin/contenido/posts/{$paginacion.next}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/admin/contenido/posts/{$paginacion.last}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}