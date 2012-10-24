<div class="btn-toolbar">
    <div class="btn-group pull-right">
        <button class="btn dropdown-toggle" data-toggle="dropdown"><img height="16" width="16" src="{function="Utils::get_gravatar($usuario.email, 32, 32)"}" /> {$usuario.nick}&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu">
			<li><a href="/favoritos/"><i class="icon icon-heart"></i> Favoritos</a></li>
			<li><a href="/borradores/"><i class="icon icon-file"></i> Borradores</a></li>
			<li><a href="/mensaje/"><i class="icon icon-envelope"></i> Mensajes</a></li>
			<li class="divider"></li>
			<li><a href="/notificaciones/"><i class="icon icon-bullhorn"></i> Notificaciones</a></li>
			<li><a href="/cuenta/"><i class="icon icon-user"></i> Cuenta</a></li>
            <li class="divider"></li>
            <li><a href="/usuario/logout"><i class="icon icon-off"></i> Salir</a></li>
        </ul>
    </div>
    <div class="btn-group pull-right">
        <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-bullhorn"></i>&nbsp;</button><!--SUCESOS GENERALES-->
        <div class="dropdown-menu" id="suceso-dropdown">
			{if="count($sucesos) > 0"}
			<ul>
				{loop="$sucesos"}
				<li>{$value}</li>
				{/loop}
			</ul>
			{else}
			<div class="alert alert-info">No tienes sucesos.</div>
			{/if}
			<div class="actions">
				<a href="/notificaciones/">Ver todos</a>
				<a href="/notificaciones/leidas/">Marcar como le&iacute;dos</a>
			</div>
        </div>
    </div>
    <div class="btn-group pull-right">
        <a class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-inbox"></i>{if="$mensajes_nuevos > 0"}&nbsp;<span class="badge badge-info">{$mensajes_nuevos}</span>{/if}</a><!--MENSAJES-->
        <div class="dropdown-menu" id="message-dropdown">
			{if="count($mensajes) > 0"}
			<ul>
				{loop="$mensajes"}
				<li class="estado-{$value.estado_string}">
					{if="$value.estado == 0"}
					<i class="icon icon-envelope"></i>
					{elseif="$value.estado == 1"}
					<i class="icon icon-inbox"></i>
					{elseif="$value.estado == 2"}
					<i class="icon icon-share-alt"></i>
					{elseif="$value.estado == 3"}
					<i class="icon icon-repeat"></i>
					{/if}
					<a class="usuario" href="/perfil/index/{$value.emisor.nick}/">{$value.emisor.nick}</a> <a href="/mensaje/ver/{$value.id}">{$value.asunto|Texto::limit_chars:20,'...', TRUE}</a> <span class="fecha">{$value.fecha->fuzzy()}</span></li>
				{/loop}
			</ul>
			{else}
			<div class="alert alert-info">No tienes mensajes esperando.</div>
			{/if}
			<div class="actions">
				<a href="/mensaje/">Bandeja de entrada</a>
			</div>
        </div>
    </div>
</div>