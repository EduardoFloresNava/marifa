<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
{if="$suceso.usuario.id == $suceso.foto.usuario.id"}
<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@coment&oacute; en su foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
{else}
	{if="$suceso.usuario.id !== $actual.id"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@coment&oacute; en tu foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
	{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@coment&oacute; en el foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.foto.usuario.nick}">{$suceso.foto.usuario.nick}</a>.
	{/if}
{/if}
	</div>
	<div class="fecha hidden-phone hidden-tablet">
		{function="$fecha->fuzzy()"}
	</div>
</div>