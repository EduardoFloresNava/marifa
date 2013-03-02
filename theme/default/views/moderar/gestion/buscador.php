<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/moderar/">Moderación</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/moderar/gestion/">Gestión</a> <span class="divider">/</span></li>
    <li class="active">Búsqueda avanzada</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Búsqueda avanzada</h2>
	<div class="pull-right">
		<form method="POST" class="form-inline" action="">
			<input type="text" name="query" id="query" value="{$query}" class="input-small" placeholder="Buscar..." title="Búsqueda de usuarios, posts, fotos, publicaciones y comentarios." />
			<select id="find" name="find" class="input-small">
				<option value="0"{if="$find == 0"} selected{/if}>Todos</option>
				<option value="1"{if="$find == 1"} selected{/if}>Usuarios</option>
				<option value="2"{if="$find == 2"} selected{/if}>Post's</option>
				<option value="3"{if="$find == 3"} selected{/if}>Comentarios en post's</option>
				<option value="4"{if="$find == 4"} selected{/if}>Fotos</option>
				<option value="5"{if="$find == 5"} selected{/if}>Comentarios en fotos</option>
			</select>
			<button type="submit" class="btn btn-primary">Buscar</button>
		</form>
	</div>
</div>
{if="Request::method() != 'POST'"}<div class="alert alert-info">Introduzca el texto a buscar. Recuerde que es más óptimo si busca un tipo de elemento en particular.</div>{/if}
{if="isset($usuarios) && count($usuarios) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">Usuarios</th>
		</tr>
		<tr>
			<th>ID</th>
			<th>Nick</th>
			<th>E-Mail</th>
		</tr>
	</thead>
	<tbody>
		{loop="$usuarios"}
		<tr>
			<td>{$value.id}</td>
			<td><a href="{#SITE_URL#}/@{$value.nick}">{$value.nick}</a></td>
			<td>{$value.email}</td>
		</tr>
		{/loop}
	</tbody>
</table>
{/if}
{if="isset($posts) && count($posts) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">Posts</th>
		</tr>
		<tr>
			<th>ID</th>
			<th>Título</th>
			<th>Autor</th>
		</tr>
	</thead>
	<tbody>
		{loop="$posts"}
		<tr>
			<td>{$value.id}</td>
			<td><a href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a></td>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
		</tr>
		{/loop}
	</tbody>
</table>
{/if}
{if="isset($post_comentarios) && count($post_comentarios) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">Comentarios en posts</th>
		</tr>
		<tr>
			<th>ID</th>
			<th>Post</th>
			<th>Autor</th>
		</tr>
	</thead>
	<tbody>
		{loop="$post_comentarios"}
		<tr>
			<td><a href="{#SITE_URL#}/post/{$value.post.categoria.seo}/{$value.post.id}/{$value.post.titulo|Texto::make_seo}.html#c-{$value.id}">{$value.id}</td>
			<td><a href="{#SITE_URL#}/post/{$value.post.categoria.seo}/{$value.post.id}/{$value.post.titulo|Texto::make_seo}.html">{$value.post.titulo}</td>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
		</tr>
		{/loop}
	</tbody>
</table>
{/if}
{if="isset($fotos) && count($fotos) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">Fotos</th>
		</tr>
		<tr>
			<th>ID</th>
			<th>Título</th>
			<th>Autor</th>
		</tr>
	</thead>
	<tbody>
		{loop="$fotos"}
		<tr>
			<td>{$value.id}</td>
			<td><a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">{$value.titulo}</a></td>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
		</tr>
		{/loop}
	</tbody>
</table>
{/if}
{if="isset($foto_comentarios) && count($foto_comentarios) > 0"}
<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="3">Comentarios en fotos</th>
		</tr>
		<tr>
			<th>ID</th>
			<th>Post</th>
			<th>Autor</th>
		</tr>
	</thead>
	<tbody>
		{loop="$foto_comentarios"}
		<tr>
			<td><a href="{#SITE_URL#}/foto/{$value.foto.categoria.seo}/{$value.foto.id}/{$value.foto.titulo|Texto::make_seo}.html#c-{$value.id}">{$value.id}</a></td>
			<td><a href="{#SITE_URL#}/foto/{$value.foto.categoria.seo}/{$value.foto.id}/{$value.foto.titulo|Texto::make_seo}.html">{$value.foto.titulo}</a></td>
			<td><a href="{#SITE_URL#}/@{$value.usuario.nick}">{$value.usuario.nick}</a></td>
		</tr>
		{/loop}
	</tbody>
</table>
{/if}