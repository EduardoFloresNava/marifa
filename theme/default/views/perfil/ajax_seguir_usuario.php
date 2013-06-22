{if="$seguidor"}
<a href="{#SITE_URL#}/@{$usuario_nick}/seguir/0" class="btn btn-primary one-click-ajax" data-one-click-spinner="true"><i class="icon-white icon-minus"></i> Dejar de seguir</a>
{else}
<a href="{#SITE_URL#}/@{$usuario_nick}/seguir/1" class="btn btn-primary one-click-ajax" data-one-click-spinner="true"><i class="icon-white icon-plus"></i> Seguir</a>
{/if}