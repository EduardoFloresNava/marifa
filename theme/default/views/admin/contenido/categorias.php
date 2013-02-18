<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Categorías</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Categorías</h2>
	<div class="pull-right">
		<a href="{#SITE_URL#}/admin/contenido/agregar_categoria/" class="btn btn-success"><i class="icon-white icon-plus"></i> Agregar</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Imagen</th>
			<th>Nombre</th>
			<th>SEO</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$categorias"}
		<tr>
			<td><img src="{#THEME_URL#}/assets/img/categoria/{$value.imagen}" /></td>
			<td>{$value.nombre}</td>
			<td>{$value.seo}</td>
			<td>
				<div class="btn-group">
					<a href="{#SITE_URL#}/admin/contenido/editar_categoria/{$value.id}" class="btn btn-mini btn-success show-tooltip" title="Editar"><i class="icon-white icon-pencil"></i></a>
					<a href="{#SITE_URL#}/admin/contenido/eliminar_categoria/{$value.id}" class="btn btn-mini btn-danger show-tooltip" title="Eliminar"><i class="icon-white icon-remove"></i></a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="3">!No hay categorías!</td>
		</tr>
		{/loop}
	</tbody>
</table>