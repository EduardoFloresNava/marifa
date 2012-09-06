<h1>Error 500:</h1>
<p>{$descripcion}</p>
{if="$backtrace"}<p>{$backtrace|nl2br}</p>{/if}
{if="$source"}<pre>{$source}</pre>{/if}