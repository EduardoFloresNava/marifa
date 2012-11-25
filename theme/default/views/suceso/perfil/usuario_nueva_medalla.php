<div class="suceso">
	<div class="icono hidden-phone">
		<i class="icon icon-certificate"></i>
	</div>
	<div class="contenido">
		<a href="/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha ganado la medalla@} <img src="{#THEME_URL#}/assets/img/medallas/{$suceso.medalla.imagen}" alt="{$suceso.medalla.nombre}" height="16" width="16" /> {$suceso.medalla.nombre}.
	</div>
	<div class="fecha visible-desktop">
		{function="$fecha->fuzzy()"}
	</div>
</div>
