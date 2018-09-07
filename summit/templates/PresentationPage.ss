<!DOCTYPE html>
<html lang="en">

<head>
    <% include Head %>
    <% include Page_GoogleAnalytics %>
    $FBTrackingCode
    $TwitterTrackingCode
    <link rel="stylesheet" type="text/css" href="/themes/openstack/static/css/tooltipster.css" />
</head>

<body class="presentation-page">
<div class="main-body">
    <% include StaticSummitPageHeaderSmall %>

    <div id="wrap">

        <!-- Begin Page Content -->
        <% if $IsWelcome %>
            <div class="presentation-app-header success">
                <div class="container">
                    <p class="status">Welcome to the OpenStack CFP!</p>
                </div>
            </div>
        <% end_if %>
        <% if PresentationDeadlineText %>
        <div class="presentation-app-header">
            <div class="container">
                <p class="status"><i class="fa fa-calendar"></i>&nbsp;{$Top.PresentationDeadlineText}</p>
            </div>
        </div>
        <% end_if %>
        $Layout
        <!-- End Page Content -->
        <div id="push"></div>
    </div>

    <% include StaticSummitPageFooter %>

    <!-- Hidden Sidebar Nav -->
    <div class="sidebar-nav">
        <nav>
            <a href="#" class="close-panel"><i class="icon-remove-sign icon-large"></i></a>
            <ul class="sidebar-menu">
                <!-- Microsite Navigation -->

                <% include SummitNav %>

                <!-- End Microsite Navigation -->
            </ul>

            <% if $CurrentSummit.RegistrationLink %>
                <a href="$CurrentSummit.RegistrationLink" class="btn register-btn-lrg">Register Now</a>
            <% end_if %>
        </nav>
    </div>
</div>
    <% include TwitterUniversalWebsiteTagCode %>
    <% include GoogleAdWordsSnippet %>
</body>
    <% include Page_LinkedinInsightTracker %>
</html>
