<ul class="nav nav-tabs">
    <li class="active"><a href="#">Posts</a></li>
	<li><a href="#">Fotos</a></li>
	<li><a href="#">Comunidades</a></li>
</ul>
<div class="row">
	<div class="span12">
		<ul class="nav nav-pills">
			<li><a href="#">Google</a></li>
			<li class="active"><a href="#">Marifa</a></li>
		</ul>
		    <form class="form-search" method="POST" action="/buscador/q/">
				<input type="text" name="q" class="input-xlarge search-query" value="{$q}">
				<button type="submit" class="btn">Search</button>
				Categorias: <select></select>
				Usuario: <input type="text" />
			</form>
	</div>
</div>
{if="isset($resultados)"}
<div class="row">
	<div class="span12">
		{loop="$resultados"}
		<div>
			<img style="float: left;" src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" />
			<div style="margin-left: 30px;">
				<p><a href="/post/index/{$value.id}/">{$value.titulo}</a></p>
				<p>{$value.fecha->fuzzy()} - <a href="/perfil/informacion/{$value.usuario.nick}">@{$value.usuario.nick}</a> - {@Puntos@} {$value.puntos} - {@Comentarios@} {$value.comentarios}<span class="pull-right">{$value.categoria.nombre}</span> <a href="/busqueda/relacionados/{$value.id}">Buscar relacionados</a></p>
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