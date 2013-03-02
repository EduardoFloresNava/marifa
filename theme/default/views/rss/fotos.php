<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title><![CDATA[{function="Utils::configuracion()->get('nombre', __('Marifa', FALSE))"} - {@Fotos@}]]></title>
		<link>{#SITE_URL#}</link>
		<description><![CDATA[{function="Utils::configuracion()->get('descripcion', __('Tu comunidad de forma simple', FALSE))"}]]></description>
		<pubDate>{if="isset($fotos.0)"}{$fotos.0.creacion->format(Fechahora::RSS)}{else}{function="date(Fechahora::RSS)"}{/if}</pubDate>

		{loop="$fotos"}<item>
			<title><![CDATA[{$value.titulo}]]></title>
			<link><![CDATA[{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html]]></link>
			<description><a href="{#SITE_URL#}/foto/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html]"><img alt="{$value.descripcion|Texto::limit_chars:30,TRUE,'...'}" src="{$value.url}" /></a><![CDATA[{$value.descripcion}]]></description>
			<pubDate>{$value.creacion->format(Fechahora::RSS)}</pubDate>
			<category>{$value.categoria.nombre}</category>
		</item>{/loop}
	</channel>
</rss>