<div id="denunciar-usuario-modal-form" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="denunciar-usuario-modal-form-title" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="denunciar-usuario-modal-form-title">{@Denunciar a@} {$usuario}</h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" action="{#SITE_URL#}/@{$usuario}/denunciar" method="POST">
            <div class="control-group">
				<label class="control-label" for="denunciar-usuario-modal-form-motivo">{@Motivo@}</label>
				<div class="controls">
					<select name="motivo" id="denunciar-usuario-modal-form-motivo">
						<option value="0">{@Perfil falso/clon@}</option>
						<option value="1">{@Usuario insultante y agresivo@}</option>
						<option value="2">{@Publicaciones inapropiadas@}</option>
						<option value="3">{@Foto del perfil inapropiada@}</option>
						<option value="4">{@Publicidad no deseada (SPAM)@}</option>
						<option value="5">{@Otra (especificar)@}</option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="denunciar-usuario-modal-form-comentario">{@Comentario@}</label>
				<div class="controls">
					<textarea id="denunciar-usuario-modal-form-comentario" name="comentario">{$comentario}</textarea>
				</div>
			</div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">{@Denunciar@}</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">{@Cerrar@}</button>
    </div>
</div>