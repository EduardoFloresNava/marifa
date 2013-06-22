<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/configuracion/">Configuración</a> <span class="divider">/</span></li>
    <li class="active">Modo Mantenimiento</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Modo mantenimiento</h2>
	<div class="pull-right btn-group">
		{if="$is_locked_hard || $is_locked_soft"}
		<a href="{#SITE_URL#}/admin/configuracion/habilitar_mantenimiento/0" class="btn btn-small btn-danger"><i class="icon-white icon-off"></i> Deshabilitar</a>
		{else}
		<a href="{#SITE_URL#}/admin/configuracion/habilitar_mantenimiento/1/1" class="btn btn-small btn-success"><i class="icon-white icon-ok"></i> Habilitar por IP</a>
		<a href="{#SITE_URL#}/admin/configuracion/habilitar_mantenimiento/1/0" class="btn btn-small btn-success"><i class="icon-white icon-ok"></i> Habilitar por usuario</a>
		{/if}
	</div>
</div>
<form method="POST" class="form-horizontal" action="">
	<fieldset>
		{if="$is_locked_hard"}
		<legend class="clearfix">
			<span class="pull-left">HardLock <small>Modo mantenimiento por IP</small></span>
			<span class="label label-success pull-left">ACTIVO</span>
		</legend>
		{else}
		<legend class="clearfix">HardLock <small>Modo mantenimiento por IP</small></legend>
		{/if}
{if="$locked_for_me_ip"}<div class="alert alert-danger"><b>¡Alerta!</b> Si el sitio entra en modo mantenimiento por IP no podrás acceder a él. Recomendamos agregar tu IP: <code>{function="get_ip_addr()"}</code></div>{/if}
		<div class="control-group{if="$error_ip"} error{elseif="$success_ip"} success{/if}">
			<label class="control-label" for="ip">Nombre del sitio:</label>
			<div class="controls">
				<textarea name="ip" class="input-xxlarge" id="ip">{$ip}</textarea>
				<span class="help-block">{if="$error_ip"}{$error_ip}{elseif="$success_ip"}{$success_ip}{else}Listado de IP's que pueden entrar cuando el sitio se encuentra en modo mantenimiento.{/if}</span>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
		</div>
	</fieldset>
{if="$locked_for_me_usuario"}<div class="alert alert-danger"><b>¡Alerta!</b> Si el sitio entra en modo mantenimiento por usuario no podrás acceder a él. Recomendamos agregar tu rango o tu nick.</div>{/if}
	<fieldset>
		{if="$is_locked_soft"}
		<legend class="clearfix">
			<span class="pull-left">SoftLock <small>Modo mantenimiento por Usuario</small></span>
			<span class="label label-success pull-left">ACTIVO</span>
		</legend>
		{else}
		<legend class="clearfix">SoftLock <small>Modo mantenimiento por Usuario</small></legend>
		{/if}

		{if="isset($error_rango_nuevo)"}<div class="alert">{$error_rango_nuevo}</div>{/if}
		<div class="control-group">
			<label class="control-label" for="group-list">Rangos con acceso:</label>
			<div class="controls">
				{loop="$rangos"}
				<div class="control-element">
					<input type="text" value="{$value.nombre}" disabled />
					<a class="btn btn-danger" href="{#SITE_URL#}/admin/configuracion/mantenimiento_quitar_rango/{$value.id}" title="Quitar rango"><i class="icon-white icon-remove"></i></a>
				</div>
				{/loop}
				<div class="control-element">
					<select name="nuevo-rango" id="nuevo-rango" title="Rango a agregar">
						<option value="">Rango...</option>
						{loop="$rangos_disponibles"}
						<option value="{$value.id}">{$value.nombre}</option>
						{/loop}
					</select>
					<button type="submit" class="btn btn-success" name="submit" value="agregar-rango" title="Agregar rango"><i class="icon-white icon-plus"></i></button>
				</div>
			</div>
		</div>

		{if="isset($error_usuario_nuevo)"}<div class="alert">{$error_usuario_nuevo}</div>{/if}
		<div class="control-group">
			<label class="control-label" for="group-list">Usuarios con acceso:</label>
			<div class="controls">
				{loop="$usuarios"}
				<div class="control-element">
					<input type="text" value="{$value.nick}" disabled />
					<a class="btn btn-danger" href="{#SITE_URL#}/admin/configuracion/mantenimiento_quitar_usuario/{$value.id}" title="Usuario rango"><i class="icon-white icon-remove"></i></a>
				</div>
				{/loop}
				<div class="control-element">
					<input type="text" value="" name="nuevo-usuario" id="agregar-usuario" placeholder="Usuario..." title="Usuario a agregar" />
					<button type="submit" class="btn btn-success" name="submit" value="agregar-usuario" title="Agregar usuario"><i class="icon-white icon-plus"></i></button>
				</div>
			</div>
		</div>

	</fieldset>
</form>