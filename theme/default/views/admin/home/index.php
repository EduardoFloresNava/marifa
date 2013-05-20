<div class="header">
	<h2{@Bienvenido al centro de administración de Marifa.@}</h2>
</div>
<div class="row-fluid statistics">
	<div class="span4">
		<h3 class="title">{@Contenido@}<small>{function="count($contenido)"} {@de@} {$contenido_total}</small></h3>
		{if="count($contenido) > 0"}
		<ul>
			{loop="$contenido"}
			<li>
				{if="$value.tipo == 'post'"}
				<a href="{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
					<i class="icon icon-book"></i>
					{function="Texto::limit_chars($value.titulo, 27, '...', FALSE)"}
						{if="$value.estado == 0"}
					<span class="label pull-right label-success">{@ACTIVO@}</span>
						{elseif="$value.estado == 1"}
					<span class="label pull-right label-info">{@BORRADOR@}</span>
						{elseif="$value.estado == 2"}
					<span class="label pull-right label-important">{@BORRADO@}</span>
						{elseif="$value.estado == 3"}
					<span class="label pull-right label-inverse">{@PENDIENTE@}</span>
						{elseif="$value.estado == 4"}
					<span class="label pull-right label-warning">{@OCULTO@}</span>
						{elseif="$value.estado == 5"}
					<span class="label pull-right label-warning">{@RECHAZADO@}</span>
						{elseif="$value.estado == 6"}
					<span class="label pull-right label-inverse">{@PAPELERA@}</span>
						{else}
					<span class="label pull-right label-important">{@DESCONOCIDO@}</span>
						{/if}
				</a>
				{else}
				<a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html">
					<i class="icon icon-picture"></i>
					{function="Texto::limit_chars($value.titulo, 27, '...', TRUE)"}
						{if="$value.estado == 0"}
					<span class="label pull-right label-success">{@ACTIVA@}</span>
						{elseif="$value.estado == 1"}
					<span class="label pull-right label-info">{@OCULTA@}</span>
						{elseif="$value.estado == 2"}
					<span class="label pull-right label-warning">{@PAPELERA@}</span>
						{elseif="$value.estado == 3"}
					<span class="label pull-right label-important">{@BORRADA@}</span>
						{else}
					<span class="label pull-right label-important">{@DESCONOCIDO@}</span>
						{/if}
				</a>
				{/if}
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">{@¡No hay contenido que mostrar!@}</div>
		{/if}
	</div>
	<div class="span4">
		<h3 class="title">{@Usuarios@} <span class="pull-right"><small>{function="count($usuarios)"} {@de@} {$usuarios_total}</small></span></h3>
		{if="count($usuarios) > 0"}
		<ul>
			{loop="$usuarios"}
			<li>
				<a href="{#SITE_URL#}/@{$value.nick}">
					{$value.nick}
					<span class="pull-right label label-{if="$value.estado == 0"}info">{@PENDIENTE@}{elseif="$value.estado == 1"}success">{@ACTIVO@}{elseif="$value.estado == 2"}warning">{@SUSPENDIDO@}{elseif="$value.estado == 3"}important">{@BANEADO@}{/if}</span>
				</a>
			</li>
			{/loop}
		</ul>
		{else}
		<div class="alert alert-info">
			{@¡Aún no hay usuarios!@}
		</div>
		{/if}
		<!--Ultimos usuarios.-->
	</div>
	<div class="span4">
		<h3 class="title">
			{@Estado CronJobs@}
			{if="$cronjob_lastexecution === NULL"}
			<span class="label label-important pull-right" title="{@No se ha realizado ninguna ejecución de las tareas programadas.@}">{@INACTIVO@}</span>
			{elseif="$cronjob_lastexecution < time() - 3600"}
			<span class="label label-warning pull-right" title="{@La última ejecución fue @}{function="Fechahora::createFromTimestamp($cronjob_lastexecution)->fuzzy()"}">{@RETRASADO@}</span>
			{else}
			<span class="label label-success pull-right" title="{@La última ejecución fue @}{function="Fechahora::createFromTimestamp($cronjob_lastexecution)->fuzzy()"}">{@ACTIVO@}</span>
			{/if}
		</h3>
		<ul>
			<li>
				{@Cola de correos:@}
				{if="$email_queue_use"}
				<span class="label label-info pull-right">{@ACTIVA@}</span>
				{else}
				<span class="label label-inverse pull-right">{@INACTIVA@}</span>
				{/if}
			</li>
			{if="$email_queue_pending !== 0 || $email_queue_use"}
			<li>{@Correos en espera:@} <span class="badge pull-right {if="$email_queue_pending == 0"}badge-success{elseif="$email_queue_pending < 10"}badge-info{elseif="$email_queue_pending < 20"}badge-warning{else}badge-important{/if}">{$email_queue_pending}</span></li>
			{/if}
		</ul>
		<!--Listado de plugins.-->
	</div>
</div>
