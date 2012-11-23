<div class="header">
	<h2>Bienvenido al centro de moderación de Marifa.</h2>
</div>
<div class="row-fluid">
	<div class="span6">
		<h3 class="title">Denuncias</h3>
		{if="count($denuncias) > 0"}
			<ul>
				{loop="$denuncias"}
				<li>
					{if="$value.tipo == 'post'"}
					<a href="/moderar/denuncias/detalle_post/{$value.id}">
						<i class="icon icon-book"></i>
						{$value.post.titulo}
						<div class="pull-right">
							{if="$value.motivo == 0"}
						<span class="label">RE-POST</span>
							{elseif="$value.motivo == 1"}
						<span class="label">SPAM</span>
							{elseif="$value.motivo == 2"}
						<span class="label">Links muertos</span>
							{elseif="$value.motivo == 3"}
						<span class="label">Irrespetuoso</span>
							{elseif="$value.motivo == 4"}
						<span class="label">Información personal</span>
							{elseif="$value.motivo == 5"}
						<span class="label">Titulo mayúscula</span>
							{elseif="$value.motivo == 6"}
						<span class="label">Pedofilia</span>
							{elseif="$value.motivo == 7"}
						<span class="label">Asqueroso</span>
							{elseif="$value.motivo == 8"}
						<span class="label">Fuente</span>
							{elseif="$value.motivo == 9"}
						<span class="label">Pobre</span>
							{elseif="$value.motivo == 10"}
						<span class="label">Foro</span>
							{elseif="$value.motivo == 11"}
						<span class="label">Protocolo</span>
							{elseif="$value.motivo == 12"}
						<span class="label">Personalizada</span>
							{else}
						<span class="label label-important">TIPO SIN DEFINIR</span>
							{/if}
						</div>
					</a>
					{elseif="$value.tipo == 'foto'"}
					<a href="/moderar/denuncias/detalle_foto/{$value.id}">
						<i class="icon icon-picture"></i>
						{$value.foto.titulo}
						<div class="pull-right">
							{if="$value.motivo == 0"}
						<span class="label">EXISTE</span>
							{elseif="$value.motivo == 1"}
						<span class="label">SPAM</span>
							{elseif="$value.motivo == 2"}
						<span class="label">CAIDA</span>
							{elseif="$value.motivo == 3"}
						<span class="label">IRRESPETUOSA</span>
							{elseif="$value.motivo == 4"}
						<span class="label">INFORMACION PERSONAL</span>
							{elseif="$value.motivo == 5"}
						<span class="label">PEDOFILIA</span>
							{elseif="$value.motivo == 6"}
						<span class="label">ASQUEROSA</span>
							{elseif="$value.motivo == 7"}
						<span class="label">PERSONALIZADA</span>
							{else}
						<span class="label label-important">TIPO SIN DEFINIR</span>
							{/if}
						</div>
					</a>
					{else}
					<a href="/moderar/denuncias/detalle_usuario/{$value.id}">
						<i class="icon icon-user"></i>
						{$value.denunciado.nick}
						<div class="pull-right">
							{if="$value.motivo == 0"}
						<span class="label">EXISTE</span>
							{elseif="$value.motivo == 1"}
						<span class="label">SPAM</span>
							{elseif="$value.motivo == 2"}
						<span class="label">CAIDA</span>
							{elseif="$value.motivo == 3"}
						<span class="label">IRRESPETUOSA</span>
							{elseif="$value.motivo == 4"}
						<span class="label">INFORMACION PERSONAL</span>
							{elseif="$value.motivo == 5"}
						<span class="label">PEDOFILIA</span>
							{elseif="$value.motivo == 6"}
						<span class="label">ASQUEROSA</span>
							{elseif="$value.motivo == 7"}
						<span class="label">PERSONALIZADA</span>
							{else}
						<span class="label label-important">TIPO SIN DEFINIR</span>
							{/if}
						</div>
					</a>
					{/if}
				</li>
				{/loop}
			</ul>
		{else}
		<div class="alert alert-success">
			No hay denuncias pendientes.
		</div>
		{/if}
	</div>
	<div class="span6">
		<h3 class="title">Contenido pendiente</h3>
		{if="count($contenido) > 0"}
			<ul>
				{loop="$contenido"}
				<li>
					{if="$value.tipo == 'post'"}
					<a href="/post/index/{$value.id}">
						<i class="icon icon-book"></i>
						<b>{$value.titulo}</b> por <b>{$value.usuario.nick}</b>
						<span class="pull-right">{$value.fecha->fuzzy()}</span>
					</a>
					{elseif="$value.tipo == 'foto_comentario'"}
					<a href="/foto/ver/{$value.foto.id}">
						<i class="icon icon-comment"></i>
						<b>{$value.usuario.nick}</b> en la foto <b>{$value.foto.titulo}</b>
						<span class="pull-right">{$value.fecha->fuzzy()}</span>
					</a>
					{else}
					<a href="/post/index/{$value.post.id}">
						<i class="icon icon-user"></i>
						<b>{$value.usuario.nick}</b> en el post <b>{$value.post.titulo}</b>
						<span class="pull-right">{$value.fecha->fuzzy()}</span>
					</a>
					{/if}
				</li>
				{/loop}
			</ul>
		{else}
		<div class="alert alert-success">
			No hay contenido esperando ser aprobado.
		</div>
		{/if}
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<h3 class="title">Historial administración</h3>
		{loop="$sucesos"}
		{else}
		<div class="alert alert-info">
			No hay acciones en el historial a&uacute;n.
		</div>
		{/loop}
	</div>
</div>
