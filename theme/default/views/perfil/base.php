<div class="row profile">
	<div class="span2">
		<img class="thumbnail" src="{function="Utils::get_gravatar($usuario.email, 150, 150)"}" />
	</div>
	<div class="span6">
		<h1 class="title">{$usuario.nick}{if="isset($usuario.nombre)"} <small>{$usuario.nombre}</small>{/if}</h1>
		{if="isset($mensaje_personal)"}<div class="mensaje-personal">{$mensaje_personal|nl2br}</div>{/if}
	</div>
	<div class="span4 profile-statistics">
		<div class="row-fluid">
			<div class="span6 well"><i class="icon icon-certificate"></i><span class="pull-right">rango</span></div>
			<div class="span6 well"><i class="icon icon-thumbs-up"></i><span class="pull-right">{if="$usuario.puntos > 1"}{$usuario.puntos} {@puntos@}{elseif="$usuario.puntos == 1"}1 {@puntos@}{else}{@sin@} {@puntos@}{/if}</span></div>
		</div>
		<div class="row-fluid">
			<div class="span6 well"><i class="icon icon-book"></i><span class="pull-right">{if="$usuario.posts > 1"}{$usuario.posts} {@posts@}{elseif="$usuario.posts == 1"}1 {@post@}{else}{@sin@} {@posts@}{/if}</span></div>
			<div class="span6 well"><i class="icon icon-comment"></i><span class="pull-right">{if="$usuario.comentarios > 1"}{$usuario.comentarios} {@comentarios@}{elseif="$usuario.comentarios == 1"}1 {@comentario@}{else}{@sin@} {@comentarios@}{/if}</span></div>
		</div>
		<div class="row-fluid">
			<div class="span6 well"><i class="icon icon-user"></i><span class="pull-right">{if="$usuario.seguidores > 1"}{$usuario.seguidores} {@seguidores@}{elseif="$usuario.seguidores == 1"}1 {@seguidor@}{else}{@sin@} {@seguidores@}{/if}</span></div>
			<div class="span6 well"><i class="icon icon-picture"></i><span class="pull-right">{if="$usuario.fotos > 1"}{$usuario.fotos} {@fotos@}{elseif="$usuario.fotos == 1"}1 {@foto@}{else}{@sin@} {@fotos@}{/if}</span></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="span12">
		<ul class="nav nav-tabs">
			{loop="menu"}
			<li{if="$value.active"}  class="active"{/if}>
				<a href="{$value.link}">{$value.caption}{if="isset($value.cantidad) && $value.cantidad > 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
			</li>
			{/loop}
		</ul>
	</div>
</div>
<div class="row profile-data">
	<div class="span12">
		{$contenido}
	</div>
</div>