<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-pencil"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.editor.nick}">{$suceso.editor.nick}</a> {@ha editado tu foto@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>