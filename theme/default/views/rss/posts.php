<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title><![CDATA[{function="Utils::configuracion()->get('nombre', __('Marifa', FALSE))"} - {@Posts@}]]></title>
		<link>{#SITE_URL#}</link>
		<description><![CDATA[{function="Utils::configuracion()->get('descripcion', __('Tu comunidad de forma simple', FALSE))"}]]></description>
		<pubDate>{if="isset($posts.0)"}{$posts.0.fecha->format(Fechahora::RSS)}{else}{function="date(Fechahora::RSS)"}{/if}</pubDate>

		{loop="$posts"}<item>
			<title><![CDATA[{$value.titulo}]]></title>
			<link><![CDATA[{#SITE_URL#}/post/{$value.categoria.seo}/{$value.id}/{$value.titulo|Texto::make_seo}.html]]></link>
			<description><![CDATA[{$value.contenido}]]></description>
			<pubDate>{$value.fecha->format(Fechahora::RSS)}</pubDate>
			<category>{$value.categoria.nombre}</category>
		</item>{/loop}
	</channel>
</rss>