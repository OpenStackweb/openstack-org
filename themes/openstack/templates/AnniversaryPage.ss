<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta property="og:url" content="$FBUrl" />
        <meta property="og:image" content="$FBImage" />
        <meta property="og:image:type" content="image/png" />
        <meta property="og:image:width" content="$FBImageW" />
        <meta property="og:image:height" content="$FBImageH" />
        <meta property="og:title" content="$Title" />
        <meta property="og:description" content="$FBDesc" />
        <title>$Title</title>
        <!--[if IE]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <!--[if gte IE 9]>
        <style type="text/css">
            .gradient {jquery
                filter: none;
            }
        </style>
        <![endif]-->
        <% include Page_GoogleAnalytics %>
    </head>

    <body id="anniversary">
        $Layout
        <% include TwitterUniversalWebsiteTagCode %>
    </body>
    <% include Page_LinkedinInsightTracker %>
</html>