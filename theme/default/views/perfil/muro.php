<h3 class="title">{@Actividad de@} {$usuario.nick}</h3>
{if="Usuario::is_login()"}
{if="Usuario::$usuario_id === $usuario.id || Usuario::puedo_referirlo($usuario.id)"}
<div class="publicar">
	<form action="/perfil/index/{$usuario.nick}/" method="POST">
		<ul class="nav nav-pills">
			<li{if="$tipo == 'texto'"} class="active"{/if}><a href="#" id="publicacion-tipo-texto"><i class="icon-white icon-pencil"></i> {if="$usuario.id !== Usuario::$usuario_id"}Publicación{else}Estado{/if}</a></li>
			<li{if="$tipo == 'foto'"} class="active"{/if}><a href="#" id="publicacion-tipo-foto"><i class="icon icon-picture"></i> Foto</a></li>
			<li{if="$tipo == 'enlace'"} class="active"{/if}><a href="#" id="publicacion-tipo-enlace"><i class="icon icon-retweet"></i> Enlace</a></li>
			<li{if="$tipo == 'video'"} class="active"{/if}><a href="#" id="publicacion-tipo-video"><i class="icon icon-play"></i> Video</a></li>
			<li class="pull-right"><input type="submit" class="btn btn-large btn-primary" value="Publicar" /></li>
		</ul>
		<div id="publicacion-contenido">
			{if="isset($error_tipo)"}<div class="alert">{$error_tipo}</div>{/if}
			{if="$tipo != 'texto'"}
				{if="isset($error_url)"}<div class="alert">{$error_url}</div>{/if}
				{if="$tipo == 'foto'"}<input type="text" placeholder="URL de la imagen..." class="url span8" name="url" value="{$url}" />{/if}
				{if="$tipo == 'enlace'"}<input type="text" placeholder="URL a publicar..." class="url span8" name="url" value="{$url}" />{/if}
				{if="$tipo == 'video'"}<input type="text" placeholder="URL del video a publicar...." class="url span8" name="url" value="{$url}" />{/if}
			{/if}
			{if="isset($error_publicacion)"}<div class="alert">{$error_publicacion}</div>{/if}
			<textarea id="publicacion" name="publicacion" class="span8">{if="isset($publicacion)"}{$publicacion}{/if}</textarea>
			<input type="hidden" name="tipo" value="{$tipo}" />
		</div>
	</form>
</div>
{elseif="Usuario::$usuario_id !== $usuario.id"}
<div class="alert alert-info"><i class="icon icon-info-sign"></i> Para poder publicar en el perfil de {$usuario.nick} debes seguirlo o él debe ser tu seguidor. Si ya cumples ese requisito tal vez estés bloqueado.</div>
{/if}
{/if}
<div class="sucesos">
{loop="$eventos"}{$value}{else}<div class="alert">No hay sucesos para este usuario.</div>{/loop}
</div>
{$paginacion}