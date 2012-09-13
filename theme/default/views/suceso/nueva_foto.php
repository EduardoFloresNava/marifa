<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-picture"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@agreg&oacute; una foto titulada@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>