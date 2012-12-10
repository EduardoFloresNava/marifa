<h2 class="title">Avisos</h2>
{loop="$advertencias"}
<table class="table table-bordered{if="$value.estado == 0"} aviso-nuevo{/if}">
	<tr>
		<td><strong>Moderador:</strong> <a href="{#SITE_URL#}/perfil/index/{$value.moderador.nick}">{$value.moderador.nick}</a></td>
		<td>
			<strong>Fecha:</strong> {$value.fecha->fuzzy()} ({$value.fecha->format('d/m/Y H:i:s')})
			<div class="btn-group pull-right">
				{if="$value.estado !== 1"}<a href="{#SITE_URL#}/cuenta/aviso_leido/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Marcar como visto"><i class="icon-white icon-eye-open"></i></a>{/if}
				<a href="{#SITE_URL#}/cuenta/borrar_aviso/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar aviso"><i class="icon-white icon-remove"></i></a>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><strong>Asunto:</strong> {$value.asunto}</td>

	</tr>
	<tr>
		<td colspan="2">{$value.contenido|Decoda::procesar}</td>
	</tr>
</table>
{else}
<div class="alert alert-success">&iexcl;No tienes ning&uacute;n aviso!</div>
{/loop}