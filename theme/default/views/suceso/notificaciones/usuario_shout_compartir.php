<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-retweet"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha compartido tu@} <a href="{#SITE_URL#}/@{$suceso.shout.usuario.nick}/publicacion/{$suceso.shout.id}">{@shout@}</a>.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>