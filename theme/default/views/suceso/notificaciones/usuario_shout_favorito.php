<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-star{if="!$suceso.agregar"}-empty{/if}"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {if="$suceso.agregar"}{@ha agregado tu@}{else}{@ha quitado tu@}{/if} <a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/publicacion/{$suceso.shout.id}">{@shout@}</a> {if="$suceso.agregar"}{@a sus favoritos@}{else}{@de sus favoritos@}{/if}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>