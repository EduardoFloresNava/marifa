<h1 class="title">Bandeja de entrada</h1>
{if="count($recibidos) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th></th>
			<th>De</th>
			<th>Asunto</th>
			<th>Fecha</th>
		</tr>
	</thead>
	<tbody>
		{loop="$recibidos"}
		<tr>
			<td>{if="$value.estado == 0"}
				<i class="icon icon-envelope"></i>
				{elseif="$value.estado == 1"}
				<i class="icon icon-inbox"></i>
				{elseif="$value.estado == 2"}
				<i class="icon icon-share-alt"></i>
				{elseif="$value.estado == 3"}
				<i class="icon icon-repeat"></i>
				{/if}</td>
			<td><a href="/perfil/index/{$value.emisor.nick}">{$value.emisor.nick}</a></td>
			<td><a href="/mensaje/ver/{$value.id}">{$value.asunto}</a>{if="$value.padre_id !== NULL"}<a class="pull-right" alt="Padre" href="/mensaje/enviado/{$value.padre_id}"><i class="icon icon-upload"></i></a>{/if}</td>
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