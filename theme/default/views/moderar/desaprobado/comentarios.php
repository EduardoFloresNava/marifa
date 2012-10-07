<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/desaprobado/">Contenido desaprobado</a> <span class="divider">/</span></li>
    <li class="active">Comentarios</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Comentarios</h2>
	<div class="btn-group pull-right">
		<a href="/moderar/desaprobado/comentarios/{$actual}/1" class="btn btn-small btn-danger{if="$tipo == 1"} active{/if}"><i class="icon-white icon-picture"></i> Fotos{if="$cantidad_fotos > 0"} ({$cantidad_fotos}){/if}</a>
		<a href="/moderar/desaprobado/comentarios/{$actual}/0" class="btn btn-small btn-primary{if="$tipo == 0"} active{/if}"><i class="icon-white icon-asterisk"></i> Posts y Fotos{if="$cantidad_total > 0"} ({$cantidad_total}){/if}</a>
		<a href="/moderar/desaprobado/comentarios/{$actual}/2" class="btn btn-small btn-success{if="$tipo == 2"} active{/if}"><i class="icon-white icon-book"></i> Posts{if="$cantidad_posts > 0"} ({$cantidad_posts}){/if}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Tipo</th>
			<th>Autor</th>
			<th>Título</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$comentarios"}
		<tr>
			<td>{if="isset($value.post)"}<i class="icon icon-book"></i>{else}<i class="icon icon-picture"></i>{/if}</td>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{if="isset($value.post)"}<a href="/post/index/{$value.post.id}">{$value.post.titulo}</a>{else}<a href="/post/index/{$value.foto.id}">{$value.foto.titulo}</a>{/if}</td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				<div class="btn-group">
					<a href="/moderar/desaprobado/mostrar_comentario/{$value.id}/{if="isset($value.post)"}1{else}2{/if}" class="btn btn-mini btn-success" rel="tooltip" title="Mostrar comentario"><i class="icon-white icon-ok"></i></a>
					<a href="/moderar/desaprobado/borrar_comentario/{$value.id}/{if="isset($value.post)"}1{else}2{/if}" class="btn btn-mini btn-danger" rel="tooltip" title="Borrar comentario"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay comentarios que mostrar!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/moderar/desaprobado/comentarios/{$paginacion.first}/{$tipo}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/moderar/desaprobado/comentarios/{$paginacion.prev}/{$tipo}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/moderar/desaprobado/comentarios/{$value}/{$tipo}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/moderar/desaprobado/comentarios/{$paginacion.next}/{$tipo}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/moderar/desaprobado/comentarios/{$paginacion.last}/{$tipo}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}