<div class="suceso clearfix">
	<div class="icono hidden-phone">
		<i class="icon icon-comment"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}#c-{$suceso.comentario_id}">{@comentado@}</a> {@el@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}/">{@shout@}</a> {@de@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
