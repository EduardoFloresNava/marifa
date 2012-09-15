<h1 class="title">Bandeja de salida</h1>
{if="count($enviados) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Para</th>
			<th>Asunto</th>
			<th>Fecha</th>
		</tr>
	</thead>
	<tbody>
		{loop="$enviados"}
		<tr>
			<td><a href="/perfil/index/{$value.receptor.nick}">{$value.receptor.nick}</a></td>
			<td><a href="/mensaje/enviado/{$value.id}">{$value.asunto}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
		</tr>
		{/loop}
	</tbody>
</table>
{else}
<div class="alert">
	No hay mensajes que mostrar.
</div>
{/if}