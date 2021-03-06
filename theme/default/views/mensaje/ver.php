<div class="row-fluid">
    <div class="span6">De: {if="$mensaje.emisor_id === NULL"}<span class="label label-important">SISTEMA</span>{else}<a href="{#SITE_URL#}/@{$mensaje.emisor.nick}">{$mensaje.emisor.nick}</a>{/if}</div>
    <div class="span6">Fecha: {$mensaje.fecha->format('d/m/Y H:i:s')} ({$mensaje.fecha->fuzzy()})</div>
</div>
<div class="row-fluid">
    <div class="span8">Asunto: {$mensaje.asunto}</div>
    <div class="span4">
        <div class="btn-group pull-right">
			{if="$mensaje.emisor_id !== NULL"}<a class="btn btn-small btn-info" href="{#SITE_URL#}/mensaje/nuevo/1/{$mensaje.id}">Responder</a>
            <a class="btn btn-small btn-info" href="{#SITE_URL#}/mensaje/nuevo/2/{$mensaje.id}">Reenviar</a>{/if}
            {if="$mensaje.estado == 1"}<a class="btn btn-small btn-primary" href="{#SITE_URL#}/mensaje/noleido/{$mensaje.id}">Marcar como no leído</a>{/if}
            <a class="btn btn-small btn-danger" href="{#SITE_URL#}/mensaje/borrar/{$mensaje.id}">Borrar</a>
        </div>
    </div>
</div>
<hr />
<div class="row-fluid">
    <div class="span12">
        {$mensaje.contenido}
    </div>
</div>