<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-pencil"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha editado tu comentario en la foto@} <a href="{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html">{$suceso.foto.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>