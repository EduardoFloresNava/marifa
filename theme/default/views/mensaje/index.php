<h1 class="title">Bandeja de entrada</h1>
{if="count($recibidos) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th>De</th>
			<th>Asunto</th>
			<th>Fecha</th>
		</tr>
	</thead>
	<tbody>
		{loop="$recibidos"}
		<tr>
			<td><a href="/perfil/index/{$value.emisor.nick}">{$value.emisor.nick}</a></td>
			<td><a href="/mensaje/ver/{$value.id}">{$value.asunto}</a></td>
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