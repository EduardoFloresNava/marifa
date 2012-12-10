<h2 class="title">Favoritos - Posts</h2>
{if="count($favoritos)"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>T&iacute;tulo</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
	{loop="$favoritos"}
		<tr>
			<td><a href="{#SITE_URL#}/post/index/{$value.id}" class="title"><img src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" /> {$value.titulo}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				<a href="{#SITE_URL#}/favoritos/borrar_post/{$value.id}" rel="tooltip" title="Quitar de favoritos" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i></a>
			</td>
		</tr>
	{/loop}
	</tbody>
</table>
{else}
<div class="alert alert-info">No tienes ning&uacute;n posts como favorito.</div>
{/if}
{$paginacion}