
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
        

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="globalsign-domain-verification" content="tWFOHNAA_WMHmHfBMq38uTgupHFugV_dZ2rqyRxNMx" />
    <meta property="og:image" content="http://www.openstack.org/assets/coa/coa-og2.jpg" />
    <meta property="og:title" content="Certified OpenStack Administrator" />
    <meta property="og:url" content="http://www.openstack.org/coa" />
    <meta property="og:description" content="OpenStack skills are in high demand. Certified OpenStack Administrator (COA) is the only professional certification offered by the OpenStack Foundation, designed to help companies identify top professionals and job seekers demonstrate their skills." />
    <title>$Title &raquo; OpenStack Open Source Cloud Computing Software</title>

    <% base_tag %>

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.openstack.org/blog/feed/" />


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
    $Message
    $Layout


    <% include Footer %>
    <% include Quantcast %>

    <script>
        // Smooth Scroll
        $(function() {
            $('a[href*=#]:not([href=#])').click(function() {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.substr(1) +']');
                if (target.length) {
                    $('html,body').animate({
                      scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            });
        });
    </script>
</body>
</html>
