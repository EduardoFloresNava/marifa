<div class="suceso clearfix">
	<a href="{#SITE_URL#}/@{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha@} <a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/publicacion/{$suceso.shout.id}#c-{$suceso.comentario_id}">{@comentado@}</a> {@el@} <a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/publicacion/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
			</div>
		</div>
	</div>
</div>