<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-eye-open"></i>
	</div>
	<div class="contenido">
	{if="$suceso.post.id === $actual.id"}
	<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha comenzado a seguir tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	{else}
	<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha comenzado a seguir el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.post.usuario.nick}">{$suceso.post.usuario.nick}</a>.
	{/if}
	</div>
	<div class="fecha hidden-phone hidden-tablet">
		{function="$fecha->fuzzy()"}
	</div>
</div>