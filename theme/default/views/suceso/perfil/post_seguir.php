<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-road"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.seguidor.nick}">{$suceso.seguidor.nick}</a> {@ha comenzado a seguir el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
