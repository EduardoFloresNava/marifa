<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li class="active">{@Configuración@}</li>
</ul>
<div class="header">
	<h2>{@Configuración@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">
	<fieldset>
		<legend>{@Datos del sitio:@}</legend>

		<div class="control-group{if="$error_nombre"} error{elseif="$success_nombre"} success{/if}">
			<label class="control-label" for="nombre">{@Nombre del sitio:@}</label>
			<div class="controls">
				<input type="text" value="{$nombre}" name="nombre" id="nombre" />
				<span class="help-inline">{if="$error_nombre"}{$error_nombre}{elseif="$success_nombre"}{$success_nombre}{else}{@El nombre de tu comunidad.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_descripcion"} error{elseif="$success_descripcion"} success{/if}">
			<label class="control-label" for="descripcion">{@Frase del sitio:@}</label>
			<div class="controls">
				<input type="text" value="{$descripcion}" name="descripcion" id="descripcion" />
				<span class="help-inline">{if="$error_descripcion"}{$error_descripcion}{elseif="$success_descripcion"}{$success_descripcion}{else}{@La frase de tu comunidad.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Datos del sistema:@}</legend>

		<div class="control-group{if="$error_timezone"} error{elseif="$success_timezone"} success{/if}">
			<label class="control-label" for="timezone">{@Zona horaria:@}</label>
			<div class="controls">
				<select name="timezone" id="timezone">
					{loop="$timezones"}
					<option value="{$value}"{if="$value === $timezone"}selected="selected"{/if}>{$value}</option>
					{/loop}
				</select>
				<span class="help-inline">{if="$error_timezone"}{$error_timezone}{elseif="$success_timezone"}{$success_timezone}{else}{@Zona horaria a utilizar.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Registro de usuarios:@}</legend>

		<div class="control-group{if="$error_registro"} error{elseif="$success_registro"} success{/if}">
			<label class="control-label" for="registro">{@Registro de usuarios:@}</label>
			<div class="controls">
				<select name="registro" id="registro">
					<option value="1"{if="$registro"} selected="selected"{/if}>Abierto</option>
					<option value="0"{if="!$registro"} selected="selected"{/if}>Cerrado</option>
				</select>
				<span class="help-inline">{if="$error_registro"}{$error_registro}{elseif="$success_registro"}{$success_registro}{else}{@Estado del registro de los usuarios. Si lo cierras no podrán registrarse nuevos usuarios.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_activacion_usuario"} error{elseif="$success_activacion_usuario"} success{/if}">
			<label class="control-label" for="activacion_usuario">{@Activación usuarios:@}</label>
			<div class="controls">
				<select name="activacion_usuario" id="activacion_usuario">
					<option value="2"{if="$activacion_usuario == 2"} selected="selected"{/if}>Directa</option>
					<option value="1"{if="$activacion_usuario == 1"} selected="selected"{/if}>E-Mail</option>
					<option value="0"{if="$activacion_usuario == 0"} selected="selected"{/if}>Manual</option>
				</select>
				<span class="help-inline">{if="$error_activacion_usuario"}{$error_activacion_usuario}{elseif="$success_activacion_usuario"}{$success_activacion_usuario}{else}{@La forma de activar las cuentas de los usuarios. Directa: no requiere ningún tipo de validación. E-Mail: se debe activar por medio de un código enviado por E-Mail. Manual: la cuenta debe ser activada de forma manual por un administrador.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_rango_defecto"} error{elseif="$success_rango_defecto"} success{/if}">
			<label class="control-label" for="rango_defecto">{@Rango por defecto:@}</label>
			<div class="controls">
				<select name="rango_defecto" id="rango_defecto">
					{loop="$rangos_permitidos"}
					<option value="{$key}"{if="$rango_defecto == $key"} selected="selected"{/if}>{$value}</option>{/loop}
				</select>
				<span class="help-inline">{if="$error_rango_defecto"}{$error_rango_defecto}{elseif="$success_rango_defecto"}{$success_rango_defecto}{else}{@Rango por defecto que se le asigna a los nuevos usuarios cuando se registran.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_usuarios_bloqueados"} error{elseif="$success_usuarios_bloqueados"} success{/if}">
			<label class="control-label" for="usuarios_bloqueados">{@Nick's bloqueados:@}</label>
			<div class="controls">
				<textarea name="usuarios_bloqueados" id="usuarios_bloqueados">{$usuarios_bloqueados}</textarea>
				<span class="help-inline">{if="$error_usuarios_bloqueados"}{$error_usuarios_bloqueados}{elseif="$success_usuarios_bloqueados"}{$success_usuarios_bloqueados}{else}{@Listado de nick's que se encuentran bloqueados. Los mismos no pueden usarse por los usuarios.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Sección fotos:@}</legend>

		<div class="control-group{if="$error_habilitar_fotos"} error{elseif="$success_habilitar_fotos"} success{/if}">
			<label class="control-label" for="habilitar_fotos">{@Estado sección fotos:@}</label>
			<div class="controls">
				<select name="habilitar_fotos" id="habilitar_fotos">
					<option value="1"{if="$habilitar_fotos"} selected="selected"{/if}>Habilitada</option>
					<option value="0"{if="!$habilitar_fotos"} selected="selected"{/if}>Deshabilitada</option>
				</select>
				<span class="help-inline">{if="$error_habilitar_fotos"}{$error_habilitar_fotos}{elseif="$success_habilitar_fotos"}{$success_habilitar_fotos}{else}{@Si está disponible la categoría de fotos o no. El deshabilitarla no borra las fotos existentes.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_privacidad_fotos"} error{elseif="$success_privacidad_fotos"} success{/if}">
			<label class="control-label" for="privacidad_fotos">{@Privacidad sección fotos:@}</label>
			<div class="controls">
				<select name="privacidad_fotos" id="privacidad_fotos">
					<option value="1"{if="$privacidad_fotos"} selected="selected"{/if}>Pública</option>
					<option value="0"{if="!$privacidad_fotos"} selected="selected"{/if}>Privada (solo usuarios registrados)</option>
				</select>
				<span class="help-inline">{if="$error_privacidad_fotos"}{$error_privacidad_fotos}{elseif="$success_privacidad_fotos"}{$success_privacidad_fotos}{else}{@Visibilidad de la categoría de fotos.@}{/if}</span>
			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend>{@Paginación:@}</legend>

		<div class="control-group{if="$error_elementos_pagina"} error{elseif="$success_elementos_pagina"} success{/if}">
			<label class="control-label" for="elementos_pagina">{@Elementos por página:@}</label>
			<div class="controls">
				<input type="text" value="{$elementos_pagina}" name="elementos_pagina" id="elementos_pagina" />
				<span class="help-inline">{if="$error_elementos_pagina"}{$error_elementos_pagina}{elseif="$success_elementos_pagina"}{$success_elementos_pagina}{else}{@Cantidad de elementos a mostrar por página.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Contacto:@}</legend>

		<div class="control-group{if="$error_contacto_tipo"} error{elseif="$success_contacto_tipo"} success{/if}">
			<label class="control-label" for="contacto_tipo">{@Tipo de contacto:@}</label>
			<div class="controls">
				<select name="contacto_tipo" id="contacto_tipo">
					<option value="0"{if="$contacto_tipo == 0"} selected="selected"{/if}>Link</option>
					<option value="1"{if="$contacto_tipo == 1"} selected="selected"{/if}>Formulario</option>
					<option value="2"{if="$contacto_tipo == 2"} selected="selected"{/if}>Mensaje</option>
				</select>
				<span class="help-inline">{if="$error_contacto_tipo"}{$error_contacto_tipo}{elseif="$success_contacto_tipo"}{$success_contacto_tipo}{else}{@Comportamiento del link de contacto del pie de página. Link define una URL (p. e. mailto:contacto@marifa.org), formulario un Formulario de contacto almacenado en la base de datos y Mensaje envia un mensaje a un usuario o grupo especificado.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_contacto_valor"} error{elseif="$success_contacto_valor"} success{/if}">
			<label class="control-label" for="contacto_valor">{@Valor contacto:@}</label>
			<div class="controls">
				<textarea name="contacto_valor" id="contacto_valor">{$contacto_valor}</textarea>
				<span class="help-inline">{if="$error_contacto_valor"}{$error_contacto_valor}{elseif="$success_contacto_valor"}{$success_contacto_valor}{else}{@Si es link la URL a setear, en caso de mensaje una lista de usuarios y/o grupos a utilizar (los grupos deben comenzar con una @). Si es formulario no será tenido en cuenta.@}{/if}</span>
			</div>
		</div>

	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Actualizar@}</button>
	</div>
</form>