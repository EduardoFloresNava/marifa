<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-book"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.publica.nick}">{$suceso.publica.nick}</a> {@ha publicado tu post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>