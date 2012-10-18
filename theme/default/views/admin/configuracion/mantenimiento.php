<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="/admin/configuracion/">Configuración</a> <span class="divider">/</span></li>
    <li class="active">Modo Mantenimiento</li>
</ul>
<div class="header clearfix">
	<h2 class="pull-left">Modo mantenimiento</h2>
	<div class="pull-right btn-group">
		{if="!$is_locked"}<a href="/admin/configuracion/habilitar_mantenimiento/1" class="btn btn-small btn-success"><i class="icon-white icon-ok"></i> Habilitar</a>{else}
		<a href="/admin/configuracion/habilitar_mantenimiento/0" class="btn btn-small btn-danger"><i class="icon-white icon-off"></i> Deshabilitar</a>{/if}
	</div>
</div>
{if="$is_locked_for_me"}<div class="alert alert-danger"><b>&iexcl;Alerta!</b> Si el sitio entra en modo mantenimiento no podrás acceder a él.</div>{/if}
<form method="POST" class="form-horizontal" action="">

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
</form>