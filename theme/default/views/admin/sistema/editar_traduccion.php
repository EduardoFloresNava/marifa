<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/sistema/">{@Sistema@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/sistema/traducciones/">{@Traducciones@}</a> <span class="divider">/</span></li>
    <li class="active">{@Detalles d@}e <strong>{$idioma}</strong></li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">{@Detalles de@} <strong>{$idioma}</strong>{if="$idioma == $activo"} <span class="label label-success">{@ACTIVO@}</span>{/if}</h2>
	<a href="{#SITE_URL#}/admin/sistema/traducciones/" class="btn btn-success pull-right">{@Volver@}</a>
</div>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>{@Cadena@}</th>
			<th>{@Traducción@}</th>
		</tr>
	</thead>
	<tbody>
		{loop="$lang"}
		<tr>
			<td>{$key|htmlentities}</td>
			<td>{$value|htmlentities}</td>
		</tr>
		{/loop}
	</tbody>
</table>