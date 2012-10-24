<h3 class="title">{@Actividad de@} {$usuario.nick}</h3>
<div class="sucesos">
{loop="$eventos"}{$value}{else}<div class="alert">No hay sucesos para este usuario.</div>{/loop}
</div>