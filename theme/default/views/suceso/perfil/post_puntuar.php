<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-asterisk"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.puntua.nick}">{$suceso.puntua.nick}</a> {@ha dado@} <span class="badge badge-info">{$suceso.puntos}</span> {if="$suceso.puntos == 1"}{@punto@}{else}{@puntos@}{/if} {@al post@} <a href="/post/index/{$suceso.post.id}">{$suceso.post.titulo}</a> {@de@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
