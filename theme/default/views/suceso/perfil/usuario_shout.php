<div class="suceso clearfix">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		{if="$actual.id == $suceso.usuario.id"}
		<a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a> {@ha publicado@}: {$suceso.shout.mensaje}
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha publicado un@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}">{@shout@}</a> {@en el perfil de @} <a href="/perfil/index/{$actual.nick}">{$actual.nick}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
	{if="$actual.id == $suceso.usuario.id"}
	<div class="links">
		<div class="btn-group pull-right">
			{if="Usuario::is_login() && Usuario::$usuario_id !== $suceso.shout.usuario_id"}
				{if="Model_Shout::s_ya_voto($suceso.shout.id, Usuario::$usuario_id)"}
			<a class="btn active" href="/perfil/votar_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/0"><i class="icon icon-thumbs-up"></i> {$suceso.shout.votos}</a>
				{else}
			<a class="btn" href="/perfil/votar_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/1"><i class="icon icon-thumbs-up"></i> {$suceso.shout.votos}</a>
				{/if}
			{else}
			<a class="btn" disabled="disabled"><i class="icon icon-thumbs-up"></i> {$suceso.shout.votos}</a>
			{/if}
			<a class="btn" href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/"><i class="icon icon-comment"></i> {$suceso.shout.comentario}</a>
			{if="Usuario::is_login() && Usuario::$usuario_id !== $suceso.shout.usuario_id"}
				{if="Model_Shout::s_es_favorito($suceso.shout.id, Usuario::$usuario_id)"}
			<a class="btn active" href="/perfil/favorito_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/0"><i class="icon icon-star"></i> {$suceso.shout.favoritos}</a>
				{else}
			<a class="btn" href="/perfil/favorito_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/1"><i class="icon icon-star"></i> {$suceso.shout.favoritos}</a>
				{/if}
			{else}
			<a class="btn" disabled="disabled"><i class="icon icon-star"></i> {$suceso.shout.favoritos}</a>
			{/if}

			{if="Usuario::is_login() && Model_Shout::s_fue_compartido($suceso.shout.id, Usuario::$usuario_id)"}
			<a class="btn active"{if="Usuario::$usuario_id == $suceso.shout.usuario_id"} disabled="disabled"{/if}><i class="icon icon-retweet"></i> {$suceso.shout.compartido}</a>
			{else}
			<a class="btn" {if="Usuario::is_login()"}href="/perfil/compartir_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}"{else}disabled="disabled"{/if}><i class="icon icon-retweet"></i> {$suceso.shout.compartido}</a>
			{/if}
			<a class="btn btn-success" href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/"><i class="icon-white icon-plus"></i></a>
		</div>
	</div>
	{/if}
</div>
