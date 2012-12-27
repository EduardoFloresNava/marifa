<div class="suceso clearfix">
	<a href="{#SITE_URL#}/@{$suceso.puntua.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.puntua.email, 50, 50)"}" alt="{$suceso.puntua.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/@{$suceso.puntua.nick}">{$suceso.puntua.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha dado@} <span class="badge badge-info">{$suceso.puntos}</span> {if="$suceso.puntos == 1"}{@punto@}{else}{@puntos@}{/if} {@al post@} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a> {@de@} <a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
			</div>
		</div>
	</div>
</div>