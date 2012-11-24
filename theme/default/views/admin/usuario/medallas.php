<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Medallas</li>
</ul>
<div class="clearfix header">
	<h2 class="pull-left">Medallas</h2>
	<div class="btn-group pull-right">
		<a href="/admin/usuario/nueva_medalla/" class="btn btn-success"><i class="icon-white icon-plus"></i> Nueva</a>
	</div>
</div>
<div class="alert"><strong>&iexcl;Advertencia!</strong> Si borra una medalla, automáticamente todos los usuarios que tengan la medalla la perderán.</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Nombre</th>
			<th>Descripción</th>
			<th>Tipo</th>
			<th>Condición</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$medallas"}
		<tr>
			<td><img src="{#THEME_URL#}/assets/img/medallas/{$value.imagen}" width="16" height="16" /> {$value.nombre}</td>
			<td>{$value.descripcion}</td>
			<td>{if="$value.tipo == 0"}Usuario{elseif="$value.tipo == 1"}Post{else}Foto{/if}</td>
			<td>
				{$value.cantidad}
				{if="$value.condicion == 0 || $value.condicion == 9"}puntos
				{elseif="$value.condicion == 1 || $value.condicion == 10"}seguidores
				{elseif="$value.condicion == 2"}siguiendo
				{elseif="$value.condicion == 3"}comentarios en posts
				{elseif="$value.condicion == 4"}comentarios en fotos
				{elseif="$value.condicion == 5"}posts
				{elseif="$value.condicion == 6"}fotos
				{elseif="$value.condicion == 7 || $value.condicion == 15 || $value.condicion == 22"}medallas
				{elseif="$value.condicion == 8"}rango
				{elseif="$value.condicion == 11 || $value.condicion == 20"}comentarios
				{elseif="$value.condicion == 12 || $value.condicion == 23"}favoritos
				{elseif="$value.condicion == 13 || $value.condicion == 24"}denuncias
				{elseif="$value.condicion == 14 || $value.condicion == 21"}visitas
				{elseif="$value.condicion == 16"}veces compartido
				{elseif="$value.condicion == 17"}votos positivos
				{elseif="$value.condicion == 18"}votos negativos
				{elseif="$value.condicion == 19"}votos totales
				{/if}</td>
			<td>
				<div class="btn-group">
					<a href="/admin/usuario/usuarios_medalla/{$value.id}" class="btn btn-mini btn-success" title="Listado de usuarios" rel="tooltip"><i class="icon-white icon-user"></i></a>
					<a href="/admin/usuario/editar_medalla/{$value.id}" class="btn btn-mini btn-info" title="Editar" rel="tooltip"><i class="icon-white icon-pencil"></i></a>
					<a href="/admin/usuario/borrar_medalla/{$value.id}" class="btn btn-mini btn-danger" title="Borrar" rel="tooltip"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay medallas definidas!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}