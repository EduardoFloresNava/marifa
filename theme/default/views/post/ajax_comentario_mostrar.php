{if="$mostrar"}
<a href="{#SITE_URL#}/post/ocultar_comentario/{$id}/0" class="btn btn-mini btn-inverse one-click-ajax" data-one-click-spinner="true" title="Ocultar"><i class="icon-white icon-eye-close"></i></a>
{else}
<a href="{#SITE_URL#}/post/ocultar_comentario/{$id}/1" class="btn btn-mini btn-info one-click-ajax" data-one-click-spinner="true" title="Mostrar"><i class="icon-white icon-eye-open"></i></a>
{/if}