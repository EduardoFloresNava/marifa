<h2 class="title">Borradores</h2>
{if="count($borradores)"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Titulo</th>
			<th>Estado</th>
			<th>Fecha</th>
		</tr>
	</thead>
	<tbody>
	{loop="$borradores"}
		<tr>
			<td><a href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html" class="title"><img src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" /> {$value.titulo}</a></td>
			<td>{if="$value.estado == 1"}<span class="badge badge-info">BORRADOR</span>{else}<span class="badge badge-info">PENDIENTE</span>{/if}</td>
			<td>{$value.fecha->fuzzy()}</td>
		</tr>
	{/loop}
	</tbody>
</table>
{else}
<div class="alert">No tienes ning&uacute;n borrador</div>
{/if}
{$paginacion}