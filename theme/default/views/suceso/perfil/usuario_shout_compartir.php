<div class="suceso clearfix">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		{if="$actual.id == $suceso.usuario.id"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha compartido a través de @} <a href="/perfil/index/{$suceso.usuario_comparte.nick}">{$suceso.usuario_comparte.nick}</a> {@el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a>: {$suceso.shout.mensaje}
		{else}
			{if="$suceso.shout.usuario.id == $suceso.usuario_comparte.id"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha compartido el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
			{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha compartido el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a> {@a través de@} <a href="/perfil/index/{$suceso.usuario_comparte.nick}">{$suceso.usuario_comparte.nick}</a>.
			{/if}
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
	{if="$actual.id == $suceso.usuario.id"}
	<div class="links">
		<div class="btn-group pull-right">
			{if="Usuario::is_login()"}
				{if="Model_Shout::s_ya_voto($suceso.shout.id, Usuario::$usuario_id)"}
			<a class="btn active" href="/perfil/votar_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/0"><i class="icon icon-thumbs-up"></i> {$suceso.shout.votos}</a>
				{else}
			<a class="btn" href="/perfil/votar_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/1"><i class="icon icon-thumbs-up"></i> {$suceso.shout.votos}</a>
				{/if}
			{else}
			<a class="btn" href="#"><i class="icon icon-thumbs-up"></i> {$suceso.shout.votos}</a>
			{/if}
			<a class="btn" href="#"><i class="icon icon-comment"></i> {$suceso.shout.comentario}</a>
			{if="Usuario::is_login()"}
				{if="Model_Shout::s_es_favorito($suceso.shout.id, Usuario::$usuario_id)"}
			<a class="btn active" href="/perfil/favorito_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/0"><i class="icon icon-star"></i> {$suceso.shout.favoritos}</a>
				{else}
			<a class="btn" href="/perfil/favorito_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/1"><i class="icon icon-star"></i> {$suceso.shout.favoritos}</a>
				{/if}
			{else}
			<a class="btn" href="#"><i class="icon icon-star"></i> {$suceso.shout.favoritos}</a>
			{/if}

			{if="Usuario::is_login() && Model_Shout::s_fue_compartido($suceso.shout.id, Usuario::$usuario_id)"}
			<a class="btn active" href="#"{if="Usuario::$usuario_id == $suceso.shout.usuario_id"} disabled="disabled"{/if}><i class="icon icon-retweet"></i> {$suceso.shout.compartido}</a>
			{else}
			<a class="btn" href="{if="Usuario::is_login()"}/perfil/compartir_publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}{/if}"><i class="icon icon-retweet"></i> {$suceso.shout.compartido}</a>
			{/if}
			<a class="btn btn-success" href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/"><i class="icon-white icon-plus"></i></a>
		</div>
	</div>
	{/if}
</div>
