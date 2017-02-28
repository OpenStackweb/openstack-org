<!DOCTYPE html>
<html lang="en">

<head>
    <% include Head %>
    <% include Page_GoogleAnalytics %>
</head>
<body class="summit-landing-body">
    <!-- Begin Page Content -->
    <div class="summit-landing-wrapper boston">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-push-6 col-sm-7 col-sm-push-5 col-xs-11 col-xs-push-1">
                <div class="summit-landing-logo-bkgd">
                    <img class="summit-landing-logo" src="/themes/openstack/static/images/summit-logo-small-white.svg" onerror="this.onerror=null; this.src=/themes/openstack/static/images/summit-logo-small-white.png" alt="OpenStack Summit">
                </div>
                <div class="summit-headline">
                    <h1>The Must Attend<br/>Open Infrastructure Event</h1>
                </div>
                <p>
                    A four-day conference for IT business leaders, cloud operators and developers covering the open infrastructure landscape.
                </p>
                <div class="summit-home-dates">
                    May 8th -11th, 2017  |  Boston, MA
                </div>
                <div class="landing-action">
                    <a href="/summit/boston-2017/" class="btn register-btn-lrg">Join Us <i class="fa fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div> 
</div>
<div class="summit-landing-bottom">
    <div class="container">
        <div class="row summit-row">

            <% with $PreviousSummit %>
            <div class="col-sm-3 small-single-summit">
                <a href="/videos/summits/barcelona-2016" class="single-summit past-summit">
                    <h2>Barcelona</h2>
                    <h4>Watch The Sessions!</h4>
                </a>
                <div class="single-summit-date past">
                    October 2016
                    <div class="date-circle"></div>
                </div>
            </div>
            <% end_with %>
            <div class="col-sm-3 small-single-summit">
                <a href="/summit/boston-2017/" class="single-summit current-summit">
                    <h3>$DateLabel</h3>
                    <h2>$Title</h2>
                    <div class="btn register-btn-lrg skinny">JOIN US</div>
                </a>
                <div class="single-summit-date current">
                    $DateLabel
                    <div class="date-circle"></div>
                </div>
            </div>
            <div class="col-sm-3 small-single-summit">
                <a href="/summit/sydney-2017/" class="single-summit future-summit">
                    <h2>Sydney</h2>
                    <h4>Get Ready</h4>
                </a>
                <div class="single-summit-date future">
                    November 6-8, 2017
                    <div class="date-circle"></div>
                </div>
            </div>
            <div class="col-sm-3 small-single-summit">
                <a href="/summit/vancouver-2018/" class="single-summit future-summit2">
                    <h2>Vancouver</h2>
                    <h4>Get Ready</h4>
                </a>
                <div class="single-summit-date future">
                    Spring 2018
                    <div class="date-circle"></div>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>
    <% include JS %>
    <% include Quantcast %>
</body>
    <% include Page_LinkedinInsightTracker %>
</html>
