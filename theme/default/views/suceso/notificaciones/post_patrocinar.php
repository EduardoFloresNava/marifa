<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-certificate"></i>
	</div>
	<div class="contenido">
		{@Tu post @} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {if="$suceso.tipo"}{@ha sido patrocinado@}{else}{@ya no está patrocinado@}{/if}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>