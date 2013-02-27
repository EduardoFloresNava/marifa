<div class="suceso{if=" ! $visto"} nuevo{/if}">
	<div class="icono hidden-phone">
		<i class="icon icon-certificate"></i>
	</div>
	<div class="contenido">
		{@Ganaste la medalla@} <img src="{#THEME_URL#}/assets/img/medallas/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'medallas'.DS, $suceso.medalla.imagen, 'small')"}" alt="{$suceso.medalla.nombre}" height="16" width="16" /> {$suceso.medalla.nombre}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>