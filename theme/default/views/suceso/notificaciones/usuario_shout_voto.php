<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-thumbs{if="$suceso.voto"}-up{else}-down{/if}"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {if="$suceso.voto"}{@ha votado tu@}{else}{@ha quitado el voto de tu@}{/if} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}">{@shout@}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>