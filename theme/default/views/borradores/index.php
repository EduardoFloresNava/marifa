<h2 class="title">Borradores</h2>
{if="count($borradores)"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Titulo</th>
			<th>Fecha</th>
		</tr>
	</thead>
	<tbody>
	{loop="$borradores"}
		<tr>
			<td><a href="/post/index/{$value.id}" class="title"><img src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" /> {$value.titulo}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
		</tr>
	{/loop}
	</tbody>
</table>
{else}
<div class="alert">No tienes ning&uacute;n borrador</div>
{/if}
{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual"}<li><a href="/borradores/index/{$paginacion.first}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="/borradores/index/{$paginacion.prev}">{@Anterior@}</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}}><a href="/borradores/index/{$value}">{$value}</a></li>
		{/loop}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/borradores/index/{$paginacion.next}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/borradores/index/{$paginacion.last}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}