<ul class="breadcrumb">
    <li><a href="/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="/moderar/denuncias/">Denuncias</a> <span class="divider">/</span></li>
    <li><a href="/moderar/denuncias/posts/">fotos</a> <span class="divider">/</span></li>
    <li class="active">Detalles</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Detalles de la denuncia #{$denuncia.id}</h2>
	<div class="pull-right btn-group">
		<a href="/moderar/denuncias/fotos/" class="btn">Volver</a>
	</div>
</div>
<table class="table table-bordered">
	<tr>
		<th>Denunciante</th>
		<td><a href="/perfil/index/{$denunciante.nick}">{$denunciante.nick}</a></td>
	</tr>
	<tr>
		<th>Foto</th>
		<td><a href="/post/index/{$foto.id}">{$foto.titulo}</a></td>
	</tr>
	<tr>
		<th>Fecha</th>
		<td>{$denuncia.fecha->fuzzy()}</td>
	</tr>
	<tr>
		<th>Motivo</th>
		<td>
			{if="$denuncia.motivo == 0"}
			<span class="label">EXISTE</span>
			{elseif="$denuncia.motivo == 1"}
			<span class="label">SPAM</span>
			{elseif="$denuncia.motivo == 2"}
			<span class="label">CAIDA</span>
			{elseif="$denuncia.motivo == 3"}
			<span class="label">IRRESPETUOSA</span>
			{elseif="$denuncia.motivo == 4"}
			<span class="label">INFORMACION PERSONAL</span>
			{elseif="$denuncia.motivo == 5"}
			<span class="label">PEDOFILIA</span>
			{elseif="$denuncia.motivo == 6"}
			<span class="label">ASQUEROSA</span>
			{elseif="$denuncia.motivo == 7"}
			<span class="label">PERSONALIZADA</span>
			{else}
			<span class="label label-important">TIPO SIN DEFINIR</span>
			{/if}
		</td>
	</tr>{if="$denuncia.motivo == 7"}
	<tr>
		<th>Comentario de la denuncia</th>
		<td>
			{$denuncia.comentario}
		</td>
	</tr>
	{/if}
</table>