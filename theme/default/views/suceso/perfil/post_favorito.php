<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-star"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.favorito.nick}">{$suceso.favorito.nick}</a> {@ha agregado a sus favoritos el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
