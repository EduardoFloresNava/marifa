<i class="icon icon-flag"></i>
{@Tu post @} <a href="{#SITE_URL#}/post/{$suceso.post.categoria.seo}/{$suceso.post.id}/{$suceso.post.titulo|Texto::make_seo}.html">{$suceso.post.titulo}</a> {if="$suceso.tipo"}{@ha sido fijado en la portada@}{else}{@ya no está fijo@}{/if}.