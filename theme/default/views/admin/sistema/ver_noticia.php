<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/sistema">{@Sistema@}</a> <span class="divider">/</span></li>
    <li class="active">{@Noticia@}</li>
</ul>
<div class="header">
	<h2>{@Detalles de la noticia@} #{$noticia.id}</h2>
</div>
<table class="table table-bordered table-striped">
	<tr>
		<th>Título:</th>
		<td>{$noticia.titulo}</td>
	</tr>
	<tr>
		<th>Contenido:</th>
		<td>{$noticia.descripcion}</td>
	</tr>
	<tr>
		<th>Fecha:</th>
		<td>{$noticia.fecha->fuzzy()} ({$noticia.fecha->format('d-m-Y H:i:s')})</td>
	</tr>
	{if="isset($noticia.autor) && $noticia.autor !== NULL"}<tr>
		<th>Autor:</th>
		<td>{$noticia.autor}</td>
	</tr>{/if}
	{if="isset($noticia.prioridad) && $noticia.prioridad !== NULL"}<tr>
		<th>Prioridad:</th>
		<td>
			{if="$noticia.prioridad !== NULL"}<span class="label {if="$noticia.prioridad == 0 || $noticia.prioridad == 1"}label-info{elseif="$noticia.prioridad == 2 || $noticia.prioridad == 3"}label-inverse{elseif="$noticia.prioridad == 4"}label-warning{else}label-important{/if}">{if="$noticia.prioridad == 0"}{@IRRELEVANTE@}{elseif="$noticia.prioridad == 1"}{@INFORMATIVO@}{elseif="$noticia.prioridad == 2"}{@NORMAL@}{elseif="$noticia.prioridad == 3"}{@RELEVANTE@}{elseif="$noticia.prioridad == 4"}{@IMPORTANTE@}{else}{@CRITICA@}{/if}</span>{/if}
		</td>
	</tr>{/if}
	{if="isset($noticia.version) && $noticia.version !== NULL"}<tr>
		<th>Versión:</th>
		<td>{$noticia.version}</td>
	</tr>{/if}
</table>