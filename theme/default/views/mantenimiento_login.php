<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Marifa - Modo mantenimiento</title>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
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

			<div class="row">
				<div class="span12">
					<form class="form-horizontal" method="POST" action="">
						<fieldset>
							<legend>Inicio de sesión</legend>

                            {if="isset($error) && $error"}<div class="alert">{$error}</div>{/if}

                            <div class="control-group{if="$error_nick"} error{/if}">
                                <label class="control-label" for="nick">E-Mail o Usuario</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge" id="nick" name="nick" value="{$nick}" />
                                    <span class="help-inline">Tu nick actual o tu E-Mail. Si has cambiado tu nick, debes colocar el último.</span>
                                </div>
                            </div>

                            <div class="control-group{if="$error_password"} error{/if}">
                                <label class="control-label" for="password">Contraseña</label>
                                <div class="controls">
                                    <input type="password" class="input-xlarge" id="password" name="password" value="" />
                                    <span class="help-inline">Clave de acceso.</span>
                                </div>
                            </div>

                            <div class="form-actions">
                                <input type="submit" value="Iniciar sesión" class="btn btn-primary btn-large" />
                            </div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		{include="footer"}

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
		<script src="{#THEME_URL#}/assets/js/jquery.masonry.min.js"></script>
		<script src="{#THEME_URL#}/assets/js/base.js"></script>
    </body>
</html>