<h3 class="title">{@Actividad de@} {$usuario.nick}</h3>
{if="Usuario::is_login() && (Usuario::$usuario_id == $usuario.id || Usuario::puedo_referirlo($usuario.id))"}
<div class="publicar">
	<form action="/perfil/index/{$usuario.nick}/" method="POST">
		<ul class="nav nav-pills">
			<li class="active"><a href="#" id=""><i class="icon-white icon-pencil"></i> {if="$usuario.id !== Usuario::$usuario_id"}Publicación{else}Estado{/if}</a></li>
			<li><a href="#" id=""><i class="icon icon-picture"></i> Foto</a></li>
			<li><a href="#" id=""><i class="icon icon-retweet"></i> Enlace</a></li>
			<li><a href="#" id=""><i class="icon icon-play"></i> Video</a></li>
			<li class="pull-right"><input type="submit" class="btn btn-large btn-primary" value="Publicar" /></li>
		</ul>
		{if="isset($error_publicacion)"}<div class="alert">{$error_publicacion}</div>{/if}
		<textarea id="publicacion" name="publicacion" class="span8">{if="isset($publicacion)"}{$publicacion}{/if}</textarea>
	</form>
</div>
{/if}
<div class="sucesos">
{loop="$eventos"}{$value}{else}<div class="alert">No hay sucesos para este usuario.</div>{/loop}
</div>
{$paginacion}