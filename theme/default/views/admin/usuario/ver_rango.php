<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/rangos">Rangos</a> <span class="divider">/</span></li>
    <li class="active">Detalles</li>
</ul>
<div class="header">
	<h2 class="title">Detalles del rango {$rango.nombre}</h2>
</div>
<table class="table table-bordered">
	<tr>
		<th>Nombre</th>
		<td>{$rango.nombre}</td>
		<th>Imagen</th>
		<td><img src="{#THEME_URL#}/assets/img/rangos/{$rango.imagen}" /></td>
		<th>Color</th>
		<td>{function="strtoupper(dechex($rango.color))"}</td>
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