<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$CurrentLocale}">
<head>
    <% base_tag %>
    <title>$PageTitle</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="globalsign-domain-verification" content="tWFOHNAA_WMHmHfBMq38uTgupHFugV_dZ2rqyRxNMx" />

    $MetaTags(false)

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.openstack.org/blog/feed/" />


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <% include Page_GoogleAnalytics %>

</head>

<body id="$URLSegment">
    <% include SiteBanner %>
    <% include Navigation %>
    <!-- Page Content -->
    $Message
    <div class="row">
    <div class="col-xs-6 col-md-2">
        <div id="language-selector"></div>
    </div>
    <div class="col-xs-6 col-md-10">
        <a target="_blank" title="Download PDF" href="$Top.Link('pdf')"><i class="fa fa-file-pdf-o pdf-export" aria-hidden="true"></i></a>
    </div>
    </div>
    $Layout
    <% include Footer %>
    <% include Quantcast %>
    <% include TwitterUniversalWebsiteTagCode %>
    <% include OpenstackSearchWidget %>
</body>
    <% include Page_LinkedinInsightTracker %>
    <script>
        var extraLanguages = $Top.getAvailableLanguages;
    </script>
    $ModuleJS('paper-page')
    $ModuleCSS('paper-page')
</html>