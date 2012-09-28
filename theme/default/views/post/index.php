<div class="row post">
	<div class="span2 usuario-perfil-lateral">
		<h3 class="title">Posteado por:</h3>
		<a href="/perfil/index/{$usuario.nick}" class="thumbnail">
			<img src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
			<h4 class="nick">{$usuario.nick}</h4>
		</a>
		{if="$me != NULL && $me != $usuario.id"}<div class="row-fluid follow">
			<a href="#" class="btn span12" style="min-height: 0;"><i class="icon icon-plus"></i> Seguir usuario</a>
		</div>{/if}
		<div class="well"><i class="icon icon-user"></i><span class="pull-right">{if="$usuario.seguidores > 1"}{$usuario.seguidores} {@seguidores@}{elseif="$usuario.seguidores == 1"}1 {@seguidor@}{else}{@sin@} {@seguidores@}{/if}</span></div>
		<div class="well"><i class="icon icon-plus"></i><span class="pull-right">{if="$usuario.puntos > 1"}{$usuario.puntos} {@puntos@}{elseif="$usuario.puntos == 1"}1 {@puntos@}{else}{@sin@} {@puntos@}{/if}</span></div>
		<div class="well"><i class="icon icon-book"></i><span class="pull-right">{if="$usuario.posts > 1"}{$usuario.posts} {@posts@}{elseif="$usuario.posts == 1"}1 {@post@}{else}{@sin@} {@posts@}{/if}</span></div>
		<div class="well"><i class="icon icon-comment"></i><span class="pull-right">{if="$usuario.comentarios > 1"}{$usuario.comentarios} {@comentarios@}{elseif="$usuario.comentarios == 1"}1 {@comentario@}{else}{@sin@} {@comentarios@}{/if}</span></div>
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
					{if="$me != NULL && $me != $usuario.id"}<a href="/post/denunciar/{$post.id}" class="btn btn-danger">Denunciar</a>{/if}
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
			<div class="span12 info">
				<ul class="tags">
					{loop="$etiquetas"}<li><a href="#">{$value}</a></li>{/loop}
				</ul>
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
								{if="$value.estado == 1"}<span class="label label-important">OCULTO</span>{elseif="$value.estado == 2"}<span class="label label-info">EN REVISION</span>{/if}
							</span>
							<div class="btn-group pull-right acciones">
								{if="$me != NULL && !$value.vote"}
								<a href="/post/voto_comentario/{$value.id}/1" class="btn btn-mini btn-success"><i class="icon-white icon-thumbs-up"></i></a>
								<a href="/post/voto_comentario/{$value.id}/-1" class="btn btn-mini btn-danger"><i class="icon-white icon-thumbs-down"></i></a>
								{/if}
								{if="$me != NULL"}<a href="#" class="btn-quote-comment btn-mini btn" data-user="{$value.usuario.nick}"><i class="icon icon-comment"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini"><i class="icon icon-pencil"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id && $value.estado != 1"}<a href="/post/ocultar_post/{$value.id}" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i></a>{/if}
							</div>
						</div>
						<div class="comentario-body">{$value.contenido}</div>
					</div>
				</div>
				{/loop}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				{if="$me != NULL"}
				<form action="/post/comentar/{$post.id}" method="POST">
					<div class="btn-toolbar bbcode-bar">
						<div class="btn-group">
							<a href="#" title="Negrita" class="btn-bold btn btn-small"><i class="icon-bold"></i></a>
							<a href="#" title="Cursiva" class="btn-italic btn btn-small"><i class="icon-italic"></i></a>
							<a href="#" title="Subrayado" class="btn-underline btn btn-small"><u><b>U</b></u><!--<i class="icon-underline"></i>--></a>
							<a href="#" title="Tachado" class="btn-strike btn btn-small"><s><b>S</b></s><!--<i class="icon-strike"></i>--></a>
						</div>
						<div class="btn-group hidden-phone">
							<a href="#" class="btn btn-small btn-align-left" title="Alinear a la izquierda"><i class="icon-align-left"></i></a>
							<a href="#" class="btn btn-small btn-align-center" title="Centrar"><i class="icon-align-center"></i></a>
							<a href="#" class="btn btn-small btn-align-right" title="Alinear a la derecha"><i class="icon-align-right"></i></a>
							<a href="#" class="btn btn-small btn-align-justify" title="Justificar"><i class="icon-align-justify"></i></a>
						</div>
						<div class="btn-group visible-phone">
							<a href="#" class="btn btn-small dropdown-toggle" title="Encabezado" data-toggle="dropdown"><i class="icon-align-center"></i> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="#" class="btn-align-left" title="Alinear a la izquierda"><i class="icon-align-left"></i> Izquierda</a></li>
								<li><a href="#" class="btn-align-center" title="Centrar"><i class="icon-align-center"></i> Centrado</a></li>
								<li><a href="#" class="btn-align-right" title="Alinear a la derecha"><i class="icon-align-right"></i> Derecha</a></li>
								<li><a href="#" class="btn-align-justify" title="Justificar"><i class="icon-align-justify"></i> Justificado</a></li>
							</ul>
						</div>
						<div class="btn-group">
							<a href="#" class="btn btn-small dropdown-toggle" title="Encabezado" data-toggle="dropdown"><i class="icon-text-height"></i> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a class="btn-h1" href="#">H1</a></li>
								<li><a class="btn-h2" href="#">H2</a></li>
								<li><a class="btn-h3" href="#">H3</a></li>
								<li><a class="btn-h4" href="#">H4</a></li>
								<li><a class="btn-h5" href="#">H5</a></li>
								<li><a class="btn-h6" href="#">H6</a></li>
							</ul>
						</div>
						<div class="btn-group">
							<a href="#" class="btn btn-small dropdown-toggle" title="Lista" data-toggle="dropdown"><i class="icon-list"></i> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a class="btn-list-sorted" href="#">Ordenada</a></li>
								<li><a class="btn-list-unsorted" href="#">Desordenada</a></li>
								<li><a class="btn-list-item" href="#">Elemento</a></li>
							</ul>
						</div>
						<div class="btn-group">
							<a href="#" title="Imagen" class="btn-picture btn btn-small"><i class="icon-picture"></i></a>
							<a href="#" title="Link" class="btn-link btn btn-small"><i class="icon-retweet"></i></a>
						</div>
						<div class="btn-group">
							<a href="#" title="Spoiler" class="btn-spoiler btn btn-small"><i class="icon-calendar"></i></a>
							<a href="#" title="Cita" class="btn-quote btn btn-small"><i class="icon-comment"></i></a>
							<a href="#" title="Código" class="btn-code btn btn-small"><i class="icon-list-alt"></i></a>
						</div>
						<div class="btn-group pull-right">
							<button type="submit" title="Comentar" class="btn btn-small btn-primary"><i class="icon-ok icon-white"></i></button>
							<a href="#" title="Vista preliminar" class="btn-preview btn btn-small btn-success"><i class="icon-eye-open icon-white"></i></a>
						</div>
					</div>
					{if="isset($comentario_success)"}
					<div class="alert alert-success">
						<strong>&iexcl;Felicidades!</strong> {$comentario_success}
					</div>{/if}
					{if="isset($comentario_error)"}
					<div class="alert">
						<strong>&iexcl;Error!</strong> {$comentario_error}
					</div>{/if}
					<textarea name="comentario" id="comentario" class="span12" placeholder="Comentario...">{if="isset($comentario_content)"}{$comentario_content}{/if}</textarea>
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