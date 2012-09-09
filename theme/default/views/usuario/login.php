<div class="span7">

    {if="isset($error)"}
    <div class="alert">
        <a class="close" data-dismiss="alert">×</a>
        <strong>Error: </strong>{$error}
    </div>
    {/if}

    <form class="form-horizontal" action="/usuario/login" method="POST">
        <fieldset>
            <legend>Inicio de Sessi&oacute;n</legend>

            <div class="control-group{if="$error_nick"} error{/if}">
                <label class="control-label" for="nick">E-Mail o Usuario</label>
                <div class="controls">
                    <input type="text" class="input-xlarge span12" id="nick" name="nick" value="{$nick}" />
                </div>
            </div>

            <div class="control-group{if="$error_password"} error{/if}">
                <label class="control-label" for="password">Contrase&ntilde;a</label>
                <div class="controls">
                    <input type="password" class="input-xlarge span12" id="password" name="password" />
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary">Ingresar</button>
                o
                <a href="/usuario/register">&iquest;Necesitas una cuenta?</a>
                <a href="/usuario/recovery">&iquest;Perdio su contrase&ntilde;a?</a>
            </div>

        </fieldset>
    </form>
</div>