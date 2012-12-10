<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">Moderaci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/">Denuncias</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/denuncias/posts/">Posts</a> <span class="divider">/</span></li>
    <li class="active">Detalles</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Detalles de la denuncia #{$denuncia.id}</h2>
	<div class="pull-right btn-group">
		<a href="{#SITE_URL#}/moderar/denuncias/posts/" class="btn">Volver</a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>Denunciante</th>
		<td><a href="{#SITE_URL#}/perfil/index/{$denunciante.nick}">{$denunciante.nick}</a></td>
	</tr>
	<tr>
		<th>Post</th>
		<td><a href="{#SITE_URL#}/post/index/{$post.id}">{$post.titulo}</a></td>
	</tr>
	<tr>
		<th>Fecha</th>
		<td>{$denuncia.fecha->fuzzy()}</td>
	</tr>
	<tr>
		<th>Motivo</th>
		<td>
			{if="$denuncia.motivo == 0"}
			<span class="label">RE-POST</span>
			{elseif="$denuncia.motivo == 1"}
			<span class="label">SPAM</span>
			{elseif="$denuncia.motivo == 2"}
			<span class="label">Links muertos</span>
			{elseif="$denuncia.motivo == 3"}
			<span class="label">Irrespetuoso</span>
			{elseif="$denuncia.motivo == 4"}
			<span class="label">Información personal</span>
			{elseif="$denuncia.motivo == 5"}
			<span class="label">Titulo mayúscula</span>
			{elseif="$denuncia.motivo == 6"}
			<span class="label">Pedofilia</span>
			{elseif="$denuncia.motivo == 7"}
			<span class="label">Asqueroso</span>
			{elseif="$denuncia.motivo == 8"}
			<span class="label">Fuente</span>
			{elseif="$denuncia.motivo == 9"}
			<span class="label">Pobre</span>
			{elseif="$denuncia.motivo == 10"}
			<span class="label">Foro</span>
			{elseif="$denuncia.motivo == 11"}
			<span class="label">Protocolo</span>
			{elseif="$denuncia.motivo == 12"}
			<span class="label">Personalizada</span>
			{else}
			<span class="label label-important">TIPO SIN DEFINIR</span>
			{/if}
		</td>
	</tr>{if="$denuncia.motivo == 12"}
	<tr>
		<th>Comentario de la denuncia</th>
		<td>
			{$denuncia.comentario}
		</td>
	</tr>
	{/if}
</table>