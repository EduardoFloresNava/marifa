{if="$sigue"}
<a href="{#SITE_URL#}/post/seguir_usuario/{$post_id}/{$usuario_id}/0" class="btn span12 one-click-ajax" data-one-click-spinner="true"><i class="icon icon-minus"></i> Dejar de seguir</a>
{else}
<a href="{#SITE_URL#}/post/seguir_usuario/{$post_id}/{$usuario_id}/1" class="btn span12 one-click-ajax" data-one-click-spinner="true"><i class="icon icon-plus"></i> Seguir usuario</a>
{/if}