<div class="fotos">
	{if="count($fotos) > 0"}
	<ul class="thumbnails">
		{loop="$fotos"}<li class="span4">
			<div class="thumbnail">
				<div class="thumbnail-size">
					<div>
						<img alt="" src="{$value.url}">
					</div>
				</div>
				<div class="caption">
					<h4>{$value.titulo} <small>{@por@} <a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></small></h4>
					<p>{$value.descripcion|nl2br}</p>
					<p class="toolbar">
						<span class="btn-toolbar">
							<span class="btn-group"><a class="btn btn-primary" href="/foto/ver/{$value.id}">{@Ver@}</a></span>
							{if="!$value.favorito"}<span class="btn-group"><a class="btn btn-info" href="/foto/favorito/{$value.id}" rel="tooltip" title="Agregar a favoritos"><i class="icon-white icon-star"></i></a></span>{/if}
							{if="!$value.voto"}<span class="btn-group"><a class="btn btn-success" href="/foto/votar/{$value.id}/1" rel="tooltip" title="Votar positivamente"><i class="icon-white icon-thumbs-up"></i></a>
							<a class="btn btn-danger" href="/foto/votar/{$value.id}/-1" rel="tooltip" title="Votar negativamente"><i class="icon-white icon-thumbs-down"></i></a></span>{/if}
							{if="$value.denunciar"}<span class="btn-group"><a class="btn btn-warning" href="/foto/denunciar/{$value.id}/" rel="tooltip" title="Denunciar"><i class="icon-white icon-exclamation-sign"></i></a></span>{/if}
						</span>
					</p>
					<p class="sumario clearfix">
						{if="$value.votos != 0"}<span class="{if="$value.votos > 0"}positivo{else}negativo{/if}">{function="abs($value.votos)"} {if="abs($value.votos) != 1"}{@votos@}{else}{@voto@}{/if}</span>{/if}
						{if="$value.votos != 0 && $value.favoritos != 0"}<span> - </span>{/if}
						{if="$value.favoritos != 0"}<span>{$value.favoritos} {if="abs($value.favoritos) != 1"}{@favoritos@}{else}{@favorito@}{/if}</span>{/if}
						{if="$value.favoritos != 0 && $value.comentarios != 0"}<span> - </span>{/if}
						{if="$value.comentarios != 0"}<span>{$value.comentarios} {if="abs($value.comentarios) != 1"}{@comentarios@}{else}{@comentarios@}{/if}</span>{/if}
						{if="$value.comentarios != 0"}<span> - </span>{/if}
						<span class="categoria">Categoria: {$value.categoria.nombre}</span>
						<span class="fecha">{function="$value.creacion->fuzzy()"}</span>
					</p>
				</div>
			</div>
		</li>{/loop}
	</ul>
	{$paginacion}
	{else}
	<div class="alert">No hay fotos disponibles</div>
	{/if}
</div>