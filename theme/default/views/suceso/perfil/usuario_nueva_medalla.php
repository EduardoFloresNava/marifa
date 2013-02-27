<div class="suceso clearfix">
	<a href="{#SITE_URL#}/@{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha ganado la medalla@} <img src="{#THEME_URL#}/assets/img/medallas/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS, $suceso.medalla.imagen, 'small')"}" alt="{$suceso.medalla.nombre}" height="16" width="16" /> {$suceso.medalla.nombre}.
			</div>
		</div>
	</div>
</div>