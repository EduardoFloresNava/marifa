{if="$mostrar"}
<a href="{#SITE_URL#}/post/ocultar_post/{$post_id}/-1" class="btn btn-inverse one-click-ajax" data-one-click-spinner="true" title="{@Ocultar@}"><i class="icon-white icon-eye-close"></i></a>
{else}
<a href="{#SITE_URL#}/post/ocultar_post/{$post_id}/1" class="btn btn-success one-click-ajax" data-one-click-spinner="true" title="{@Mostrar@}"><i class="icon-white icon-eye-open"></i></a>
{/if}