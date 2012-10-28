<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		{if="$suceso.usuario.id === $suceso.foto_usuario.id"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha comentado en su foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha comentado en la foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.foto_usuario.nick}">{$suceso.foto_usuario.nick}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
