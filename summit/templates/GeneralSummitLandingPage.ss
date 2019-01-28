<!DOCTYPE html>
<html lang="en" $OGNS>

<head>
    <% include Head %>
    <% include Page_GoogleAnalytics %>
    $CurrentSummitPageController().FBTrackingCode
    $CurrentSummitPageController().TwitterTrackingCode
    <!-- GoogleAdWords Start -->
    $CurrentSummitPageController().GATrackingCode
    <!-- GoogleAdWords End -->
    <link rel="stylesheet" type="text/css" href="/themes/openstack/static/css/tooltipster.css" />
</head>

<body>
    <div class="main-body static-summit-about-page all-summit">
        <% include GeneralSummitLandingPageHeader %>

        <div id="wrap">

            <!-- Begin Page Content -->
            $Layout
            <!-- End Page Content -->

        </div>

        <% include DownloadAppModal %>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="button-wrapper">
                            <% include EventbriteRegisterLink Summit=$CurrentSummit(),Position='f' %>

                            <div class="links">
                                $CurrentSummitPageController().getSummitPageText(FooterLinksLeft)
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 openstack">
                        <div class="logo">
                            <a href="https://www.openstack.org"></a>
                            <h5>Organized by the openstack foundation</h5>
                        </div>

                        <div class="links">
                            $CurrentSummitPageController().getSummitPageText(FooterLinksRight)
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <% include Quantcast %>
    <% include TwitterUniversalWebsiteTagCode %>

    <div id="orderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Thanks for Registering! What's Next?</h4>
                </div>
                <div class="modal-body">
                    <p><b><a href="https://www.openstack.org/summit/denever-2019/travel/">Book your hotel room now</a></b> through one of our recommended hotels.</p>
                    <p>An email receipt with details of your Summit registration and purchase will be sent to the address that you registered with.</p>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript" src="/themes/openstack/static/js/jquery.tooltipster.min.js"></script>
<script>
    var order_complete = false;
        <% if $CurrentSummitPageController().getOrder() %>
        order_complete = {$CurrentSummitPageController().getOrder()};
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
