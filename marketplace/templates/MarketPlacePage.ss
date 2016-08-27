<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <% base_tag %>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="globalsign-domain-verification" content="tWFOHNAA_WMHmHfBMq38uTgupHFugV_dZ2rqyRxNMx" />
    $MetaTags(false)

    <title>$Title &raquo; OpenStack Open Source Cloud Computing Software</title>
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.openstack.org/blog/feed/" />
    <!-- Fonts -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->    
    <% include Analytics %>

  </head>

  <body id="$URLSegment">
      <% include SiteBanner %>
      <% include Navigation %>
      
      <!-- Page Content -->

        <div class="container">
            <% include MarketPlaceFrontendNav %>
        </div>
        $Message
        $Layout
        $Form

    <% include Footer %>
    <% include Quantcast %>
  </body>

</html>