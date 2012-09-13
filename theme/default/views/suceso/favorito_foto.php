<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-heart"></i>
	</div>
	<div class="contenido">
	{if="$suceso.foto.id === $actual.id"}
	<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@agreg&oacute; como favorita tu foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
	{else}
	<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@agreg&oacute; como favorita la foro@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.foto.usuario.nick}">{$suceso.foto.usuario.nick}</a>.
	{/if}
	</div>
	<div class="fecha hidden-phone hidden-tablet">
		{function="$fecha->fuzzy()"}
	</div>
</div>