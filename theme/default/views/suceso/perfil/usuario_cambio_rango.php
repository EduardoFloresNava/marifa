<div class="suceso clearfix">
	{if="$suceso.usuario.id !== $actual.id"}
	<a href="{#SITE_URL#}/perfil/index/{$suceso.moderador.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.moderador.email, 50, 50)"}" alt="{$suceso.moderador.nick}" /></a>
	{else}
	<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	{/if}
	<div class="cuerpo">
		<div class="cabecera">
			{if="$suceso.usuario.id !== $actual.id"}
			<a href="{#SITE_URL#}/perfil/index/{$suceso.moderador.nick}">{$suceso.moderador.nick}</a>
			{else}
			<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.seguidor.nick}</a>
			{/if}
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.usuario.id !== $actual.id"}
				{@Ha cambiado el rango de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
				{else}
				{@Ahora tiene como rango@} <img src="{#THEME_URL#}/assets/img/rangos/{$suceso.rango.imagen}" /><span style="color: #{function="sprintf('%06s', dechex($suceso.rango.color))"}"><strong>{$suceso.rango.nombre}</strong></span>.
				{/if}
			</div>
		</div>
	</div>
</div>