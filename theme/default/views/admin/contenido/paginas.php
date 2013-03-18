<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/contenido/">{@Contenido@}</a> <span class="divider">/</span></li>
    <li class="active">{@Páginas@}</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">{@Páginas@}</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/contenido/nueva_pagina/" class="btn btn-success"><i class="icon-white icon-plus"></i> {@Nueva@}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>{@Título@}</th>
			<th>{@Menu@}</th>
			<th>{@Estado@}</th>
			<th>{@Creación@}</th>
			<th>{@Acciones@}</th>
		</tr>
	</thead>
	<tbody>
		{loop="$paginas"}
		<tr>
			<td><a href="{#SITE_URL#}/paginas/{$value.id}-{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a></td>
			<td><span class="label">{if="$value.menu == 0"}SUPERIOR{elseif="$value.menu == 1"}INFERIOR{else}AMBOS{/if}</span></td>
			<td>{if="$value.estado == 0"}<span class="label label-important">OCULTO</span>{else}<span class="label label-success">VISIBLE</span>{/if}</td>
			<td>{$value.creacion->fuzzy()}{if="$value.modificacion->getTimestamp() > $value.creacion->getTimestamp()"} (modificado {$value.modificacion->fuzzy()}){/if}</td>
			<td>
				<div class="btn-group">
					<a href="{#SITE_URL#}/admin/contenido/editar_pagina/{$value.id}" class="btn btn-mini btn-primary" title="{@Editar página@}"><i class="icon-white icon-pencil"></i></a>
					{if="$value.estado == 0"}
					<a href="{#SITE_URL#}/admin/contenido/estado_pagina/{$value.id}/1" class="btn btn-mini btn-info" title="{@Activar página@}"><i class="icon-white icon-ok-circle"></i></a>
					{else}
					<a href="{#SITE_URL#}/admin/contenido/estado_pagina/{$value.id}/0" class="btn btn-mini btn-inverse" title="{@Ocultar página@}"><i class="icon-white icon-remove-circle"></i></a>
					{/if}
					<a href="{#SITE_URL#}/admin/contenido/borrar_pagina/{$value.id}" class="btn btn-mini btn-danger" title="{@Borrar página@}"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">{@¡No hay páginas que mostrar!@}</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}