<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
	<li><a href="{#SITE_URL#}/admin/usuario/">Usuarios</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/usuario/medallas">Medallas</a> <span class="divider">/</span></li>
    <li class="active">Editar</li>
</ul>
<div class="header">
	<h2>Editar medalla {$nombre}</h2>
</div>
<form method="POST" class="form-horizontal" action="">

	<div class="control-group{if="$error_nombre"} error{/if}">
		<label class="control-label" for="nombre">Nombre</label>
		<div class="controls">
			<input type="text" value="{$nombre}" name="nombre" id="nombre" class="input-xxlarge" />
			<span class="help-block">{if="$error_nombre"}{$error_nombre}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_descripcion"} error{/if}">
		<label class="control-label" for="descripcion">Descripción</label>
		<div class="controls">
			<textarea name="descripcion" id="descripcion" class="input-xxlarge">{$descripcion}</textarea>
			<span class="help-block">{if="$error_descripcion"}{$error_descripcion}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_imagen"} error{/if}">
		<label class="control-label" for="imagen">Imagen</label>
		<div class="controls">
			<select name="imagen" id="imagen">
				{loop="$imagenes_medallas"}
				<option style="padding-left: 22px; background: transparent url({#THEME_URL#}/assets/img/medallas/{$key}) no-repeat 2px 0;" value="{$value}"{if="$value == $imagen"} selected="selected"{/if}>{$value}</option>
				{/loop}
			</select>
			<span class="help-block">{if="$error_imagen"}{$error_imagen}{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_condicion"} error{/if}">
		<label class="control-label" for="condicion">Tipo</label>
		<div class="controls">
			<select name="condicion" id="condicion">
				<optgroup label="Usuario">
					<option value="0"{if="$condicion==0"} selected="selected"{/if}>Puntos</option>
					<option value="1"{if="$condicion==1"} selected="selected"{/if}>Seguidores</option>
					<option value="2"{if="$condicion==2"} selected="selected"{/if}>Siguiendo</option>
					<option value="3"{if="$condicion==3"} selected="selected"{/if}>Comentarios en posts</option>
					<option value="4"{if="$condicion==4"} selected="selected"{/if}>Comentarios en fotos</option>
					<option value="5"{if="$condicion==5"} selected="selected"{/if}>Posts</option>
					<option value="6"{if="$condicion==6"} selected="selected"{/if}>Fotos</option>
					<option value="7"{if="$condicion==7"} selected="selected"{/if}>Medallas</option>
					<!--<option value="8"{if="$condicion==8"} selected="selected"{/if}>Rango</option>-->
				</optgroup>
				<optgroup label="Post">
					<option value="9"{if="$condicion==9"} selected="selected"{/if}>Puntos</option>
					<option value="10"{if="$condicion==10"} selected="selected"{/if}>Seguidores</option>
					<option value="11"{if="$condicion==11"} selected="selected"{/if}>Comentarios</option>
					<option value="12"{if="$condicion==12"} selected="selected"{/if}>Favoritos</option>
					<option value="13"{if="$condicion==13"} selected="selected"{/if}>Denuncias</option>
					<option value="14"{if="$condicion==14"} selected="selected"{/if}>Visitas</option>
					<option value="15"{if="$condicion==15"} selected="selected"{/if}>Medallas</option>
					<!--<option value="16"{if="$condicion==16"} selected="selected"{/if}>Veces compartido</option>-->
				</optgroup>
				<optgroup label="Foto">
					<option value="17"{if="$condicion==17"} selected="selected"{/if}>Votos positivos</option>
					<option value="18"{if="$condicion==18"} selected="selected"{/if}>Votos negativos</option>
					<option value="19"{if="$condicion==19"} selected="selected"{/if}>Votos netos</option>
					<option value="20"{if="$condicion==20"} selected="selected"{/if}>Comentarios</option>
					<option value="21"{if="$condicion==21"} selected="selected"{/if}>Visitas</option>
					<option value="22"{if="$condicion==22"} selected="selected"{/if}>Medallas</option>
					<option value="23"{if="$condicion==23"} selected="selected"{/if}>Favoritos</option>
					<option value="24"{if="$condicion==24"} selected="selected"{/if}>Denuncias</option>
				</optgroup>
			</select>
			<span class="help-block">{if="$error_condicion"}{$error_condicion}{else}Condición que debe cumplir para ganar la medalla.{/if}</span>
		</div>
	</div>

	<div class="control-group{if="$error_cantidad"} error{/if}">
		<label class="control-label" for="cantidad">Cantidad</label>
		<div class="controls">
			<input type="text" value="{$cantidad}" name="cantidad" id="cantidad" class="span10" />
			<span class="help-block">{if="$error_cantidad"}{$error_cantidad}{else}Cantidad de la condición necesaria para ganar la medalla.{/if}</span>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Guardar</button> o <a href="{#SITE_URL#}/admin/usuario/medallas/">Volver</a>
	</div>
</form>