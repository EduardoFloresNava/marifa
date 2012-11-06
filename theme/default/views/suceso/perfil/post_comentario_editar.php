<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-pencil"></i>
	</div>
	<div class="contenido">
		{if="$suceso.usuario.id === $suceso.comentario_usuario.id"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha editado su comentario en el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha editado el comentario de@} <a href="/perfil/index/{$suceso.comentario_usuario.nick}">{$suceso.comentario_usuario.nick}</a> {@en el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
