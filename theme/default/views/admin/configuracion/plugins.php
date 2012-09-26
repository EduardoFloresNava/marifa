<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Plugins</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Plugins</h2>
	<div class="btn-group pull-right">
		<a href="/admin/configuracion/buscar_actualizaciones_plugins/" class="btn btn-info"><i class="icon-white icon-globe"></i> Buscar actualizaciones</a>
		<a href="/admin/configuracion/agregar_plugin/" class="btn btn-success"><i class="icon-white icon-plus"></i> Agregar plugin</a>
	</div>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
{if="isset($error)"}<div class="alert">{$error}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
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
			<td>{if="$value.estado"}
				<a href="/admin/configuracion/buscar_actualizaciones_plugins/{$key}" class="btn btn-mini btn-success">Buscar actualizaciones</a>
				<a href="/admin/configuracion/desactivar_plugin/{$key}" class="btn btn-mini btn-danger">Desinstalar</a>
				{else}
				<a href="/admin/configuracion/activar_plugin/{$key}" class="btn btn-mini btn-success">Instalar</a>
				<a href="/admin/configuracion/borrar_plugin/{$key}" class="btn btn-mini btn-danger">Borrar</a>
			{/if}</td>
		</tr>
{else}
		<tr>
			<td colspan="5" class="alert">&iexcl;No hay plugins presentes!</td>
		</tr>
{/loop}
	</tbody>
</table>