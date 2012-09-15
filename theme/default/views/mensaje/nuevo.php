<h1 class="title">Enviar mensaje</h1>
<div class="row">
	<div class="span12">
		{if="isset($success)"}
		<div class="alert alert-success">
			<a class="close" data-dismiss="alert">×</a>
			<strong>Felicitaciones: </strong>{$success}
		</div>
		{else}
		<form method="POST" class="form-horizontal" action="">

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}

			<div class="control-group{if="$error_para"} error{/if}">
				<label class="control-label" for="titulo">Para</label>
				<div class="controls">
					{if="isset($tipo) && $tipo == 1"}
					<input type="text" id="para" name="para" value="{$para}" class="span10" disabled="disabled" />
					{else}
					<input type="text" id="para" name="para" value="{$para}" class="span10" placeholder="usuario, usuario2, ..." />
					{/if}
					<span class="help-block">{if="$error_para"}{$error_para}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_asunto"} error{/if}">
				<label class="control-label" for="asunto">Asunto</label>
				<div class="controls">
					<input type="text" id="asunto" name="asunto" value="{$asunto}" class="span10" placeholder="Asunto del mensaje..." />
					<span class="help-block">{if="$error_asunto"}{$error_asunto}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_contenido"} error{/if}">
				<label class="control-label" for="titulo">Contenido</label>
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
					<textarea name="contenido" id="contenido" class="span10" placeholder="Mensaje...">{$contenido}</textarea>
					<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-large btn-primary">Enviar</button>
			</div>
		</form>
		{/if}
	</div>
</div>