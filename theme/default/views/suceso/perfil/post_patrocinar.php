<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-certificate"></i>
	</div>
	<div class="contenido">
		{@El post @} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {if="$suceso.tipo"}{@ha sido patrocinado@}{else}{@ya no está patrocinado@}{/if}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
