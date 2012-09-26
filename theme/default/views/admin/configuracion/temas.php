<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Temas</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Temas</h2>
	<div class="btn-group pull-right">
		<a href="/admin/configuracion/instalar_tema/" class="btn btn-success">Instalar tema</a>
		{if="$preview !== ''"}<a class="btn btn-info" href="/admin/configuracion/terminar_preview_tema"><i class="icon-white icon-eye-close"></i> Terminar previsualización</a>{/if}
	</div>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
{if="isset($error)"}<div class="alert">{$error}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
<div class="row-fluid theme-list">
	<ul class="thumbnails">
{loop="$temas"}
		<li class="span4">
			<div class="thumbnail">
				<img alt="Imagen no disponible" src="/theme/{$value.key}/thumbnail.png" />
				<div class="caption">
					<h3>{$value.nombre}{if="isset($value.author)"}<small>by <b>{$value.author}</b></small>{/if}</h3>
					<p>{$value.descripcion}</p>
					<p>{if="$value.key != $actual"}<div class="btn-group">
						<a class="btn btn-primary" href="/admin/configuracion/activar_tema/{$value.key}">Activar</a>
						{if="$value.key != $preview"}<a class="btn btn-info" href="/admin/configuracion/previsualizar_tema/{$value.key}">Vista previa</a>
						{else}<a class="btn btn-info" href="/admin/configuracion/terminar_preview_tema">Fin vista previa</a>{/if}
						{if="$value.key != $preview"}<a class="btn btn-danger" href="/admin/configuracion/eliminar_tema/{$value.key}">Desinstalar</a>{/if}
						</div>
					{/if}</p>
				</div>
			</div>
		</li>
{/loop}
	</ul>
</div>