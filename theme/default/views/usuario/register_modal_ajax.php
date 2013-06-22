<div id="register-modal-form" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="register-modal-form-title" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="register-modal-form-title">{@Crear nueva cuenta@}</h3>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" action="" method="POST">
            <div class="control-group">
                <label class="control-label" for="register-modal-form-nick">{@Nick@}</label>
                <div class="controls">
                    <input type="text" id="register-modal-form-nick" name="nick" title="{@Su apellido, se permiten caracteres alphanuméricos, espacios y '.@}" data-placement="right" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="register-modal-form-email">{@E-Mail@}</label>
                <div class="controls">
                    <input type="text" id="register-modal-form-email" name="email" title="{@Su casilla de E-Mail. Su formato debe ser nombre@dominio.tdl@}" data-placement="right" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="register-modal-form-password">{@Contraseña@}</label>
                <div class="controls">
                    <input type="password" id="register-modal-form-password" name="password" title="{@Su clave de acceso. Puede contener caracteres alphanuméricos, @, #, +, - /, * y _, '.' y ','.@}" data-placement="right" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="register-modal-form-c_password">{@Repetir Contraseña@}</label>
                <div class="controls">
                    <input type="password" id="register-modal-form-c_password" name="c_password" title="{@Confirmación de la contraseña@}" data-placement="right" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="register-modal-form-captcha">{@CAPTCHA@}</label>
                <div class="controls">
                    <input type="text" id="register-modal-form-captcha" name="captcha" />
                    <img src="{#SITE_URL#}/home/captcha" style="display: block;" />
                </div>
            </div>

        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">{@Crear cuenta@}</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">{@Cerrar@}</button>
    </div>
</div>