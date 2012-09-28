<div class="row">
	<div class="span2 usuario-perfil-lateral">
		<h3 class="title">{@Autor@}:</h3>
				<a href="/perfil/index/{$usuario.nick}" class="thumbnail">
			<img src="{function="Utils::get_gravatar($usuario.email, 160, 160)"}" />
			<h4 class="nick">{$usuario.nick}</h4>
		</a>
		{if="$me != NULL && $me != $usuario.id"}<div class="row-fluid">
			<a href="#" class="btn span12" style="min-height: 0;">Seguir usuario</a>
		</div>{/if}
		<div class="well"><i class="icon icon-user"></i><span class="pull-right">{if="$usuario.seguidores > 1"}{$usuario.seguidores} {@seguidores@}{elseif="$usuario.seguidores == 1"}1 {@seguidor@}{else}{@sin@} {@seguidores@}{/if}</span></div>
		<div class="well"><i class="icon icon-plus"></i><span class="pull-right">{if="$usuario.puntos > 1"}{$usuario.puntos} {@puntos@}{elseif="$usuario.puntos == 1"}1 {@puntos@}{else}{@sin@} {@puntos@}{/if}</span></div>
		<div class="well"><i class="icon icon-book"></i><span class="pull-right">{if="$usuario.posts > 1"}{$usuario.posts} {@posts@}{elseif="$usuario.posts == 1"}1 {@post@}{else}{@sin@} {@posts@}{/if}</span></div>
		<div class="well"><i class="icon icon-comment"></i><span class="pull-right">{if="$usuario.comentarios > 1"}{$usuario.comentarios} {@comentarios@}{elseif="$usuario.comentarios == 1"}1 {@comentario@}{else}{@sin@} {@comentarios@}{/if}</span></div>
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
			<h3 class="title">{$foto.titulo}<small>{$foto.creacion->fuzzy()}</small></h3>
			<div class="thumbnail" style="margin: 0 auto; min-height: 200px">
				<img src="{$foto.url}" />
			</div>
			<div class="contenido-foto">{$foto.descripcion}</div>
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
			{if="$foto.visitas !== NULL"}<div class="btn-group">
				<span href="#" class="btn">{@Visitas@}: {$foto.visitas}</span>
				{if="$foto.visitas > 0"}<span href="#" class="btn">{@&Uacute;ltima visita@}: {$foto.ultima_visita->fuzzy()}</span>{/if}
			</div>{/if}
			{if="$me != NULL && $foto.usuario_id != $me"}
			<div class="btn-group pull-right">
				<a href="#" class="btn btn-danger">{@Denunciar@}</a>
			</div>
			{/if}
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
							<span>
								<a href="#">{$value.usuario.nick}</a>
								<small>{$value.fecha->fuzzy()}</small>
							</span>
							<div class="btn-group pull-right">
								{if="$me != NULL"}<a href="#" class="btn-quote-comment btn btn-mini btn" data-user="{$value.usuario.nick}"><i class="icon icon-comment"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini"><i class="icon icon-pencil"></i></a>{/if}
								{if="$me != NULL && $me == $value.usuario.id"}<a href="#" class="btn btn-mini btn-danger"><i class="icon-white icon-remove"></i></a>{/if}
							</div>
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
		<form method="POST" action="/foto/comentar/{$foto.id}">
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
				<strong>{@&iexcl;Felicidades!@}</strong> {$comentario_success}
			</div>{/if}
			{if="isset($comentario_error)"}
			<div class="alert">
				<strong>{@&iexcl;Error!@}</strong> {$comentario_error}
			</div>{/if}
			<textarea class="span10" name="comentario" id="comentario" placeholder="Comentario...">{$comentario_content}</textarea>
		</form>
		{else}
			{if="$puedo_comentar"}
		<div class="alert">{@Para poder comentar debes estar registrado.@}</div>
			{else}
		<div class="alert">{@Los comentarios se encuentran cerrados.@}</div>
			{/if}
		{/if}
	</div>
</div>