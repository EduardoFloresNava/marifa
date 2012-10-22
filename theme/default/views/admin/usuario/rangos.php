<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Rangos</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Rangos</h2>
	<div class="btn-group pull-right">
		<a href="/admin/usuario/nuevo_rango/" class="btn btn-success"><i class="icon-white icon-plus"></i> Nuevo</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Orden</th>
			<th>Nombre</th>
			<th>Color</th>
			<th>Imagen</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$rangos"}
		<tr>
			<td>#{$value.orden}</td>
			<td>{$value.nombre}</td>
			<td><span style="color: #{function="dechex($value.color)"}; background-color: #{function="Utils::getContrastYIQ(dechex($value.color))"};">{function="strtoupper(dechex($value.color))"}</span></td>
			<td><img src="{#THEME_URL#}/assets/img/rangos/{$value.imagen}" /></td>
			<td style="text-align: center;">
				<div class="btn-group">
					<a href="/admin/usuario/ver_rango/{$value.id}" class="btn btn-mini">Ver Detalles</a>
					<a href="/admin/usuario/editar_rango/{$value.id}" class="btn btn-mini btn-info">Editar</a>
					<a href="/admin/usuario/borrar_rango/{$value.id}" class="btn btn-mini btn-danger">Borrar</a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="6">&iexcl;No hay rangos definidos!</td>
		</tr>
		{/loop}
	</tbody>
</table>