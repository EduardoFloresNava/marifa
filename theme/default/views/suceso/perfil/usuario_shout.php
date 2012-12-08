<div class="suceso clearfix">
	<a href="#" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.shout.usuario.email, 50, 50)"}" alt="{$suceso.shout.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			{if="$actual.id == $suceso.usuario.id"}
			<a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>
			{else}
			<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			{/if}
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$actual.id == $suceso.usuario.id"}
					{if="$suceso.shout.tipo == 0"}
						{$suceso.shout.mensaje_bbcode}
					{elseif="$suceso.shout.tipo == 1"}
					<a href="{$suceso.shout.valor}"><img class="shout-imagen" src="{$suceso.shout.valor}" /></a>
						{$suceso.shout.mensaje_bbcode}
					{elseif="$suceso.shout.tipo == 2"}
					<blockquote>
						<p><a href="{$suceso.shout.valor.0}">{$suceso.shout.valor.0}</a></p>
						<p>{$suceso.shout.valor.1}</p>
					</blockquote>
						{$suceso.shout.mensaje_bbcode}
					{else}
						{if="$suceso.shout.valor.0 == 'youtube'"}
						<iframe src="http://youtube.com/embed/{$suceso.shout.valor.1}" width="680" height="483" frameborder="0" allowfullscreen></iframe>
						{elseif="$suceso.shout.valor.0 == 'vimeo'"}
						<iframe src="http://player.vimeo.com/video/{$suceso.shout.valor.1}" width="680" height="483" frameborder="0" allowfullscreen></iframe>
						{/if}
						{$suceso.shout.mensaje_bbcode}
					{/if}
				{else}
				{@Ha publicado un@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}">{@shout@}</a> {@en el perfil de @} <a href="/perfil/index/{$actual.nick}">{$actual.nick}</a>
				{/if}
			</div>
		</div>
		{if="$actual.id == $suceso.usuario.id"}
		<div class="pie">
			<ul class="clearfix">
				<li><a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">M&aacute;s informaci&oacute;n</a></li>

				{if="Usuario::is_login() && Model_Shout::s_fue_compartido($suceso.shout.id, Usuario::$usuario_id)"}
				<li class="active"><i class="icon icon-retweet"></i> <strong>{$suceso.shout.compartido}</strong></li>
				{else}
					{if="Usuario::is_login()"}
				<li><a href="/perfil/compartir_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}"><i class="icon icon-retweet"></i> <strong>{$suceso.shout.compartido}</strong></a></li>
					{else}
				<li><i class="icon icon-retweet"></i> <strong>{$suceso.shout.compartido}</strong></li>
					{/if}
				{/if}

				{if="Usuario::is_login() && Usuario::$usuario_id !== $suceso.shout.usuario_id"}
					{if="Model_Shout::s_es_favorito($suceso.shout.id, Usuario::$usuario_id)"}
				<li class="active"><a href="/perfil/favorito_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/0"><i class="icon icon-star"></i> <strong>{$suceso.shout.favoritos}</strong></a></li>
					{else}
				<li><a href="/perfil/favorito_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/1"><i class="icon icon-star"></i> <strong>{$suceso.shout.favoritos}</strong></a></li>
					{/if}
				{else}
				<li><i class="icon icon-star"></i> <strong>{$suceso.shout.favoritos}</strong></li>
				{/if}
				<li><a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/"><i class="icon icon-comment"></i> <strong>{$suceso.shout.comentario}</strong></a></li>
				{if="Usuario::is_login() && Usuario::$usuario_id !== $suceso.shout.usuario_id"}
					{if="Model_Shout::s_ya_voto($suceso.shout.id, Usuario::$usuario_id)"}
				<li class="active"><a href="/perfil/votar_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/0"><i class="icon icon-thumbs-up"></i> <strong>{$suceso.shout.votos}</strong></a></li>
					{else}
				<li><a href="/perfil/votar_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/1"><i class="icon icon-thumbs-up"></i> <strong>{$suceso.shout.votos}</strong></a></li>
					{/if}
				{else}
				<li><i class="icon icon-thumbs-up"></i> <strong>{$suceso.shout.votos}</strong></li>
				{/if}
			</ul>
		</div>
		{/if}
	</div>
</div>
