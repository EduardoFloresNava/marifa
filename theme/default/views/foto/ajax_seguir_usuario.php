{if="!$sigue_usuario"}
<a href="{#SITE_URL#}/foto/seguir_usuario/{$foto_id}/{$usuario_id}/1" class="btn span12 one-click-ajax" data-one-click-spinner="true"><i class="icon icon-plus"></i> Seguir usuario</a>
{else}
<a href="{#SITE_URL#}/foto/seguir_usuario/{$foto_id}/{$usuario_id}/0" class="btn span12 one-click-ajax" data-one-click-spinner="true"><i class="icon icon-minus"></i> Dejar de seguir</a>
{/if}