<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="clearfix">
		<div class="icono hidden-phone">
			<i class="icon icon-user"></i>
		</div>
		<div class="contenido">
			<a href="{#SITE_URL#}/perfil/index/{$suceso.shout.usuario.nick}">{$suceso.shout.usuario.nick}</a> {@te ha citado en un@} <a href="{#SITE_URL#}/perfil/publicacion/{$suceso.shout.usuario.nick}/{$suceso.shout.id}">{@shout@}</a>.
		</div>
		<div class="fecha visible-desktop">
			{function="$fecha->fuzzy()"}
		</div>
	</div>
</div>
