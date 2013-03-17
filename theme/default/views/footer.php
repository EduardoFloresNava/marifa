<footer class="footer container">
	<p>{if="isset($contacto_url)"}<a href="{$contacto_url}">Contacto</a> - {/if}<a href="/pages/protocolo">Protocolo</a> - <a href="/pages/tyc">Términos y condiciones</a> - <a href="/pages/privacidad">Privacidad de datos</a> - <a href="/pages/dmca">Report Abuse - DMCA</a></p>
	<p><strong>{#SITE_URL|parse_url:PHP_URL_HOST#}</strong> &copy; 2012{if="date('Y') > 2012"}-{function="date('Y')"}{/if} - Basado en <a href="http://www.marifa.com.ar/" rel="follow" title="Marifa">Marifa</a>{if="isset($execution)"} - {$execution}{/if}</p>
</footer>
