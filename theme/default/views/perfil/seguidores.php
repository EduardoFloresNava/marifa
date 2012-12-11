<h3 class="title">{@Usuarios que siguen a@} {$usuario.nick}:</h3>
{if="count($seguidores) > 0"}
<table class="table table-striped table-bordered">
	<tbody>
	{loop="$seguidores"}
		<tr>
			<td><a href="{#SITE_URL#}/perfil/index/{$value.nick}">{$value.nick}</a></td>
		</tr>
	{/loop}
	</tbody>
</table>
{$paginacion_seguidores}
{else}
	<div class="alert">{@No tiene seguidores a&uacute;n@}.</div>
{/if}
<h3 class="title">{@Usuarios que seguidos por@} {$usuario.nick}:</h3>
{if="count($sigue) > 0"}
<table class="table table-striped table-bordered">
	<tbody>
	{loop="$sigue"}
		<tr>
			<td><a href="{#SITE_URL#}/perfil/index/{$value.nick}">{$value.nick}</a></td>
		</tr>
	{/loop}
	</tbody>
</table>
{$paginacion_sigue}
{else}
	<div class="alert">{@No sigue ning&uacute;n usuario a&uacute;n@}.</div>
{/if}