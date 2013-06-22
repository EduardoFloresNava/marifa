<div class="row post">
	<div class="span2 usuario-perfil-lateral">
		<h3 class="title">Posteado por:</h3>
			<a href="{#SITE_URL#}/@{$usuario.nick}" class="thumbnail user-icon">
			<img src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
			<h4 class="nick">{$usuario.nick}</h4>
		</a>
		{if="$me !== NULL && $me !== $usuario.id"}
		<div class="row-fluid follow">
			{if="!$sigue_usuario"}
			<a href="{#SITE_URL#}/post/seguir_usuario/{$post.id}/{$usuario.id}/1" class="btn span12 one-click-ajax" data-one-click-spinner="true"><i class="icon icon-plus"></i> Seguir usuario</a>
			{else}
			<a href="{#SITE_URL#}/post/seguir_usuario/{$post.id}/{$usuario.id}/0" class="btn span12 one-click-ajax" data-one-click-spinner="true"><i class="icon icon-minus"></i> Dejar de seguir</a>
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
				<a href="{#SITE_URL#}/post/{$post_anterior.categoria.seo}/{$post_anterior.id}/{$post_anterior.titulo|Texto::make_seo}.html" class="btn btn-mini"><i class="icon icon-chevron-left"></i></a>
				<a href="{#SITE_URL#}/post/{$post_siguiente.categoria.seo}/{$post_siguiente.id}/{$post_siguiente.titulo|Texto::make_seo}.html" class="btn btn-mini"><i class="icon icon-chevron-right"></i></a>
			</div>
			<h2 class="title">{$post.titulo}</h2>
			<div class="aleatorio">
				<a href="{#SITE_URL#}/post/{$post_aleatorio.categoria.seo}/{$post_aleatorio.id}/{$post_aleatorio.titulo|Texto::make_seo}.html" class="btn btn-mini pull-right"><i class="icon icon-random"></i></a>
			</div>
		</div>
		<div class="contenido-post">{$post.contenido}</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="pull-left btn-group">
					{if="$modificar_editar"}<a href="{#SITE_URL#}/post/editar/{$post.id}" class="btn btn-success" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
					{if="$me != NULL && !$sigo_post"}<a href="{#SITE_URL#}/post/seguir_post/{$post.id}" class="btn one-click-ajax" title="Seguir Post">Seguir Post</a>{/if}
					{if="$me != NULL && !$es_favorito"}<a href="{#SITE_URL#}/post/favorito/{$post.id}" class="btn" title="Agregar a favoritos"><i class="icon icon-star"></i></a>{/if}
					{if="$me != NULL && $me != $usuario.id && $post.estado == 0"}<a href="{#SITE_URL#}/post/denunciar/{$post.id}" id="post-denunciar" class="btn btn-danger">Denunciar</a>{/if}
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#" title="Moderar"><i class="icon icon-cog"></i></a>
					<ul class="dropdown-menu" id="post-options-menu">
						{if="$modificar_especiales"}
						<li>
							{if="$post.sticky"}
							<a href="{#SITE_URL#}/post/fijar_post/{$post.id}/-1" class="one-click-ajax" data-one-click-spinner="true">Desfijar</a>
							{else}
							<a href="{#SITE_URL#}/post/fijar_post/{$post.id}/1" class="one-click-ajax" data-one-click-spinner="true">Fijar</a>
							{/if}
						</li>
						<li>
							{if="$post.sponsored"}
							<a href="{#SITE_URL#}/post/patrocinar_post/{$post.id}/-1" class="one-click-ajax" data-one-click-spinner="true"><i class="icon icon-bookmark-empty"></i> Quitar patrocinio</a>
							{else}
							<a href="{#SITE_URL#}/post/patrocinar_post/{$post.id}/1" class="one-click-ajax" data-one-click-spinner="true"><i class="icon icon-bookmark-empty"></i> Patrocinar</a>
							{/if}
						</li>
						{/if}
						{if="$post.estado == 0"}
							{if="$modificar_ocultar"}
							<li><a href="{#SITE_URL#}/post/ocultar_post/{$post.id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-eye-close"></i> Ocultar</a></li>
							{/if}
							{if="$modificar_aprobar"}
							<li><a href="{#SITE_URL#}/post/aprobar_post/{$post.id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-down"></i> Rechazar</a></li>
							{/if}
							{if="$modificar_borrar"}
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-trash"></i> Enviar a la papelera</a></li>
							{/if}
						{/if}
						{if="$post.estado == 1"}
							{if="$me == $usuario.id"}
							<li><a href="{#SITE_URL#}/post/publicar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-ok"></i> Publicar</a></li>
							{/if}
							{if="$modificar_borrar"}
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
							{/if}
						{/if}
						{if="$post.estado == 3"}
							{if="$modificar_aprobar"}
							<li><a href="{#SITE_URL#}/post/aprobar_post/{$post.id}/1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-up"></i> Aprobar</a></li>
							<li><a href="{#SITE_URL#}/post/aprobar_post/{$post.id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-down"></i> Rechazar</a></li>
							{/if}
							{if="$modificar_borrar"}
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
							{/if}
						{/if}
						{if="$post.estado == 4"}
							{if="$modificar_ocultar"}
							<li><a href="{#SITE_URL#}/post/ocultar_post/{$post.id}/1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-eye-open"></i> Mostrar</a></li>
							{/if}
							{if="$modificar_borrar"}
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
							{/if}
						{/if}
						{if="$post.estado == 5"}
							{if="$modificar_aprobar"}
							<li><a href="{#SITE_URL#}/post/aprobar_post/{$post.id}/1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-up"></i> Aprobar</a></li>
							{/if}
							{if="$modificar_borrar"}
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
							{/if}
						{/if}
						{if="$post.estado == 6"}
							{if="$modificar_borrar"}
							<li><a href="{#SITE_URL#}/post/restaurar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon-white icon-refresh"></i> Restaurar</a></li>
							<li><a href="{#SITE_URL#}/post/borrar_post/{$post.id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"></i><i class="icon-white icon-remove"></i> Borrar</a></li>
							{/if}
						{/if}
					</ul>
				</div>
				<div class="pull-right btn-group">
					<span class="btn" title="Seguidores"><i class="icon icon-user"></i> {$post.seguidores}</span>
					<span class="btn" title="Puntos"><i class="icon icon-asterisk"></i> {$post.puntos}</span>
					<span class="btn" title="Vistas"><i class="icon icon-eye-open"></i> {$post.vistas}</span>
					<span class="btn" title="Favoritos"><i class="icon icon-star"></i> {$post.favoritos}</span>
				</div>
				{if="$me != NULL && is_array($puntuacion)"}
				<div class="pull-right btn-group" id="post-puntuar-container">
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Puntuar <span class="caret"></span></a>
					<ul class="dropdown-menu">
						{loop="$puntuacion"}<li><a href="{#SITE_URL#}/post/puntuar/{$post.id}/{$value}" class="one-click-ajax" data-one-click-container="#post-puntuar-container">+{$value}</a></li>{/loop}
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
					<i class="icon icon-time"></i> {function="$post.fecha->fuzzy()"} en {$categoria.nombre}
				</div>
			</div>
		</div>
		<div class="row-fluid comentarios">
			<div class="span12">
				{loop="$comentarios"}
				<div class="row-fluid comentario" id="c-{$value.id}">
					<div class="span1 hidden-phone">
						<img class="thumbnail" src="{function="Utils::get_gravatar($value.usuario.email, 48, 48)"}" />
					</div>
					<div class="span11 comentario-data">
						<div class="clearfix head">
							<span class="informacion">
								<a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a>
								<small>{function="$value.fecha->fuzzy()"}</small>
								{if="$value.votos != 0"}<span class="badge badge-{if="$value.votos > 0"}success{else}important{/if}">{$value.votos|abs}</span>{/if}
								{if="$value.estado == 1"}<span class="label label-warning">OCULTO</span>{elseif="$value.estado == 2"}<span class="label label-important">BORRADO</span>{/if}
							</span>
							{if="$me != NULL"}
							<div class="btn-toolbar pull-right acciones">
								{if="!$value.vote && $podemos_votar_comentarios"}
								<div class="btn-group">
									<a href="{#SITE_URL#}/post/voto_comentario/{$value.id}/1" class="btn btn-mini btn-success one-click-ajax" data-one-click-spinner="true" ><i class="icon-white icon-thumbs-up"></i></a>
									<a href="{#SITE_URL#}/post/voto_comentario/{$value.id}/-1" class="btn btn-mini btn-danger one-click-ajax" data-one-click-spinner="true" ><i class="icon-white icon-thumbs-down"></i></a>
								</div>
								{/if}
								<div class="btn-group">
									<a href="#" class="btn-quote-comment btn-mini btn" title="Citar comentario" data-user="{$value.usuario.nick}" data-comment="p{$value.id}"><i class="icon icon-comment"></i></a>
									{if="($me == $value.usuario.id || $comentario_editar) && $value.estado != 2"}<a href="{#SITE_URL#}/post/editar_comentario/{$value.id}" class="btn btn-mini btn-primary" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
									{if="($me == $value.usuario.id || $comentario_ocultar) && $value.estado == 0"}<a href="{#SITE_URL#}/post/ocultar_comentario/{$value.id}/0" class="btn btn-mini btn-inverse one-click-ajax" data-one-click-spinner="true"  title="Ocultar"><i class="icon-white icon-eye-close"></i></a>{/if}
									{if="$value.estado == 1 && $comentario_ocultar"}<a href="{#SITE_URL#}/post/ocultar_comentario/{$value.id}/1" class="btn btn-mini btn-info one-click-ajax" data-one-click-spinner="true"  title="Mostrar"><i class="icon-white icon-eye-open"></i></a>{/if}
									{if="$comentario_eliminar && $value.estado != 2"}<a href="{#SITE_URL#}/post/eliminar_comentario/{$value.id}" class="btn btn-mini btn-danger" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
								</div>
							</div>
							{/if}
						</div>
						<div class="comentario-body">{$value.contenido}</div>
					</div>
				</div>
				{/loop}
				{$paginacion}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				{if="$me != NULL && $podemos_comentar"}
				<form action="{#SITE_URL#}/post/comentar/{$post.id}" method="POST" class="comentar">
					{include="helper/bbcode_bar"}
					{if="isset($comentario_success)"}
					<div class="alert alert-success">
						<strong>!Felicidades!</strong> {$comentario_success}
					</div>{/if}
					{if="isset($comentario_error)"}
					<div class="alert">
						<strong>!Error!</strong> {$comentario_error}
					</div>{/if}
					<textarea name="comentario" data-preview="{#SITE_URL#}/post/preview/" id="comentario" class="span12" placeholder="Comentario...">{if="isset($comentario_content)"}{$comentario_content}{/if}</textarea>
				</form>
				{else}
					{if="$podemos_comentar"}
				<div class="alert">
					<strong>!Atención!</strong> Solo usuarios registrados pueden comentar este post.
				</div>
					{else}
				<div class="alert">
					<strong>!Atención!</strong> Los comentarios se encuentran cerrados.
				</div>
					{/if}
				{/if}
			</div>
		</div>
	</div>
</div>