<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-pencil"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.editor.nick}">{$suceso.editor.nick}</a> {@ha editado tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>