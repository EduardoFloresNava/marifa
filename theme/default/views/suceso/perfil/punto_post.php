<div class="suceso">
	<div class="icono hidden-">
		<i class="icon icon-plus"></i>
	</div>
	<div class="contenido">
	{if="$suceso.post.id === $actual.id"}
	<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@di&oacute;@} {$suceso.puntos} {@puntos a tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	{else}
	<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@di&oacute;@} {$suceso.puntos} {@puntos a el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.post.usuario.nick}">{$suceso.post.usuario.nick}</a>.
	{/if}
	</div>
	<div class="fecha hidden-phone hidden-tablet">
		{function="$fecha->fuzzy()"}
	</div>
</div>