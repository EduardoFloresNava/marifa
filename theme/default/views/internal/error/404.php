<h1 class="title">Error 404:</h1>
{if="$debug"}
<p>{$descripcion}</p>
{if="$backtrace"}<p>{$backtrace|nl2br}</p>{/if}
{if="$source"}<pre>{$source}</pre>{/if}
{else}
<div class="alert alert-danger"><i class="icon icon-warning-sign"></i> La URL '{function="Request::current_url()"}' no se encuentra disponible. Continue su navegación desde la <a href="/">portada</a>.</div>
{/if}
