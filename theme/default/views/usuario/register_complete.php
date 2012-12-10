<div class="alert alert-success">
	<h2>Felicitaciones:</h2>
	{if="$tipo == 0"}
	El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe esperar que un administrador active su cuenta, cuando eso suceda ser&aacute;s notificado por correo.
	{elseif="$tipo == 1"}
	El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe seguir las instrucciones que fueron enviadas a su casilla de <strong>E-Mail</strong>.
	{else}
	El registro se ha realizado <strong>correctamente</strong>. Ya puedes acceder a tu cuenta iniciando sesi&oacute;n <a href="{#SITE_URL#}/usuario/login/">aqu&iacute;</a>.
	{/if}
</div>