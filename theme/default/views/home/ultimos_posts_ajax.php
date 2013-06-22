<h3 class="title clearfix">Últimos posts<small class="pull-right leyenda">Leyenda: <span><i class="icon icon-bookmark"></i>Fijo - <i class="icon icon-certificate"></i>Patrocinado - <i class="icon icon-lock"></i>Privado</span></small></h3>
{if="count($sticky) == 0 && count($ultimos_posts) == 0"}
<div class="ultimo-post-list alert">No hay posts aún.</div>
{else}
<div class="ultimo-post-list">
	{loop="$sticky"}
	<div class="ultimo-post clearfix fijo">
		<div class="categoria hidden-phone">
			<img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.categoria.imagen, 'small')"}" />
		</div>
		<div class="contenido">
			{if="$value.privado"}<i class="icon icon-lock show-tooltip" title="Privado"></i> {/if}{if="$value.sponsored"}<i class="icon icon-certificate show-tooltip" title="Patrocinado"></i> {/if}<i class="icon icon-bookmark show-tooltip" title="Fijo"></i> <a class="titulo" href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a>
			<div class="info">
				{@Por@}: <a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a> - {@Puntos@}: {$value.puntos} - {@Comentarios@}: {$value.comentarios} - Categoría: <a href="/post/categoria/{$value.categoria.seo}">{$value.categoria.nombre}</a>
			</div>
		</div>
		<div class="fecha visible-desktop">
			{$value.fecha->fuzzy()}
		</div>
	</div>
	{/loop}
	{loop="$ultimos_posts"}
	<div class="ultimo-post clearfix{if="$value.sponsored"} patrocinado{/if}">
		<div class="categoria hidden-phone">
			<img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.categoria.imagen, 'small')"}" />
		</div>
		<div class="contenido">
			{if="$value.privado"}<i class="icon icon-lock show-tooltip" title="Privado"></i> {/if}{if="$value.sponsored"}<i class="icon icon-certificate show-tooltip" title="Patrocinado"></i> {/if}<a class="titulo" href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a>
			<div class="info">
				{@Por@}: <a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a> - {@Puntos@}: {$value.puntos} - {@Comentarios@}: {$value.comentarios} - Categoría: <a href="/post/categoria/{$value.categoria.seo}">{$value.categoria.nombre}</a>
			</div>
		</div>
		<div class="fecha visible-desktop">
			{$value.fecha->fuzzy()}
		</div>
	</div>
	{/loop}
</div>
{/if}
{$paginacion}