<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Noticias</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Noticias</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/contenido/nueva_noticia/" class="btn btn-success"><i class="icon-white icon-plus"></i> Nueva</a>
		<a href="{#SITE_URL#}/admin/contenido/limpiar_noticias/" class="btn btn-danger"><i class="icon-white icon-remove"></i> Borrar todas</a>
		<a href="{#SITE_URL#}/admin/contenido/ocultar_noticias/" class="btn btn-warning"><i class="icon-white icon-eye-close"></i> Desactivar todas</a>
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
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td style="text-align: center;">
				<div class="btn-group">
					<a href="{#SITE_URL#}/admin/contenido/editar_noticia/{$value.id}" class="btn btn-mini btn-info show-tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>
					{if="$value.estado == 0"}<a href="/{#SITE_URL#}admin/contenido/estado_noticia/{$value.id}/1" class="btn btn-mini btn-success show-tooltip" title="Mostrar"><i class="icon-white icon-eye-open"></i></a>{else}<a href="{#SITE_URL#}/admin/contenido/estado_noticia/{$value.id}/0" class="btn btn-mini btn-inverse show-tooltip" title="Ocultar"><i class="icon-white icon-eye-close"></i></a>{/if}
					<a href="{#SITE_URL#}/admin/contenido/borrar_noticia/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>
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