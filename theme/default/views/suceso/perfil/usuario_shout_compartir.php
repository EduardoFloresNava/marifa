<div class="suceso clearfix">
	<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
			{if="$actual.id == $suceso.usuario.id"}
				{@Ha compartido a través de @} <a href="{#SITE_URL#}/perfil/index/{$suceso.usuario_comparte.nick}">{$suceso.usuario_comparte.nick}</a> {@el@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a>: {$suceso.shout.mensaje}
			{else}
				{if="$suceso.shout.usuario.id == $suceso.usuario_comparte.id"}
				{@Ha compartido el@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
				{else}
				{@Ha compartido el@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a> {@a través de@} <a href="{#SITE_URL#}/perfil/index/{$suceso.usuario_comparte.nick}">{$suceso.usuario_comparte.nick}</a>.
				{/if}
			{/if}
			</div>
		</div>
		{if="$actual.id == $suceso.usuario.id"}
		<div class="pie">
			<ul class="clearfix">
				<li><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/publicacion/{$suceso.shout.id}/">Más información</a></li>

				{if="Usuario::is_login() && Model_Shout::s_fue_compartido($suceso.shout.id, Usuario::$usuario_id)"}
				<li class="active"><i class="icon icon-retweet"></i> <strong>{$suceso.shout.compartido}</strong></li>
				{else}
					{if="Usuario::is_login()"}
				<li><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/compartir_publicacion/{$suceso.shout.id}"><i class="icon icon-retweet"></i> <strong>{$suceso.shout.compartido}</strong></a></li>
					{else}
				<li><i class="icon icon-retweet"></i> <strong>{$suceso.shout.compartido}</strong></li>
					{/if}
				{/if}

				{if="Usuario::is_login() && Usuario::$usuario_id !== $suceso.shout.usuario_id"}
					{if="Model_Shout::s_es_favorito($suceso.shout.id, Usuario::$usuario_id)"}
				<li class="active"><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/favorito_publicacion/{$suceso.shout.id}/0"><i class="icon icon-star"></i> <strong>{$suceso.shout.favoritos}</strong></a></li>
					{else}
				<li><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/favorito_publicacion/{$suceso.shout.id}/1"><i class="icon icon-star"></i> <strong>{$suceso.shout.favoritos}</strong></a></li>
					{/if}
				{else}
				<li><i class="icon icon-star"></i> <strong>{$suceso.shout.favoritos}</strong></li>
				{/if}
				<li><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/publicacion/{$suceso.shout.id}/"><i class="icon icon-comment"></i> <strong>{$suceso.shout.comentario}</strong></a></li>
				{if="Usuario::is_login() && Usuario::$usuario_id !== $suceso.shout.usuario_id"}
					{if="Model_Shout::s_ya_voto($suceso.shout.id, Usuario::$usuario_id)"}
				<li class="active"><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/votar_publicacion/{$suceso.shout.id}/0"><i class="icon icon-thumbs-up"></i> <strong>{$suceso.shout.votos}</strong></a></li>
					{else}
				<li><a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/votar_publicacion/{$suceso.shout.id}/1"><i class="icon icon-thumbs-up"></i> <strong>{$suceso.shout.votos}</strong></a></li>
					{/if}
				{else}
				<li><i class="icon icon-thumbs-up"></i> <strong>{$suceso.shout.votos}</strong></li>
				{/if}
			</ul>
		</div>
		{/if}
	</div>
</div>