<div class="alert alert-success">
	<h2>Felicitaciones:</h2>
	{if="$tipo == 0"}
	El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe esperar que un administrador active su cuenta, cuando eso suceda serás notificado por correo.
	{elseif="$tipo == 1"}
	El registro se ha realizado <strong>correctamente</strong>. Para poder acceder a su cuenta debe seguir las instrucciones que fueron enviadas a su casilla de <strong>E-Mail</strong>.
	{else}
	El registro se ha realizado <strong>correctamente</strong>. Ya puedes acceder a tu cuenta iniciando sesión <a href="{#SITE_URL#}/usuario/login/">aquí</a>.
	{/if}
</div>