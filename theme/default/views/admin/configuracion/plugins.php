<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Plugins</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Plugins</h2>
	<div class="btn-group pull-right">
		<a href="{#SITE_URL#}/admin/configuracion/buscar_actualizaciones_plugins/" class="btn btn-info"><i class="icon-white icon-globe"></i> Buscar actualizaciones</a>
		<a href="{#SITE_URL#}/admin/configuracion/agregar_plugin/" class="btn btn-success"><i class="icon-white icon-plus"></i> Agregar plugin</a>
	</div>
</div>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Nombre</th>
			<th>Autor</th>
			<th>Descripción</th>
			<th>Versión</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
{loop="$plugins"}
		<tr>
			<td>{$value.nombre}</td>
			<td>{$value.autor}</td>
			<td>{$value.descripcion}</td>
			<td>{$value.version}</td>
			<td>
				<div class="btn-group">
				{if="$value.estado"}
					<a href="{#SITE_URL#}/admin/configuracion/buscar_actualizaciones_plugins/{$key}" class="btn btn-mini btn-success" rel="tooltip" title="Buscar actualizaciones"><i class="icon-white icon-globe"></i></a>
					<a href="{#SITE_URL#}/admin/configuracion/desactivar_plugin/{$key}" class="btn btn-mini btn-danger" rel="tooltip" title="Desinstalar"><i class="icon-white icon-remove"></i></a>
				{else}
					<a href="{#SITE_URL#}/admin/configuracion/activar_plugin/{$key}" class="btn btn-mini btn-success">Instalar</a>
					<a href="{#SITE_URL#}/admin/configuracion/borrar_plugin/{$key}" class="btn btn-mini btn-danger">Borrar</a>
				{/if}
				</div>
			</td>
		</tr>
{else}
		<tr>
			<td colspan="5" class="alert">!No hay plugins presentes!</td>
		</tr>
{/loop}
	</tbody>
</table>