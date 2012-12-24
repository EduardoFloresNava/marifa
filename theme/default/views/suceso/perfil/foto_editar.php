<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$suceso.editor.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.editor.email, 50, 50)"}" alt="{$suceso.editor.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.editor.nick}">{$suceso.editor.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.editor.id === $suceso.usuario.id"}
				{@Ha editado su foto@} <a href="{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html">{$suceso.foto.titulo}</a>.
				{else}
				{@Ha editado la foto@} <a href="{#SITE_URL#}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html">{$suceso.foto.titulo}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
				{/if}
			</div>
		</div>
	</div>
</div>