<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Fotos</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Fotos</h2>
	<div class="pull-right">
		<form action="/admin/contenido/fotos" class="form-search" method="POST">
			<div class="input-append">
				<input type="text" name="q" class="search-query" value="{$q}" placeholder="Buscar..." />
				<button type="submit" class="btn submit"><i class="icon icon-search"></i></button>
			</div>
		</form>
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
			<td style="text-align: center;">
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
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/admin/contenido/fotos/{$paginacion.first}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/admin/contenido/fotos/{$paginacion.prev}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/admin/contenido/fotos/{$value}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/admin/contenido/fotos/{$paginacion.next}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/admin/contenido/fotos/{$paginacion.last}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}