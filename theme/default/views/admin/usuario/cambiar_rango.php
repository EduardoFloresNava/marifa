<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Cambiar rango</li>
</ul>
<div class="header">
	<h2>Cambiar rango de <a href="{#SITE_URL#}/perfil/index/{$usuario.nick}">{$usuario.nick}</a></h2>
</div>
<h4 class="title">Seleccione el rango que desea asignar:</h4>
<ul style="list-style: none;">
	{loop="$rangos"}
	{if="$value.id !== $usuario.rango"}<li><a style="color: #{function="sprintf('%06s', dechex($value.color))"};" href="{#SITE_URL#}/admin/usuario/cambiar_rango/{$usuario.id}/{$value.id}/"><img src="{#THEME_URL#}/assets/img/rangos/{$value.imagen}" /> {$value.nombre}</a></li>{/if}
	{/loop}
</ul>