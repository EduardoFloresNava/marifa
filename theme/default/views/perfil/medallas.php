<h2{if="count($medallas) <= 0"} class="title"{/if}>{@Medallas de@} {$usuario.nick}</h2>
{if="count($medallas) > 0"}
<div class="lista-medallas">
	{loop="$medallas"}
	<div class="medalla clearfix">
		<img src="{#THEME_URL#}/assets/img/medallas/{$value.medalla.imagen}" alt="{$value.medalla.nombre}" class="pull-left" />
		<div class="pull-left">
			<h4>{$value.medalla.nombre}<small class="fecha">{$value.fecha->fuzzy()}</small></h4>
			<div class="descripcion">{$value.medalla.descripcion}</div>
		</div>
	</div>
	{/loop}
</div>
{else}
<div class="alert alert-info">El usuario no tiene medallas aún.</div>
{/if}