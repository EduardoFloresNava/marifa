<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/rangos">Rangos</a> <span class="divider">/</span></li>
    <li class="active">Detalles</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Detalles del rango <img src="{#THEME_URL#}/assets/img/rangos/{$rango.imagen}" /> <span style="color: #{function="sprintf('%06s', dechex($rango.color))"};">{$rango.nombre}</span>{if="$rango.id == $rango_defecto"} <span class="label label-info">POR DEFECTO</span>{/if}</h2>
	<div class="btn-group pull-right">
		<a href="/admin/usuario/editar_rango/{$rango.id}" class="btn btn-info"><i class="icon-white icon-pencil"></i> Editar</a>
		<a href="/admin/usuario/rangos" class="btn btn-success"><i class="icon-white icon-arrow-left"></i> Volver</a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>Tipo</th>
		<td>{if="$rango.tipo == 0"}Especial{elseif="$rango.tipo == 1"}Puntos{elseif="$rango.tipo == 2"}Posts{elseif="$rango.tipo == 3"}Fotos{else}Comentarios{/if}</td>
	</tr>
	{if="$rango.tipo != 0"}<tr>
		<th>Cantidad</th><td>{$rango.cantidad}</td>
	</tr>{/if}
	<tr>
		<th>Puntos por día</th>
		<td>{$rango.puntos}</td>
	</tr>
	<tr>
		<th>Puntos por post</th>
		<td>{$rango.puntos_dar}</td>
	</tr>
</table>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
<form method="POST" action="" name="permisos">
	<div class="btn-toolbar">
		<div class="btn-group">
			<a href="#" id="permiso-tipo-usuario" class="btn btn-info">Usuario</a>
			<a href="#" id="permiso-tipo-moderador" class="btn btn-info">Moderador</a>
			<a href="#" id="permiso-tipo-administrador" class="btn btn-info">Administrador</a>
		</div>
		<div class="btn-group pull-right">
			<button type="submit" class="btn btn-primary">Actualizar</button>
		</div>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Permiso</th>
				<th>Descripci&oacute;n</th>
				<td>Estado</td>
			</tr>
		</thead>
		<tbody>
			{loop="$permisos"}
			<tr>
				<th>{$value.0}</th>
				<td>{$value.1}</td>
				<td><input type="checkbox" name="{$key}" id="{$key}"{if="in_array($key, $permisos_rango)"} checked="checked"{/if} /></td>
			</tr>
			{/loop}
		</tbody>
	</table>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
	</div>
</form>