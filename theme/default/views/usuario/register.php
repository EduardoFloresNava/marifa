<form class="form-horizontal" id="register" action="" method="POST">
    <fieldset>
        <legend>Nueva cuenta</legend>

        <div class="control-group{if="$error_nick"} error{/if}">
            <label class="control-label" for="nick">Nick</label>
            <div class="controls">
                <input type="text" id="nick" name="nick" value="{$nick}" />
                <p class="help-inline">Su apellido, se permiten caracteres alphanuméricos, espacios y '.</p>
            </div>
        </div>

        <div class="control-group{if="$error_email"} error{/if}">
            <label class="control-label" for="email">E-Mail</label>
            <div class="controls">
                <input type="text" id="email" name="email" value="{$email}" />
                <p class="help-inline">Su casilla de E-Mail. Su formato debe ser nombre@dominio.tdl</p>
            </div>
        </div>

        <div class="control-group{if="$error_password"} error{/if}">
            <label class="control-label" for="password">Contraseña</label>
            <div class="controls">
                <input type="password" id="password" name="password" />
                <p class="help-inline">Su clave de acceso. Puede contener caracteres alphanuméricos, @, #, +, - /, * y _, '.' y ','.</p>
            </div>
        </div>

        <div class="control-group{if="$error_c_password"} error{/if}">
            <label class="control-label" for="c_password">Repetir Contraseña</label>
            <div class="controls">
                <input type="password" id="c_password" name="c_password" />
            </div>
        </div>

		<div class="control-group{if="$error_captcha"} error{/if}">
            <label class="control-label" for="captcha">CAPTCHA</label>
            <div class="controls">
                <input type="text" id="captcha" name="captcha" value="{$captcha}" />
				<img src="{#SITE_URL#}/home/captcha" style="display: block;" />
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" id="register-button">Registrarse</button>
            o
            <a href="{#SITE_URL#}/usuario/login/">¿Iniciar sesión?</a>
        </div>
    </fieldset>
</form>