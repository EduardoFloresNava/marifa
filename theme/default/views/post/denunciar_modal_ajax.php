<div id="denunciar-post-modal-form" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="denunciar-post-modal-form-title" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="denunciar-post-modal-form-title">{@Denunciar@} <a href="{#SITE_URL#}/post/{$post.categoria.seo}/{$post.id}/{$post.titulo|Texto::make_seo}.html">{$post.titulo}</a></h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" action="{#SITE_URL#}/post/denunciar/{$post.id}" method="POST">
            <div class="control-group">
				<label class="control-label" for="denunciar-post-modal-form-motivo">{@Motivo@}</label>
				<div class="controls">
					<select name="motivo" id="denunciar-post-modal-form-motivo">
						<option value="0" {if="$motivo == 0"}selected="selected"{/if}>Re-post</option>
						<option value="1" {if="$motivo == 1"}selected="selected"{/if}>spam</option>
						<option value="2" {if="$motivo == 2"}selected="selected"{/if}>Contiene links muertos</option>
						<option value="3" {if="$motivo == 3"}selected="selected"{/if}>Racista o irrespetuoso</option>
						<option value="4" {if="$motivo == 4"}selected="selected"{/if}>Contiene información personal</option>
						<option value="5" {if="$motivo == 5"}selected="selected"{/if}>Titulo en mayúsculas</option>
						<option value="6" {if="$motivo == 6"}selected="selected"{/if}>Contiene pedofilia</option>
						<option value="7" {if="$motivo == 7"}selected="selected"{/if}>Gore o asqueroso</option>
						<option value="8" {if="$motivo == 8"}selected="selected"{/if}>Fuente incorrecta</option>
						<option value="9" {if="$motivo == 9"}selected="selected"{/if}>Contenido pobre o crap</option>
						<option value="10" {if="$motivo == 10"}selected="selected"{/if}>No es un foro</option>
						<option value="11" {if="$motivo == 11"}selected="selected"{/if}>No cumple el protocolo</option>
						<option value="12" {if="$motivo == 12"}selected="selected"{/if}>Otra (especificar)</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="denunciar-post-modal-form-comentario">{@Comentario@}</label>
				<div class="controls">
					<textarea id="denunciar-post-modal-form-comentario" name="comentario">{$comentario}</textarea>
				</div>
			</div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">{@Denunciar@}</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">{@Cerrar@}</button>
    </div>
</div>