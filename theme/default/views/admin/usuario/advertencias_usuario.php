<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario">Usuarios</a> <span class="divider">/</span></li>
    <li class="active">Advertencias a <a href="{#SITE_URL#}/@{$usuario.nick}">{$usuario.nick}</a></li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Advertencias a <a href="{#SITE_URL#}/@{$usuario.nick}">{$usuario.nick}</a></h2>
	<div class="pull-right btn-group">
		<a href="{#SITE_URL#}/admin/usuario/" class="btn btn-small btn-success">Volver</a>
	</div>
</div>
{loop="$advertencias"}
<table class="table table-bordered">
	<tr>
		<td><strong>Moderador:</strong> <a href="{#SITE_URL#}/@{$value.moderador.nick}">{$value.moderador.nick}</a></td>
		<td><strong>Fecha:</strong> {$value.fecha->fuzzy()} ({$value.fecha->format('d/m/Y H:i:s')})</td>
		<td>
			<div class="btn-group">
				<a href="{#SITE_URL#}/admin/usuario/borrar_advertencia_usuario/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar advertencia"><i class="icon-white icon-remove"></i></a>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3"><strong>Asunto:</strong> {$value.asunto}</td>

	</tr>
	<tr>
		<td colspan="3">{$value.contenido|Decoda::procesar}</td>
	</tr>
</table>
{else}
<div class="alert alert-info">!No hay advertencias para mostrar!</div>
{/loop}