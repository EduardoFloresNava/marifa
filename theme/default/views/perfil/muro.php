<h3 class="title">{@Actividad de@} {$usuario.nick}</h3>
<div class="row-fluid">
	<div class="span12">
		<form>
			<textarea class="span12" placeholder="{@Publicar en el muro de@} {$usuario.nick}"></textarea>
		</form>
	</div>
</div>
<div class="sucesos">
{loop="$eventos"}{$value}{/loop}
</div>