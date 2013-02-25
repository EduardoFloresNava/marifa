<ul class="breadcrumb">
    <li><a href="{#SITE_URL#}/admin/">Administración</a> <span class="divider">/</span></li>
    <li><a href="{#SITE_URL#}/admin/sistema/">Sistema</a> <span class="divider">/</span></li>
    <li class="active">Traducciones</li>
</ul>
<div class="header">
	<h2>Traducciones</h2>
</div>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Idioma</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{loop="$traducciones"}
		<tr>
			<td>{$value}</td>
			<td>
				<a href="{#SITE_URL#}/admin/sistema/traducciones/{$value}/" class="btn btn-mini btn-success">Editar</a>
				<a href="{#SITE_URL#}/admin/sistema/activar_traduccion/{$value}/" class="btn btn-mini btn-primary">Utilizar por defecto</a>
			</td>
		</tr>
		{/loop}
	</tbody>
</table>