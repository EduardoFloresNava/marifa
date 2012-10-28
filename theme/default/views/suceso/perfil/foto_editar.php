<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-pencil"></i>
	</div>
	<div class="contenido">
		{if="$suceso.editor.id === $suceso.usuario.id"}
		<a href="/perfil/index/{$suceso.editor.nick}">{$suceso.editor.nick}</a> {@ha editado su foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
		{else}
		<a href="/perfil/index/{$suceso.editor.nick}">{$suceso.editor.nick}</a> {@ha editado el foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
		{/if}
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
