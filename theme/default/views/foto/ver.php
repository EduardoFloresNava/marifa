<div class="row">
	<div class="span2">
		<h3>{@Autor@}:</h3>
		<img class="thumbnail" src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
		<h4>{$usuario.nick}</h4>
		{if="$me != NULL && $me != $usuario.id"}<div class="row-fluid">
			<a href="#" class="btn span12" style="min-height: 0;">{@Seguir usuario@}</a>
		</div>{/if}
		<p><strong>{@Seguidores@}:</strong> {$usuario.seguidores}</p>
		<p><strong>{@Puntos@}:</strong> {$usuario.puntos}</p>
		<p><strong>{@Posts@}:</strong> {$usuario.posts}</p>
		<p><strong>{@Comentarios@}:</strong> {$usuario.comentarios}</p>
	</div>
	<div class="span10">
		{if="isset($success)"}
		<div class="alert alert-success">
			<strong>{@&iexcl;Felicidades!@}</strong> {$success}
		</div>{/if}
		<!--
		<div class="row-fluid">
			LISTADO DE FOTOS DEL USUARIO.
		</div>-->
		<div>
			<h3>{$foto.titulo}<small class="pull-right">{$foto.creacion->fuzzy()}</small></h3>
			<div class="thumbnail" style="margin: 0 auto; min-height: 200px">
				<img src="{$foto.url}" />
			</div>
		</div>
		<div class="btn-toolbar">
			<div class="btn-group">
				{if="!$ya_vote"}<a href="/foto/votar/{$foto.id}/1" class="btn btn-success"><i class="icon-white icon-thumbs-up"></i></a>{/if}
				<span href="#" class="btn{if="$foto.votos != 0"} {if="$foto.votos < 0"}btn-danger{else}btn-success{/if}{/if}">{@Votos@}: {$foto.votos}</span>
				{if="!$ya_vote"}<a href="/foto/votar/{$foto.id}/-1" class="btn btn-danger"><i class="icon-white icon-thumbs-down"></i></a>{/if}
			</div>
			<div class="btn-group">
				{if="!$es_favorito"}<a href="/foto/favorito/{$foto.id}" class="btn btn-success"><i class="icon-white icon-star"></i> {@Agregar a favoritos@}</a>{/if}
				<span href="#" class="btn">{@Favoritos@}: {$foto.favoritos}</span>
			</div>
			<div class="btn-group">
				<span href="#" class="btn">{@Visitas@}: {$foto.visitas}</span>
				{if="$foto.visitas > 0"}<span href="#" class="btn">{@&Uacute;ltima visita@}: {$foto.ultima_visita->fuzzy()}</span>{/if}
			</div>
			{if="$me != NULL && $foto.usuario_id != $me"}
			<div class="btn-group pull-right">
				<a href="#" class="btn btn-danger">{@Denunciar@}</a>
			</div>
			{/if}
		</div>
		<div class="row-fluid">
			<div class="span12">
				{loop="$comentarios"}
				<div class="row-fluid">
					<div class="span1">
						<img class="thumbnail" src="{function="Utils::get_gravatar($value.usuario.email, 48, 48)"}" />
					</div>
					<div class="span11">
						<div class="clearfix">
							<span>
								<a href="#">{$value.usuario.nick}</a>
								<small>{$value.fecha->fuzzy()}</small>
							</span>
							<div class="btn-group pull-right">
								{if="$me != NULL"}<a href="#" class="btn btn-mini btn"><i class="icon icon-comment"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini"><i class="icon icon-pencil"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i></a>{/if}
							</div>
						</div>
						<pre>{$value.comentario}</pre>
					</div>
				</div>
				{else}
				<div class="alert">
					{@No hay comentarios sobre esta foto hasta el momento.@}
				</div>
				{/loop}
			</div>
		</div>
		{if="$me != NULL"}
		<form method="POST" action="/foto/comentar/{$foto.id}">
			{if="isset($comentario_success)"}
			<div class="alert alert-success">
				<strong>{@&iexcl;Felicidades!@}</strong> {$comentario_success}
			</div>{/if}
			{if="isset($comentario_error)"}
			<div class="alert">
				<strong>{@&iexcl;Error!@}</strong> {$comentario_error}
			</div>{/if}
			<textarea class="span10" name="comentario" value="comentario">{$comentario_content}</textarea>
			<button type="submit" class="btn btn-primary">{@Comentar@}</button>
		</form>
		{else}
		<div class="alert">{@Para poder comentar debes estar registrado.@}</div>
		{/if}
	</div>
</div>