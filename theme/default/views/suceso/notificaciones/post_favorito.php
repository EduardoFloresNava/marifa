<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-star"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.favorito.nick}">{$suceso.favorito.nick}</a> {@ha agregado a sus favoritos tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>