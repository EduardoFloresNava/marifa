<h2 class="title">Requerimientos:</h2>
<table class="table requerimientos">
	<thead>
		<tr>
			<th>Elemento</th>
			<th>Requerido</th>
			<th>Actual</th>
		</tr>
	</thead>
	<tbody>
		{loop="$requerimientos"}
		{if="is_array($value)"}
		<tr class="{if="$value.estado"}ok{else}{if="isset($value.opcional) && $value.opcional"}warn{else}err{/if}{/if}">
			<th><i class="icon icon-{if="$value.estado"}ok{else}{if="isset($value.opcional) && $value.opcional"}warning-sign{else}remove{/if}{/if}"></i> {$value.titulo}</th>
			<td>{$value.requerido}</td>
			<td>{$value.actual}</td>
		</tr>
		{else}
		<tr>
			<th colspan="3">{$value}</th>
		</tr>
		{/if}
		{/loop}
	</tbody>
</table>
{if="$can_continue"}<a class="btn btn-large btn-success" href="/installer/bd/">Continuar <i class="icon-white icon-arrow-right"></i></a>{else}<a class="btn btn-large btn-primary" href="/install.php/home/requerimientos"><i class="icon-white icon-refresh"></i> Reintentar</a>{/if}