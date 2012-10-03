<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Marifa {if="isset($title)"} - {$title}{/if}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="{#THEME_URL#}/assets/css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }
        </style>
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

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="/">Marifa</a>
                    {if="isset($user_header)"}{$user_header}{/if}
                    <div class="nav-collapse">
                        <ul class="nav">
                            {loop="master_bar"}
                            <li{if="$value.active"}  class="active"{/if}>
                                <a href="{$value.link}">{$value.caption}{if="isset($value.cantidad) && $value.cantidad > 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
                            </li>
                            {/loop}
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container">
			{if="isset($top_bar)"}
			<ul class="nav nav-tabs">
				{loop="top_bar"}
				<li{if="$value.active"}  class="active"{/if}>
					<a href="{$value.link}">{$value.caption}{if="isset($value.cantidad) && $value.cantidad > 0"} <span class="badge{if="isset($value.tipo)"} badge-{$value.tipo}{/if}">{$value.cantidad}</span>{/if}</a>
				</li>
				{/loop}
			</ul>
			{/if}
			{if="isset($flash_success)"}<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a>{$flash_success}</div>{/if}
			{if="isset($flash_error)"}<div class="alert"><a class="close" data-dismiss="alert">×</a>{$flash_error}</div>{/if}
			{$contenido}
		</div>
		<footer class="footer container">
			<p>&copy; 2012{if="date('Y') > 2012"}-{function="date('Y')"}{/if} - Equipo desarrollo Marifa {if="isset($execution)"} - {$execution}{/if}</p>
		</footer>

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
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
		<script src="{#THEME_URL#}/assets/js/base.js"></script>
        {if="DEBUG"}<script src="{#THEME_URL#}/assets/js/jquery.php-profiler.js"></script>{/if}
    </body>
</html>