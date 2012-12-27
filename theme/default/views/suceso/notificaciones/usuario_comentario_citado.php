<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha citado tu@} <a href="{if="isset($suceso.comentario.foto_id)"}{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.comentario.foto_id}/{$suceso.foto.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{else}{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{/if}">comentario</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>