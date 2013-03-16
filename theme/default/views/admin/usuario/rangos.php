<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario/">{@Usuarios@}</a> <span class="divider">/</span></li>
    <li class="active">{@Rangos@}</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">{@Rangos@}</h2>
	<div class="btn-group pull-right">
		<a href="/admin/usuario/nuevo_rango/" class="btn btn-success"><i class="icon-white icon-plus"></i> {@Nuevo@}</a>
	</div>
</div>
<div class="alert alert-info">
	{@<strong>¡Importante!</strong> En el caso de que un usuario tenga un rango especial y por un logro pueda pasar a otro rango, solo se va a realizar si el nuevo rango se encuentra con un orden menor (es decir, por arriba).@}
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>{@Orden@}</th>
			<th>{@Nombre@}</th>
			<th>{@Descripción@}</th>
			<th>{@Usuarios@}</th>
			<th title="{@Puntos por día@}">{@PPD@}</th>
			<th title="{@Puntos por post@}">{@PPP@}</th>
			<th>{@Tipo@}</th>
			<th>{@Acciones@}</th>
		</tr>
	</thead>
	<tbody>
		{loop="$rangos"}
		<tr{if="$value.id == $rango_defecto"} class="alert-info"{/if}>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						#{$value.orden}
					</div>
					<div class="btn-group">
						{if="$value.orden !== 1"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/1" title="{@Colocar primero@}"><i class="icon icon-arrow-up"></i></a>{/if}
						{if="$value.orden !== 1"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/{$value.orden - 1}" title="{@Subir 1 posición@}"><i class="icon icon-chevron-up"></i></a>{/if}
						{if="$value.orden !== count($rangos)"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/{$value.orden + 1}" title="{@Bajar 1 posición@}"><i class="icon icon-chevron-down"></i></a>{/if}
						{if="$value.orden !== count($rangos)"}<a class="btn btn-mini" href="{#SITE_URL#}/admin/usuario/mover_rango/{$value.id}/{function="count($rangos)"}" title="{@Colocar último@}"><i class="icon icon-arrow-down"></i></a>{/if}
					</div>
				</div>
			</td>
			<td><img src="{#THEME_URL#}/assets/img/rangos/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS, $value.imagen, 'small')"}" /> <span style="color: #{function="sprintf('%06s', dechex($value.color))"};">{$value.nombre}</span></td>
			<td>{$value.descripcion}</td>
			<td>{$value.usuarios}</td>
			<td>{$value.puntos}</td>
			<td>{$value.puntos_dar}</td>
			{if="$value.tipo == 0"}<td>{@Especial@}</td>
			{else}<td>{$value.cantidad} {if="$value.tipo == 1"}{@Puntos@}{elseif="$value.tipo == 2"}{@Posts@}{elseif="$value.tipo == 3"}{@Fotos@}{else}{@Comentarios@}{/if}</td>
			{/if}
			<td>
				<div class="btn-group">
					<a href="{#SITE_URL#}/admin/usuario/usuarios_rango/{$value.id}" class="btn btn-mini btn-success" title="{@Listado de usuarios@}"><i class="icon-white icon-user"></i></a>
					<a href="{#SITE_URL#}/admin/usuario/ver_rango/{$value.id}" class="btn btn-mini" title="{@Permisos@}"><i class="icon icon-lock"></i></a>
					<a href="{#SITE_URL#}/admin/usuario/editar_rango/{$value.id}" class="btn btn-mini btn-info" title="{@Editar@}"><i class="icon-white icon-pencil"></i></a>
					{if="$value.id !== $rango_defecto && count($rangos) > 1 && $value.usuarios == 0"}<a href="{#SITE_URL#}/admin/usuario/borrar_rango/{$value.id}" class="btn btn-mini btn-danger" title="{@Borrar@}"><i class="icon-white icon-remove"></i></a>{/if}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="6">{@¡No hay rangos definidos!@}</td>
		</tr>
		{/loop}
	</tbody>
</table>