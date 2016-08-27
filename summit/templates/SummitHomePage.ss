<!DOCTYPE html>
<html lang="en">

<head>
    <% include Head %>
    <% include Analytics %>
</head>
<body class="summit-landing-body">
    <!-- Begin Page Content -->
    <div class="summit-landing-wrapper austin">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <img class="summit-landing-logo" src="/summit/images/summit-logo.svg" onerror="this.onerror=null; this.src='/summit/images/summit-logo.png'" alt="OpenStack Summit">
                    <div>
                        <p>
                            $IntroText
                        </p>
                    </div>
                    <% if $CurrentSummit.IsUpComing %>
                            <div class="countdown">
                                $Top.CountdownDigits($CurrentSummit.ID)
                            </div>
                            <div class="countdown-text">
                                Days until $CurrentSummit.Name
                            </div>
                    <% end_if %>
                    <div class="landing-action">
                        <a href="/summit/barcelona-2016/" class="btn register-btn-lrg">JOIN US <i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="summit-landing-bottom">
    <div class="container">
        <div class="row summit-row">
            <% with $CurrentSummit %>
            <div class="col-sm-6 col-sm-push-3 large-single-summit">
                <a href="/summit/barcelona-2016/" class="single-summit current-summit">
                    <h3>$DateLabel</h3>
                    <h2>$Title</h2>
                    <div class="btn register-btn-lrg skinny">JOIN US</div>
                </a>
                <div class="single-summit-date current">
                    $DateLabel
                    <div class="date-circle"></div>
                </div>
            </div>
            <% end_with %>

            <% with $PreviousSummit %>
            <div class="col-sm-3 col-sm-pull-6 small-single-summit">
                <a href="/videos/summits/show/6" class="single-summit past-summit">
                    <h2>Austin</h2>
                    <h4>Watch The Sessions!</h4>
                </a>
                <div class="single-summit-date past">
                    April 2016
                    <div class="date-circle"></div>
                </div>
            </div>
            <% end_with %>
            
            <% with $NextSummit %>
            <div class="col-sm-3 small-single-summit">
                <a href="summit/boston-2017/" class="single-summit future-summit">
                    <h2>Boston</h2>
                    <h4>Get Ready</h4>
                </a>
                <div class="single-summit-date future">
                    May 8-12, 2017
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
