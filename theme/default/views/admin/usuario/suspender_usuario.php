<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Suspender</li>
</ul>
<div class="header">
	<h2 class="title">Suspender a <a href="/perfil/index/{$usuario.nick}">{$usuario.nick}</a></h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_motivo"} error{/if}">
		<label class="control-label" for="motivo">Motivo</label>
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
			<textarea name="motivo" id="motivo" class="span10">{$motivo}</textarea>
			<span class="help-block">{if="$error_motivo"}{$error_motivo}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_fin"} error{/if}">
		<label class="control-label" for="fin">Finalizaci&oacute;n</label>
		<div class="controls">
			<input type="text" value="{$fin}" name="fin" id="fin" class="span10" />
			<span class="help-block">{if="$error_fin"}{$error_fin}{else}Fecha de terminación. El formato es el de strtotime.{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Crear</button> o <a href="/admin/usuario">Volver</a>
	</div>
</form>