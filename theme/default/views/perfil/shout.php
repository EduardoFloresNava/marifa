<h2 class="title">{@Publicación de@} {$shout.usuario.nick}</h2>
<div class="well contenido-shout">
	{if="$shout.tipo == 0"}
		{$shout.mensaje_bbcode}
	{elseif="$shout.tipo == 1"}
	<a href="{$shout.valor}"><img class="shout-imagen" src="{$shout.valor}" /></a>
		{$shout.mensaje_bbcode}
	{elseif="$shout.tipo == 2"}
	<blockquote>
		<p><a href="{$shout.valor.0}">{$shout.valor.0}</a></p>
		<p>{$shout.valor.1}</p>
	</blockquote>
		{$shout.mensaje_bbcode}
	{else}
		{if="$shout.valor.0 == 'youtube'"}
		<iframe src="http://youtube.com/embed/{$shout.valor.1}" width="748" height="531" frameborder="0" allowfullscreen></iframe>
		{elseif="$shout.valor.0 == 'vimeo'"}
		<iframe src="http://player.vimeo.com/video/{$shout.valor.1}" width="748" height="531" frameborder="0" allowfullscreen></iframe>
		{/if}
		{$shout.mensaje_bbcode}
	{/if}
</div>
<div class="btn-group links-shout">
	{if="Usuario::is_login() && Usuario::$usuario_id !== $usuario.id"}
		{if="Model_Shout::s_ya_voto($shout.id, Usuario::$usuario_id)"}
	<a class="btn active" href="{#SITE_URL#}/@{$usuario.nick}/votar_publicacion/{$shout.id}/0"><i class="icon icon-thumbs-up"></i> {$shout.votos}</a>
		{else}
	<a class="btn" href="{#SITE_URL#}/@{$usuario.nick}/votar_publicacion/{$shout.id}/1"><i class="icon icon-thumbs-up"></i> {$shout.votos}</a>
		{/if}
	{else}
	<span class="btn" disabled="disabled"><i class="icon icon-thumbs-up"></i> {$shout.votos}</span>
	{/if}
	<span class="btn"><i class="icon icon-comment"></i> {$shout.comentario}</span>
	{if="Usuario::is_login() && Usuario::$usuario_id !== $usuario.id"}
		{if="Model_Shout::s_es_favorito($shout.id, Usuario::$usuario_id)"}
	<a class="btn active" href="{#SITE_URL#}/@{$usuario.nick}/favorito_publicacion/{$shout.id}/0"><i class="icon icon-star"></i> {$shout.favoritos}</a>
		{else}
	<a class="btn" href="{#SITE_URL#}/@{$usuario.nick}/favorito_publicacion/{$shout.id}/1"><i class="icon icon-star"></i> {$shout.favoritos}</a>
		{/if}
	{else}
	<span class="btn" disabled="disabled"><i class="icon icon-star"></i> {$shout.favoritos}</span>
	{/if}

	{if="Usuario::is_login() && Model_Shout::s_fue_compartido($shout.id, Usuario::$usuario_id)"}
	<span class="btn active" {if="Usuario::$usuario_id == $shout.usuario_id"} disabled="disabled"{/if}><i class="icon icon-retweet"></i> {$shout.compartido}</span>
	{else}
		{if="Usuario::is_login()"}
	<a class="btn" href="{#SITE_URL#}/@{$usuario.nick}/compartir_publicacion/{$shout.id}"><i class="icon icon-retweet"></i> {$shout.compartido}</a>
		{else}
	<span class="btn" disabled="disabled"><i class="icon icon-retweet"></i> {$shout.compartido}</span>
		{/if}
	{/if}
</div>
{if="$shout.comentario > 0"}
<div class="comentarios">
	{loop="$shout.comentarios"}
	<div class="comentario clearfix" id="c-{$value.id}">
		<a href="{#SITE_URL#}/@{$value.usuario.nick}"><img class="thumbnail pull-left" src="{function="Utils::get_gravatar($value.usuario.email, 64, 64)"}" /></a>
		<div class="content">
			<h4 class="title"><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a><small><i class="icon icon-time"></i> {$value.fecha->fuzzy()}</small></h4>
			<div class="body">{$value.comentario}</div>
		</div>
	</div>
	{/loop}
</div>
{/if}
<h4 class="title">Comentar</h4>
<form action="{#SITE_URL#}/@{$usuario.nick}/comentar_publicacion/{$shout.id}" method="POST">
	<textarea id="comentario" name="comentario" class="span8"></textarea>
	<input type="submit" class="btn btn-large btn-primary" value="Comentar" />
</form>