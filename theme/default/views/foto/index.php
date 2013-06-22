{if="isset($header)"}{$header}{/if}
<div class="fotos">
	{if="count($fotos) > 0"}
	<ul class="thumbnails">
		{loop="$fotos"}<li class="span4">
			<div class="thumbnail">
				<div class="thumbnail-size">
					<div>
						<img alt="{$value.descripcion_clean|Texto::limit_chars:30,TRUE,'...'}" src="{$value.url}">
					</div>
				</div>
				<div class="caption">
					<h4>{$value.titulo}</h4>
					<small>{@por@} <a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a> {function="$value.creacion->fuzzy()"}</small>
					<p class="descripcion">{$value.descripcion|nl2br}</p>
					<p class="toolbar">
						<span class="btn-toolbar">
							<span class="btn-group"><a class="btn btn-primary" href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{@Ver@}</a></span>
							{if="!$value.favorito"}<span class="btn-group"><a class="btn btn-info one-click-ajax" data-one-click-spinner="true" href="{#SITE_URL#}/foto/favorito/{$value.id}" title="{@Agregar a favoritos@}"><i class="icon-white icon-star"></i></a></span>{/if}
							{if="!$value.voto"}
							<span class="btn-group votar-foto-{$value.id}">
								<a class="btn btn-success one-click-ajax" data-one-click-spinner="true" data-one-click-items=".votar-foto-{$value.id} .btn" data-one-click-container=".votar-foto-{$value.id}" href="{#SITE_URL#}/foto/votar/{$value.id}/1" title="{@Votar positivamente@}"><i class="icon-white icon-thumbs-up"></i></a>
								<a class="btn btn-danger one-click-ajax" data-one-click-spinner="true" data-one-click-items=".votar-foto-{$value.id} .btn" data-one-click-container=".votar-foto-{$value.id}" href="{#SITE_URL#}/foto/votar/{$value.id}/-1" title="{@Votar negativamente@}"><i class="icon-white icon-thumbs-down"></i></a>
							</span>
							{/if}
							{if="$value.denunciar"}<span class="btn-group"><a class="btn btn-warning denunciar-foto" href="{#SITE_URL#}/foto/denunciar/{$value.id}/" data-modal-id="{$value.id}" title="{@Denunciar@}"><i class="icon-white icon-exclamation-sign"></i></a></span>{/if}
						</span>
					</p>
					<p class="sumario clearfix">
						{if="$value.votos != 0"}<span title="{@Votos@}" class="{if="$value.votos > 0"}positivo{else}negativo{/if}"><i class="icon icon-thumbs-{if="$value.votos > 0"}up{else}down{/if}"></i> {function="abs($value.votos)"}</span>{/if}
						{if="$value.votos != 0 && ($value.favoritos != 0 || $value.comentarios != 0)"}<span> - </span>{/if}
						{if="$value.favoritos != 0"}<span title="{@Favoritos@}"><i class="icon icon-star"></i> {$value.favoritos}</span>{/if}
						{if="$value.favoritos != 0 && $value.comentarios != 0"}<span> - </span>{/if}
						{if="$value.comentarios != 0"}<span title="{@Comentarios@}"><i class="icon icon-comment"></i> {$value.comentarios}</span>{/if}
						{if="$value.comentarios != 0"}<span> - </span>{/if}
						<span class="categoria">{@Categoría@}: <a href="{#SITE_URL#}/foto/categoria/{$value.categoria.seo}">{$value.categoria.nombre}</a></span>
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