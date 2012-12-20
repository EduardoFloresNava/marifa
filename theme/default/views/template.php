<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>{if="isset($title_raw)"}{$title_raw}{elseif="isset($title)"}{$title} - {/if}{if="isset($brand)"}{$brand}{else}Marifa{/if}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="{#THEME_URL#}/assets/css/bootstrap.css" rel="stylesheet">
        <link href="{#THEME_URL#}/assets/css/bootstrap-responsive.css" rel="stylesheet">
        <link href="{#THEME_URL#}/assets/css/base.css" rel="stylesheet">
        {if="DEBUG"}<link href="{#THEME_URL#}/assets/css/profiler.css" rel="stylesheet">{/if}

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

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="/">{if="isset($brand)"}{$brand}{else}Marifa{/if}</a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            {loop="master_bar"}
                            <li{if="$value.active"}  class="active"{/if}>
                                <a href="{$value.link}">{if="isset($value.icon)"}<i class="icon-white icon-{$value.icon}"></i> {/if}{$value.caption}{if="isset($value.cantidad) && $value.cantidad > 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
                            </li>
                            {/loop}
                        </ul>
                        {if="isset($user_header)"}{$user_header}{/if}
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
			{if="isset($noticia)"}
				{if="is_array($noticia)"}
				<div class="alert alert-info alert-container">
					{loop="$noticia"}
					<div class="alert-item"><a class="close" data-dismiss="alert">×</a><i class="icon icon-bullhorn"></i> {$value}</div>
					{/loop}
				</div>
				{else}
					<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><i class="icon icon-bullhorn"></i> {$noticia}</div>
				{/if}
			{/if}
			{if="isset($top_bar)"}
			<ul class="nav nav-tabs">
				{loop="top_bar"}
				<li{if="$value.active"}  class="active"{/if}>
					<a href="{$value.link}">{$value.caption}{if="isset($value.cantidad) && $value.cantidad > 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
				</li>
				{/loop}
			</ul>
			{/if}
			{if="isset($is_locked) && $is_locked"}<div class="alert alert-info"><b>&iexcl;Importante!</b> El sitio se encuentra en modo mantenimiento, no todos los usuarios pueden acceder a el sitio.<a class="close" data-dismiss="alert">×</a></div>{/if}
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
		{include="footer"}
		<div class="pop-notification"></div>

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
		<script type="text/javascript">
			window.site_url = "{#SITE_URL#}/";
			window.theme_url = "{#THEME_URL#}/";
		</script>
        <script src="{#THEME_URL#}/assets/js/jquery.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-transition.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-alert.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-modal.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-dropdown.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-scrollspy.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-tab.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-tooltip.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-popover.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-button.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-collapse.js"></script>
        <script src="{#THEME_URL#}/assets/js/bootstrap-carousel.js"></script>
		<script src="{#THEME_URL#}/assets/js/bootstrap-typeahead.js"></script>
		<script src="{#THEME_URL#}/assets/js/jquery.markitup.js"></script>
		<script src="{#THEME_URL#}/assets/js/bbcode.markitup.js"></script>
		<script src="{#THEME_URL#}/assets/js/jquery.masonry.min.js"></script>
		<script src="{#THEME_URL#}/assets/js/jquery.textext.min.js"></script>
		<script src="{#THEME_URL#}/assets/js/base.js"></script>
        {if="DEBUG"}<script src="{#THEME_URL#}/assets/js/jquery.php-profiler.js"></script>{/if}
    </body>
</html>