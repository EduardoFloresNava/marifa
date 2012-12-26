<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$suceso.publica.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.publica.email, 50, 50)"}" alt="{$suceso.publica.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.publica.nick}">{$suceso.publica.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.publica.id === $suceso.usuario.id"}
				{@Ha publicado su post@} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a>.
				{else}
				{@Ha publicado el post@} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
				{/if}
			</div>
		</div>
	</div>
</div>