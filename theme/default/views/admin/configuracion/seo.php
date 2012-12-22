<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administraci&oacute;n</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/configuracion/">Configuraci&oacute;n</a> <span class="divider">/</span></li>
    <li class="active">SEO</li>
</ul>
<div class="header">
	<h2>Configuraci&oacute;n de las optimizaciones para buscadores (SEO)</h2>
</div>
<form method="POST" class="form-horizontal" action="">
	<fieldset>
		<legend>Palabras claves:</legend>

		<div class="control-group{if="$error_largo_minimo"} error{elseif="$success_largo_minimo"} success{/if}">
			<label class="control-label" for="largo_minimo">Largo m&iacute;nimo:</label>
			<div class="controls">
				<input type="text" value="{$largo_minimo}" name="largo_minimo" id="largo_minimo" />
				<span class="help-block">{if="$error_largo_minimo"}{$error_largo_minimo}{elseif="$success_largo_minimo"}{$success_largo_minimo}{else}La cantidad de caracteres m&iacute;nima que debe tener para ser una palabra clave. 0 para cualquier largo.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_cantidad_minima_ocurrencias"} error{elseif="$success_cantidad_minima_ocurrencias"} success{/if}">
			<label class="control-label" for="cantidad_minima_ocurrencias">Ocurrencias:</label>
			<div class="controls">
				<input type="text" value="{$cantidad_minima_ocurrencias}" name="cantidad_minima_ocurrencias" id="cantidad_minima_ocurrencias" />
				<span class="help-block">{if="$error_cantidad_minima_ocurrencias"}{$error_cantidad_minima_ocurrencias}{elseif="$success_cantidad_minima_ocurrencias"}{$success_cantidad_minima_ocurrencias}{else}Cantidad m&iacute;nima de veces que debe aparecer la palabra para considerarse clave. 1 para cualquier palabra.{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_palabras_comunes"} error{elseif="$success_palabras_comunes"} success{/if}">
			<label class="control-label" for="palabras_comunes">Palabras omitidas:</label>
			<div class="controls">
				<textarea name="palabras_comunes" id="palabras_comunes">{function="implode("\n", $palabras_comunes)"}</textarea>
				<span class="help-block">{if="$error_palabras_comunes"}{$error_palabras_comunes}{elseif="$success_palabras_comunes"}{$success_palabras_comunes}{else}Listado de palabras que no pueden ser palabras claves. Una palabra por linea.{/if}</span>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
	</div>
</form>