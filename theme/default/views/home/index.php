{include="home/topbar"}
<div class="row">
	<div class="{if="isset($ultimas_fotos)"}span7{else}span9{/if}">
		<h3 class="title clearfix">Últimos posts<small class="pull-right leyenda">Leyenda: <span><i class="icon icon-bookmark"></i>Fijo - <i class="icon icon-certificate"></i>Patrocinado - <i class="icon icon-lock"></i>Privado</span></small></h3>
		{if="count($sticky) == 0 && count($ultimos_posts) == 0"}
		<div class="alert">No hay posts aún.</div>
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
	</div>
	<div class="span3">
		<div class="home-search">
			<!--<ul class="nav nav-tabs">
				<li class="active"><a href="#">Posts</a></li>
				<li><a href="#">Temas</a></li>
			</ul>-->
			<div class="row-fluid titulo">
				<h3>Buscador</h3>
			</div>
			<div class="row-fluid contenido">
				<form action="{#SITE_URL#}/buscador/q/" method="POST" enctype="multipart/form-data">
					<input type="text" class="span8" name="q" id="q" placeholder="Contenido a buscar...">
					<button type="submit" class="btn span4">Buscar</button>
				</form>
			</div>
			<a class="more-options" href="{#SITE_URL#}/buscador/">Opciones</a>
		</div>
		<div class="home-statistics">
			<div class="row-fluid">
				<div class="span6"><i class="icon icon-user"></i>{$cantidad_usuarios_online} conectados</div>
				<div class="span6"><i class="icon icon-user"></i>{$cantidad_usuarios}</div>
			</div>
			<div class="row-fluid">
				<div class="span6"><i class="icon icon-book"></i>{$cantidad_posts}</div>
				<div class="span6"><i class="icon icon-comment"></i>{$cantidad_comentarios_posts}</div>
			</div>
			{if="isset($cantidad_fotos) && isset($cantidad_comentarios_fotos)"}<div class="row-fluid">
				<div class="span6"><i class="icon icon-picture"></i>{$cantidad_fotos}</div>
				<div class="span6"><i class="icon icon-comment"></i>{$cantidad_comentarios_fotos}</div>
			</div>{/if}
		</div>
		<div>
			<h3 class="title">Últimos comentarios</h3>
			{if="count($ultimos_comentarios) > 0"}
			<ol class="last-comment-list">
			{loop="$ultimos_comentarios"}
				<li>
					{if="isset($value.post)"}
					<b><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></b> <a href="{#SITE_URL#}/post/{$value.post.categoria.seo}/{$value.post.id}/{$value.post.titulo|Texto::make_seo}.html#c-{$value.id}">{$value.post.titulo}</a>
					{else}
					<b><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></b> <a href="{#SITE_URL#}/foto/{$value.foto.categoria.seo}/{$value.foto.id}/{$value.foto.titulo|Texto::make_seo}.html#c-{$value.id}">{$value.foto.titulo}</a>
					{/if}
				</li>
			{/loop}
			</ol>
			{else}
			<div class="alert">No hay comentarios</div>
			{/if}
		</div>
		<div class="home-list">
			<h3 class="title">TOPs posts</h3>
			{if="count($top_posts) > 0"}
			<ol>
			{loop="$top_posts"}
				<li><a href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}<span class="badge pull-right">{$value.puntos}</a></li>
			{/loop}
			</ol>
			{else}
			<div class="alert">No hay puntos</div>
			{/if}
		</div>
		<div class="home-list">
			<h3 class="title">TOPs usuarios</h3>
			<ol>
			{loop="$usuario_top"}
				<li><a href="{#SITE_URL#}/@{$value.nick}">{$value.nick}<span class="badge pull-right">{$value.puntos}</a></li>
			{/loop}
			</ol>
		</div>
	</div>
	{if="isset($ultimas_fotos)"}<div class="span2 hidden-phone">
		<div>
			<h3 class="title">Últimas fotos</h3>
			{if="isset($ultimas_fotos[0])"}
			<!--170x150-->
			<a href="{#SITE_URL#}/foto/{$ultimas_fotos.0.categoria.seo}/{$ultimas_fotos.0.id}/{$ultimas_fotos.0.titulo|Texto::make_seo}.html">
				<div class="thumbnail" style="min-height: 50px;">
					<img alt="{$ultimas_fotos.0.descripcion_clean|Texto::limit_chars:20,FALSE,'...'}" src="{$ultimas_fotos.0.url}" />
				</div>
			</a>
			{else}
			<div class="alert">No hay fotos para mostrar</div>
			{/if}
		</div>
	</div>{/if}
</div>