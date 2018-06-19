
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="globalsign-domain-verification" content="tWFOHNAA_WMHmHfBMq38uTgupHFugV_dZ2rqyRxNMx" />
    <title>$getPageTitle()</title>

    $MetaTags(false)

    <% base_tag %>

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.openstack.org/blog/feed/" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png"> 
    <link rel="icon" type="image/png" href="/favicon/favicon-32x32.png" sizes="32x32"> 
    <link rel="icon" type="image/png" href="/favicon/favicon-16x16.png" sizes="16x16"> 
    <link rel="manifest" href="/favicon/manifest.json"> 
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5"> 

    <!-- Cookie Bot -->
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="e11e4375-71b9-426d-a76d-61eae3ddc08f" type="text/javascript" async></script>
    <script id="CookieDeclaration" src="https://consent.cookiebot.com/e11e4375-71b9-426d-a76d-61eae3ddc08f/cd.js" type="text/javascript" async></script>


      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <% include Page_GoogleAnalytics %>
    <% include Page_LinkedinInsightTracker %>
  </head>

  <body id="$URLSegment">
      <% include SiteBanner %>
      <% include Navigation %>
      
      <!-- Page Content -->
      <div class="container">
        $Message        
        $Layout
      </div>

    <% include Footer %>
    <% include Quantcast %>
    <% include TwitterUniversalWebsiteTagCode %>
    <% include GoogleAdWordsSnippet %>

  </body>
    <% include Page_LinkedinInsightTracker %>
</html>