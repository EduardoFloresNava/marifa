<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/contenido/">Contenido</a> <span class="divider">/</span></li>
    <li class="active">Categorias</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Categorias</h2>
	<div class="pull-right">
		<a href="/admin/contenido/agregar_categoria/" class="btn btn-success"><i class="icon-white icon-plus"></i> Agregar</a>
	</div>
</div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Imagen</th>
			<th>Nombre</th>
			<th>Acciones</th>
		</tr>
	</thead>
	<tbody>
		{loop="$categorias"}
		<tr>
			<td><img src="{#THEME_URL#}/assets/img/categoria/{$value.imagen}" /></td>
			<td>{$value.nombre}</td>
			<td style="text-align: center;">
				<div class="btn-group">
					<a href="/admin/contenido/editar_categoria/{$value.id}" class="btn btn-mini btn-success">Editar</a>
					<a href="/admin/contenido/eliminar_categoria/{$value.id}" class="btn btn-mini btn-danger">Eliminar</a>
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td class="alert" colspan="5">&iexcl;No hay categorias!</td>
		</tr>
		{/loop}
	</tbody>
</table>