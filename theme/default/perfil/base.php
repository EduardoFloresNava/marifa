<div class="row">
	<div class="span2">
		<img class="thumbnail" src="{function="Utils::get_gravatar($usuario.email, 150, 150)"}" />
	</div>
	<div class="span6">
		INFORMACION DEL USUARIO
	</div>
	<div class="span4">
		<div class="row-fluid">
			<div class="span6">RANGO</div>
			<div class="span6">PUNTOS</div>
		</div>
		<div class="row-fluid">
			<div class="span6">POSTS</div>
			<div class="span6">COMENTARIOS</div>
		</div>
		<div class="row-fluid">
			<div class="span6">SEGUIDORES</div>
			<div class="span6">FOTOS</div>
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
<div class="row">
	<div class="span12">
		{$contenido}
	</div>
</div>