<div class="moderar">
	<h1 class="title">Panel de moderación</h1>
	{if="isset($top_bar)"}
	<div class="row-fluid">
		<div class="span2 menu">
			<ul class="nav nav-list">
				{loop="top_bar"}{if="isset($value.link)"}<li{if="$value.active"}  class="active"{/if}>
					<a href="{#SITE_URL#}{$value.link}">{$value.caption}{if="isset($value.cantidad) && $value.cantidad >= 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
				</li>
				{else}<li class="nav-header">{$value.caption}</li>{/if}{/loop}
			</ul>
		</div>
		<div class="span10 contenido">{$contenido}</div>
	</div>{else}{$contenido}{/if}
</div>