<div class="row">
	<div class="span7">
		<h3 class="title">&Uacute;ltimos posts<small class="pull-right">Leyenda: <span class="leyenda-post fijo"><i class="icon icon-bookmark"></i>Fijo</span> <span class="leyenda-post patrocinado"><i class="icon icon-certificate"></i>Patrocinado</span> <span class="leyenda-post privado"><i class="icon icon-lock"></i>Privado</span></small></h3>
		{loop="$sticky"}
		<div class="ultimo-post fijo">
			<div class="categoria hidden-phone">
				<img src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" />
			</div>
			<div class="contenido">
				{if="$value.privado"}<i class="icon icon-lock show-tooltip" title="Privado"></i> {/if}{if="$value.sponsored"}<i class="icon icon-certificate show-tooltip" title="Patrocinado"></i> {/if}<i class="icon icon-bookmark show-tooltip" title="Fijo"></i> <a class="titulo" href="/post/index/{$value.id}/">{$value.titulo}</a>
				<div class="info">
					{@Por@}: <a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a> - {@Puntos@}: {$value.puntos} - {@Comentarios@}: {$value.comentarios} - Categoria: {$value.categoria.nombre}
				</div>
			</div>
			<div class="fecha visible-desktop">
				{$value.fecha->fuzzy()}
			</div>
		</div>
		{/loop}
		{loop="$ultimos_posts"}
		<div class="ultimo-post{if="$value.sponsored"} patrocinado{/if}">
			<div class="categoria hidden-phone">
				<img src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" />
			</div>
			<div class="contenido">
				{if="$value.privado"}<i class="icon icon-lock show-tooltip" title="Privado"></i> {/if}{if="$value.sponsored"}<i class="icon icon-certificate show-tooltip" title="Patrocinado"></i> {/if}<a class="titulo" href="/post/index/{$value.id}/">{$value.titulo}</a>
				<div class="info">
					{@Por@}: <a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a> - {@Puntos@}: {$value.puntos} - {@Comentarios@}: {$value.comentarios} - Categoria: {$value.categoria.nombre}
				</div>
			</div>
			<div class="fecha visible-desktop">
				{$value.fecha->fuzzy()}
			</div>
		</div>
		{else}
			{if="count($sticky) == 0"}
		<div class="alert">No hay posts aún.</div>
			{/if}
		{/loop}
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
				<form action="/buscador/q/" method="POST" enctype="multipart/form-data">
					<input type="text" class="span8" name="q" id="q" placeholder="Contenido a buscar...">
					<button type="submit" class="btn span4">Buscar</button>
				</form>
			</div>
			<a class="more-options" href="/buscador/">Opciones</a>
		</div>
		<div class="home-statistics">
			<div class="row-fluid">
				<div class="span6"><i class="icon icon-user"></i>{$cantidad_usuarios_online} online</div>
				<div class="span6"><i class="icon icon-user"></i>{$cantidad_usuarios}</div>
			</div>
			<div class="row-fluid">
				<div class="span6"><i class="icon icon-book"></i>{$cantidad_posts}</div>
				<div class="span6"><i class="icon icon-comment"></i>{$cantidad_comentarios_posts}</div>
			</div>
			<div class="row-fluid">
				<div class="span6"><i class="icon icon-picture"></i>{$cantidad_fotos}</div>
				<div class="span6"><i class="icon icon-comment"></i>{$cantidad_comentarios_fotos}</div>
			</div>
		</div>
		<div>
			<h3 class="title">&Uacute;ltimos comentarios</h3>
			{if="count($ultimos_comentarios) > 0"}
			<ol>
			{loop="$ultimos_comentarios"}
				<li>
					{if="isset($value.post)"}
					<b><a href="/perfil/informacion/{$value.usuario.nick}">{$value.usuario.nick}</a></b> <a href="/post/index/{$value.post.id}/#c-{$value.id}">{$value.post.titulo}</a>
					{else}
					<b><a href="/perfil/informacion/{$value.usuario.nick}">{$value.usuario.nick}</a></b> <a href="/foto/ver/{$value.foto.id}/#c-{$value.id}">{$value.foto.titulo}</a>
					{/if}
				</li>
			{/loop}
			</ol>
			{else}
			<div class="alert">No hay comentarios</div>
			{/if}
		</div>
		<div>
			<h3 class="title">TOPs posts</h3>
			{if="count($top_posts) > 0"}
			<ol>
			{loop="$top_posts"}
				<li><a href="/post/index/{$value.id}">{$value.titulo}<span class="badge pull-right">{$value.puntos}</a></li>

			{/loop}
			</ol>
			{else}
			<div class="alert">No hay puntos</div>
			{/if}
		</div>
		<div>
			<h3 class="title">TOPs usuarios</h3>
			<ol>
			{loop="$usuario_top"}
				<li><a href="/perfil/index/{$value.nick}">{$value.nick}<span class="badge pull-right">{$value.puntos}</a></li>
			{/loop}
			</ol>
		</div>
	</div>
	<div class="span2 hidden-phone">
		<div>
			<h3 class="title">&Uacute;ltimas fotos</h3>
			{if="isset($ultimas_fotos[0])"}
			<!--170x150-->
			<a href="/foto/ver/{$ultimas_fotos.0.id}">
				<div class="thumbnail" style="min-height: 50px;">
					<img src="{$ultimas_fotos.0.url}" />
				</div>
			</a>
			{else}
			<div class="alert">No hay fotos para mostrar</div>
			{/if}
		</div>
	</div>
</div>