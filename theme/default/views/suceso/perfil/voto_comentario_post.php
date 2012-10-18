<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-thumbs-{if="$suceso.voto"}up{else}down{/if}"></i>
	</div>
	<div class="contenido">
		{if="$suceso.u_voto.id === $actual.id"}
		<a href="/perfil/index/{$suceso.u_voto.nick}">{$suceso.u_voto.nick}</a> {@vot&oacute;@} {if="$suceso.voto"}<span class="label label-success">+1</span>{else}<span class="label label-important">-1</span>{/if} {@el comentario de@} <a href="/perfil/index/{$suceso.u_comentario.nick}">{$suceso.u_comentario.nick}</a> {@en el post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> de <a href="">{$suceso.post.usuario.nick}</a>.
		{else}
		<a href="/perfil/index/{$suceso.u_voto.nick}">{$suceso.u_voto.nick}</a> {@vot&oacute;@} {if="$suceso.voto"}<span class="label label-success">+1</span>{else}<span class="label label-important">-1</span>{/if} {@el comentario de@} <a href="/perfil/index/{$suceso.u_comentario.nick}">{$suceso.u_comentario.nick}</a> {@en tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
		{/if}
	</div>
	<div class="fecha hidden-phone hidden-tablet">
		{function="$fecha->fuzzy()"}
	</div>
</div>