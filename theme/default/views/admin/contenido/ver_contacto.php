<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/">{@Contenido@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/contacto/">{@Contacto@}</a> <span class="divider">/</span></li>
    <li class="active">{@Ver contacto@}</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">{@Ver contacto@}</h2>
	<div class="pull-right btn-group">
		<a href="{#SITE_URL#}/admin/contenido/contacto/" class="btn btn-primary" title="{@Volver@}"><i class="icon-white icon-arrow-left"></i></a>
		{if="$contacto.estado == 0"}
		<a href="{#SITE_URL#}/admin/contenido/estado_contacto/1/{$contacto.id}" class="btn btn-info" title="{@Marcar como leído@}"><i class="icon-white icon-eye-open"></i></a>
		{else}
		<a href="{#SITE_URL#}/admin/contenido/estado_contacto/0/{$contacto.id}" class="btn btn-inverse" title="{@Marcar como nuevo@}"><i class="icon-white icon-eye-close"></i></a>
		{/if}
		<a href="{#SITE_URL#}/admin/contenido/eliminar_contacto/{$contacto.id}" class="btn btn-danger" title="{@Eliminar@}"><i class="icon-white icon-remove"></i></a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>ID</th>
		<td>{$contacto.id}</td>
	</tr>
	<tr>
		<th>Nombre</th>
		<td>{$contacto.nombre}</td>
	</tr>
	<tr>
		<th>Asunto</th>
		<td>{$contacto.asunto}</td>
	</tr>
	<tr>
		<th>Mensaje</th>
		<td>{$contacto.contenido}</td>
	</tr>
</table>