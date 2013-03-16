<div id="denunciar-foto-modal-form-{$foto.id}" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="denunciar-foto-modal-form-title" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="denunciar-foto-modal-form-title-{$foto.id}">{@Denunciar@} <a href="{#SITE_URL#}/foto/{$foto.categoria.seo}/{$foto.id}/{$foto.titulo|Texto::make_seo}.html">{@foto@}</a></h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" action="{#SITE_URL#}/foto/denunciar/{$foto.id}" method="POST">
            <div class="control-group">
				<label class="control-label" for="denunciar-foto-modal-form-motivo-{$foto.id}">{@Motivo@}</label>
				<div class="controls">
					<select name="motivo" id="denunciar-foto-modal-form-motivo-{$foto.id}">
						<option value="0" {if="$motivo == 0"}selected="selected"{/if}>{@Ya está publicada@}</option>
						<option value="1" {if="$motivo == 1"}selected="selected"{/if}>{@Se hace Spam@}</option>
						<option value="2" {if="$motivo == 2"}selected="selected"{/if}>{@La imagen está caída@}</option>
						<option value="3" {if="$motivo == 3"}selected="selected"{/if}>{@Es racista o irrespetuosa@}</option>
						<option value="4" {if="$motivo == 4"}selected="selected"{/if}>{@Contiene información personal@}</option>
						<option value="5" {if="$motivo == 5"}selected="selected"{/if}>{@Contiene pedofilia@}</option>
						<option value="6" {if="$motivo == 6"}selected="selected"{/if}>{@Es gore o asqueros@}</option>
						<option value="7" {if="$motivo == 7"}selected="selected"{/if}>{@Otra (especificar)@}</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="denunciar-foto-modal-form-comentario-{$foto.id}">{@Comentario@}</label>
				<div class="controls">
					<textarea id="denunciar-foto-modal-form-comentario-{$foto.id}" name="comentario">{$comentario}</textarea>
				</div>
			</div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">{@Denunciar@}</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">{@Cerrar@}</button>
    </div>
</div>