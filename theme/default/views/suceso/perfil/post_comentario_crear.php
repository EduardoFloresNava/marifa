<div class="suceso clearfix">
	<a href="#" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.usuario.email, 50, 50)"}" alt="{$suceso.usuario.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{if="$suceso.usuario.id === $suceso.post_usuario.id"}
				{@Ha comentado en su post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
				{else}
				{@Ha comentado en el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.post_usuario.nick}">{$suceso.post_usuario.nick}</a>.
				{/if}
			</div>
		</div>
	</div>
</div>
