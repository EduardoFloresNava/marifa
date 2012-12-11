<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li class="active">Contenido</li>
</ul>
<div class="header">
	<h2>Contenido</h2>
</div>
<div class="row-fluid statistics">
	<div class="span4">
		<h2 class="title">Posts</h2>
		<ul>
			<li>Activos: <span class="badge badge-success pull-right">{$post_estado.activo}</span></li>
			<li>En revisi&oacute;n: <span class="badge badge-info pull-right">{$post_estado.pendiente}</span></li>
			<li>Borrados: <span class="badge badge-important pull-right">{$post_estado.borrado}</span></li>
			<li>Rechazados: <span class="badge badge-warning pull-right">{$post_estado.rechazado}</span></li>
			<li>Ocultos: <span class="badge badge-warning pull-right">{$post_estado.oculto}</span></li>
			<li class="total">Total: <span class="badge pull-right">{$post_estado.total}</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2 class="title">Comentarios posts</h2>
		<ul>
			<li>Visibles: <span class="badge badge-success pull-right">{$post_comentarios_estado.visible}</span></li>
			<li>Ocultos: <span class="badge badge-warning pull-right">{$post_comentarios_estado.oculto}</span></li>
			<li>Borrados: <span class="badge badge-important pull-right">{$post_comentarios_estado.borrado}</span></li>
			<li class="total">Total: <span class="badge pull-right">{$post_comentarios_estado.total}</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2 class="title">Posts categor&iacute;a</h2>
		{if="count($posts_categorias) > 0"}
		<ul>
			{loop="$posts_categorias"}
			<li>{$key}: <span class="badge pull-right">{$value}</span></li>
			{/loop}
		</ul>
		{else}
		<div class="alert">No hay posts a&uacute;n.</div>
		{/if}
	</div>
</div>
<div class="row-fluid statistics">
	<div class="span4">
		<h2 class="title">Fotos</h2>
		<ul>
			<li>Activas: <span class="badge badge-success pull-right">{$foto_estado.activa}</span></li>
			<li>Ocultas: <span class="badge badge-warning pull-right">{$foto_estado.oculta}</span></li>
			<li>Papelera: <span class="badge badge-warning pull-right">{$foto_estado.papelera}</span></li>
			<li>Borradas: <span class="badge badge-important pull-right">{$foto_estado.borrada}</span></li>
			<li class="total">Total: <span class="badge pull-right">{$foto_estado.total}</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2 class="title">Comentarios fotos</h2>
		<ul>
			<li>Visibles: <span class="badge badge-success pull-right">{$foto_comentarios_estado.visible}</span></li>
			<li>Ocultos: <span class="badge badge-warning pull-right">{$foto_comentarios_estado.oculto}</span></li>
			<li>Borrados: <span class="badge badge-important pull-right">{$foto_comentarios_estado.borrado}</span></li>
			<li class="total">Total: <span class="badge pull-right">{$foto_comentarios_estado.total}</span></li>
		</ul>
	</div>
	<div class="span4">
		<h2 class="title">Fotos por categor&iacute;a</h2>
		{if="count($fotos_categorias) > 0"}
		<ul>
			{loop="$fotos_categorias"}
			<li>{$key}: <span class="badge pull-right">{$value}</span></li>
			{/loop}
		</ul>
		{else}
		<div class="alert">No hay fotos aún.</div>
		{/if}
	</div>
</div>