<h2 class="title">{@Publicación de @} {$shout.usuario.nick}</h2>
<div class="well contenido-shout">{$shout.mensaje}</div>
<div class="btn-group links-shout">
	{if="Usuario::is_login() && Usuario::$usuario_id !== $usuario.id"}
		{if="Model_Shout::s_ya_voto($shout.id, Usuario::$usuario_id)"}
	<a class="btn active" href="/perfil/votar_publicacion/{$usuario.nick}/{$shout.id}/0"><i class="icon icon-thumbs-up"></i> {$shout.votos}</a>
		{else}
	<a class="btn" href="/perfil/votar_publicacion/{$usuario.nick}/{$shout.id}/1"><i class="icon icon-thumbs-up"></i> {$shout.votos}</a>
		{/if}
	{else}
	<a class="btn" disabled="disabled"><i class="icon icon-thumbs-up"></i> {$shout.votos}</a>
	{/if}
	<span class="btn"><i class="icon icon-comment"></i> {$shout.comentario}</span>
	{if="Usuario::is_login() && Usuario::$usuario_id !== $usuario.id"}
		{if="Model_Shout::s_es_favorito($shout.id, Usuario::$usuario_id)"}
	<a class="btn active" href="/perfil/favorito_publicacion/{$usuario.nick}/{$shout.id}/0"><i class="icon icon-star"></i> {$shout.favoritos}</a>
		{else}
	<a class="btn" href="/perfil/favorito_publicacion/{$usuario.nick}/{$shout.id}/1"><i class="icon icon-star"></i> {$shout.favoritos}</a>
		{/if}
	{else}
	<a class="btn" disabled="disabled"><i class="icon icon-star"></i> {$shout.favoritos}</a>
	{/if}

	{if="Usuario::is_login() && Model_Shout::s_fue_compartido($shout.id, Usuario::$usuario_id)"}
	<a class="btn active" href="#"{if="Usuario::$usuario_id == $shout.usuario_id"} disabled="disabled"{/if}><i class="icon icon-retweet"></i> {$shout.compartido}</a>
	{else}
	<a class="btn" {if="Usuario::is_login()"}href="/perfil/compartir_publicacion/{$usuario.nick}/{$shout.id}"{else}disabled="disabled"{/if}"><i class="icon icon-retweet"></i> {$shout.compartido}</a>
	{/if}
</div>
{if="$shout.comentario > 0"}
<div class="comentarios">
	{loop="$shout.comentarios"}
	<div class="comentario clearfix">
		<a href="/perfil/index/{$value.usuario.nick}"><img class="thumbnail pull-left" src="{function="Utils::get_gravatar($value.usuario.email, 64, 64)"}" /></a>
		<div class="content">
			<h4 class="title"><a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a><small>{$value.fecha->fuzzy()}</small></h4>
			<div>{$value.comentario}</div>
		</div>
	</div>
	{/loop}
</div>
{/if}
<h4 class="title">Comentar</h4>
<form action="/perfil/comentar_publicacion/{$usuario.nick}/{$shout.id}/" method="POST">
	<textarea id="comentario" name="comentario" class="span8"></textarea>
	<input type="submit" class="btn btn-large btn-primary" value="Comentar" />
</form>