<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Logs</li>
</ul>
<div class="header">
	<h2>Visualizador de Log's</h2>
</div>
<div class="btn-group" style="margin-bottom: 10px;">
	{loop="$file_list"}{if="!isset($actual) || $value != $actual"}
	<a href="/admin/home/logs/{$value}/" class="btn">{$value}</a>
	{else}
	<a href="" class="btn" disabled="disabled">{$value}</a>
	{/if}{/loop}
</div>
{if="isset($lineas)"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">Visualizando '{$actual}'</th>
		</tr>
		<tr>
			<th>Fecha</th>
			<th>Tipo</th>
			<th>Mensaje</th>
		</tr>
	</thead>
	<tbody>
		{loop="$lineas"}
		<tr{if="$value.tipo=='ERROR'"} class="alert alert-danger"{elseif="$value.tipo=='WARNING'"} class="alert"{elseif="$value.tipo=='INFO'"} class="alert alert-info"{else} class="alert alert-success"{/if}>
			<td>{$value.fecha->format('d/m/Y H:i:s')}</td>
			<td>{$value.tipo}</td>
			<td>{$value.str}</td>
		</tr>
		{/loop}
	</tbody>
</table>
{/if}