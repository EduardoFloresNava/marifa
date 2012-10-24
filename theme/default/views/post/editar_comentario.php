<h2 class="title">Editar comentario:</h2>
<form class="form-horizontal" action="" method="POST">

	{if="Usuario::$usuario_id !== $usuario.id"}
	<div class="alert alert-info">
		El comentario que estás editando es de otro usuario.
	</div>{/if}

	<div class="control-group">
		<label class="control-label" for="titulo">T&iacute;tulo del post:</label>
		<div class="controls">
			<input type="text" disabled="disabled" name="titulo" class="input-xxlarge" id="titulo" value="{$post.titulo}" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="autor">Autor del comentario:</label>
		<div class="controls">
			<input type="text" disabled="disabled" name="autor" class="input-xxlarge" id="autor" value="{$usuario.nick}" />
		</div>
	</div>

	<div class="control-group{if="$error_contenido"} error{/if}">
		<label class="control-label" for="contenido">Contenido</label>
		<div class="controls">
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
				<div class="btn-group">
					<a href="#" title="Vista preliminar" class="btn-preview btn btn-small btn-success"><i class="icon-eye-open icon-white"></i></a>
				</div>
			</div>
			<textarea name="contenido" id="contenido" class="input-xxlarge">{$contenido}</textarea>
			<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<input type="submit" value="Editar" class="btn btn-large btn-primary" /> o <a href="/post/index/{$comentario.post_id}">Volver</a>
	</div>
</form>