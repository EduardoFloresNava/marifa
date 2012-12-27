{if="count($shouts) > 0"}
<div class="alert alert-info">Publicaciones con la etiqueta: <strong>{$etiqueta}</strong></div>
<div class="publicaciones">
	{loop="$shouts"}
	<div class="publicacion clearfix">
		<a href="{#SITE_URL#}/perfil/index/{$value.usuario.nick}" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($value.usuario.email, 50, 50)"}" alt="{$value.usuario.nick}" /></a>
		<div class="cuerpo">
			<div class="cabecera">
				<a href="{#SITE_URL#}/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a>
				<span class="fecha"><i class="icon icon-time"></i> {function="$value.fecha->fuzzy()"}</span>
			</div>
			<div class="contenido">
				<div class="wrapper">
					{if="$value.tipo == 0"}
						{$value.mensaje_bbcode}
					{elseif="$value.tipo == 1"}
					<a href="{$value.valor}"><img class="shout-imagen" src="{$value.valor}" /></a>
						{$value.mensaje_bbcode}
					{elseif="$value.tipo == 2"}
					<blockquote>
						<p><a href="{$value.valor.0}">{$value.valor.0}</a></p>
						<p>{$value.valor.1}</p>
					</blockquote>
						{$value.mensaje_bbcode}
					{else}
						{if="$value.valor.0 == 'youtube'"}
						<iframe src="http://youtube.com/embed/{$suceso.shout.valor.1}" width="680" height="483" frameborder="0" allowfullscreen></iframe>
						{elseif="$value.valor.0 == 'vimeo'"}
						<iframe src="http://player.vimeo.com/video/{$suceso.shout.valor.1}" width="680" height="483" frameborder="0" allowfullscreen></iframe>
						{/if}
						{$value.mensaje_bbcode}
					{/if}
				</div>
			</div>
			<div class="pie">
				<ul class="clearfix">
					<li><a href="{#SITE_URL#}/perfil/publicacion/{$value.usuario.nick}/{$value.id}/">Más información</a></li>

					{if="Usuario::is_login() && Model_Shout::s_fue_compartido($value.id, Usuario::$usuario_id)"}
					<li class="active"><i class="icon icon-retweet"></i> <strong>{$value.compartido}</strong></li>
					{else}
						{if="Usuario::is_login()"}
					<li><a href="{#SITE_URL#}/perfil/compartir_publicacion/{$value.usuario.nick}/{$value.id}"><i class="icon icon-retweet"></i> <strong>{$value.compartido}</strong></a></li>
						{else}
					<li><i class="icon icon-retweet"></i> <strong>{$value.compartido}</strong></li>
						{/if}
					{/if}

					{if="Usuario::is_login() && Usuario::$usuario_id !== $value.usuario_id"}
						{if="Model_Shout::s_es_favorito($value.id, Usuario::$usuario_id)"}
					<li class="active"><a href="{#SITE_URL#}/perfil/favorito_publicacion/{$value.usuario.nick}/{$value.id}/0"><i class="icon icon-star"></i> <strong>{$value.favoritos}</strong></a></li>
						{else}
					<li><a href="{#SITE_URL#}/perfil/favorito_publicacion/{$value.usuario.nick}/{$value.id}/1"><i class="icon icon-star"></i> <strong>{$value.favoritos}</strong></a></li>
						{/if}
					{else}
					<li><i class="icon icon-star"></i> <strong>{$value.favoritos}</strong></li>
					{/if}
					<li><a href="{#SITE_URL#}/perfil/publicacion/{$value.usuario.nick}/{$value.id}/"><i class="icon icon-comment"></i> <strong>{$value.comentario}</strong></a></li>
					{if="Usuario::is_login() && Usuario::$usuario_id !== $value.usuario_id"}
						{if="Model_Shout::s_ya_voto($value.id, Usuario::$usuario_id)"}
					<li class="active"><a href="{#SITE_URL#}/perfil/votar_publicacion/{$value.usuario.nick}/{$value.id}/0"><i class="icon icon-thumbs-up"></i> <strong>{$value.votos}</strong></a></li>
						{else}
					<li><a href="{#SITE_URL#}/perfil/votar_publicacion/{$value.usuario.nick}/{$value.id}/1"><i class="icon icon-thumbs-up"></i> <strong>{$value.votos}</strong></a></li>
						{/if}
					{else}
					<li><i class="icon icon-thumbs-up"></i> <strong>{$value.votos}</strong></li>
					{/if}
				</ul>
			</div>
		</div>
	</div>
	{/loop}
</div>
{else}
<div class="alert">No hay publicaciones con la etiqueta: <strong>{$etiqueta}</strong>.</div>
{/if}