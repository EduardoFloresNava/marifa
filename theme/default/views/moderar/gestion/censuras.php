<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">{@Moderación@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/gestion/">{@Gestión@}</a> <span class="divider">/</span></li>
    <li class="active">{@Censuras@}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">{@Censuras@}</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/moderar/gestion/nueva_censura/" class="btn btn-success"><i class="icon-white icon-plus"></i> {@Nueva@}</a>
		<a href="{#SITE_URL#}/moderar/gestion/verificar_censura/" class="btn btn-inverse"><i class="icon-white icon-plus"></i> ̣{@Probar activas@}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>{@Valor@}</th>
			<th>{@Tipo@}</th>
			<th>{@Censura@}</th>
			<th>{@Estado@}</th>
			<th>{@Acciones@}</th>
		</tr>
	</thead>
	<tbody>
		{loop="$censuras"}
		<tr>
			<td>{$value.valor}</td>
			<td><span class="label">{if="$value.tipo == 0"}{@TEXTO@}{elseif="$value.tipo == 1"}{@PALABRA@}{else}{@REGEX@}{/if}</label></td>
			<td>{$value.censura}</td>
			<td><span class="label {if="$value.estado == 0"}label-important{else}label-success{/if}">{if="$value.estado == 0"}{@INACTIVO@}{else}{@ACTIVO@}{/if}</label></td>
			<td>
				{if="$value.estado == 0"}
					<a href="{#SITE_URL#}/moderar/gestion/estado_censura/{$value.id}/1" class="btn btn-mini btn-info" title="Activar"><i class="icon-white icon-eye-open"></i></a>
				{else}
					<a href="{#SITE_URL#}/moderar/gestion/estado_censura/{$value.id}/0" class="btn btn-mini btn-inverse" title="Desactivar"><i class="icon-white icon-eye-close"></i></a>
				{/if}
				<a href="{#SITE_URL#}/moderar/gestion/editar_censura/{$value.id}" class="btn btn-mini btn-primary" title="Editar"><i class="icon-white icon-pencil"></i></a>
				<a href="{#SITE_URL#}/moderar/gestion/borrar_censura/{$value.id}" class="btn btn-mini btn-danger" title="Eliminar"><i class="icon-white icon-remove"></i></a>
				<a href="{#SITE_URL#}/moderar/gestion/verificar_censura/{$value.id}" class="btn btn-mini btn-info" title="Testear censura"><i class="icon-white icon-info-sign"></i></a>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="4">{@¡No hay palabras censuradas!@}</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}