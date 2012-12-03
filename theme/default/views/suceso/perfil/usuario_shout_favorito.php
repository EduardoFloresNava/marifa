<div class="suceso clearfix">
	<div class="icono hidden-phone">
		<i class="icon icon-star{if="!$suceso.agregar"}-empty{/if}"></i>
	</div>
	<div class="contenido">
		{if="$suceso.agregar"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha agregado a sus favoritos el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha quitado de sus favoritos el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
