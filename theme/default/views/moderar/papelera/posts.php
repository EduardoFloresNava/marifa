<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">Moderaci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/papelera/">Papelera de reciclaje</a> <span class="divider">/</span></li>
    <li class="active">Posts</li>
</ul>
<div class="header">
	<h2>Posts</h2>
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
		{loop="$posts"}
		<tr>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
			<td><a href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a></td>
			<td>{$value.fecha->fuzzy()}</td>
			<td>
				<div class="btn-group">
					<a href="{#SITE_URL#}/post/editar/{$value.id}" class="btn btn-mini btn-primary show-tooltip" title="Editar post"><i class="icon-white icon-pencil"></i></a>
					<a href="{#SITE_URL#}/moderar/papelera/restaurar_post/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Restaurar post"><i class="icon-white icon-refresh"></i></a>
					<a href="{#SITE_URL#}/moderar/papelera/borrar_post/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Borrar post"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay post en la papelera!</td>
		</tr>
		{/loop}
	</tbody>
</table>
{$paginacion}