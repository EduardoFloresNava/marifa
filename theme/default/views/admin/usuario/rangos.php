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
			<td><div class="btn-toolbar">
					<div class="btn-group">
						#{$value.orden}
					</div>
					<div class="btn-group">
						{if="$value.orden !== 1"}<a class="btn btn-mini" href="/admin/usuario/mover_rango/{$value.id}/1" rel="tooltip" title="Colocar primero"><i class="icon icon-arrow-up"></i></a>{/if}
						{if="$value.orden !== 1"}<a class="btn btn-mini" href="/admin/usuario/mover_rango/{$value.id}/{$value.orden - 1}" rel="tooltip" title="Subir 1 posición"><i class="icon icon-chevron-up"></i></a>{/if}
						{if="$value.orden !== count($rangos)"}<a class="btn btn-mini" href="/admin/usuario/mover_rango/{$value.id}/{$value.orden + 1}" rel="tooltip" title="Bajar 1 posición"><i class="icon icon-chevron-down"></i></a>{/if}
						{if="$value.orden !== count($rangos)"}<a class="btn btn-mini" href="/admin/usuario/mover_rango/{$value.id}/{function="count($rangos)"}" rel="tooltip" title="Colocar último"><i class="icon icon-arrow-down"></i></a>{/if}
					</div>
				</div>
			</td>
			<td>{$value.nombre}</td>
			<td><span style="color: #{function="sprintf('%06s', dechex($value.color))"}; background-color: #{function="Utils::get_contrast_yiq(sprintf('%06s', dechex($value.color)))"};">#{function="strtoupper(sprintf('%06s', dechex($value.color)))"}</span></td>
			<td><img src="{#THEME_URL#}/assets/img/rangos/{$value.imagen}" /></td>
			<td>
				<div class="btn-group">
					<a href="/admin/usuario/ver_rango/{$value.id}" class="btn btn-mini" title="Permisos" rel="tooltip"><i class="icon icon-lock"></i></a>
					<a href="/admin/usuario/editar_rango/{$value.id}" class="btn btn-mini btn-info" title="Editar" rel="tooltip"><i class="icon-white icon-pencil"></i></a>
					{if="$value.id !== $rango_defecto && count($rangos) > 1"}<a href="/admin/usuario/borrar_rango/{$value.id}" class="btn btn-mini btn-danger" title="Borrar" rel="tooltip"><i class="icon-white icon-remove"></i></a>{/if}
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