<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li class="active">{@Configuración@}</li>
</ul>
<div class="header">
	<h2>{@Configuración@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">
	<fieldset>
		<legend>Datos del sitio:</legend>

		<div class="control-group{if="$error_nombre"} error{elseif="$success_nombre"} success{/if}">
			<label class="control-label" for="nombre">Nombre del sitio:</label>
			<div class="controls">
				<input type="text" value="{$nombre}" name="nombre" id="nombre" />
				<span class="help-inline">{if="$error_nombre"}{$error_nombre}{elseif="$success_nombre"}{$success_nombre}{else}El nombre de tu comunidad.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_descripcion"} error{elseif="$success_descripcion"} success{/if}">
			<label class="control-label" for="descripcion">Frase del sitio:</label>
			<div class="controls">
				<input type="text" value="{$descripcion}" name="descripcion" id="descripcion" />
				<span class="help-inline">{if="$error_descripcion"}{$error_descripcion}{elseif="$success_descripcion"}{$success_descripcion}{else}La frase de tu comunidad.{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Registro de usuarios:</legend>

		<div class="control-group{if="$error_registro"} error{elseif="$success_registro"} success{/if}">
			<label class="control-label" for="registro">Registro de usuarios:</label>
			<div class="controls">
				<select name="registro" id="registro">
					<option value="1"{if="$registro"} selected="selected"{/if}>Abierto</option>
					<option value="0"{if="!$registro"} selected="selected"{/if}>Cerrado</option>
				</select>
				<span class="help-inline">{if="$error_registro"}{$error_registro}{elseif="$success_registro"}{$success_registro}{else}Estado del registro de los usuarios. Si lo cierras no podrán registrarse nuevos usuarios.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_activacion_usuario"} error{elseif="$success_activacion_usuario"} success{/if}">
			<label class="control-label" for="activacion_usuario">Activación usuarios:</label>
			<div class="controls">
				<select name="activacion_usuario" id="activacion_usuario">
					<option value="2"{if="$activacion_usuario == 2"} selected="selected"{/if}>Directa</option>
					<option value="1"{if="$activacion_usuario == 1"} selected="selected"{/if}>E-Mail</option>
					<option value="0"{if="$activacion_usuario == 0"} selected="selected"{/if}>Manual</option>
				</select>
				<span class="help-inline">{if="$error_activacion_usuario"}{$error_activacion_usuario}{elseif="$success_activacion_usuario"}{$success_activacion_usuario}{else}La forma de activar las cuentas de los usuarios. Directa: no requiere ningún tipo de validación. E-Mail: se debe activar por medio de un código enviado por E-Mail. Manual: la cuenta debe ser activada de forma manual por un administrador.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_rango_defecto"} error{elseif="$success_rango_defecto"} success{/if}">
			<label class="control-label" for="rango_defecto">Rango por defecto:</label>
			<div class="controls">
				<select name="rango_defecto" id="rango_defecto">
					{loop="$rangos_permitidos"}
					<option value="{$key}"{if="$rango_defecto == $key"} selected="selected"{/if}>{$value}</option>{/loop}
				</select>
				<span class="help-inline">{if="$error_rango_defecto"}{$error_rango_defecto}{elseif="$success_rango_defecto"}{$success_rango_defecto}{else}Rango por defecto que se le asigna a los nuevos usuarios cuando se registran.{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Sección fotos:</legend>

		<div class="control-group{if="$error_habilitar_fotos"} error{elseif="$success_habilitar_fotos"} success{/if}">
			<label class="control-label" for="habilitar_fotos">Estado sección fotos:</label>
			<div class="controls">
				<select name="habilitar_fotos" id="habilitar_fotos">
					<option value="1"{if="$habilitar_fotos"} selected="selected"{/if}>Habilitada</option>
					<option value="0"{if="!$habilitar_fotos"} selected="selected"{/if}>Deshabilitada</option>
				</select>
				<span class="help-inline">{if="$error_habilitar_fotos"}{$error_habilitar_fotos}{elseif="$success_habilitar_fotos"}{$success_habilitar_fotos}{else}Si está disponible la categoría de fotos o no. El deshabilitarla no borra las fotos existentes.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_privacidad_fotos"} error{elseif="$success_privacidad_fotos"} success{/if}">
			<label class="control-label" for="privacidad_fotos">Privacidad sección fotos:</label>
			<div class="controls">
				<select name="privacidad_fotos" id="privacidad_fotos">
					<option value="1"{if="$privacidad_fotos"} selected="selected"{/if}>Pública</option>
					<option value="0"{if="!$privacidad_fotos"} selected="selected"{/if}>Privada (solo usuarios registrados)</option>
				</select>
				<span class="help-inline">{if="$error_privacidad_fotos"}{$error_privacidad_fotos}{elseif="$success_privacidad_fotos"}{$success_privacidad_fotos}{else}Visibilidad de la categoría de fotos.{/if}</span>
			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend>Paginación:</legend>

		<div class="control-group{if="$error_elementos_pagina"} error{elseif="$success_elementos_pagina"} success{/if}">
			<label class="control-label" for="elementos_pagina">Elementos por página:</label>
			<div class="controls">
				<input type="text" value="{$elementos_pagina}" name="elementos_pagina" id="elementos_pagina" />
				<span class="help-inline">{if="$error_elementos_pagina"}{$error_elementos_pagina}{elseif="$success_elementos_pagina"}{$success_elementos_pagina}{else}Cantidad de elementos a mostrar por página.{/if}</span>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
	</div>
</form>