<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.agregar"}
				{@Ha agregado a sus favoritos el@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
				{else}
				{@Ha quitado de sus favoritos el@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
				{/if}
			</div>
		</div>
	</div>
</div>