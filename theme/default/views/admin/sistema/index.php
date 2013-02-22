<div class="header clearfix">
	<h2 class="pull-left">Resumen del sistema.</h2>
	<div class="pull-right">
		<a href="{#SITE_URL#}/admin/sistema/verificar_actualizaciones" class="btn btn-success"{if="$sistema_last_check !== NULL"} title="Ultima verificación hace: {function="$sistema_last_check->fuzzy()"}"{/if}><i class="icon-white icon-globe"></i> Buscar actualizaciones</a>
	</div>
</div>
<div class="row-fluid statistics">
	<div class="span4">
		<h3 class="title">Actualizaciones de plugins<small>-</small></h3>
		<div class="alert alert-info">¡No hay actualizaciones!</div>
	</div>
	<div class="span4">
		<h3 class="title">Actualizaciones de temas<small>-</small></h3>
		<div class="alert alert-info">¡No hay actualizaciones!</div>
	</div>
	<div class="span4">
		<h3 class="title">Actualizaciones del sistema<small>v{#VERSION#}</small></h3>
		{if="is_array($sistema) && count($sistema) > 0"}
		<ul>
			{loop="$sistema"}
			<li class="clearfix">Versión: <span class="label label-info">{$key}</span><div class="pull-right"><a href="{#SITE_URL#}/admin/sistema/actualizar_sistema/{$key}" class="btn btn-mini btn-success">{if="in_array($key, $sistema_descargadas)"}Actualizar{else}Descargar{/if}</a></div></li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">¡No hay actualizaciones!</div>
		{/if}
	</div>
</div>