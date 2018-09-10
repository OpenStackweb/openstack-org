<!DOCTYPE html>
<html lang="en" $OGNS>

    <head>
        <% include Head %>
        <% include Page_GoogleAnalytics %>
        $FBTrackingCode
        $TwitterTrackingCode
        <!-- GoogleAdWords Start -->
        $GATrackingCode
        <!-- GoogleAdWords End -->
        <link rel="stylesheet" type="text/css" href="/themes/openstack/static/css/tooltipster.css" />
    </head>

    <body>
        <div class="main-body static-summit-about-page">
            <% include StaticSummitPageHeaderSmall %>

            <div id="wrap">

                <!-- Begin Page Content -->
                $Layout
                <!-- End Page Content -->

                <div id="push"></div>
            </div>

            <% if not Summit.RegistrationLink %>
                <% include RegistrationModal %>
            <% end_if %>

            <% include DownloadAppModal %>

            <% include StaticSummitPageFooter %>

        </div>

        <% include Quantcast %>
        <% include TwitterUniversalWebsiteTagCode %>
        <% include GoogleAdWordsSnippet %>

        <div id="orderModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Thanks for Registering! What's Next?</h4>
                    </div>
                    <div class="modal-body">
                        <p><b><a href="https://www.openstack.org/summit/berlin-2018/travel/">Book your hotel room now</a></b> through one of our recommended hotels.</p>
                        <p>An email receipt with details of your Summit registration and purchase will be sent to the address that you registered with.</p>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script type="text/javascript" src="/themes/openstack/static/js/jquery.tooltipster.min.js"></script>
    <script>
        var order_complete = false;
        <% if getOrder() %>
            order_complete = {$getOrder()};
        <% end_if %>

        $(document).ready(function() {
            $('.tracks-tooltip').tooltipster({
                maxWidth: '300'
            });

            if (order_complete) {
                $('#orderModal').modal('show');
            }
        });
    </script>
    <% include Page_LinkedinInsightTracker %>
</html>
