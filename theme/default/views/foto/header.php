<ul class="nav nav-tabs">
	<li{if="$active == 'index'"}  class="active"{/if}><a href="{#SITE_URL#}/foto/">Fotos</a></li>
	{if="Usuario::is_login()"}
	<li><a href="{#SITE_URL#}/foto/nueva">Agregar Foto</a></li>
	<li{if="$active == 'mis_fotos'"}  class="active"{/if}><a href="{#SITE_URL#}/foto/mis_fotos">Mis Fotos</a></li>
	{/if}
	<li class="pull-right">Categoría: <select id="foto-menu-categoria"><option value="">Todas</option>{loop="$categorias"}<option value="{$value.seo}"{if="$value.seo == $categoria"} selected="selected"{/if}>{$value.nombre}</option>{/loop}</select></li>
</ul>