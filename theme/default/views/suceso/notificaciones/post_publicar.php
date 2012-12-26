<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-book"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/perfil/index/{$suceso.publica.nick}">{$suceso.publica.nick}</a> {@ha publicado tu post@} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>