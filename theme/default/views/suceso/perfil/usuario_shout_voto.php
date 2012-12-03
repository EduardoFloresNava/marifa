<div class="suceso clearfix">
	<div class="icono hidden-phone">
		<i class="icon icon-thumbs-{if="$suceso.voto"}up{else}down{/if}"></i>
	</div>
	<div class="contenido">
		{if="$suceso.voto"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha votado el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha quitado su voto del@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
