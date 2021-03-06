<h2 class="title">Favoritos - Fotos</h2>
{if="count($favoritos)"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Título</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
	{loop="$favoritos"}
		<tr>
			<td><a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html" class="title"><img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.categoria.imagen, 'small')"}" /> {$value.titulo}</a></td>
			<td>{$value.creacion->fuzzy()}</td>
			<td>
				<a href="{#SITE_URL#}/favoritos/borrar_foto/{$value.id}" rel="tooltip" title="Quitar de favoritos" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i></a>
			</td>
		</tr>
	{/loop}
	</tbody>
</table>
{else}
<div class="alert alert-info">No tienes ninguna foto como favorito.</div>
{/if}
{$paginacion}