<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-asterisk"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.puntua.nick}">{$suceso.puntua.nick}</a> {@ha dado@} <span class="badge badge-info">{$suceso.puntos}</span> {if="$suceso.puntos == 1"}{@punto@}{else}{@puntos@}{/if} {@a tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>