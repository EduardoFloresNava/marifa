<h3 class="title">{@&Uacute;ltimos post creados por@} {$usuario.nick}:</h3>
{if="isset($post) && count($post) > 0"}
<table class="table table-striped table-bordered">
	<tbody>
	{loop="$posts"}
		<tr>
			<td><a href="/post/index/{$value.id}">{$value.titulo}<span class="pull-right">{$value.puntos} {@puntos@}</span></a></td>
		</tr>
	{/loop}
	</tbody>
</table>
{else}
<div class="alert"><strong>{$usuario.nick}</strong> {@no tiene posts a&uacute;n.@}</div>
{/if}