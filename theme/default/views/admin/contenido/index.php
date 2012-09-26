<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Contenido</li>
</ul>
<div class="header">
	<h2>Contenido</h2>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
<div class="row-fluid">
	<div class="span4">
		<h2>Posts</h2>
		<ul>
			<li>Total: <span class="badge pull-right">{$posts_total}</span></li>
			<li>Activos: <span class="badge pull-right">{$posts_activos}</span></li>
			<li>Borrados: <span class="badge pull-right">{$posts_borrados}</span></li>
			<li>En revisión: <span class="badge pull-right">{$posts_revision}</span></li>
			<li>Esperando corrección: <span class="badge pull-right">{$posts_correccion}</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2>Comentarios posts</h2>
		<ul>
			<li>Total: <span class="badge pull-right">??</span></li>
			<li>Activos: <span class="badge pull-right">??</span></li>
			<li>Borrados: <span class="badge pull-right">??</span></li>
			<li>En revisión: <span class="badge pull-right">??</span></li>
			<li>Esperando corrección: <span class="badge pull-right">??</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2>Posts activos por categoría</h2>
		<ul>
			{loop="$posts_categorias"}
			<li>{$value.categoria.nombre}: <span class="badge pull-right">{$value.cantidad}</span></li>
			{/loop}
		</ul>
	</div>
</div>
<div class="row-fluid">
	<div class="span4">
		<h2>Fotos</h2>
		<ul>
			<li>Total: <span class="badge pull-right">{$fotos_total}</span></li>
			<li>Activos: <span class="badge pull-right">{$fotos_activas}</span></li>
			<li>Ocultos: <span class="badge pull-right">{$fotos_ocultas}</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2>Comentario fotos</h2>
		<ul>
			<li>Total: <span class="badge pull-right">??</span></li>
			<li>Activos: <span class="badge pull-right">??</span></li>
			<li>Borrados: <span class="badge pull-right">??</span></li>
			<li>En revisión: <span class="badge pull-right">??</span></li>
			<li>Esperando corrección: <span class="badge pull-right">??</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2>Fotos por categoría</h2>
		<ul>
			{loop="$fotos_categorias"}
			<li>{$value.categoria.nombre}: <span class="badge pull-right">{$value.cantidad}</span></li>
			{/loop}
		</ul>
	</div>
</div>