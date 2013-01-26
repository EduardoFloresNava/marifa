<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Marifa Installer {if="isset($title)"} - {$title}{/if}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="{#THEME_URL#}/assets/css/bootstrap.css" rel="stylesheet">
        <link href="{#THEME_URL#}/assets/css/bootstrap-responsive.css" rel="stylesheet">
        <link href="{#THEME_URL#}/assets/css/base.css" rel="stylesheet">
        <link href="{#THEME_URL#}/assets/css/profiler.css" rel="stylesheet">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="{#THEME_URL#}/assets/ico/favicon.ico">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{#THEME_URL#}/assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{#THEME_URL#}/assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{#THEME_URL#}/assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="{#THEME_URL#}/assets/ico/apple-touch-icon-57-precomposed.png">
    </head>

    <body>
        <div class="container">
			<h1 class="logo-title">Instalador de Marifa</h1>
			{if="isset($steps) && count($steps) > 0"}
			{$step_width=round(100/count($steps), 2)}
			<div class="progress">
				{loop="$steps"}
				<div class="bar bar-{if="$value.estado == 0"}info{elseif="$value.estado == 1"}success{else}warning{/if}" style="width: {if="$key < count($steps) - 1"}{$step_width}{else}{$a = 100 - ($step_width * ( -1 + count($steps)))}{/if}%;">{$value.caption}{if="$value.estado == 0"}({$key+1} de {$a = count($steps)}){/if}</div>
				{/loop}
			</div>
			{/if}
			{if="isset($flash_success)"}
				{if="is_array($flash_success)"}
				<div class="alert alert-success alert-container">
					{loop="$flash_success"}
					<div class="alert-item"><a class="close" data-dismiss="alert">×</a><i class="icon icon-ok"></i> {$value}</div>
					{/loop}
				</div>
				{else}
					<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-ok"></i> {$flash_success}</div>
				{/if}
			{/if}
			{if="isset($flash_info)"}
				{if="is_array($flash_info)"}
				<div class="alert alert-info alert-container">
					{loop="$flash_info"}
					<div class="alert-item"><a class="close" data-dismiss="alert">×</a><i class="icon icon-info-sign"></i> {$value}</div>
					{/loop}
				</div>
				{else}
					<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><i class="icon icon-info-sign"></i> {$flash_info}</div>
				{/if}
			{/if}
			{if="isset($flash_error)"}
				{if="is_array($flash_error)"}
				<div class="alert alert-container">
					{loop="$flash_error"}
					<div class="alert-item"><a class="close" data-dismiss="alert">×</a><i class="icon icon-remove-sign"></i> {$value}</div>
					{/loop}
				</div>
				{else}
					<div class="alert"><a class="close" data-dismiss="alert">×</a><i class="icon icon-remove-sign"></i> {$flash_error}</div>
				{/if}
			{/if}
			{$contenido}
		</div>
		<footer class="footer container">
			<p><strong>{#SITE_URL|parse_url:PHP_URL_HOST#}</strong> &copy; 2012{if="date('Y') > 2012"}-{function="date('Y')"}{/if} - Basado en <a href="http://www.marifa.com.ar/" rel="follow" title="Marifa">Marifa</a>{if="isset($execution)"} - {$execution}{/if}</p>
		</footer>

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="{#THEME_URL#}/assets/js/jquery.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-dropdown.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-tab.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-tooltip.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-button.js"></script>
		<script src="{#THEME_URL#}/assets/js/base.js"></script>
        {if="DEBUG"}<script src="{#THEME_URL#}/assets/js/jquery.php-profiler.js"></script>{/if}
    </body>
</html>