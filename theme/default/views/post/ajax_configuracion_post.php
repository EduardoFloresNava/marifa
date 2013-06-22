<ul class="dropdown-menu" id="post-options-menu" data-one-click-items=".adm-btn">
	{if="$modificar_especiales"}
	<li>
		{if="$sticky"}
		<a href="{#SITE_URL#}/post/fijar_post/{$id}/-1" class="one-click-ajax" data-one-click-spinner="true">Desfijar</a>
		{else}
		<a href="{#SITE_URL#}/post/fijar_post/{$id}/1" class="one-click-ajax" data-one-click-spinner="true">Fijar</a>
		{/if}
	</li>
	<li>
		{if="$sponsored"}
		<a href="{#SITE_URL#}/post/patrocinar_post/{$id}/-1" class="one-click-ajax" data-one-click-spinner="true"><i class="icon icon-bookmark-empty"></i> Quitar patrocinio</a>
		{else}
		<a href="{#SITE_URL#}/post/patrocinar_post/{$id}/1" class="one-click-ajax" data-one-click-spinner="true"><i class="icon icon-bookmark-empty"></i> Patrocinar</a>
		{/if}
	</li>
	{/if}
	{if="$estado == 0"}
		{if="$modificar_ocultar"}
		<li><a href="{#SITE_URL#}/post/ocultar_post/{$id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-eye-close"></i> Ocultar</a></li>
		{/if}
		{if="$modificar_aprobar"}
		<li><a href="{#SITE_URL#}/post/aprobar_post/{$id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-down"></i> Rechazar</a></li>
		{/if}
		{if="$modificar_borrar"}
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-trash"></i> Enviar a la papelera</a></li>
		{/if}
	{/if}
	{if="$estado == 1"}
		{if="$me == $usuario_id"}
		<li><a href="{#SITE_URL#}/post/publicar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-ok"></i> Publicar</a></li>
		{/if}
		{if="$modificar_borrar"}
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
		{/if}
	{/if}
	{if="$estado == 3"}
		{if="$modificar_aprobar"}
		<li><a href="{#SITE_URL#}/post/aprobar_post/{$id}/1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-up"></i> Aprobar</a></li>
		<li><a href="{#SITE_URL#}/post/aprobar_post/{$id}/-1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-down"></i> Rechazar</a></li>
		{/if}
		{if="$modificar_borrar"}
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
		{/if}
	{/if}
	{if="$estado == 4"}
		{if="$modificar_ocultar"}
		<li><a href="{#SITE_URL#}/post/ocultar_post/{$id}/1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-eye-open"></i> Mostrar</a></li>
		{/if}
		{if="$modificar_borrar"}
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
		{/if}
	{/if}
	{if="$estado == 5"}
		{if="$modificar_aprobar"}
		<li><a href="{#SITE_URL#}/post/aprobar_post/{$id}/1" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-hand-up"></i> Aprobar</a></li>
		{/if}
		{if="$modificar_borrar"}
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon icon-remove"></i> Borrar</a></li>
		{/if}
	{/if}
	{if="$estado == 6"}
		{if="$modificar_borrar"}
		<li><a href="{#SITE_URL#}/post/restaurar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"><i class="icon-white icon-refresh"></i> Restaurar</a></li>
		<li><a href="{#SITE_URL#}/post/borrar_post/{$id}/" class="adm-btn one-click-ajax" data-one-click-spinner="true" data-one-click-container="#post-options-menu"></i><i class="icon-white icon-remove"></i> Borrar</a></li>
		{/if}
	{/if}
</ul>