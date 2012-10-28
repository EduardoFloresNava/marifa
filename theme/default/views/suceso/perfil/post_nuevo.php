<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-book"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@cre&oacute; el post titulado@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>