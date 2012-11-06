<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-hand-{if="$suceso.voto"}up{else}down{/if}"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha votado@} {if="$suceso.voto"}<span class="badge badge-success">{@POSITIVAMENTE@}</span>{else}<span class="badge badge-important">{@NEGATIVAMENTE@}</span>{/if} {@la foto titulada@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.foto_usuario.nick}">{$suceso.foto_usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>