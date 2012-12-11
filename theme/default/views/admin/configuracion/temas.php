<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li class="active">Temas</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Temas</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/configuracion/instalar_tema/" class="btn btn-success"><i class="icon-white icon-plus"></i> Instalar tema</a>
		{if="$preview !== ''"}<a class="btn btn-info" href="{#SITE_URL#}/admin/configuracion/terminar_preview_tema"><i class="icon-white icon-eye-close"></i> Terminar pre-visualización</a>{/if}
	</div>
</div>
<div class="row-fluid theme-list">
	<ul class="thumbnails">
{loop="$temas"}
		<li class="span4">
			<div class="thumbnail">
				<img alt="{$value.nombre}" src="{#SITE_URL#}/theme/{$value.key}/thumbnail.png" />
				<div class="caption">
					<h3>{$value.nombre}{if="isset($value.author)"}<small>by <b>{$value.author}</b></small>{/if}</h3>
					<p>{$value.descripcion}</p>
					<p>{if="$value.key != $actual"}<div class="btn-group">
						<a class="btn btn-primary" href="{#SITE_URL#}/admin/configuracion/activar_tema/{$value.key}">Activar</a>
						{if="$value.key != $preview"}<a class="btn btn-info" href="/admin/configuracion/previsualizar_tema/{$value.key}">Vista previa</a>
						{else}<a class="btn btn-info" href="{#SITE_URL#}/admin/configuracion/terminar_preview_tema">Fin vista previa</a>{/if}
						{if="$value.key != $preview"}<a class="btn btn-danger" href="{#SITE_URL#}/admin/configuracion/eliminar_tema/{$value.key}">Desinstalar</a>{/if}
						</div>
					{/if}</p>
				</div>
			</div>
		</li>
{/loop}
	</ul>
</div>