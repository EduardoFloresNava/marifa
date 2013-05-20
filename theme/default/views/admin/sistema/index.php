<h2 class="title">{@Resumen del sistema@}:</h2>
<div class="alert alert-warning">{@Esta es un característica que se encuentra en una fase prematura de desarrollo. El proceso de actualización por medio de esta herramienta puede generar un daño en el sistema. Antes de usarlo realice un backup del sistema.@}</div>
{if="isset($token_api) && $token_api !== NULL"}
<div class="alert alert-info">
	<p><strong>{@¡Tu sitio se encuentra registrado en el servidor de actualizaciones!@}</strong></p>
	<p>{@Tu token de acceso es@} <code>{$token_api}</code> {@el cual se encuentra@} {if="$token_status === TRUE"}<span class="label label-success">{@ACTIVO@}</span>{else}{if="$token_status.0 == 3"}<span class="label label-warning">{@INACTIVO@}</span> {@por@} <code>{$token_status.1}</code>{elseif="$token_status.0 == 4"}<span class="label label-important">{@BLOQUEADO@}</span> {@por@} <code>{$token_status.1}</code>{else}<span class="label">{@DESCONOCIDO@}</label>{/if}{/if} <a href="{#SITE_URL#}/admin/sistema/registrar_sitio_api" class="btn btn-primary btn-mini" title="{@Actualizar@}"><i class="icon-white icon-refresh"></i></a></p>
</div>
<div class="row-fluid statistics">
	<div class="span6">
		<h3 class="title small-btn">{@Noticias@}{if="$token_api !== NULL && $token_status === TRUE"}<a href="{#SITE_URL#}/admin/sistema/obtener_nuevas_noticias" class="btn btn-primary btn-mini pull-right" title="{if="$noticias_last_check !== NULL"}{@Ultima verificación@}: {function="$noticias_last_check->fuzzy()"}{else}{@No se han verificado las noticias@}{/if}"><i class="icon-white icon-refresh"></i></a>{/if}</h3>
		{if="isset($noticias) && count($noticias) > 0"}
		<ul>
			{loop="$noticias"}
			<li>
				{if="isset($value.prioridad) && $value.prioridad !== NULL"}<span class="label {if="$value.prioridad == 0 || $value.prioridad == 1"}label-info{elseif="$value.prioridad == 2 || $value.prioridad == 3"}label-inverse{elseif="$value.prioridad == 4"}label-warning{else}label-important{/if}">{if="$value.prioridad == 0"}{@IRRELEVANTE@}{elseif="$value.prioridad == 1"}{@INFORMATIVO@}{elseif="$value.prioridad == 2"}{@NORMAL@}{elseif="$value.prioridad == 3"}{@RELEVANTE@}{elseif="$value.prioridad == 4"}{@IMPORTANTE@}{else}{@CRITICA@}{/if}</span>{/if}
				{if="isset($value.version) && $value.version !== NULL"}<span class="label label-info label">{$value.version}</span>{/if}
				<a href="{#SITE_URL#}/admin/sistema/ver_noticia/{$value.id}">{$value.titulo}</a>
				{if="isset($value.autor) && $value.autor !== NULL"}<small>{@por@} {$value.autor}</small>{/if}
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">{@¡No hay noticias!@}</div>
		{/if}
	</div>
	<div class="span6">
		<h3 class="title small-btn">{@Sistema@}{if="$token_api !== NULL && $token_status === TRUE"}<a href="{#SITE_URL#}/admin/sistema/verificar_actualizaciones" class="btn btn-primary btn-mini pull-right" title="{if="$sistema_last_check !== NULL"}{@Ultima verificación@}: {function="$sistema_last_check->fuzzy()"}{else}{@No se han verificado las actualizaciones@}{/if}"><i class="icon-white icon-refresh"></i></a>{/if}<small title="{@Versión actual@}">v{#VERSION#}</small></h3>
		{if="is_array($sistema) && count($sistema) > 0"}
		<ul>
			{loop="$sistema"}
			<li class="clearfix">{@Versión@}: <span class="label label-info">{$key}</span>{if="$value.prioridad !== 1"}<span class="label {if="$value.prioridad == 0"}label-important{else}label-info{/if}">{if="$value.prioridad == 0"}{@CRITICA@}{else}{@OPCIONAL@}{/if}</span>{/if}<div class="pull-right"><a href="{#SITE_URL#}/admin/sistema/actualizar_sistema/{$key}" class="btn btn-mini btn-success">{if="in_array($key, $sistema_descargadas)"}{@Actualizar@}{else}{@Descargar@}{/if}</a></div></li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">{@¡No hay actualizaciones!@}</div>
		{/if}
	</div>
</div>
{else}
<div class="alert alert-info">
	<h4>{@¡Sitio sin registrar en el servidor de actualizaciones!@}</h4>
	<p>
		{@Para poder utilizar los beneficios del sistema de actualizaciones debe registrar su sitio.@}
		{@El registro del sitio no envía ninguna información más que la URL del sitio que el sistema va a asociar al IP.@}
	</p>
	<p>
		{@Para poder hacer uso del API debe mantener la leyenda en el pie de página que indica que el sitio está desarrollado usando Marifa.@}
	</p>
	<a href="{#SITE_URL#}/admin/sistema/registrar_sitio_api" class="btn btn-success">{@Registrar sitio@}</a>
</div>
{/if}