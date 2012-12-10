<h1 class="title">Enviar mensaje</h1>
<div class="row">
	<div class="span12">
		<form method="POST" class="form-horizontal" action="">

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}

			<div class="control-group{if="$error_para"} error{/if}">
				<label class="control-label" for="titulo">Para</label>
				<div class="controls">
					{if="isset($tipo) && $tipo == 1"}
					<input type="text" id="para" name="para" value="{$para}" class="span10" disabled="disabled" />
					{else}
					<input type="text" id="para" name="para" value="{$para}" class="span10" placeholder="usuario, usuario2, ..." />
					{/if}
					<span class="help-block">{if="$error_para"}{$error_para}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_asunto"} error{/if}">
				<label class="control-label" for="asunto">Asunto</label>
				<div class="controls">
					<input type="text" id="asunto" name="asunto" value="{$asunto}" class="span10" placeholder="Asunto del mensaje..." />
					<span class="help-block">{if="$error_asunto"}{$error_asunto}{/if}</span>
				</div>
			</div>

			<div class="control-group{if="$error_contenido"} error{/if}">
				<label class="control-label" for="titulo">Contenido</label>
				<div class="controls">
					{include="helper/bbcode_bar"}
					<textarea name="contenido" id="contenido" class="span10" data-preview="{#SITE_URL#}/mensaje/preview" placeholder="Mensaje...">{$contenido}</textarea>
					<span class="help-block">{if="$error_contenido"}{$error_contenido}{/if}</span>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-large btn-primary">Enviar</button>
			</div>
		</form>
		{/if}
	</div>
</div>