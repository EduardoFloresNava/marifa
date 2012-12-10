<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$actual.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($actual.email, 50, 50)"}" alt="{$actual.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$actual.nick}">{$actual.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha sido citado por@} <a href="{#SITE_URL#}/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a> {@en un@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}">{@shout@}</a>.
			</div>
		</div>
	</div>
</div>