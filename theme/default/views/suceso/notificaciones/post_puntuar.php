<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-asterisk"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.puntua.nick}">{$suceso.puntua.nick}</a> {@ha dado@} <span class="badge badge-info">{$suceso.puntos}</span> {if="$suceso.puntos == 1"}{@punto@}{else}{@puntos@}{/if} {@a tu post@} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>