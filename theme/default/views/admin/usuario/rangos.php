<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Rangos</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Rangos</h2>
	<div class="btn-group pull-right">
		<a href="/admin/usuario/nuevo_rango/" class="btn btn-success"><i class="icon-white icon-plus"></i> Nuevo</a>
	</div>
</div>
<div class="alert alert-info">
	<strong>&iexcl;Importante!</strong> En el caso de que un usuario tenga un rango especial y por un logro pueda pasar a otro rango, solo se va a realizar si el nuevo rango se encuentra con un orden menor (es decir, por arriba).
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Orden</th>
			<th>Nombre</th>
			<th>Usuarios</th>
			<th>Puntos por d&iacute;a</th>
			<th>Puntos por post</th>
			<th>Tipo</th>
			<th>Requerimiento</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$rangos"}
		<tr{if="$value.id == $rango_defecto"} class="alert-info"{/if}>
			<td><div class="btn-toolbar">
					<div class="btn-group">
						#{$value.orden}
					</div>
					<div class="btn-group">
						{if="$value.orden !== 1"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/1" rel="tooltip" title="Colocar primero"><i class="icon icon-arrow-up"></i></a>{/if}
						{if="$value.orden !== 1"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/{$value.orden - 1}" rel="tooltip" title="Subir 1 posición"><i class="icon icon-chevron-up"></i></a>{/if}
						{if="$value.orden !== count($rangos)"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/{$value.orden + 1}" rel="tooltip" title="Bajar 1 posici&oacute;n"><i class="icon icon-chevron-down"></i></a>{/if}
						{if="$value.orden !== count($rangos)"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/{function="count($rangos)"}" rel="tooltip" title="Colocar último"><i class="icon icon-arrow-down"></i></a>{/if}
					</div>
				</div>
			</td>
			<td><img src="{#THEME_URL#}/assets/img/rangos/{$value.imagen}" /> <span style="color: #{function="sprintf('%06s', dechex($value.color))"};">{$value.nombre}</span></td>
			<td>{$value.usuarios}</td>
			<td>{$value.puntos}</td>
			<td>{$value.puntos_dar}</td>
			{if="$value.tipo == 0"}<td colspan="2">Especial</td>
			{else}<td>{if="$value.tipo == 1"}Puntos{elseif="$value.tipo == 2"}Posts{elseif="$value.tipo == 3"}Fotos{else}Comentarios{/if}</td>
			<td>{$value.cantidad}</td>
			{/if}
			<td>
				<div class="btn-group">
					<a href="{#SITE_URL#}/admin/usuario/usuarios_rango/{$value.id}" class="btn btn-mini btn-success" title="Listado de usuarios" rel="tooltip"><i class="icon-white icon-user"></i></a>
					<a href="{#SITE_URL#}/admin/usuario/ver_rango/{$value.id}" class="btn btn-mini" title="Permisos" rel="tooltip"><i class="icon icon-lock"></i></a>
					<a href="{#SITE_URL#}/admin/usuario/editar_rango/{$value.id}" class="btn btn-mini btn-info" title="Editar" rel="tooltip"><i class="icon-white icon-pencil"></i></a>
					{if="$value.id !== $rango_defecto && count($rangos) > 1 && $value.usuarios == 0"}<a href="{#SITE_URL#}/admin/usuario/borrar_rango/{$value.id}" class="btn btn-mini btn-danger" title="Borrar" rel="tooltip"><i class="icon-white icon-remove"></i></a>{/if}
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