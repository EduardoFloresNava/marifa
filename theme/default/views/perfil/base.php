<div class="row profile">
	<div class="span2">
		<img class="thumbnail" src="{function="Utils::get_gravatar($usuario.email, 150, 150)"}" />
	</div>
	<div class="span6">
		<h1 class="title">{$usuario.nick}{if="isset($usuario.nombre)"} <small>{$usuario.nombre}</small>{/if}</h1>
		{if="isset($mensaje_personal)"}<div class="mensaje-personal">{$mensaje_personal|nl2br}</div>{/if}
		{if="Usuario::is_login() && $usuario.id !== Usuario::$usuario_id"}<a href="{#SITE_URL#}/@{$usuario.nick}/denunciar" class="btn btn-warning"><i class="icon-white icon-exclamation-sign"></i> Denunciar</a>
		{if="Usuario::is_login()"}{if="!$bloqueado"}<a href="{#SITE_URL#}/@{$usuario.nick}/bloquear" class="btn btn-danger"><i class="icon-white icon-ban-circle"></i> Bloquear</a>{else}<a href="{#SITE_URL#}/@{$usuario.nick}/desbloquear" class="btn btn-success"><i class="icon-white icon-ok-sign"></i> Desbloquear</a>{/if}
		{if="$seguidor"}<a href="{#SITE_URL#}/@{$usuario.nick}/seguir/0" class="btn btn-primary"><i class="icon-white icon-minus"></i> Dejar de seguir</a>{else}<a href="{#SITE_URL#}/@{$usuario.nick}/seguir/1" class="btn btn-primary"><i class="icon-white icon-plus"></i> Seguir</a>{/if}{/if}{/if}
	</div>
	<div class="span4 profile-statistics">
		<div class="row-fluid">
			<div class="span6 well"><i class="icon icon-certificate"></i><span class="pull-right" style="color: #{function="sprintf('%06s', dechex($usuario.rango.color))"};">{$usuario.rango.nombre}</span></div>
			<div class="span6 well"><i class="icon icon-plus"></i><span class="pull-right">{if="$usuario.puntos > 1"}{$usuario.puntos} {@puntos@}{elseif="$usuario.puntos == 1"}1 {@puntos@}{else}{@sin@} {@puntos@}{/if}</span></div>
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
	<div class="span8">
		{$contenido}
	</div>
	<div class="span4">
		<h3 class="title">Medallas{if="count($medallas) > 0"}<small>{function="count($medallas)"}</small>{/if}</h3>
		{if="count($medallas) > 0"}
		<ul class="small-user-list clearfix">
			{loop="$medallas"}
			<li>
				<a href="{#SITE_URL#}/@{$usuario.nick}/medallas" class="show-tooltip" title="{$value.medalla.nombre}"><img src="{#THEME_URL#}/assets/img/medallas/{$value.medalla.imagen}" alt="{$value.medalla.nombre}" /></a>
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">!{$usuario.nick} aún no tiene ninguna medalla!</div>
		{/if}
		<h3 class="title">Seguidores{if="$usuario.seguidores > 0"}<small>{$usuario.seguidores}</small>{/if}</h3>
		{if="count($seguidores) > 0"}
		<ul class="small-user-list clearfix">
			{loop="$seguidores"}
			<li>
				<a href="{#SITE_URL#}/@{$value.nick}" class="show-tooltip" title="{$value.nick}">
					<img src="{function="Utils::get_gravatar($value.email, 74, 74)"}" width="74" height="74" />
				</a>
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">!Aún no hay usuarios siguiendo a {$usuario.nick}!</div>
		{/if}
		<h3 class="title">Siguiendo{if="$usuario.sigue > 0"}<small>{$usuario.sigue}</small>{/if}</h3>
		{if="count($sigue) > 0"}
		<ul class="small-user-list clearfix">
			{loop="$sigue"}
			<li>
				<a href="{#SITE_URL#}/@{$value.nick}" class="show-tooltip" title="{$value.nick}">
					<img src="{function="Utils::get_gravatar($value.email, 74, 74)"}" width="74" height="74" />
				</a>
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">!{$usuario.nick} aún no sigue a ningún usuario!</div>
		{/if}
	</div>
</div>