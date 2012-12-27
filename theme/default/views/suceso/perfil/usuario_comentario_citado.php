<div class="suceso clearfix">
	<a href="{#SITE_URL#}/@{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.comentario_usuario.id !== $actual.id"}
				{@Ha citado el@} <a href="{if="isset($suceso.comentario.foto_id)"}{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{else}{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{/if}">comentario</a> {@de@} <a href="{#SITE_URL#}/@{$suceso.comentario_usuario.nick}">{$suceso.comentario_usuario.nick}</a>.
				{else}
				{@Ha citado tu@} <a href="{if="isset($suceso.comentario.foto_id)"}{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{else}{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{/if}">comentario</a>.
				{/if}
			</div>
		</div>
	</div>
</div>