<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Posts</h2>
	<div class="pull-right btn-toolbar">
		<div class="btn-group">
			<button class="btn">
				{if="$tipo == 0"}<i class="icon icon-ok-circle"></i> Activos{if="$cantidades.activo > 0"} ({$cantidades.activo}){/if}{/if}
				{if="$tipo == 1"}<i class="icon icon-file"></i> Borradores{if="$cantidades.borrador > 0"} ({$cantidades.borrador}){/if}{/if}
				{if="$tipo == 2"}<i class="icon icon-remove-circle"></i> Eliminados{if="$cantidades.borrado > 0"} ({$cantidades.borrado}){/if}{/if}
				{if="$tipo == 3"}<i class="icon icon-time"></i> Pendientes{if="$cantidades.pendiente > 0"} ({$cantidades.pendiente}){/if}{/if}
				{if="$tipo == 4"}<i class="icon icon-eye-close"></i> Ocultos{if="$cantidades.oculto > 0"} ({$cantidades.oculto}){/if}{/if}
				{if="$tipo == 5"}<i class="icon icon-remove-circle"></i> Rechazados{if="$cantidades.rechazado > 0"} ({$cantidades.rechazado}){/if}{/if}
				{if="$tipo == 6"}<i class="icon icon-trash"></i> Papelera{if="$cantidades.papelera > 0"} ({$cantidades.papelera}){/if}{/if}
				{if="$tipo == 7"}<i class="icon icon-asterisk"></i> Todas{if="$cantidades.total > 0"} ({$cantidades.total}){/if}{/if}
			</button>
			<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li{if="$tipo == 0"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/0"><i class="icon{if="$tipo == 0"}-white{/if} icon-ok-circle"></i> Activos{if="$cantidades.activo > 0"}<span class="badge">{$cantidades.activo}</span>{/if}</a></li>
				<li{if="$tipo == 1"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/1"><i class="icon{if="$tipo == 1"}-white{/if} icon-file"></i> Borradores{if="$cantidades.borrador > 0"}<span class="badge">{$cantidades.borrador}</span>{/if}</a></li>
				<li{if="$tipo == 2"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/2"><i class="icon{if="$tipo == 2"}-white{/if} icon-remove-circle"></i> Eliminados{if="$cantidades.borrado > 0"}<span class="badge">{$cantidades.borrado}</span>{/if}</a></li>
				<li{if="$tipo == 3"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/3"><i class="icon{if="$tipo == 3"}-white{/if} icon-time"></i> Pendientes{if="$cantidades.pendiente > 0"}<span class="badge">{$cantidades.pendiente}</span>{/if}</a></li>
				<li{if="$tipo == 4"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/4"><i class="icon{if="$tipo == 4"}-white{/if} icon-eye-close"></i> Ocultos{if="$cantidades.oculto > 0"}<span class="badge">{$cantidades.oculto}</span>{/if}</a></li>
				<li{if="$tipo == 5"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/5"><i class="icon{if="$tipo == 5"}-white{/if} icon-remove-circle"></i> Rechazados{if="$cantidades.rechazado > 0"}<span class="badge">{$cantidades.rechazado}</span>{/if}</a></li>
				<li{if="$tipo == 6"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/6"><i class="icon{if="$tipo == 6"}-white{/if} icon-trash"></i> Papelera{if="$cantidades.papelera > 0"}<span class="badge">{$cantidades.papelera}</span>{/if}</a></li>
				<li class="divider"></li>
				<li{if="$tipo == 7"} class="active"{/if}><a href="{#SITE_URL#}/admin/contenido/posts/{$actual}/7"><i class="icon{if="$tipo == 7"}-white{/if} icon-asterisk"></i> Todas{if="$cantidades.total > 0"}<span class="badge">{$cantidades.total}</span>{/if}</a></li>
			</ul>
		</div>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>T&iacute;tulo</th>
			<th>Autor</th>
			<th>Creado</th>
			<th>Estado</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$posts"}
		<tr>
			<td><a href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a></td>
			<td><a href="{#SITE_URL#}/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
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
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/5" class="btn btn-mini btn-warning" title="Rechazar el post" rel="tooltip"><i class="icon-white icon-hand-down"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/4" class="btn btn-mini btn-inverse" title="Ocultar el post" rel="tooltip"><i class="icon-white icon-eye-close"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/6" class="btn btn-mini btn-danger" title="Enviar a la papelera" rel="tooltip"><i class="icon-white icon-trash"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 1"}
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 2"}
					<!--<a href="" class="btn btn-danger">Rechazar</a> ENVIAR COMO BORRADOR-->
					{elseif="$value.estado == 3"}
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Aprobar" rel="tooltip"><i class="icon-white icon-hand-up"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/5" class="btn btn-mini btn-warning" title="Rechazar el post" rel="tooltip"><i class="icon-white icon-hand-down"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 4"}
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Mostrar el post" rel="tooltip"><i class="icon-white icon-eye-open"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 5"}
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Aprobar" rel="tooltip"><i class="icon-white icon-hand-up"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
					{elseif="$value.estado == 6"}
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/0" class="btn btn-mini btn-success" title="Restaurar el post" rel="tooltip"><i class="icon-white icon-refresh"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/cambiar_estado_post/{$value.id}/2" class="btn btn-mini btn-danger" title="Eliminar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
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
{$paginacion}