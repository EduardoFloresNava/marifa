<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Fotos</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Fotos</h2>
	<div class="pull-right btn-group">
		<!--<form action="/admin/contenido/fotos" class="form-search" method="POST">
			<div class="input-append">
				<input type="text" name="q" class="search-query" value="{if="isset($q)"}{$q}{/if}" placeholder="Buscar..." />
				<button type="submit" class="btn submit"><i class="icon icon-search"></i></button>
			</div>
		</form>-->
		<a href="/admin/contenido/fotos/{$actual}/0" class="btn btn-small btn-success{if="$tipo == 0"} active{/if}"><i class="icon-white icon-ok-circle"></i> Activas{if="$cantidades.activa > 0"} ({$cantidades.activa}){/if}</a>
		<a href="/admin/contenido/fotos/{$actual}/1" class="btn btn-small btn-inverse{if="$tipo == 1"} active{/if}"><i class="icon-white icon-eye-close"></i> Ocultas{if="$cantidades.oculta > 0"} ({$cantidades.oculta}){/if}</a>
		<a href="/admin/contenido/fotos/{$actual}/2" class="btn btn-small btn-warning{if="$tipo == 2"} active{/if}"><i class="icon-white icon-trash"></i> Papelera{if="$cantidades.papelera > 0"} ({$cantidades.papelera}){/if}</a>
		<a href="/admin/contenido/fotos/{$actual}/3" class="btn btn-small btn-danger{if="$tipo == 3"} active{/if}"><i class="icon-white icon-remove-circle"></i> Eliminadas{if="$cantidades.borrada > 0"} ({$cantidades.borrada}){/if}</a>
		<a href="/admin/contenido/fotos/{$actual}/4" class="btn btn-small{if="$tipo == 4"} active{/if}"><i class="icon icon-asterisk"></i> Todas{if="$cantidades.total > 0"} ({$cantidades.total}){/if}</a>
	</div>
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
		{loop="$fotos"}
		<tr>
			<td><a href="/foto/index/{$value.id}">{$value.titulo}</a></td>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td>{$value.creacion->fuzzy()}</td>
			<td><span class="label label-{if="$value.estado == 0"}success">VISIBLE{else}important">OCULTA{/if}</span></td>
			<td>
				<div class="btn-group">
					{if="$value.estado == 0"}<a href="/admin/contenido/ocultar_foto/{$value.id}" class="btn btn-mini btn-info">Ocultar</a>{else}
					<a href="/admin/contenido/mostrar_foto/{$value.id}" class="btn btn-mini btn-success">Mostrar</a>{/if}
					<a href="/admin/contenido/eliminar_foto/{$value.id}" class="btn btn-mini btn-danger">Eliminar</a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay Fotos!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}