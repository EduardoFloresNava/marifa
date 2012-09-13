<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-thumbs-{if="$suceso.voto > 0"}up{else}down{/if}"></i>
	</div>
	<div class="contenido">
		{if="$suceso.usuario.id === $actual.id"}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@vot&oacute;@} {if="$suceso.voto > 0"}<span class="label label-success">+1</span>{else}<span class="label label-important">-1</span>{/if} {@la foto titulada@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a> de <a href="">{$suceso.foto.usuario.nick}</a>.
		{else}
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@vot&oacute;@} {if="$suceso.voto > 0"}<span class="label label-success">+1</span>{else}<span class="label label-important">-1</span>{/if} {@la foto titulada@} <a href="/foto/ver/{$suceso.foto.id}">{$suceso.foto.titulo}</a>.
		{/if}
	</div>
	<div class="fecha hidden-phone hidden-tablet">
		{function="$fecha->fuzzy()"}
	</div>
</div>