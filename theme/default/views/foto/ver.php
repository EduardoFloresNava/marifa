<div class="row">
	<div class="span2 usuario-perfil-lateral">
		<h3 class="title">{@Autor@}:</h3>
			<a href="{#SITE_URL#}/@{$usuario.nick}" class="thumbnail user-icon">
			<img src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
			<h4 class="nick">{$usuario.nick}</h4>
		</a>
		{if="$me !== NULL && $me !== $usuario.id"}
		<div class="row-fluid follow">
			{if="!$sigue_usuario"}
			<a href="{#SITE_URL#}/foto/seguir_usuario/{$foto.id}/{$usuario.id}/1" class="btn span12" style="min-height: 0;"><i class="icon icon-plus"></i> Seguir usuario</a>
			{else}
			<a href="{#SITE_URL#}/foto/seguir_usuario/{$foto.id}/{$usuario.id}/0" class="btn span12" style="min-height: 0;"><i class="icon icon-minus"></i> Dejar de seguir</a>
			{/if}
		</div>
		{/if}
		<div class="well"><i class="icon icon-user"></i><span class="pull-right">{if="$usuario.seguidores > 1"}{$usuario.seguidores} {@seguidores@}{elseif="$usuario.seguidores == 1"}1 {@seguidor@}{else}{@sin@} {@seguidores@}{/if}</span></div>
		<div class="well"><i class="icon icon-plus"></i><span class="pull-right">{if="$usuario.puntos > 1"}{$usuario.puntos} {@puntos@}{elseif="$usuario.puntos == 1"}1 {@puntos@}{else}{@sin@} {@puntos@}{/if}</span></div>
		<div class="well"><i class="icon icon-book"></i><span class="pull-right">{if="$usuario.posts > 1"}{$usuario.posts} {@posts@}{elseif="$usuario.posts == 1"}1 {@post@}{else}{@sin@} {@posts@}{/if}</span></div>
		<div class="well"><i class="icon icon-comment"></i><span class="pull-right">{if="$usuario.comentarios > 1"}{$usuario.comentarios} {@comentarios@}{elseif="$usuario.comentarios == 1"}1 {@comentario@}{else}{@sin@} {@comentarios@}{/if}</span></div>
	</div>
	<div class="span10">
		<!--
		<div class="row-fluid">
			LISTADO DE FOTOS DEL USUARIO.
		</div>-->
		<div>
			<h3 class="title">{$foto.titulo}<small>{$foto.creacion->fuzzy()}</small></h3>
			<div class="thumbnail" style="margin: 0 auto; min-height: 200px">
				<img alt="{$foto.descripcion_clean|Texto::limit_chars:30,TRUE,'...'}" src="{$foto.url}" />
			</div>
			<div class="contenido-foto">{$foto.descripcion}</div>
		</div>
		<div class="btn-toolbar">
			{if="$foto.estado == 0 && ($permiso_ocultar || $permiso_papelera || $permiso_borrar || $permiso_editar)"}
			<div class="btn-group">
				{if="$permiso_editar"}<a href="{#SITE_URL#}/foto/editar/{$foto.id}/" class="btn btn-primary show-tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
				{if="$permiso_ocultar"}<a href="{#SITE_URL#}/foto/ocultar_foto/{$foto.id}/" class="btn btn-inverse show-tooltip" title="Ocultar"><i class="icon-white icon-eye-close"></i></a>{/if}
				{if="$permiso_papelera"}<a href="{#SITE_URL#}/foto/borrar_foto/{$foto.id}/1" class="btn btn-warning show-tooltip" title="Enviar a la papelera"><i class="icon-white icon-trash"></i></a>{/if}
				{if="$permiso_borrar"}<a href="{#SITE_URL#}/foto/borrar_foto/{$foto.id}/" class="btn btn-danger show-tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
			</div>
			{elseif="$foto.estado == 1 && ($permiso_ocultar || $permiso_borrar || $permiso_editar)"}
			<div class="btn-group">
				{if="$permiso_editar"}<a href="{#SITE_URL#}/foto/editar/{$foto.id}/" class="btn btn-primary show-tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
				{if="$permiso_ocultar"}<a href="{#SITE_URL#}/foto/ocultar_foto/{$foto.id}/" class="btn btn-success show-tooltip" title="Mostrar"><i class="icon-white icon-eye-open"></i></a>{/if}
				{if="$permiso_borrar"}<a href="{#SITE_URL#}/foto/borrar_foto/{$foto.id}/" class="btn btn-danger show-tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
			</div>
			{elseif="$foto.estado == 2 && ($permiso_papelera || $permiso_borrar || $permiso_editar)"}
			<div class="btn-group">
				{if="$permiso_editar"}<a href="{#SITE_URL#}/foto/editar/{$foto.id}/" class="btn btn-primary show-tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
				{if="$permiso_papelera"}<a href="{#SITE_URL#}/foto/restaurar_foto/{$foto.id}/" class="btn btn-success show-tooltip" title="Restaurar"><i class="icon-white icon-refresh"></i></a>{/if}
				{if="$permiso_borrar"}<a href="{#SITE_URL#}/foto/borrar_foto/{$foto.id}/" class="btn btn-danger show-tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
			</div>
			{/if}
			<div class="btn-group">
				{if="!$ya_vote"}<a href="{#SITE_URL#}/foto/votar/{$foto.id}/1" class="btn btn-success"><i class="icon-white icon-thumbs-up"></i></a>{/if}
				<span class="btn{if="$foto.votos != 0"} {if="$foto.votos < 0"}btn-danger{else}btn-success{/if}{/if}">{@Votos@}: {$foto.votos}</span>
				{if="!$ya_vote"}<a href="{#SITE_URL#}/foto/votar/{$foto.id}/-1" class="btn btn-danger"><i class="icon-white icon-thumbs-down"></i></a>{/if}
			</div>
			<div class="btn-group">
				{if="!$es_favorito"}<a href="{#SITE_URL#}/foto/favorito/{$foto.id}" class="btn btn-success"><i class="icon-white icon-star"></i> {@Agregar a favoritos@}</a>{/if}
				<span class="btn">{@Favoritos@}: {$foto.favoritos}</span>
			</div>
			{if="$foto.visitas !== NULL"}<div class="btn-group">
				<span class="btn">{@Visitas@}: {$foto.visitas}</span>
				{if="$foto.visitas > 0"}<span class="btn">{@última visita@}: {$foto.ultima_visita->fuzzy()}</span>{/if}
			</div>{/if}
			{if="$me != NULL && $foto.usuario_id != $me"}
			<div class="btn-group pull-right">
				<a href="{#SITE_URL#}/foto/denunciar/{$foto.id}" class="btn btn-danger">{@Denunciar@}</a>
			</div>
			{/if}
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
								{if="$value.estado == 1"}<span class="label label-warning">OCULTO</span>{elseif="$value.estado == 2"}<span class="label label-important">BORRADO</span>{/if}
							</span>
							{if="$me != NULL"}
							<div class="btn-toolbar pull-right acciones">
								<div class="btn-group">
									<a href="#" class="btn-quote-comment btn-mini btn" data-user="{$value.usuario.nick}" data-comment="f{$value.id}"><i class="icon icon-comment"></i></a>
									{if="($me == $value.usuario.id || $comentario_editar) && $value.estado != 2"}<a href="{#SITE_URL#}/foto/editar_comentario/{$value.id}" class="btn btn-mini btn-primary show-tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>{/if}
									{if="($me == $value.usuario.id || $comentario_ocultar) && $value.estado == 0"}<a href="{#SITE_URL#}/foto/ocultar_comentario/{$value.id}/0" class="btn btn-mini btn-inverse show-tooltip" title="Ocultar"><i class="icon-white icon-eye-close"></i></a>{/if}
									{if="$value.estado == 1 && $comentario_ocultar"}<a href="{#SITE_URL#}/foto/ocultar_comentario/{$value.id}/1" class="btn btn-mini btn-info show-tooltip" title="Mostrar"><i class="icon-white icon-eye-open"></i></a>{/if}
									{if="$comentario_eliminar && $value.estado != 2"}<a href="{#SITE_URL#}/foto/eliminar_comentario/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar"><i class="icon-white icon-remove"></i></a>{/if}
								</div>
							</div>
							{/if}
						</div>
						<div class="comentario-body">{$value.comentario}</div>
					</div>
				</div>
				{else}
					{if="$puedo_comentar"}
				<div class="alert">
					{@No hay comentarios sobre esta foto hasta el momento.@}
				</div>
					{/if}
				{/loop}
			</div>
		</div>
		{if="$me != NULL && $puedo_comentar"}
		<form method="POST" action="{#SITE_URL#}/foto/comentar/{$foto.id}" class="comentar">
			{include="helper/bbcode_bar"}
			{if="isset($comentario_success)"}
			<div class="alert alert-success">
				<strong>{@!Felicidades!@}</strong> {$comentario_success}
			</div>{/if}
			{if="isset($comentario_error)"}
			<div class="alert alert-danger">
				<strong>{@!Error!@}</strong> {$comentario_error}
			</div>{/if}
			<textarea class="span10" name="comentario" data-preview="{#SITE_URL#}/foto/preview/" id="comentario" placeholder="Comentario...">{$comentario_content}</textarea>
		</form>
		{else}
			{if="$puedo_comentar"}
		<div class="alert">{@Para poder comentar debes estar registrado.@}</div>
			{else}
		<div class="alert alert-info">{@Los comentarios se encuentran cerrados.@}</div>
			{/if}
		{/if}
	</div>
</div>