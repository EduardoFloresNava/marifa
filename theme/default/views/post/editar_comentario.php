<h2 class="title">Editar comentario:</h2>
<form class="form-horizontal" action="" method="POST">

	{if="Usuario::$usuario_id !== $usuario.id"}
	<div class="alert alert-info">
		El comentario que estás editando es de otro usuario.
	</div>{/if}

	<div class="control-group">
		<label class="control-label" for="titulo">Título del post:</label>
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
			{include="helper/bbcode_bar"}
			<textarea name="contenido" id="contenido" class="input-xxlarge" data-preview="{#SITE_URL#}/foto/preview">{$contenido}</textarea>
			<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<input type="submit" value="Editar" class="btn btn-large btn-primary" /> o <a href="{#SITE_URL#}/post/{$post.categoria.seo}/{$post.id}/{$post.titulo|Texto::make_seo}.html">Volver</a>
	</div>
</form>