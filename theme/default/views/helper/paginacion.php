{if="$total > $cpp"}
<div class="pagination pagination-centered">
	<ul>
		{if="$paginacion.first != $actual && isset($paginacion.pages.0) && $paginacion.pages.0 > 1"}<li><a href="{function="sprintf($url, $paginacion.first)"}">&laquo;</a></li>{/if}
		{if="$paginacion.prev > 0"}<li><a href="{function="sprintf($url, $paginacion.prev)"}">{@Anterior@}</a></li>{/if}
		{if="$paginacion.first != $actual && isset($paginacion.pages.0) && $paginacion.pages.0 > 1"}<li><a href="#">...</a></li>{/if}
		{loop="$paginacion.pages"}
		<li{if="$value == $actual"} class="active"{/if}><a href="{function="sprintf($url, $value)"}">{$value}</a></li>
		{/loop}
		{if="$paginacion.last != $actual && $paginacion.last > 0 && count($paginacion.pages) > 0 && $paginacion.pages[count($paginacion.pages) - 1] < $paginacion.last"}<li><a href="#">...</a></li>{/if}
		{if="$paginacion.next <= $paginacion.last && $paginacion.next > 0"}<li><a href="{function="sprintf($url, $paginacion.next)"}">{@Siguiente@}</a></li>{/if}
		{if="$paginacion.last != $actual && $paginacion.last > 0 && count($paginacion.pages) > 0 && $paginacion.pages[count($paginacion.pages) - 1] < $paginacion.last"}<li><a href="{function="sprintf($url, $paginacion.last)"}">&raquo;</a></li>{/if}
	</ul>
</div>
{/if}