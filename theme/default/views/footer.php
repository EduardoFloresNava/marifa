<footer class="footer container">
	<p>{if="isset($contacto_url)"}<a href="{$contacto_url}">Contacto</a>{/if}{include="menu_pie"}</p>
	<p><strong>{#SITE_URL|parse_url:PHP_URL_HOST#}</strong> &copy; 2012{if="date('Y') > 2012"}-{function="date('Y')"}{/if} - Basado en <a href="http://www.marifa.com.ar/" rel="follow" title="Marifa">Marifa</a>{if="isset($execution)"} - {$execution}{/if}</p>
</footer>
