<!DOCTYPE html>
<html lang="en">

<head>
    <% include Head %>
    <% include Analytics %>
</head>

<body class="summit-landing-body">


    <!-- Begin Page Content -->
    <div class="summit-landing-wrapper">
    <div class="container">
    	<div class="row">
    		<div class="col-lg-12 col-md-12 col-sm-12">
                <img class="summit-landing-logo" src="/summit/images/summit-logo.svg" onerror="this.onerror=null; this.src='/summit/images/summit-logo.png'" alt="OpenStack Summit">
                <div>
                    <p>
                        $IntroText
                    </p>
                </div>
                <div class="countdown">
                    <% loop $CountdownDigits %>
                        <span>$Digit</span>
                    <% end_loop %>
                </div>
                <div class="countdown-text">
                    Days until $CurrentSummit.Title
                </div>
                <div class="landing-action">
                    <a href="$CurrentSummit.Link" class="btn register-btn-lrg">Upcoming Summit <i class="fa fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="summit-landing-bottom">
    <div class="container">
        <div class="row summit-row">
            <% with $CurrentSummit %>
            <div class="col-lg-6 col-lg-push-3 col-md-6 col-md-push-3 col-sm-6 col-sm-push-3 large-single-summit">
                <a href="$Link" class="single-summit current-summit">
                    <h3>$DateLabel</h3>
                    <h2>$Title</h2>
                    <div class="btn register-btn-lrg skinny">Join Us</div>
                </a>
                <div class="single-summit-date current">
                    $DateLabel
                    <div class="date-circle"></div>
                </div>
            </div>
            <% end_with %>

            <% with $PreviousSummit %>
            <div class="col-lg-3 col-lg-pull-6 col-md-3 col-md-pull-6 col-sm-3 col-sm-pull-6 small-single-summit">
                <a href="//www.openstack.org/summit/openstack-paris-summit-2014" class="single-summit past-summit">
                    <h2>Paris</h2>
                    <h4>Thanks for coming!</h4>
                </a>
                <div class="single-summit-date past">
                    Nov 3-7, 2014
                    <div class="date-circle"></div>
                </div>
            </div>
            <% end_with %>
            
            <% with $NextSummit %>
            <div class="col-lg-3 col-md-3 col-sm-3 small-single-summit">
                <a href="/summit/tokyo-2015/" class="single-summit future-summit">
                    <h2>Tokyo</h2>
                    <h4>Get Ready</h4>
                </a>
                <div class="single-summit-date future">
                    October 2015
                    <div class="date-circle"></div>
                </div>
            </div>
            <% end_with %>
        </div>
    </div>
    <hr>
</div>
    <% include JS %>
    <% include Quantcast %>
</body>

</html>