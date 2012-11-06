<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-hand-{if="$suceso.voto"}up{else}down{/if}"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha votado@} {if="$suceso.voto"}<span class="badge badge-success">{@POSITIVAMENTE@}</span>{else}<span class="badge badge-important">{@NEGATIVAMENTE@}</span>{/if} {@tu foto titulada@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>