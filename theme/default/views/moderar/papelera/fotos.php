<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">Moderaci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/papelera/">Papelera de reciclaje</a> <span class="divider">/</span></li>
    <li class="active">Fotos</li>
</ul>
<div class="header">
	<h2>Fotos</h2>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Autor</th>
			<th>Título</th>
			<th>Fecha</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$fotos"}
		<tr>
			<td><a href="{#SITE_URL#}/perfil/index/{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}">{$value.titulo}</a></td>
			<td>{$value.creacion->fuzzy()}</td>
			<td>
				<div class="btn-group">
					<a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html" class="btn btn-mini btn-info show-tooltip" title="Ver foto"><i class="icon-white icon-eye-close"></i></a>
					<a href="{#SITE_URL#}/foto/editar/{$value.id}" class="btn btn-mini btn-primary show-tooltip" title="Editar foto"><i class="icon-white icon-pencil"></i></a>
					<a href="{#SITE_URL#}/moderar/papelera/restaurar_foto/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Restaurar foto"><i class="icon-white icon-refresh"></i></a>
					<a href="{#SITE_URL#}/moderar/papelera/borrar_foto/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar foto"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay fotos en la papelera!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}