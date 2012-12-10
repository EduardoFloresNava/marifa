<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$suceso.puntua.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.puntua.email, 50, 50)"}" alt="{$suceso.puntua.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.puntua.nick}">{$suceso.puntua.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha dado@} <span class="badge badge-info">{$suceso.puntos}</span> {if="$suceso.puntos == 1"}{@punto@}{else}{@puntos@}{/if} {@al post@} <a href="{#SITE_URL#}/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
			</div>
		</div>
	</div>
</div>