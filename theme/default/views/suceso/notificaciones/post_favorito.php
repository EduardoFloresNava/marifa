<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-star"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.favorito.nick}">{$suceso.favorito.nick}</a> {@ha agregado a sus favoritos tu post@} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>