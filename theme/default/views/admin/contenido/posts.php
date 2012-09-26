<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header">
	<h2>Posts</h2>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
{if="isset($error)"}<div class="alert">{$error}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
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
			<td><span class="label label-{if="$value.estado == 0"}success">ACTIVO{elseif="$value.estado == 1"}info">BORRADOR{else}important">BORRADO{/if}</span></td>
			<td style="text-align: center;">
				<div class="btn-group">
					{if="$value.estado != 2"}<a href="/admin/contenido/eliminar_post/{$value.id}" class="btn btn-mini btn-danger">Eliminar</a>{/if}
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