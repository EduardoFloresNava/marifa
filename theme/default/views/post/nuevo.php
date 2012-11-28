<div class="row">
	<div class="span12">
		<h2 class="title">Nuevo post:</h2>
		<form method="POST" class="form-horizontal" action="">

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}

			<div class="control-group{if="$error_titulo"} error{/if}">
				<label class="control-label" for="titulo">T&iacute;tulo</label>
				<div class="controls">
					<input type="text" id="titulo" name="titulo" value="{$titulo}" class="input-xxlarge" />
					<span class="help-block">{if="$error_titulo"}{$error_titulo}{/if}</span>
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
					<textarea name="contenido" id="contenido" data-preview="/post/preview" class="input-xxlarge">{$contenido}</textarea>
					<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_tags"} error{/if}">
				<label class="control-label" for="tags">Etiquetas</label>
				<div class="controls">
					<input type="text" id="tags" name="tags" value="{$tags}" class="input-xxlarge" />
					<span class="help-block">{if="$error_tags"}{$error_tags}{else}Listado de etiquetas separadas por ','. Las etiquetas deben ser alphanuméricas y contener espacios.{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_captcha"} error{/if}">
				<label class="control-label" for="captcha">CAPTCHA</label>
				<div class="controls">
					<input type="text" id="captcha" name="captcha" value="{$captcha}" />
					<span class="help-block">{if="$error_captcha"}{$error_captcha}{else}Ingresa el código que aparece a continuación.{/if}</span>
					<img src="/home/captcha" style="display: block;" />
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<h3 class="title">Categor&iacute;a</h3>
					{if="$error_categoria"}<div class="alert alert-danger">{$error_categoria}</div>{/if}
					<select class="span12" name="categoria" id="categoria" size="10">
						{loop="$categorias"}
						<option value="{$value.seo}"{if="$categoria == $value.seo"} selected="selected"{/if}>{$value.nombre|htmlentities:ENT_NOQUOTES}</option>{/loop}
					</select>
				</div>

				<div class="span6">
					<h3 class="title">Opciones</h3>

					<label class="checkbox">
						<input type="checkbox" id="privado" name="privado" value="1"{if="$privado"} checked{/if}><strong>S&oacute;lo usuarios registrados</strong>
						<p>Tu post ser&aacute; visto s&oacute;lo por los usuarios que est&eacute;n registrados.</p>
					</label>
					<label class="checkbox">
						<input type="checkbox" id="comentar" name="comentar" value="1"{if="$comentar"} checked{/if}><strong>Comentarios cerrados</strong>
						<p>No se permiten comentarios en el post.</p>
					</label>
					{if="$permisos_especiales"}<label class="checkbox">
						<input type="checkbox" id="patrocinado" name="patrocinado" value="1"{if="$patrocinado"} checked{/if}><strong>Patrocinado</strong>
						<p>Resalta este post entre los dem&aacute;s.</p>
					</label>
					<label class="checkbox">
						<input type="checkbox" id="sticky" name="sticky" value="1"{if="$sticky"} checked{/if}><strong>Sticky</strong>
						<p>Colocar a este post fijo en la home.</p>
					</label>{/if}
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" name="submit" value="borrador" class="btn btn-large btn-info">Guardar como borrador</button>
				<button type="submit" name="submit" value="enviar" class="btn btn-large btn-primary">Crear</button>
			</div>
		</form>
	</div>
</div>