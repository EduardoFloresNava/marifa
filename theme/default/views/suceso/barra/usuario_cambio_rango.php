<i class="icon icon-certificate"></i>
{if="isset($suceso.moderador) && is_array($suceso.moderador)"}
<a href="{#SITE_URL#}/@{$suceso.moderador.nick}">{$suceso.moderador.nick}</a> {@ha cambiado tu rango a @} <img src="{#THEME_URL#}/assets/img/rangos/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS, $suceso.rango.imagen, 'small')"}" /><span style="color: #{function="sprintf('%06s', dechex($suceso.rango.color))"}"><strong>{$suceso.rango.nombre}</strong></span>.
{else}
{@Ahora eres un @} <img src="{#THEME_URL#}/assets/img/rangos/{function="Icono::elemento(VIEW_PATH.THEME.DS.'assets'.DS.'img'.DS.'rangos'.DS, $suceso.rango.imagen, 'small')"}" /><span style="color: #{function="sprintf('%06s', dechex($suceso.rango.color))"}"><strong>{$suceso.rango.nombre}</strong></span> por cumplir los requisitos.
{/if}