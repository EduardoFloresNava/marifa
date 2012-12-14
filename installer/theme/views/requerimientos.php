<h2 class="title">Requerimientos:</h2>
<table class="table table-bordered">
	<tr class="ok">
		<th><i class="icon icon-ok"></i> Correcto</th>
		<td>El requisito se cimple correctamente.</td>
	</tr>
	<tr class="warn">
		<th><i class="icon icon-warning-sign"></i> Opcional</th>
		<td>El no se cumple pero no es necesario.</td>
	</tr>
	<tr class="err">
		<th><i class="icon icon-remove"></i> Requerido</th>
		<td>Es necesario cumplir el requisito para continuar.</td>
	</tr>
</table>
<div class="leyenda">

</div>
<table class="table table-bordered">
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
{if="$can_continue"}<a class="btn btn-large btn-success" href="{function="Installer_Step::url_siguiente('requerimientos')"}">Continuar <i class="icon-white icon-arrow-right"></i></a>{else}<a class="btn btn-large btn-primary" href="{#SITE_URL#}/installer/requerimientos"><i class="icon-white icon-refresh"></i> Reintentar</a>{/if}