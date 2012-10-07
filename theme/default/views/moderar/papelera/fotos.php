<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/papelera/">Papelera de reciclaje</a> <span class="divider">/</span></li>
    <li class="active">Fotos</li>
</ul>
<div class="header">
	<h2>Fotos</h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Autor</th>
			<th>Título</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$fotos"}
		<tr>
			<td><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="/foto/ver/{$value.id}">{$value.titulo}</a></td>
			<td>{$value.creacion->fuzzy()}</td>
			<td>
				<div class="btn-group">
					<a href="/foto/ver/{$value.id}" class="btn btn-mini btn-info" rel="tooltip" title="Ver foto"><i class="icon-white icon-eye-close"></i></a>
					<a href="/foto/editar/{$value.id}" class="btn btn-mini btn-primary" rel="tooltip" title="Editar foto"><i class="icon-white icon-pencil"></i></a>
					<a href="/moderar/papelera/restaurar_foto/{$value.id}" class="btn btn-mini btn-success" rel="tooltip" title="Restaurar foto"><i class="icon-white icon-refresh"></i></a>
					<a href="/moderar/papelera/borrar_foto/{$value.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Borrar foto"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay fotos en la papelera!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/moderar/papelera/fotos/{$paginacion.first}/{$tipo}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/moderar/papelera/fotos/{$paginacion.prev}/{$tipo}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/moderar/papelera/fotos/{$value}/{$tipo}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/moderar/papelera/fotos/{$paginacion.next}/{$tipo}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/moderar/papelera/fotos/{$paginacion.last}/{$tipo}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}