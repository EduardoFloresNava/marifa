<i class="icon icon-comment"></i>
<a href="{#SITE_URL#}/perfil/index/{$suceso.usuario.nick}">{$suceso.usuario.nick}</a> {@ha citado tu@} <a href="{if="isset($suceso.comentario.foto_id)"}/foto/{$suceso.foto.categoria.seo}/{$suceso.foto.id}/{$suceso.foto.titulo|Texto::make_seo}.html#c-{$suceso.comentario.id}{else}/post/index/{$suceso.comentario.post_id}#c-{$suceso.comentario.id}{/if}">comentario</a>.