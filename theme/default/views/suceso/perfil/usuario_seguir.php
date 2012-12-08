<div class="suceso clearfix">
	<a href="#" class="usuario"><img class="thumbnail" src="{function="Utils::get_gravatar($suceso.seguidor.email, 50, 50)"}" alt="{$suceso.seguidor.nick}" /></a>
	<div class="cuerpo">
		<div class="cabecera">
			<a href="/perfil/index/{$suceso.seguidor.nick}">{$suceso.seguidor.nick}</a>
			<span class="fecha"><i class="icon icon-time"></i> {function="$fecha->fuzzy()"}</span>
		</div>
		<div class="contenido">
			<div class="wrapper">
				{@Ha comenzado a seguir a@} <a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a>.
			</div>
		</div>
	</div>
</div>
