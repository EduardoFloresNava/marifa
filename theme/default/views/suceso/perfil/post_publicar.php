<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-pencil"></i>
	</div>
	<div class="contenido">
		{if="$suceso.publica.id === $suceso.usuario.id"}
		<a href="/perfil/index/{$suceso.publica.nick}">{$suceso.publica.nick}</a> {@ha publicado su post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
		{else}
		<a href="/perfil/index/{$suceso.publica.nick}">{$suceso.publica.nick}</a> {@ha publicado el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
