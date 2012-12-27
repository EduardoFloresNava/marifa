<div class="row-fluid">
    <div class="span6">Para: <a href="{#SITE_URL#}/@{$mensaje.receptor.nick}">{$mensaje.receptor.nick}</a></div>
    <div class="span6">Fecha: {$mensaje.fecha->format('d/m/Y H:i:s')} ({$mensaje.fecha->fuzzy()})</div>
</div>
<div class="row-fluid">
    <div class="span12">Asunto: {$mensaje.asunto}</div>
</div>
<hr />
<div class="row-fluid">
    <div class="span12">
        {$mensaje.contenido}
    </div>
</div>