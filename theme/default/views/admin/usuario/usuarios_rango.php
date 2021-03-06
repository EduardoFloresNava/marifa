<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario/rangos">Rangos</a> <span class="divider">/</span></li>
    <li class="active">Usuarios con el rango <span style="color: #{function="sprintf('%06s', dechex($rango.color))"};">{$rango.nombre}</span></li>
</ul>
<div class="header">
	<h2>Usuarios con el rango <img src="{#THEME_URL#}/assets/img/rangos/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS, $rango.imagen, 'small')"}" /><span style="color: #{function="sprintf('%06s', dechex($rango.color))"};">{$rango.nombre}</span></h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Nick</th>
			<th>E-Mail</th>
			<th>Ultima vez activo</th>
			<th>Estado</th>
		</tr>
	</thead>
	<tbody>
		{loop="$usuarios"}
		<tr>
			<td><a href="{#SITE_URL#}/@{$value.nick}">{$value.nick}</a></td>
			<td>{$value.email}</td>
			<td>{if="is_object($value.lastactive)"}{$value.lastactive->fuzzy()}{else}{@Nunca@}{/if}</td>
			<td><span class="label label-{if="$value.estado == 0"}info">PENDIENTE{elseif="$value.estado == 1"}success">ACTIVO{elseif="$value.estado == 2"}warning">SUSPENDIDO{elseif="$value.estado == 3"}important">BANEADO{/if}</span></td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="4">!No hay usuarios!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}