<div class="row post">
	<div class="span2 usuario-perfil-lateral">
		<h3 class="title">Posteado por:</h3>
			<a href="/perfil/index/{$usuario.nick}" class="thumbnail user-icon">
			<img src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
			<h4 class="nick">{$usuario.nick}</h4>
		</a>
		{if="$me !== NULL && $me !== $usuario.id"}
		<div class="row-fluid follow">
			{if="!$sigue_usuario"}
			<a href="/post/seguir_usuario/{$post.id}/{$usuario.id}/1" class="btn span12" style="min-height: 0;"><i class="icon icon-plus"></i> Seguir usuario</a>
			{else}
			<a href="/post/seguir_usuario/{$post.id}/{$usuario.id}/0" class="btn span12" style="min-height: 0;"><i class="icon icon-minus"></i> Dejar de seguir</a>
			{/if}
		</div>
		{/if}
		<div class="well"><i class="icon icon-user"></i><span class="pull-right">{if="$usuario.seguidores > 1"}{$usuario.seguidores} {@seguidores@}{elseif="$usuario.seguidores == 1"}1 {@seguidor@}{else}{@sin@} {@seguidores@}{/if}</span></div>
		<div class="well"><i class="icon icon-plus"></i><span class="pull-right">{if="$usuario.puntos > 1"}{$usuario.puntos} {@puntos@}{elseif="$usuario.puntos == 1"}1 {@puntos@}{else}{@sin@} {@puntos@}{/if}</span></div>
		<div class="well"><i class="icon icon-book"></i><span class="pull-right">{if="$usuario.posts > 1"}{$usuario.posts} {@posts@}{elseif="$usuario.posts == 1"}1 {@post@}{else}{@sin@} {@posts@}{/if}</span></div>
		<div class="well"><i class="icon icon-comment"></i><span class="pull-right">{if="$usuario.comentarios > 1"}{$usuario.comentarios} {@comentarios@}{elseif="$usuario.comentarios == 1"}1 {@comentario@}{else}{@sin@} {@comentarios@}{/if}</span></div>
	</div>
	<div class="span10 contenido">
		<div class="cabecera">
			<div class="lineal btn-group">
				<a href="#" class="btn btn-mini"><i class="icon icon-chevron-left"></i></a>
				<a href="#" class="btn btn-mini"><i class="icon icon-chevron-right"></i></a>
			</div>
			<h2 class="title">{$post.titulo}</h2>
			<div class="aleatorio">
				<a href="#" class="btn btn-mini pull-right"><i class="icon icon-random"></i></a>
			</div>
		</div>
		<div class="contenido-post">{$post.contenido}</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="pull-left btn-group">
					{if="$modificar_especiales"}
						{if="$post.sticky"}<a href="/post/fijar_post/{$post.id}/-1" class="btn btn-info">Quitar fijo</a>{else}<a href="/post/fijar_post/{$post.id}/1" class="btn btn-info">Fijar</a>{/if}
						{if="$post.sponsored"}<a href="/post/patrocinar_post/{$post.id}/-1" class="btn btn-info">Quitar patrocinio</a>{else}<a href="/post/patrocinar_post/{$post.id}/1" class="btn btn-info">Patrocinar</a>{/if}
					{/if}
					{if="$modificar_editar"}<a href="/post/editar/{$post.id}" class="btn btn-success" rel="tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
					{if="$post.estado == 0"}
						{if="$modificar_ocultar"}<a href="/post/ocultar_post/{$post.id}/-1" class="btn btn-inverse" rel="tooltip" title="Ocultar"><i class="icon-white icon-eye-close"></i></a>{/if}
						{if="$modificar_aprobar"}<a href="/post/aprobar_post/{$post.id}/-1" class="btn btn-warning" rel="tooltip" title="Rechazar"><i class="icon-white icon-hand-down"></i></a>{/if}
						{if="$modificar_borrar"}<a href="/post/borrar_post/{$post.id}/" class="btn btn-danger" rel="tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>
						<a href="/post/borrar_post/{$post.id}/-1" class="btn btn-danger" rel="tooltip" title="Enviar a la papelera"><i class="icon-white icon-trash"></i></a>{/if}
					{/if}
					{if="$post.estado == 1"}
						{if="$me == $usuario.id"}<a href="/post/publicar_post/{$post.id}/" class="btn btn-success" rel="tooltip" title="Publicar"><i class="icon-white icon-ok"></i></a>{/if}
						{if="$modificar_borrar"}<a href="/post/borrar_post/{$post.id}/" class="btn btn-danger" rel="tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
					{/if}
					{if="$post.estado == 3"}
						{if="$modificar_aprobar"}<a href="/post/aprobar_post/{$post.id}/1" class="btn btn-success" rel="tooltip" title="Aprobar"><i class="icon-white icon-hand-up"></i></a>
						<a href="/post/aprobar_post/{$post.id}/-1" class="btn btn-warning"><i class="icon-white icon-hand-down" rel="tooltip" title="Rechazar"></i> </a>{/if}
						{if="$modificar_borrar"}<a href="/post/borrar_post/{$post.id}/" class="btn btn-danger" rel="tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
					{/if}
					{if="$post.estado == 4"}
						{if="$modificar_ocultar"}<a href="/post/ocultar_post/{$post.id}/1" class="btn btn-success" rel="tooltip" title="Mostrar"><i class="icon-white icon-eye-open"></i></a>{/if}
						{if="$modificar_borrar"}<a href="/post/borrar_post/{$post.id}/" class="btn btn-danger" rel="tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
					{/if}
					{if="$post.estado == 5"}
						{if="$modificar_aprobar"}<a href="/post/aprobar_post/{$post.id}/1" class="btn btn-success" rel="tooltip" title="Aprobar"><i class="icon-white icon-hand-up"></i></a>{/if}
						{if="$modificar_borrar"}<a href="/post/borrar_post/{$post.id}/" class="btn btn-danger" rel="tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
					{/if}
					{if="$post.estado == 6"}
						{if="$modificar_borrar"}<a href="/post/restaurar_post/{$post.id}/" class="btn btn-success" rel="tooltip" title="Restaurar"><i class="icon-white icon-refresh"></i></a>
						<a href="/post/borrar_post/{$post.id}/" class="btn btn-danger" rel="tooltip" title="Borrar"></i><i class="icon-white icon-remove"></i></a>{/if}
					{/if}
					{if="$me != NULL && !$sigo_post"}<a href="/post/seguir_post/{$post.id}" class="btn" rel="tooltip" title="Seguir Post">Seguir Post</a>{/if}
					{if="$me != NULL && !$es_favorito"}<a href="/post/favorito/{$post.id}" class="btn" rel="tooltip" title="Agregar a favoritos"><i class="icon icon-star"></i></a>{/if}
					{if="$me != NULL && $me != $usuario.id && $post.estado == 0"}<a href="/post/denunciar/{$post.id}" class="btn btn-danger">Denunciar</a>{/if}
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
				<div class="row-fluid comentario" id="c-{$value.id}">
					<div class="span1">
						<img class="thumbnail" src="{function="Utils::get_gravatar($value.usuario.email, 48, 48)"}" />
					</div>
					<div class="span11 comentario-data">
						<div class="clearfix head">
							<span class="informacion">
								<a href="/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a>
								<small>{function="$value.fecha->fuzzy()"}</small>
								{if="$value.votos != 0"}<span class="badge badge-{if="$value.votos > 0"}success{else}important{/if}">{$value.votos|abs}</span>{/if}
								{if="$value.estado == 1"}<span class="label label-warning">OCULTO</span>{elseif="$value.estado == 2"}<span class="label label-important">BORRADO</span>{/if}
							</span>
							{if="$me != NULL"}
							<div class="btn-toolbar pull-right acciones">
								{if="!$value.vote && $podemos_votar_comentarios"}
								<div class="btn-group">
									<a href="/post/voto_comentario/{$value.id}/1" class="btn btn-mini btn-success"><i class="icon-white icon-thumbs-up"></i></a>
									<a href="/post/voto_comentario/{$value.id}/-1" class="btn btn-mini btn-danger"><i class="icon-white icon-thumbs-down"></i></a>
								</div>
								{/if}
								<div class="btn-group">
									<a href="#" class="btn-quote-comment btn-mini btn" data-user="{$value.usuario.nick}" data-comment="p{$value.id}"><i class="icon icon-comment"></i></a>
									{if="($me == $value.usuario.id || $comentario_editar) && $value.estado != 2"}<a href="/post/editar_comentario/{$value.id}" class="btn btn-mini btn-primary" rel="tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
									{if="($me == $value.usuario.id || $comentario_ocultar) && $value.estado == 0"}<a href="/post/ocultar_comentario/{$value.id}/0" class="btn btn-mini btn-inverse" rel="tooltip" title="Ocultar"><i class="icon-white icon-eye-close"></i></a>{/if}
									{if="$value.estado == 1 && $comentario_ocultar"}<a href="/post/ocultar_comentario/{$value.id}/1" class="btn btn-mini btn-info" rel="tooltip" title="Mostrar"><i class="icon-white icon-eye-open"></i></a>{/if}
									{if="$comentario_eliminar && $value.estado != 2"}<a href="/post/eliminar_comentario/{$value.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
								</div>
							</div>
							{/if}
						</div>
						<div class="comentario-body">{$value.contenido}</div>
					</div>
				</div>
				{/loop}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				{if="$me != NULL && $podemos_comentar"}
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
					<textarea name="comentario" data-preview="/post/preview/" id="comentario" class="span12" placeholder="Comentario...">{if="isset($comentario_content)"}{$comentario_content}{/if}</textarea>
				</form>
				{else}
					{if="$podemos_comentar"}
				<div class="alert">
					<strong>&iexcl;Atenci&oacute;n!</strong> Solo usuarios registrados pueden comentar este post.
				</div>
					{else}
				<div class="alert">
					<strong>&iexcl;Atenci&oacute;n!</strong> Los comentarios se encuentran cerrados.
				</div>
					{/if}
				{/if}
			</div>
		</div>
	</div>
</div>