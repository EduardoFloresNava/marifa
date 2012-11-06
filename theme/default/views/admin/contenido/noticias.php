<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Noticias</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Noticias</h2>
	<div class="btn-group pull-right">
		<a href="/admin/contenido/nueva_noticia/" class="btn btn-success">Nueva</a>
		<a href="/admin/contenido/limpiar_noticias/" class="btn btn-danger">Borrar todas</a>
		<a href="/admin/contenido/ocultar_noticias/" class="btn btn-warning">Desactivar todas</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Id</th>
			<th>Noticia</th>
			<th>Estado</th>
			<th>Creador</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$noticias"}
		<tr>
			<td># {$value.id}</td>
			<td>{$value.contenido}</td>
			<td>{if="$value.estado == 0"}<span class="label label-important">OCULTO</span>{else}<span class="label label-success">VISIBLE</span>{/if}</td>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td style="text-align: center;">
				<div class="btn-group">
					<a href="/admin/contenido/editar_noticia/{$value.id}" class="btn btn-mini btn-info">Editar</a>
					{if="$value.estado == 0"}<a href="/admin/contenido/estado_noticia/{$value.id}/1" class="btn btn-mini btn-success">Mostrar</a>{else}<a href="/admin/contenido/estado_noticia/{$value.id}/0" class="btn btn-mini btn-warning">Ocultar</a>{/if}
					<a href="/admin/contenido/borrar_noticia/{$value.id}" class="btn btn-mini btn-danger">Borrar</a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="6">&iexcl;No hay noticias!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}