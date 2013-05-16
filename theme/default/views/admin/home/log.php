<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li class="active">{@Log's@}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">{@Visualizador de Log's@}</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/home/borrar_logs_vacios" class="btn btn-warning" title="{@Eliminar todos los logs que se encuentran vacios.@}">{@Borrar vacios@}</a>
		{if="function_exists('gzcompress')"}<a href="{#SITE_URL#}/admin/home/comprimir_logs" class="btn btn-primary" title="{@Comprimir los archivos log's viejos para ahorrar espacio@}">{@Comprimir@}</a>{/if}
	</div>
</div>
<div class="btn-group" style="margin-bottom: 10px;">
	{loop="$file_list"}{if="!isset($actual) || $value != $actual"}
	<a href="{#SITE_URL#}/admin/home/logs/{$value}/" class="btn btn-mini">{$value}</a><a href="{#SITE_URL#}/admin/home/borrar_log/{$value}/" class="btn btn-mini btn-danger show-tooltip" title="{@Borrar@}"><i class="icon-white icon-remove"></i></a>
	{else}
	<a href="" class="btn btn-mini" disabled="disabled">{$value}</a><a href="{#SITE_URL#}/admin/home/borrar_log/{$value}/" class="btn btn-mini btn-danger show-tooltip" title="{@Borrar@}"><i class="icon-white icon-remove"></i></a>
	{/if}{/loop}
</div>
{if="isset($lineas)"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">{@Visualizando@} '{$actual}'</th>
		</tr>
		<tr>
			<th>{@Fecha@}</th>
			<th>{@Tipo@}</th>
			<th>{@Mensaje@}</th>
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