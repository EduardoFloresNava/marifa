{if="$post_sponsored"}
<a href="{#SITE_URL#}/post/patrocinar_post/{$post_id}/-1" class="one-click-ajax" data-one-click-spinner="true"><i class="icon icon-bookmark-empty"></i> Quitar patrocinio</a>
{else}
<a href="{#SITE_URL#}/post/patrocinar_post/{$post_id}/1" class="one-click-ajax" data-one-click-spinner="true"><i class="icon icon-bookmark-empty"></i> Patrocinar</a>
{/if}