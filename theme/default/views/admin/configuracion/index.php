<ul class="breadcrumb">
    <li><a href="/admin/">Administración</a> <span class="divider">/</span></li>
    <li class="active">Configuración</li>
</ul>
<div class="header">
	<h2>Configuración</h2>
</div>
{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
{if="isset($error)"}<div class="alert">{$error}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}
<form method="POST" class="form-horizontal" action="">

	{if="isset($success)"}<div class="alert alert-success">{$success}<button type="button" class="close" data-dismiss="alert">×</button></div>{/if}

	<div class="form-actions">
		<button type="submit" class="btn btn-large btn-primary">Actualizar</button>
	</div>
</form>