<div class="suceso clearfix">
	<div class="clearfix">
		<div class="icono hidden-phone">
			<i class="icon icon-user"></i>
		</div>
		<div class="contenido">
			<a href="/perfil/index/{$actual.nick}/">{$actual.nick}</a> {@ha sido citado por@} <a href="/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a> {@en un@} <a href="/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}">{@shout@}</a>.
		</div>
		<div class="fecha visible-desktop">
			{function="$fecha->fuzzy()"}
		</div>
	</div>
</div>
