{if="!$bloqueado"}
<a href="{#SITE_URL#}/@{$usuario_nick}/bloquear" class="btn btn-danger one-click-ajax" data-one-click-spinner="true"><i class="icon-white icon-ban-circle"></i> Bloquear</a>
{else}
<a href="{#SITE_URL#}/@{$usuario_nick}/desbloquear" class="btn btn-success one-click-ajax" data-one-click-spinner="true"><i class="icon-white icon-ok-sign"></i> Desbloquear</a>
{/if}