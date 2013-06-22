<tr id="mensaje-{$value.id}" data-one-click-items=".one-click-ajax">
	<td>{if="$value.estado == 0"}
		<i class="icon icon-envelope" title="{@Nuevo@}"></i>
		{elseif="$value.estado == 1"}
		<i class="icon icon-inbox" title="{@Visto@}"></i>
		{elseif="$value.estado == 2"}
		<i class="icon icon-share-alt" title="{@Respuesta enviada@}"></i>
		{elseif="$value.estado == 3"}
		<i class="icon icon-repeat" title="{@Reenviado@}"></i>
		{/if}</td>
	<td>{if="$value.emisor === NULL"}<span class="label label-important">SISTEMA</span>{else}<a href="{#SITE_URL#}/@{$value.emisor.nick}">{$value.emisor.nick}</a>{/if}</td>
	<td><a href="{#SITE_URL#}/mensaje/ver/{$value.id}">{$value.asunto}</a>{if="$value.padre_id !== NULL"}<a class="pull-right" title="{@Ver padre@}" href="/mensaje/enviado/{$value.padre_id}"><i class="icon icon-upload"></i></a>{/if}</td>
	<td><span title="{$value.fecha->format('d/m/Y H:i:s')}">{$value.fecha->fuzzy()}</span></td>
	<td>
		<div class="btn-group">
			{if="$value.estado == 0"}<a href="{#SITE_URL#}/mensaje/leido/{$value.id}" class="btn btn-mini btn-info one-click-ajax" data-one-click-spinner="true" data-one-click-container="#mensaje-{$value.id}" title="{@Marcar como leído@}"><i class="icon-white icon-eye-open"></i></a>{/if}
			{if="$value.estado == 1"}<a href="{#SITE_URL#}/mensaje/noleido/{$value.id}" class="btn btn-mini btn-inverse one-click-ajax" data-one-click-spinner="true" data-one-click-container="#mensaje-{$value.id}" title="{@Marcar como nuevo@}"><i class="icon-white icon-eye-close"></i></a>{/if}
			<a href="{#SITE_URL#}/mensaje/borrar/{$value.id}" class="btn btn-mini btn-danger one-click-ajax" data-one-click-spinner="true" data-one-click-container="#mensaje-{$value.id}" title="{@Eliminar@}"><i class="icon-white icon-remove"></i></a>
		</div>
	</td>
</tr>