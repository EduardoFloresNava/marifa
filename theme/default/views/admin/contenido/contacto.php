<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/">{@Contenido@}</a> <span class="divider">/</span></li>
    <li class="active">{@Contacto@}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">{@Contacto@}</h2>
	<div class="pull-right">
		<a href="{#SITE_URL#}/admin/contenido/estado_contacto/1" class="btn btn-success"><i class="icon-white icon-eye-open"></i> {@Marcar todos como leídos@}</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>{@Nombre@}</th>
			<th>{@Asunto@}</th>
			<th>{@Acciones@}</th>
		</tr>
	</thead>
	<tbody>
		{loop="$contactos"}
		<tr{if="$value.estado == 0"} class="alert alert-info"{/if}>
			<td>{$value.nombre}</td>
			<td>{$value.asunto}</td>
			<td>
				<div class="btn-group">
					{if="$value.estado == 0"}
					<a href="{#SITE_URL#}/admin/contenido/estado_contacto/1/{$value.id}" class="btn btn-mini btn-info" title="{@Marcar como leído@}"><i class="icon-white icon-eye-open"></i></a>
					{else}
					<a href="{#SITE_URL#}/admin/contenido/estado_contacto/0/{$value.id}" class="btn btn-mini btn-inverse" title="{@Marcar como nuevo@}"><i class="icon-white icon-eye-close"></i></a>
					{/if}
					<a href="{#SITE_URL#}/admin/contenido/ver_contacto/{$value.id}" class="btn btn-mini btn-success" title="{@Ver mensaje de contacto@}"><i class="icon-white icon-plus"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/eliminar_contacto/{$value.id}" class="btn btn-mini btn-danger" title="{@Eliminar@}"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="3">{@¡No hay mensajes de contacto!@}</td>
		</tr>
		{/loop}
	</tbody>
</table>