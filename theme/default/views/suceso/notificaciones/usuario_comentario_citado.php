<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha citado tu@} <a href="{if="isset($suceso.comentario.foto_id)"}/foto/ver/{$suceso.comentario.foto_id}#c-{$suceso.comentario.id}{else}/post/index/{$suceso.comentario.post_id}#c-{$suceso.comentario.id}{/if}">comentario</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>