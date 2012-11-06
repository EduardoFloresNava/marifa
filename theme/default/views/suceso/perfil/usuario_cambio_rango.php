<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-certificate"></i>
	</div>
	<div class="contenido">
		{if="$suceso.usuario.id !== $actual.id"}
		<a href="/perfil/index/{$suceso.moderador.nick}">{$suceso.moderador.nick}</a> {@ha cambiado el rango de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ahora tiene como rango@} <img src="{#THEME_URL#}/assets/img/rangos/{$suceso.rango.imagen}" /><span style="color: #{function="sprintf('%06s', dechex($suceso.rango.color))"}"><strong>{$suceso.rango.nombre}</strong></span>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
