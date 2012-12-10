<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-flag"></i>
	</div>
	<div class="contenido">
		{@Tu post @} <a href="{#SITE_URL#}/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {if="$suceso.tipo"}{@ha sido fijado en la portada@}{else}{@ya no está fijo@}{/if}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>