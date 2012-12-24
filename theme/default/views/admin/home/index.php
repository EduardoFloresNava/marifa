<div class="header">
	<h2>Bienvenido al centro de administraci&oacute;n de Marifa.</h2>
</div>
<div class="row-fluid statistics">
	<div class="span4">
		<h3 class="title">Contenido<small>{function="count($contenido)"} de {$contenido_total}</small></h3>
		{if="count($contenido) > 0"}
		<ul>
			{loop="$contenido"}
			<li>
				{if="$value.tipo == 'post'"}
				<a href="{#SITE_URL#}/post/index/{$value.id}/">
					<i class="icon icon-book"></i>
					{function="Texto::limit_chars($value.titulo, 35, '...', TRUE)"}
						{if="$value.estado == 0"}
					<span class="label pull-right label-success">ACTIVO</span>
						{elseif="$value.estado == 1"}
					<span class="label pull-right label-info">BORRADOR</span>
						{elseif="$value.estado == 2"}
					<span class="label pull-right label-important">BORRADO</span>
						{elseif="$value.estado == 3"}
					<span class="label pull-right label-inverse">PENDIENTE</span>
						{elseif="$value.estado == 4"}
					<span class="label pull-right label-warning">OCULTO</span>
						{elseif="$value.estado == 5"}
					<span class="label pull-right label-warning">RECHAZADO</span>
						{elseif="$value.estado == 6"}
					<span class="label pull-right label-inverse">PAPELERA</span>
						{else}
					<span class="label pull-right label-important">DESCONOCIDO</span>
						{/if}
				</a>
				{else}
				<a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
					<i class="icon icon-picture"></i>
					{function="Texto::limit_chars($value.titulo, 35, '...', TRUE)"}
						{if="$value.estado == 0"}
					<span class="label pull-right label-success">ACTIVA</span>
						{elseif="$value.estado == 1"}
					<span class="label pull-right label-info">OCULTA</span>
						{elseif="$value.estado == 2"}
					<span class="label pull-right label-warning">PAPELERA</span>
						{elseif="$value.estado == 3"}
					<span class="label pull-right label-important">BORRADA</span>
						{else}
					<span class="label pull-right label-important">DESCONOCIDO</span>
						{/if}
				</a>
				{/if}
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">No hay contenido que mostrar.</div>
		{/if}
		<!--INFORME DE CONTENIDO: Cantidad posts, fotos, usuarios, comentario, etc.-->
	</div>
	<div class="span4">
		<h3 class="title">Usuarios <span class="pull-right"><small>{function="count($usuarios)"} de {$usuarios_total}</small></span></h3>
		{if="count($usuarios) > 0"}
		<ul>
			{loop="$usuarios"}
			<li>
				<a href="{#SITE_URL#}/perfil/index/{$value.nick}">
					{$value.nick}
					<span class="pull-right label label-{if="$value.estado == 0"}info">PENDIENTE{elseif="$value.estado == 1"}success">ACTIVO{elseif="$value.estado == 2"}warning">SUSPENDIDO{elseif="$value.estado == 3"}important">BANEADO{/if}</span>
				</a>
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">
			A&uacute;n no hay usuarios.
		</div>
		{/if}
		<!--Ultimos usuarios.-->
	</div>
	<div class="span4">
		<h3 class="title">Actualizaciones <span class="pull-right"><small>v{#VERSION}</small></span></h3>
		{if="isset($version)"}
		<div class="version-info">
			Ultima versi&oacute;n disponible:
			<div class="pull-right">
				{if="$version_new"}
				<div class="btn-group" style="display: inline-block;">
					<a href="{$download.zip}" class="btn btn-mini">ZIP</a>
					<a href="{$download.tar}" class="btn btn-mini">TAR.GZ</a>
				</div>
				&nbsp;<span class="label label-info pull-right">{$version}</span>
				{else}
				<span class="label label-info">{$version}</span>
				{/if}
			</div>
		</div>
		{/if}
		<!--Listado de plugins.-->
	</div>
</div>
