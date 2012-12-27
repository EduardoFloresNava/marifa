<h3 class="title">{@últimos post creados por@} {$usuario.nick}:</h3>
{if="isset($post) && count($post) > 0"}
<table class="table table-striped table-bordered">
	<tbody>
	{loop="$post"}
		<tr>
			<td><a href="{#SITE_URL#}/post/index/{$value.id}">{$value.titulo}<span class="pull-right">{$value.puntos} {@puntos@}</span></a></td>
		</tr>
	{/loop}
	</tbody>
</table>
{else}
<div class="alert"><strong>{$usuario.nick}</strong> {@no tiene posts aún.@}</div>
{/if}
{$paginacion}