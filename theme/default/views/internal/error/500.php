<h1 class="title">Error 500:</h1>
{if="$debug"}
<p>{$descripcion}</p>
{if="$backtrace"}<p>{$backtrace|nl2br}</p>{/if}
{if="$source"}<pre>{$source}</pre>{/if}
{else}
<div class="alert alert-danger"><i class="icon icon-warning-sign"></i> Se ha producido un error al procesar la petici&oacute;n. El administrador del sitio ya ha sido informado del problema. El mismo ser&aacute; solucionado a la brevedad.</div>
{/if}
