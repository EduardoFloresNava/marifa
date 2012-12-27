<div class="busqueda">
<!--	<ul class="nav nav-tabs">
		<li class="active"><a href="#">Posts</a></li>
		<li><a href="#">Fotos</a></li>
		<li><a href="#">Comunidades</a></li>
	</ul>-->
	<div class="search-bar">
		<form class="form-search clearfix" id="search" method="POST" action="/buscador/q/">
			<input type="text" name="q" class="query show-tooltip" title="Búsqueda" value="{$q}" placeholder="Busqueda..." />
			<select name="categoria" class="show-tooltip" title="Categoria">
				<option value="todos"{if="$categoria == 'todos'"} selected="selected"{/if}>Todas</option>
				{loop="$categorias"}
				<option value="{$value.seo}"{if="$categoria == $value.seo"} selected="selected"{/if}>{$value.nombre}</option>
				{/loop}
			</select>
			<input name="usuario" value="{$usuario}" type="text" placeholder="Usuario..." class="show-tooltip usuario" title="Usuario" />
			<button type="submit" class="btn btn-success"><i class="icon-white icon-search"></i></button>
		</form>
		{if="isset($relacionado)"}<div class="alert alert-success relacionado"><i class="icon icon-info-sign"></i> Posts relacionados a <a href="{#SITE_URL#}/post/{$relacionado.post.categoria.seo}/{$relacionado.post.id}/{$relacionado.post.titulo|Texto::make_seo}.html">{$relacionado.post.titulo}</a> de <a href="{#SITE_URL#}/@{$relacionado.post.usuario.nick}">{$relacionado.post.usuario.nick}</a></div>{/if}
	</div>
	{if="isset($resultados)"}
		{if="count($resultados) > 0"}
	<div class="ultimo-post-list">
		{loop="$resultados"}
		<div class="ultimo-post clearfix{if="$value.sponsored"} patrocinado{/if}">
			<div class="categoria hidden-phone">
				<img src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" />
			</div>
			<div class="contenido">
				{if="$value.privado"}<i class="icon icon-lock show-tooltip" title="Privado"></i> {/if}{if="$value.sponsored"}<i class="icon icon-certificate show-tooltip" title="Patrocinado"></i> {/if}<a class="titulo" href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a>
				<div class="info">
					{@Por@}: <a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a> - {@Puntos@}: {$value.puntos} - {@Comentarios@}: {$value.comentarios} - Categoría: <a href="/post/categoria/{$value.categoria.seo}">{$value.categoria.nombre} - <a href="{#SITE_URL#}/buscador/relacionados/{$value.id}">Buscar relacionados</a></a>
				</div>
			</div>
			<div class="fecha visible-desktop">
				{$value.fecha->fuzzy()}
			</div>
		</div>
		{/loop}
	</div>
		{else}
	<div class="alert alert-info">
		No hay resultados para la búsqueda.
	</div>
		{/if}
	{$paginacion}{if="count($resultados) > 0"}<span>{@Mostrando@} {function="count($resultados)"} {@de@} {$total}</span>{/if}
	{/if}
</div>