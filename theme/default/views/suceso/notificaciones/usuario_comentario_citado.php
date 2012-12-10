<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha citado tu@} <a href="{if="isset($suceso.comentario.foto_id)"}{#SITE_URL#}/foto/ver/{$suceso.comentario.foto_id}#c-{$suceso.comentario.id}{else}{#SITE_URL#}/post/index/{$suceso.comentario.post_id}#c-{$suceso.comentario.id}{/if}">comentario</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>