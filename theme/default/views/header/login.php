<div class="btn-toolbar">
    <div class="btn-group pull-right">
        <button class="btn dropdown-toggle" data-toggle="dropdown"><img height="16" width="16" src="" />{$usuario.nick}&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu">
			<li><a href="/favoritos">Favoritos</a></li>
			<li><a href="/borradores">Borradores</a></li>
			<li><a href="/cuenta">Cuenta</a></li>
			<li><a href="/notificaciones">Notificaciones</a></li>
            <li><a href="/mensajes">Mensajes</a></li>
            <li><a href="/perfil">Perfil</a></li>
            <li class="divider"></li>
            <li><a href="/usuario/logout">Salir</a></li>
        </ul>
    </div>
    <div class="btn-group pull-right">
        <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-bullhorn"></i>&nbsp;</button><!--SUCESOS GENERALES-->
        <ul class="dropdown-menu" id="message-dropdown">
        </ul>
    </div>
    <div class="btn-group pull-right">
        <a class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-inbox"></i>&nbsp;</a><!--MENSAJES-->
        <ul class="dropdown-menu">
        </ul>
    </div>
</div>