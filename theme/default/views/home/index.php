<div class="row">
	<div class="span7">
		<h3>&Uacute;ltimos posts</h3>
		{loop="$ultimos_posts"}
		<div>
			<img style="float: left;" src="{#THEME_URL#}/assets/img/categoria/{$value.categoria.imagen}" />
			<div style="margin-left: 30px;">
				<p><a href="/post/index/{$value.id}/">{$value.titulo}</a></p>
				<p>{$value.fecha->fuzzy()} - <a href="/perfil/informacion/{$value.usuario.nick}">@{$value.usuario.nick}</a> - {@Puntos@} {$value.puntos} - {@Comentarios@} {$value.comentarios}<span class="pull-right">{$value.categoria.nombre}</span></p>
			</div>
		</div>
		{/loop}
	</div>
	<div class="span3">
		<div>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#">Posts</a></li>
				<li><a href="#">Comunidades</a></li>
				<li><a href="#">Temas</a></li>
			</ul>
			<div class="row-fluid">
				<form>
					<div class="input-prepend">
						<input type="text" class="span8">
						<button type="submit" class="btn span4">Search</button>
					</div>
				</form>
			</div>
			<a href="#">Opciones</a>
		</div>
		<div>
			<div class="row-fluid">
				<div class="span6">{$cantidad_usuarios_online} {@Usuarios Online@}</div>
				<div class="span6">{$cantidad_usuarios} {@Miembros@}</div>
			</div>
			<div class="row-fluid">
				<div class="span6">{$cantidad_posts} {@Posts@}</div>
				<div class="span6">{$cantidad_comentarios_posts} {@Comentarios@}</div>
			</div>
			<div class="row-fluid">
				<div class="span6">{$cantidad_fotos} {@Fotos@}</div>
				<div class="span6">{$cantidad_comentarios_fotos} {@Comentarios en fotos@}</div>
			</div>
			<div class="row-fluid">
				<div class="span6">???? {@Comunidades@}</div>
				<div class="span6">???? {@Posts comunidades@}</div>
			</div>
		</div>
		<div>
			<h3>&Uacute;ltimos comentarios</h3>
			<ol>
			{loop="$ultimos_comentarios"}
				<li>
					<b><a href="/perfil/informacion/{$value.usuario.nick}">{$value.usuario.nick}</a></b> <a href="/post/index/{$value.post.id}">{$value.post.titulo}</a>
				</li>
			{/loop}
			</ol>
		</div>
		<div>
			<h3>TOPs posts</h3>
			<ol>
			{loop="$top_posts"}
				<li><a href="/post/index/{$value.id}">{$value.titulo}<span class="badge pull-right">{$value.puntos}</a></li>
			{/loop}
			</ol>
		</div>
		<div>
			<h3>TOPs usuarios</h3>
			<ol>
			{loop="$usuario_top"}
				<li><a href="/perfil/index/{$value.nick}">{$value.nick}<span class="badge pull-right">{$value.puntos}</a></li>
			{/loop}
			</ol>
		</div>
	</div>
	<div class="span2 hidden-phone">
		<div>
			<h3>&Uacute;ltimas fotos</h3>
			{if="isset($ultimas_fotos[0])"}
			<!--170x150-->
			<a href="/foto/ver/{$ultimas_fotos.0.id}">
				<div class="thumbnail" style="min-height: 50px;">
					<img src="{$ultimas_fotos.0.url}" />
				</div>
			</a>
			{/if}
		</div>
		<div>
			<h3>Afiliados</h3>
			<div class="thumbnail">
				<img src="http://placehold.it/170x50" />
			</div>
			<div class="thumbnail">
				<img src="http://placehold.it/170x50" />
			</div>
			<div class="thumbnail">
				<img src="http://placehold.it/170x50" />
			</div>
			<a href="#">Afiliate a Marifa</a>
		</div>
	</div>
</div>