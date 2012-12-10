<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li class="active">Logs</li>
</ul>
<div class="header">
	<h2>Visualizador de Log's</h2>
</div>
<div class="btn-group" style="margin-bottom: 10px;">
	{loop="$file_list"}{if="!isset($actual) || $value != $actual"}
	<a href="{#SITE_URL#}/admin/home/logs/{$value}/" class="btn btn-mini">{$value}</a><a href="{#SITE_URL#}/admin/home/borrar_log/{$value}/" class="btn btn-mini btn-danger show-tooltip" title="Borrar log"><i class="icon-white icon-remove"></i></a>
	{else}
	<a href="" class="btn btn-mini" disabled="disabled">{$value}</a><a href="{#SITE_URL#}/admin/home/borrar_log/{$value}/" class="btn btn-mini btn-danger show-tooltip" title="Borrar log"><i class="icon-white icon-remove"></i></a>
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