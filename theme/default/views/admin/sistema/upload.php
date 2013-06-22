<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">{@Administración@}</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/sistema/">{@Sistema@}</a> <span class="divider">/</span></li>
    <li class="active">{@Configurar carga de archivos@}</li>
</ul>
<div class="header">
	<h2>{@Configurar carga de archivos@}</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<fieldset>
		<legend>{@Archivos binarios (cualquier cosa que no sea una imagen)@}</legend>

		<div class="control-group{if="$error_file_maxsize"} error{/if}">
			<label class="control-label" for="file_maxsize">{@Tamaño máximo@}</label>
			<div class="controls">
				<input type="text" name="file_maxsize" id="file_maxsize" value="{$file_maxsize}" />
				<span class="help-inline">{if="$error_file_maxsize"}{$error_file_maxsize}{else}{@Cantidad de Bytes máxima que pueden tener los archivos de tamaño.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_file_path"} error{/if}">
			<label class="control-label" for="file_path">{@Directorio donde poner los archivos@}</label>
			<div class="controls">
				<input type="text" name="file_path" id="file_path" value="{$file_path}" />
				<span class="help-inline">{if="$error_file_path"}{$error_file_path}{else}{@Cantidad de Bytes máxima que pueden tener los archivos de tamaño.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_file_usehash"} error{/if}">
			<label class="control-label" for="file_usehash">{@Usar hash@}</label>
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" value="1" name="file_usehash" id="file_usehash" {if="$file_usehash"}checked="checked"{/if}>
					{@Utilizar un hash para nombrar los archivos.@}
				</label>
				<span class="help-inline">{if="$error_file_usehash"}{$error_file_usehash}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Imágenes@}</legend>

		<div class="control-group{if="$error_image_maxsize"} error{/if}">
			<label class="control-label" for="image_maxsize">{@Tamaño máximo@}</label>
			<div class="controls">
				<input type="text" name="image_maxsize" id="image_maxsize" value="{$image_maxsize}" />
				<span class="help-inline">{if="$error_image_maxsize"}{$error_image_maxsize}{else}{@Cantidad de Bytes máxima que pueden tener los archivos de tamaño.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_image_min_px"} error{/if}">
			<label class="control-label" for="image_min_px">{@Tamaño mínimo de la imagen@}</label>
			<div class="controls">
				<input type="text" name="image_min_px" id="image_min_px" value="{$image_min_px}" />
				<span class="help-inline">{if="$error_image_min_px"}{$error_image_min_px}{else}{@Tamaño mínimo de la imagen en px (pixel's).@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_image_max_px"} error{/if}">
			<label class="control-label" for="image_max_px">{@Tamaño máximo de la imagen@}</label>
			<div class="controls">
				<input type="text" name="image_max_px" id="image_max_px" value="{$image_max_px}" />
				<span class="help-inline">{if="$error_image_max_px"}{$error_image_max_px}{else}{@Tamaño máximo de la imagen en px (pixel's).@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_image_savemethod"} error{/if}">
			<label class="control-label" for="image_savemethod">{@Método de almacenamiento.@}</label>
			<div class="controls">
				<select name="image_savemethod" id="image_savemethod">
					{loop="$image_save_methods"}
					<option value="{$key}"{if="$key == $image_savemethod"} selected="selected"{/if}>{$value}</option>
					{/loop}
				</select>
				<span class="help-inline">{if="$error_image_savemethod"}{$error_image_savemethod}{else}{@Método utilizado para guardar las imágenes.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Imágenes en disco@}</legend>

		<div class="control-group{if="$error_image_disk_savepath"} error{/if}">
			<label class="control-label" for="image_disk_savepath">{@Ruta del directorio@}</label>
			<div class="controls">
				<input type="text" name="image_disk_savepath" id="image_disk_savepath" value="{$image_disk_savepath}" />
				<span class="help-inline">{if="$error_image_disk_savepath"}{$error_image_disk_savepath}{else}{@Directorio donde se almacenan las imágenes.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_image_disk_saveurl"} error{/if}">
			<label class="control-label" for="image_disk_saveurl">{@Url del directorio@}</label>
			<div class="controls">
				<input type="text" name="image_disk_saveurl" id="image_disk_saveurl" value="{$image_disk_saveurl}" />
				<span class="help-inline">{if="$error_image_disk_saveurl"}{$error_image_disk_saveurl}{else}{@Url relativa a la del sitio para acceder al directorio de fotos.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_image_disk_usehash"} error{/if}">
			<label class="control-label" for="image_disk_usehash">{@Nombrar con hash@}</label>
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" value="1" name="image_disk_usehash" id="image_disk_usehash" {if="$image_disk_usehash"}checked="checked"{/if}>
					{@Si los nombres de las imágenes son lo originales un hash.@}
				</label>
				<span class="help-inline">{if="$error_image_disk_usehash"}{$error_image_disk_usehash}{/if}</span>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{@Imágenes a imgur@}</legend>

		<div class="control-group{if="$error_image_imgur_apikey"} error{/if}">
			<label class="control-label" for="image_imgur_apikey">{@Clave del API@}</label>
			<div class="controls">
				<input type="text" name="image_imgur_apikey" id="image_imgur_apikey" value="{$image_imgur_apikey}" />
				<span class="help-inline">{if="$error_image_imgur_apikey"}{$error_image_imgur_apikey}{else}{@Clave para acceder al API de imgur.@}{/if}</span>
			</div>
		</div>

		<div class="control-group{if="$error_image_imgur_timeout"} error{/if}">
			<label class="control-label" for="image_imgur_timeout">{@Tiempo máximo carga@}</label>
			<div class="controls">
				<input type="text" name="image_imgur_timeout" id="image_imgur_timeout" value="{$image_imgur_timeout}" />
				<span class="help-inline">{if="$error_image_imgur_timeout"}{$error_image_imgur_timeout}{else}{@Cantidad de segundos máxima para esperar la carga por parte de imgur.@}{/if}</span>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">{@Actualizar@}</button>
	</div>
</form>