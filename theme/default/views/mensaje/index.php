<h1 class="title">Bandeja de entrada</h1>
{if="count($recibidos) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th></th>
			<th>De</th>
			<th>Asunto</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$recibidos"}
		<tr>
			<td>{if="$value.estado == 0"}
				<i class="icon icon-envelope" title="Nuevo"></i>
				{elseif="$value.estado == 1"}
				<i class="icon icon-inbox" title="Visto"></i>
				{elseif="$value.estado == 2"}
				<i class="icon icon-share-alt" title="Respuesta enviada"></i>
				{elseif="$value.estado == 3"}
				<i class="icon icon-repeat" title="Reenviado"></i>
				{/if}</td>
			<td><a href="/perfil/index/{$value.emisor.nick}">{$value.emisor.nick}</a></td>
			<td><a href="/mensaje/ver/{$value.id}">{$value.asunto}</a>{if="$value.padre_id !== NULL"}<a class="pull-right" rel="tooltip" title="Ver padre" href="/mensaje/enviado/{$value.padre_id}"><i class="icon icon-upload"></i></a>{/if}</td>
			<td><span rel="tooltip" title="{$value.fecha->format('d/m/Y H:i:s')}">{$value.fecha->fuzzy()}</span></td>
			<td>
				<div class="btn-group">
					{if="$value.estado == 0"}<a href="/mensaje/leido/{$value.id}" class="btn btn-mini btn-info" rel="tooltip" title="Marcar como leido"><i class="icon-white icon-eye-open"></i></a>{/if}
					{if="$value.estado == 1"}<a href="/mensaje/noleido/{$value.id}" class="btn btn-mini btn-inverse" rel="tooltip" title="Marcar como nuevo"><i class="icon-white icon-eye-close"></i></a>{/if}
					<a href="/mensaje/borrar/{$value.id}" class="btn btn-mini btn-danger" rel="tooltip" title="Eliminar"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{/loop}
	</tbody>
</table>
{else}
<div class="alert">
	No hay mensajes que mostrar.
</div>
{/if}
{$paginacion}