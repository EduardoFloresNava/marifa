<h2 class="title">Importar la base de datos</h2>
<table class="table">
	<thead>
		<tr>
			<th>Consulta</th>
			<th>Resultado</th>
		</tr>
	</thead>
	<tbody>
		{loop="$consultas"}
		<tr{if="isset($value.error)"} class="err"{/if}{if="isset($value.success)"} class="ok"{/if}>
			<td>{$value.titulo}</td>
			<td>{if="isset($value.error)"}{$value.error}{/if}{if="isset($value.success)"}OK{/if}</td>
		</tr>
		{/loop}
	</tbody>
</table>
{if="!isset($execute)"}<form action="" method="POST"><input type="submit" value="Ejecutar" class="btn btn-large btn-primary" /></form>
{else}<a href="/installer/configuracion/" class="btn btn-large btn-success">Continuar</a>{/if}