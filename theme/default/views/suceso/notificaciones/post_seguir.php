<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-road"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.seguidor.nick}">{$suceso.seguidor.nick}</a> {@ha comenzado a seguir tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>