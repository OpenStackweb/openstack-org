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
<div class="main-body">

    <% include SummitPageHeaderSmall %>

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

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row footer-links">
                <div class="col-lg-2 col-sm-2">
                    <h3>OpenStack</h3>
                    <ul>
                        <li><a href="/foundation">About The Foundation</a></li>
                        <li><a href="http://openstack.org/projects/">Projects</a></li>
                        <li><a href="http://openstack.org/projects/openstack-security/">OpenStack Security</a></li>
                        <li><a href="http://openstack.org/projects/openstack-faq/">Common Questions</a></li>
                        <li><a href="http://openstack.org/blog/">Blog</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <h3>Community</h3>
                    <ul>
                        <li><a href="http://openstack.org/community/">User Groups</a></li>
                        <li><a href="http://openstack.org/community/events/">Events</a></li>
                        <li><a href="http://openstack.org/community/jobs/">Jobs</a></li>
                        <li><a href="http://openstack.org/foundation/companies/">Companies</a></li>
                        <li><a href="https://wiki.openstack.org/wiki/How_To_Contribute">Contribute</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <h3>Documentation</h3>
                    <ul>
                        <li><a href="http://docs.openstack.org">OpenStack Manuals</a></li>
                        <li><a href="http://openstack.org/software/start/">Getting Started</a></li>
                        <li><a href="http://developer.openstack.org">API Documentation</a></li>
                        <li><a href="https://wiki.openstack.org">Wiki</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <h3>Branding & Legal</h3>
                    <ul>
                        <li><a href="http://openstack.org/brand/">Logos & Guidelines</a></li>
                        <li><a href="http://openstack.org/brand/openstack-trademark-policy/">Trademark Policy</a></li>
                        <li><a href="http://openstack.org/privacy/">Privacy Policy</a></li>
                        <li><a href="https://wiki.openstack.org/wiki/How_To_Contribute#Contributor_License_Agreement">OpenStack CLA</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <h3>Stay In Touch</h3>
                    <a href="https://twitter.com/OpenStack" target="_blank" class="social-icons footer-twitter"></a>
                    <a href="https://www.facebook.com/openstack" target="_blank" class="social-icons footer-facebook"></a>
                    <a href="https://www.linkedin.com/company/openstack" target="_blank" class="social-icons footer-linkedin"></a>
                    <a href="https://www.youtube.com/user/OpenStackFoundation" target="_blank" class="social-icons footer-youtube"></a>
                    <!-- <form class="form-inline">
                        <div class="form-group newsletter-form">
                            <label>Join Our Newsletter</label>
                            <input class="newsletter-input" type="input" placeholder="Email">
                            <button type="submit" class="newsletter-btn">Join</button>
                        </div>
                    </form> -->
                    <p class="fine-print">
                        The OpenStack project is provided under the Apache 2.0 license. Openstack.org is powered by <a href="http://rackspace.com" target="_blank">Rackspace Cloud Computing</a>.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Hidden Sidebar Nav -->
    <div class="sidebar-nav">
        <nav>
            <a href="#" class="close-panel"><i class="icon-remove-sign icon-large"></i></a>
            <ul class="sidebar-menu">
                <!-- Microsite Navigation -->

                <% include SummitNav %>

                <!-- End Microsite Navigation -->
            </ul>

            <% if $Summit.RegistrationLink %>
                <a href="$Summit.RegistrationLink" class="btn register-btn-lrg">Register Now</a>
            <% end_if %>

        </nav>
    </div>
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
                    <p><b><a href="https://www.openstack.org/summit/vancouver-2018/travel/">Book your hotel room now at a discounted rate</a></b> through our hotel blocks.</p>
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
