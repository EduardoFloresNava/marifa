<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-star"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha agregado a sus favoritos la foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.foto_usuario.nick}">{$suceso.foto_usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>