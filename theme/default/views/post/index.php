<div class="row post">
	<div class="span2 usuario">
		<h3 class="title">Posteado por:</h3>
		<img class="thumbnail" src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
		<h4 class="nick">{$usuario.nick}</h4>
		{if="$me != NULL && $me != $usuario.id"}<div class="row-fluid">
			<a href="#" class="btn span12" style="min-height: 0;">Seguir usuario</a>
		</div>{/if}
		<p><strong>Seguidores:</strong> {$usuario.seguidores}</p>
		<p><strong>Puntos:</strong> {$usuario.puntos}</p>
		<p><strong>Posts:</strong> {$usuario.posts}</p>
		<p><strong>Comentarios:</strong> {$usuario.comentarios}</p>
	</div>
	<div class="span10 contenido">
		<div class="row-fluid cabecera">
			<div class="span1 lineal btn-group">
				<a href="#" class="btn btn-mini"><i class="icon icon-chevron-left"></i></a>
				<a href="#" class="btn btn-mini"><i class="icon icon-chevron-right"></i></a>
			</div>
			<div class="span10">
				<h2 class="title">{$post.titulo}</h2>
			</div>
			<div class="span1 aleatorio">
				<a href="#" class="btn btn-mini pull-right"><i class="icon icon-random"></i></a>
			</div>
		</div>
		<pre>{$post.contenido}</pre>
		<div class="row-fluid">
			<div class="span12">
				<div class="pull-left btn-group">
					{if="$me != NULL && !$sigo_post"}<a href="/post/seguir_post/{$post.id}" class="btn">Seguir Post</a>{/if}
					{if="$me != NULL && !$es_favorito"}<a href="/post/favorito/{$post.id}" class="btn">Agregar a favoritos</a>{/if}
					{if="$me != NULL && $me != $usuario.id"}<a href="#" class="btn btn-danger">Denunciar</a>{/if}
				</div>
				<div class="pull-right btn-group">
					<span class="btn">{$post.seguidores} Seguidores</span>
					<span class="btn">{$post.puntos} Puntos</span>
					<span class="btn">{$post.vistas} Visitas</span>
					<span class="btn">{$post.favoritos} Favoritos</span>
				</div>
				{if="$me != NULL && is_array($puntuacion)"}
				<div class="pull-right btn-group">
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Puntuar <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						{loop="$puntuacion"}<li><a href="/post/puntuar/{$post.id}/{$value}">+{$value}</a></li>{/loop}
					</ul>
				</div>
				{/if}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="pull-left">
					<i class="icon icon-tag"></i> Etiquetas:
					<ul>
						{loop="$etiquetas"}<li>{$value}</li>{/loop}
					</ul>
				</div>
				<div class="pull-right">
					Creado: {function="$post.fecha->fuzzy()"}
				</div>
				<div class="pull-right">
					Categoria: {$categoria.nombre}
				</div>
			</div>
		</div>
		<div class="row-fluid comentarios">
			<div class="span12">
				{loop="$comentarios"}
				<div class="row-fluid comentario">
					<div class="span1">
						<img class="thumbnail" src="{function="Utils::get_gravatar($value.usuario.email, 48, 48)"}" />
					</div>
					<div class="span11">
						<div class="clearfix head">
							<span class="informacion">
								<a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a>
								<small>{function="$value.fecha->fuzzy()"}</small>
								{if="$value.votos != 0"}<span class="badge badge-{if="$value.votos > 0"}success{else}important{/if}">{$value.votos|abs}</span>{/if}
							</span>
							<div class="btn-group pull-right acciones">
								{if="$me != NULL && !$value.vote"}
								<a href="/post/voto_comentario/{$value.id}/1" class="btn btn-mini btn-success"><i class="icon-white icon-thumbs-up"></i></a>
								<a href="/post/voto_comentario/{$value.id}/-1" class="btn btn-mini btn-danger"><i class="icon-white icon-thumbs-down"></i></a>
								{/if}
								{if="$me != NULL"}<a href="#" class="btn btn-mini btn"><i class="icon icon-comment"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini"><i class="icon icon-pencil"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i></a>{/if}
							</div>
						</div>
						<pre>{$value.contenido}</pre>
					</div>
				</div>
				{/loop}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				{if="$me != NULL"}
				<form action="/post/comentar/{$post.id}" method="POST">
					{if="isset($comentario_success)"}
					<div class="alert alert-success">
						<strong>&iexcl;Felicidades!</strong> {$comentario_success}
					</div>{/if}
					{if="isset($comentario_error)"}
					<div class="alert">
						<strong>&iexcl;Error!</strong> {$comentario_error}
					</div>{/if}
					<textarea name="comentario" id="comentario" class="span12">{if="isset($comentario_content)"}{$comentario_content}{/if}</textarea>
					<button class="btn btn-primary" type="submit">Comentar</button>
				</form>
				{else}
				<div class="alert">
					<strong>&iexcl;Atenci&oacute;n!</strong> Solo usuarios registrados pueden comentar este post.
				</div>
				{/if}
			</div>
		</div>
	</div>
</div>