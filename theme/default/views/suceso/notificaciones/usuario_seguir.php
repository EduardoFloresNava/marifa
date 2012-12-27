<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-road"></i>
	</div>
	<div class="contenido">
		<a href="{#SITE_URL#}/@{$suceso.seguidor.nick}">{$suceso.seguidor.nick}</a> {@ha comenzado a seguirte@}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>