{if="$ocultar"}
<a href="{#SITE_URL#}/foto/ocultar_foto/{$foto_id}/" class="btn btn-inverse one-click-ajax" data-one-click-spinner="true" title="{@Ocultar foto@}"><i class="icon-white icon-eye-close"></i></a>
{else}
<a href="{#SITE_URL#}/foto/ocultar_foto/{$foto_id}/" class="btn btn-success one-click-ajax" data-one-click-spinner="true" title="{@Mostrar foto@}"><i class="icon-white icon-eye-open"></i></a>
{/if}