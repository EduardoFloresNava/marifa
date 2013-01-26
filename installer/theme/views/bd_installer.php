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
		{if="is_array($value)"}
		<tr{if="isset($value.error)"} class="err"{/if}{if="isset($value.success)"} class="ok"{/if}>
			<td>{$value.titulo}</td>
			<td>{if="isset($value.error)"}{$value.error}{/if}{if="isset($value.success)"}OK{/if}</td>
		</tr>
		{else}
		<tr>
			<th colspan="2">Actualización a {$value}:</th>
		</tr>
		{/if}
		{/loop}
	</tbody>
</table>
{if="!isset($execute)"}<form action="" method="POST"><input type="submit" value="Ejecutar" class="btn btn-large btn-primary" /></form>
{else}<a href="{function="Installer_Step::url_siguiente('bd_install')"}" class="btn btn-large btn-success">Continuar</a>{/if}