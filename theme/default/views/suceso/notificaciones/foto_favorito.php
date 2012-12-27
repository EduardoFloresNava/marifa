<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-star"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha agregado a sus favoritos tu foto@} <a href="{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html">{$suceso.foto.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>