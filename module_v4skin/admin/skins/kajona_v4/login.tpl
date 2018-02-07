<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>[lang,commons_skin_title,commons] [%%webpathTitle%%]</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="generator" content="Kajona Core, https://github.com/artemeon/core" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="stylesheet" href="_webpath_/[webpath,module_system]/scripts/jqueryui/css/smoothness/jquery-ui.custom.css?_system_browser_cachebuster_" type="text/css" />
    <link rel="stylesheet" href="_webpath_/[webpath,module_system]/scripts/qtip2/jquery.qtip.min.css?_system_browser_cachebuster_" type="text/css" />
    <link rel="stylesheet" href="_webpath_/[webpath,module_v4skin]/admin/skins/kajona_v4/less/styles.min.css?_system_browser_cachebuster_" type="text/css" />

    <script src="_webpath_/[webpath,module_system]/scripts/jquery/jquery.min.js?_system_browser_cachebuster_"></script>
    <script src="_webpath_/[webpath,module_system]/scripts/routie/routie.min.js?_system_browser_cachebuster_"></script>
    %%head%%
    <script src="_webpath_/[webpath,module_system]/scripts/requirejs/require.js?_system_browser_cachebuster_"></script>
    <script type="text/javascript">
        require(['app'], function() {});
    </script>

    <link rel="shortcut icon" href="_webpath_/[webpath,module_v4skin]/admin/skins/kajona_v4/img/favicon.png">
</head>

<body class="login">

<div class="container-fluid">
    <div class="row">

        <div class="col-md-5 center-block" id="content">

            <div class="panel panelDefault" id="loginContainer">
                <div class="panel-header">
                    <h3>[lang,commons_skin_header,commons]</h3>
                </div>
                <div class="panel-body">
                    <!--[if lt IE 9]>
                    <div class="alert alert-danger">
                        You are using an outdated version of Internet Explorer. Please use a modern webbrowser like Mozilla Firefox or Google Chrome, or upgrade your Internet Explorer installation to access this application.<br /><br />
                        Sie verwenden eine veraltete Version des Internet Explorers. Bitte verwenden Sie einen modernen Webbrowser wie Mozilla Firefox oder Google Chrome oder aktualisieren Sie Ihre Internet Explorer Installation um auf diese Anwendung zuzugreifen.
                    </div>
                    <![endif]-->
                    <!--[if lt IE 9]><style type="text/css"> #moduleOutput {display: none;} </style><![endif]-->
                    <div id="moduleOutput">%%content%%</div>
                </div>
                <div class="panel-footer">
                    [lang,commons_login_footer,commons]
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>