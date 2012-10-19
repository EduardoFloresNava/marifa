<h3 class="title">{@Actividad de@} {$usuario.nick}</h3>
{if="Usuario::is_login()"}<div class="row-fluid">
	<div class="span12">
		<form>
			<textarea class="span12" placeholder="{@Publicar en el muro de@} {$usuario.nick}"></textarea>
		</form>
	</div>
</div>{/if}
<div class="sucesos">
{loop="$eventos"}{$value}{else}<div class="alert">No hay sucesos para este usuario.</div>{/loop}
</div>