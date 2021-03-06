<h1 class="title">{@Top de posts@}</h1>
<div class="row-fluid">
	<div class="span2 top-options">
		<h3 class="title">{@Categorías@}</h3>
		<form action="" method="post">
			<select onchange="this.form.submit()" class="span12" name="categoria" id="categoria">
				<option value=""{if="$categoria == '' || $categoria == 'todas'"}selected="selected"{/if}>{@Todas@}</option>
				{loop="$categorias"}
				<option value="{$value.seo}"{if="$categoria == $value.seo"}selected="selected"{/if}>{$value.nombre|htmlentities:ENT_NOQUOTES}</option>{/loop}
			</select>
		</form>
		<h3 class="title">{@Período<@}/h3>
		<a href="{#SITE_URL#}/tops/index/{$categoria}/1" class="btn btn-mini{if="$periodo == 1"} active{/if}">{@Ayer@}</a>
		<a href="{#SITE_URL#}/tops/index/{$categoria}/2" class="btn btn-mini{if="$periodo == 2"} active{/if}">{@Hoy@}</a>
		<a href="{#SITE_URL#}/tops/index/{$categoria}/3" class="btn btn-mini{if="$periodo == 3"} active{/if}">{@Esta semana@}</a>
		<a href="{#SITE_URL#}/tops/index/{$categoria}/4" class="btn btn-mini{if="$periodo == 4"} active{/if}">{@Del mes@}</a>
		<a href="{#SITE_URL#}/tops/index/{$categoria}/0" class="btn btn-mini{if="$periodo == 0"} active{/if}">{@Todos los tiempos@}</a>
	</div>
	<div class="span10 top-list">
		<div class="row-fluid">
			<div class="span6">
				<h2 class="title">{@Post con más puntos@}</h2>
				{if="count($puntos) <= 0"}
				<div class="alert">{@!No hay posts!@}</div>
				{else}
				<ol>
					{loop="$puntos"}
					<li>
						<a href="{#SITE_URL#}/post/{$value.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
							<img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.imagen, 'small')"}" /> {$value.titulo}
							<span class="badge">{$value.puntos|intval}</span>
						</a>
					</li>
					{/loop}
				</ol>
				{/if}
			</div>
			<div class="span6">
				<h2 class="title">{@Post con más favoritos@}</h2>
				{if="count($favoritos) <= 0"}
				<div class="alert">{@!No hay posts!@}</div>
				{else}
				<ol>
					{loop="$favoritos"}
					<li>
						<a href="{#SITE_URL#}/post/{$value.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
							<img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.imagen, 'small')"}" /> {$value.titulo}
							<span class="badge">{$value.favoritos|intval}</span>
						</a>
					</li>
					{/loop}
				</ol>
				{/if}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<h2 class="title">{@Post con más seguidores@}</h2>
				{if="count($seguidores) <= 0"}
				<div class="alert">{@!No hay posts!@}</div>
				{else}
				<ol>
					{loop="$seguidores"}
					<li>
						<a href="{#SITE_URL#}/post/{$value.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
							<img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.imagen, 'small')"}" /> {$value.titulo}
							<span class="badge">{$value.seguidores|intval}</span>
						</a>
					</li>
					{/loop}
				</ol>
				{/if}
			</div>
			<div class="span6">
				<h2 class="title">{@Post con más comentarios@}</h2>
				{if="count($comentarios) <= 0"}
				<div class="alert">{@!No hay posts!@}</div>
				{else}
				<ol>
					{loop="$comentarios"}
					<li>
						<a href="{#SITE_URL#}/post/{$value.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
							<img src="{#THEME_URL#}/assets/img/categoria/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'categoria'.DS, $value.imagen, 'small')"}" /> {$value.titulo}
							<span class="badge">{$value.comentarios|intval}</span>
						</a>
					</li>
					{/loop}
				</ol>
				{/if}
			</div>
		</div>
	</div>
</div>