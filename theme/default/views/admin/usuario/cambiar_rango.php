<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Cambiar rango</li>
</ul>
<div class="header">
	<h2>Cambiar rango de <a href="/perfil/index/{$usuario.nick}">{$usuario.nick}</a></h2>
</div>
<h4 class="title">Seleccione el rango que desea asignar:</h4>
<ul>
	{loop="$rangos"}
	{if="$value.id !== $usuario.rango"}<li><a href="/admin/usuario/cambiar_rango/{$usuario.id}/{$value.id}/">{$value.nombre}</a></li>{/if}
	{/loop}
</ul>