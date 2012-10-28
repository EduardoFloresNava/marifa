<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-flag"></i>
	</div>
	<div class="contenido">
		{@El post @} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {if="$suceso.tipo"}{@ha sido fijado en la portada@}{else}{@ya no está fijo@}{/if}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
