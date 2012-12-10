<div class="header clearfix">
	<h2 class="pull-left">Notificaciones</h2>
	<div class="pull-right">
		<a href="{#SITE_URL#}/notificaciones/vistas/" class="btn btn-inverse"><i class="icon-white icon-eye-open"></i> Marcar como vistas</a>
	</div>
</div>
<div class="sucesos">
{loop="$sucesos"}{$value}{else}<div class="alert">No hay notificaciones.</div>{/loop}
</div>
{$paginacion}