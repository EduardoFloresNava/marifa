<div class="suceso clearfix">
	<a href="{#SITE_URL#}/@{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha publicado una foto titulada@} <a href="{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html">{$suceso.foto.titulo}</a>.
			</div>
		</div>
	</div>
</div>