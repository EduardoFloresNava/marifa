<div class="busqueda">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#">Posts</a></li>
		<li><a href="#">Fotos</a></li>
		<li><a href="#">Comunidades</a></li>
	</ul>
	<div class="row">
		<div class="span12">
			<form class="form-search" id="search" method="POST" action="/buscador/q/">
				<div class="search-input">
					<input type="text" name="q" class="query" value="{$q}">
					<button type="submit"><i class="icon icon-search"></i></button>
				</div>
				<div class="search-options row-fluid">
					<div class="span4">
						<div class="btn-group" data-toggle="buttons-radio">
							<button type="button" class="btn">Google</button>
							<button type="button" class="btn active">Marifa</button>
						</div>
					</div>
					<div class="span4">
						Categorias:
						<select name="categoria">
							<option value="todos"{if="$categoria == 'todos'"} selected="selected"{/if}>Todas</option>
							{loop="$categorias"}
							<option value="{$value.seo}"{if="$categoria == $value.seo"} selected="selected"{/if}>{$value.nombre}</option>
							{/loop}
						</select>
					</div>
					<div class="span4">
						Usuario: <input name="usuario" value="{$usuario}" type="text" />
					</div>
				</div>
			</form>
		</div>
	</div>
	{if="isset($relacionado)"}{$relacionado}{/if}
	{if="isset($resultados)"}
	<div class="row">
		<div class="span12">
			{loop="$resultados"}
			<div>
				<img style="float: left;" src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" />
				<div style="margin-left: 30px;">
					<p><a href="/post/index/{$value.id}/">{$value.titulo}</a></p>
					<p>{$value.fecha->fuzzy()} - <a href="/perfil/informacion/{$value.usuario.nick}">@{$value.usuario.nick}</a> - {@Puntos@} {$value.puntos} - {@Comentarios@} {$value.comentarios}<span class="pull-right">{$value.categoria.nombre}</span> <a href="/buscador/relacionados/{$value.id}">Buscar relacionados</a></p>
				</div>
			</div>
			{else}
			<div class="alert">
				No hay resultados para <strong>'{$q}'</strong>.
			</div>
			{/loop}
			{if="count($resultados) > 0"}
			<div class="pagination pagination-centered">
				<ul>
					{if="$paginacion.first != $actual"}<li><a href="/buscador/q/{$q|urlencode}/{$paginacion.first}">&laquo;</a></li>{/if}
					{if="$paginacion.prev > 0"}<li><a href="/buscador/q/{$q|urlencode}/{$paginacion.prev}">{@Anterior@}</a></li>{/if}
					{loop="$paginacion.pages"}
					<li{if="$value == $actual"} class="active"{/if}}><a href="/buscador/q/{$q|urlencode}/{$value}">{$value}</a></li>
					{/loop}
					{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="/buscador/q/{$q|urlencode}/{$paginacion.next}">{@Siguiente@}</a></li>{/if}
					{if="$paginacion.last != $actual && $paginacion.last > 0"}<li><a href="/buscador/q/{$q|urlencode}/{$paginacion.last}">&raquo;</a></li>{/if}
				</ul>
			</div>
			<span>{@Mostrando@} {$cantidad} {@de@} {$total}</span>
			{/if}
		</div>
	</div>
	{/if}
</div>