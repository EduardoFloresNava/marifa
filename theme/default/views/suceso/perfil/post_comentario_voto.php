<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha votado@} {if="$suceso.voto"}<span class="badge badge-success">{@POSITIVAMENTE@}</span>{else}<span class="badge badge-danger">{@NEGATIVAMENTE@}</span>{/if} {@el comentario de@} <a href="/perfil/index/{$suceso.comentario_usuario.nick}">{$suceso.comentario_usuario.nick}</a> {@en el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
