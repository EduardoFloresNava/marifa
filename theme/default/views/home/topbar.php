{if="isset($top_bar)"}
<ul class="nav nav-tabs">
	{loop="top_bar"}
	<li{if="$value.active"}  class="active"{/if}>
		<a href="{#SITE_URL#}{$value.link}">{$value.caption}{if="isset($value.cantidad) && $value.cantidad > 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
	</li>
	{/loop}
	<li class="pull-right">Categoría: <select id="post-menu-categoria"><option value="">Todas</option>{loop="$categorias"}<option value="{$value.seo}"{if="$value.seo == $categoria"} selected="selected"{/if}>{$value.nombre}</option>{/loop}</select></li>
</ul>
{/if}