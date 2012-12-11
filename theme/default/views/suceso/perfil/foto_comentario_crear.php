<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.usuario.id === $suceso.foto_usuario.id"}
				{@Ha comentado en su foto@} <a href="{#SITE_URL#}/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
				{else}
				{@Ha comentado en la foto@} <a href="{#SITE_URL#}/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.foto_usuario.nick}">{$suceso.foto_usuario.nick}</a>.
				{/if}
			</div>
		</div>
	</div>
</div>